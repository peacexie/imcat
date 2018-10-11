<?php
namespace imcat;

//数据库操作类，加载了外部的数据库驱动类

class glbDBObj{

    public $db = NULL; // 当前数据库操作对象
    public $config = array();
    public $driver = '';
    public $sql = '';//sql语句，主要用于输出构造成的sql语句
    public $pre = '';//表前缀，主要用于在其他地方获取表前缀
    public $ext = '';//表后缀，主要用于在其他地方获取表后缀
    public $data = array();// 数据信息
    public $otps = array(); // 查询表达式参数
    static $uobj = array(); //实例化的前数据对象

    function __construct($config=array()){
        $this->config = empty($config) ? self::getCfg() : $config;
        $this->otps['field'] = '*';//默认查询字段
        $this->driver = $this->config['db_driver'];
        $this->pre = $this->config['db_prefix'];//数据表前缀
        $this->ext = $this->config['db_suffix'];//数据表后缀
    }

    //连接数据库
    function connect(){ 
        global $_cbase;
        if(!is_object($this->db)){ //$this->db不是对象，则连接数据库
            $_cbase['run']['qstart'] = microtime(1); //分析Start
            $file = 'db'.ucfirst($this->driver);
            require_once DIR_IMCAT."/adpt/dbdrv/{$file}.php";
            $class = "\\imcat\\$file";
            $this->db = new $class();//连接数据库
            $this->db->connect($this->config);
        }
    }

    //执行时间分析
    function runTimer($mode='-'){
        global $_cbase;
        $run_end = microtime(1); //分析Start
        $run_one = $run_end-$_cbase['run']['qstart'];
        $_cbase['run']['qtime'] += $run_one;
        $_cbase['run']['query']++;
        $tpl = $_cbase['run']['tplname'];
        $noid = $this->runChkbug($_cbase,$mode); 
        if(empty($noid)) return;
        if($_cbase['debug']['db_sql']=='db'){
            $info = basDebug::bugInfo();
            $sql = basReq::in($this->sql); $used = 1000*$run_one; $run = $_cbase['run']; 
            $kid = basKeyid::kidTemp().$noid;//.basKeyid::kidRand('24',4);
            $vals = "'$kid','$sql','$used','{$info['vp']}','$tpl','','{$run['stamp']}'"; 
            $this->db->run("INSERT INTO ".$this->table('logs_dbsql',2)."(kid,`sql`,used,page,tpl,tag,atime)VALUES($vals)"); 
        }elseif(!empty($_cbase['debug']['db_sql'])){
            $fmt = $_cbase['debug']['db_sql'];
            $file = date($fmt).".dbsql";
            basDebug::bugLogs('dbobj',$this->sql,$file,'file');
        }
    }

    function runChkbug($_cbase, $mode){
        if(!strpos($_cbase['debug']['db_acts'],$mode)) return 0;
        $indefine = 0;
        foreach($_cbase['debug']['db_area'] as $key){
            if(defined($key)){
                $indefine = 1;
                break;
            }
        }
        if(!$indefine) return 0;
        static $__noid;
        if(empty($__noid)){
            $__noid = 101;
        }else{
            $__noid++;    
        }
        return ($__noid>=960) ? 0 : $__noid;
    }

    //回调方法，连贯操作的实现
    function __call($method, $args) {
        $method = strtolower($method);
        if(in_array($method,array('field','data','where','groups','having','order','limit','cache'))){
            $this->otps[$method] = $args[0]; //接收数据
            return $this; //返回对象，连贯查询
        }else{
            glbError::show($method.':No Found.');
        }
    }
    
    //设置表，$nofix为true的时候，不加上默认的表前缀
    function table($table, $nofix=false){
        $full = '`'.$this->pre.$table.$this->ext.'`';
        if($nofix===2){ 
            return $full;
        }elseif($nofix){
            $this->otps['table'] = '`'.$table.'`';
        }else{
            $this->otps['bare_tab'] = $table;
            $this->otps['table'] = $full;
        }
        return $this;
    }

    //执行原生sql语句，如果sql是查询语句，返回二维数组
    function query($sql){ 
        $this->sql = $sql;
        //判断当前的sql是否是查询语句
        if(strpos(trim(strtolower($sql)),'select')===0){ 
            $data = $this->db->arr($this->sql);
            $this->runTimer('qSelect');
            return $data;
        }else{ //不是查询条件，执行之后，直接返回
            $query = $this->run($this->sql,'qOther');
            return $query;
        }
    }

    //执行sql语句
    function run($sql, $act=''){ 
        global $_cbase;
        $this->sql = $sql;
        $re = $this->db->run($this->sql);
        $this->runTimer($act);
        return $re;
    }

    //统计行数
    function count(){
        $table = $this->otps['table'];//当前表
        $field = 'count(*)';//查询的字段
        $where = $this->_parseCond();//条件
        $this->sql="SELECT $field FROM $table $where";          
        $re = $this->db->val($this->sql);
        $this->runTimer('count');
        return $re;
    }

    //只查询一条信息，返回一维数组    
    function find(){
        $table = $this->otps['table'];//当前表
        $field = $this->otps['field'];//查询的字段
        $this->otps['limit']=1;//限制只查询一条数据
        $where = $this->_parseCond();//条件
        $this->otps['field']='*';//设置下一次查询时，字段的默认值
        $this->sql="SELECT $field FROM $table $where";
        $data = array();
        $data = $this->db->row($this->sql);
        $this->runTimer('find');
        return $data;
    }

    //查询多条信息，返回数组
    function select(){
        $table = $this->otps['table'];//当前表
        $field = $this->otps['field'];//查询的字段
        $where = $this->_parseCond();//条件
        $this->otps['field'] = '*';//设置下一次查询时，字段的默认值
        $this->sql = "SELECT $field FROM $table $where";
        $data = array();
        $data = $this->db->arr($this->sql);
        $this->runTimer('select');
        return $data;
    }

    //插入数据
    function insert($log='a') {
        $table = $this->otps['table'];//当前表
        $data = $this->_parseData('add',$log);//要插入的数据
        $this->sql = "INSERT INTO $table $data";
        $this->run($this->sql,'insert');
        if($this->db->affRows){
            $id = $this->db->lastID;
            return empty($id)?$this->db->affRows:$id;
        }
        return false;
    }

    //替换数据
    function replace($log='a') {
        $table = $this->otps['table'];//当前表
        $data = $this->_parseData('add',$log);//要插入的数据
        $this->sql="REPLACE INTO $table $data" ;
        $this->run($this->sql,'replace');
        if($this->db->affRows){
            return  $this->db->lastID;
        }
        return false;
    }

    //修改更新
    function update($log='e'){ 
        $table = $this->otps['table'];//当前表
        $data = $this->_parseData('save',$log);//要更新的数据
        $where = $this->_parseCond();//更新条件
        if(empty($where)) return false;    //修改条件为空时，则返回false，避免不小心将整个表数据修改了
        $this->sql = "UPDATE $table SET $data $where" ;
        $this->run($this->sql,'update');
        return $this->db->affRows;
    }

    //删除
    function delete(){
        $table = $this->otps['table'];//当前表
        $where = $this->_parseCond();//条件
        if(empty($where)) return false;    //删除条件为空时，则返回false，避免数据不小心被全部删除
        $this->sql = "DELETE FROM $table $where";
        $this->run($this->sql,'delete');
        return $this->db->affRows;
    }

    // 取得数据表的字段信息
    function fields($tab){
        $tab = is_array($tab) ? $tab[0] : $tab;
        $a = $this->db->fields("$this->pre$tab$this->ext");
        $this->runTimer('fields');
        return $a;
    }

    // 取得数据库的表信息
    function tables($info=0){
        $a = $info ? $this->db->tabinfo() : $this->db->tables();
        $this->runTimer('tables'); 
        $tab = array();
        foreach($a as $v){ 
            if($info){
                $v['Name'] = str_replace(array("($this->pre","$this->ext)"),'',"({$v['Name']})");
                if(($this->pre && strstr($v['Name'],'(')) || ($this->ext && strpos($v['Name'],')'))) continue;
                $tab[] = $v;
            }else{
                $table = str_replace(array("($this->pre","$this->ext)"),'',"($v)");
                if(($this->pre && strstr($table,'(')) || ($this->ext && strpos($table,')'))) continue;
                $tab[] = $table;
            }
        }
        return $tab;
    }

    // 取得创建表sql
    function create($tab){
        $a = $this->db->create("{$this->pre}$tab{$this->ext}");
        $this->runTimer('create');
        return $a;
    }
    //返回sql语句
    function getSql(){
        return $this->sql;
    }
    //返回quoteSql语句
    function quoteSql($sql){
        return $this->db->quoteSql($sql);
    }

    //解析数据,添加数据时$type=add,更新数据时$type=save
    public function _parseData($type, $log='') {
        if(empty($this->otps['data'])){
            die("Error, Null-Data!");
        }
        //如果数据是字符串，直接返回
        if(is_string($this->otps['data'])){
            return $this->otps['data'];
        }
        $arfix = array('advs_','base','bext','coms_','docs_','types','users');
        if($log && in_array(substr($this->otps['bare_tab'],0,5), $arfix)){
            $dlog = basSql::logData($log);
            $this->otps['data'] = array_merge($dlog, $this->otps['data']);
        }
        switch($type){
            case 'add':
                $data=array();
                $data['key'] = "";
                $data['value'] = "";
                foreach($this->otps['data'] as $key=>$value){
                    $data['key'] .= "`$key`,";
                    $data['value'] .= "'$value',";
                }
                $data['key'] = substr($data['key'], 0,-1);//去除后面的逗号
                $data['value'] = substr($data['value'], 0,-1);//去除后面的逗号
                return " (".$data['key'].") VALUES (".$data['value'].") ";
                break;
            case 'save':
                $data = "";
                foreach($this->otps['data'] as $key=>$value){
                    $data .= "`$key`='$value',";
                }
                $data = substr($data, 0,-1);    //去除后面的逗号
                return $data;
                break;
            default:
                return false;
        }        
    }

    //解析sql查询条件
    public function _parseCond() {
        $cond = ""; 
        //解析where()方法
        if(!empty($this->otps['where'])){
            $cond = " WHERE ";
            if(is_string($this->otps['where'])){
                $cond .= $this->otps['where'];
            }else if(is_array($this->otps['where'])){
                    foreach($this->otps['where'] as $key=>$value){
                         $cond .= " `$key`='$value' AND ";
                    }
                    $cond = substr($cond, 0,-4);    
            }else{
                $cond = "";
            }
            unset($this->otps['where']);//清空$this->otps['where']数据
        }
        if(!empty($this->otps['groups'])&&is_string($this->otps['groups'])){
            $cond .= " GROUP BY ".$this->otps['groups'];
            unset($this->otps['groups']);
        }
        if(!empty($this->otps['having'])&&is_string($this->otps['having'])){
            $cond .= " HAVING ".$this->otps['having'];
            unset($this->otps['having']);
        }
        if(!empty($this->otps['order'])&&is_string($this->otps['order'])){
            $cond .= " ORDER BY ".$this->otps['order'];
            unset($this->otps['order']);
        }
        if(!empty($this->otps['limit'])&&(is_string($this->otps['limit'])||is_numeric($this->otps['limit']))){
            $cond .= " LIMIT ".$this->otps['limit'];
            unset($this->otps['limit']);
        }
        if(empty($cond))
            return "";
        return $cond;
    }
    
    // 
    static function dbObj($config=array(), $catch=0){ 
        $dbcfg = self::getCfg(); 
        if(empty($config)){
            $key = 'db0_main';
        }elseif(is_string($config)){ // `user`
            $key = $config;
            $config = isset($dbcfg[$key]) ? $dbcfg[$key] : $dbcfg;
            $key = isset($dbcfg[$key]) ? "db1_$key" : 'db0_main';
        }else{ // array()
            $config = array_merge($dbcfg, $config);
            $key = 'dba_'.$config['db_host'].'_'.$config['db_name'];
        }
        $key .= $catch;
        if(empty(self::$uobj[$key])){
            $class = $catch ? '\\imcat\\glbDBCache' : '\\imcat\\glbDBObj';
            self::$uobj[$key] = new $class($config); 
            self::$uobj[$key]->connect($config); 
        }
        return self::$uobj[$key];
    }
    
    static function getCfg($key=''){
        static $dbcfg;
        if(empty($dbcfg)){
            require DIR_ROOT.'/cfgs/boot/cfg_db.php';
            $dbcfg = $_cfgs;
        }
        return ($key && isset($dbcfg[$key])) ? $dbcfg[$key] : $dbcfg;
    }

}
