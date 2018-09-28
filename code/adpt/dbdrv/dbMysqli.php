<?php
namespace imcat;

//mysqli数据库基类
class dbMysqli {
    
    public $link;    
    public $lastID = 0;
    public $affRows = 0;
    public $dbName = '';
    public $config = array();
    
    // 初始化,是否支持mysqli
    function __construct(){
        if(!function_exists('mysqli_connect')){
            glbError::show('mysqli NOT SUPPERT!'); 
        } 
    }
    //连接数据库
    function connect($config=array()){
        $this->dbName = $config['db_name'];
        $this->config = $config; 
        if(!$this->link = @mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name'], $config['db_port'])) {
            $this->error('mysqli_connect Error!');
        }else{
            mysqli_query($this->link, "SET character_set_connection=".$config['db_cset'].", character_set_results=".$config['db_cset'].", character_set_client=binary");
        }
        /*/ 
        mysqli_query($this->link, "SET sql_mode=''");
        return mysqli_select_db($this->link, $config['db_name']);
        //*/
    }
    
    //查询sql语句// select
    function query($sql, $re='def'){
        if(!($res = mysqli_query($this->link, $sql))){
            $this->error('MySQL Query Error', $sql);
        }
        if($re=='def'){ //关系数组
            $data = array();
            while($row=mysqli_fetch_array($res, 1)) $data[]=$row;
            return $data;
        }else{ //MYSQL_NUM(2) : MYSQL_ASSOC(1) :MYSQL_BOTH(3) 
            $mode = intval($re); $mode = $mode==2 ? 2 : 1;
            return mysqli_fetch_array($res, $mode);
        }
    }
    function arr($sql) { // fetch_array
        return $this->query($sql);
    }
    function row($sql, $type=1) { // row
        return $this->query($sql, $type);
    }
    function val($sql) { // val
        $row = $this->row($sql, 2);
        return @$row[0];
    }
    // 执行语句(update, delete insert, other)
    function run($sql, $rTimer=0){ 
        if(!($query = mysqli_query($this->link, $sql))){
            $this->error('MySQL Query Error', $sql);
        }
        if($rTimer) return 0; // Timer Return
        $this->affRows = mysqli_affected_rows($this->link);
        if(preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql)) {
            $this->lastID = ($id = mysqli_insert_id($this->link)) >= 0 ? $id : 0;
        }
        return $this->lastID;
        //如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysqli_insert_id() 返回的值将不正确。
        //可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
        //$rs = mysqli_query($this->linkID, "Select LAST_INSERT_ID() as lid");
        //$row = mysqli_fetch_array($rs);
        //return $row["lid"];
    }
    
    // 取得数据表的字段信息
    function fields($tab){
        $q = mysqli_query($this->link, "show full fields from $tab");
        $a = array();
        if($q){
        while($r=mysqli_fetch_assoc($q)){
            $k = $r['Field']; 
            $t['name'] = $k;
            $t['type'] = $r['Type'];
            $t['notnull'] = $r['Null']==='NO';
            $t['default'] = $r['Default'];
            $t['primary'] = $r['Key']==='PRI'; //!empty()
            $t['autoinc'] = $r['Extra']==='auto_increment';
            $t['Comment'] =  $r['Comment'];
            $a[$k] = $t;    
        } }
        return $a;
    }
    // 取得数据库的表信息
    function tables(){
        $dbname = $this->dbName;
        $q = mysqli_query($this->link, "SHOW TABLES FROM $dbname"); 
        $a = array();
        while($r = mysqli_fetch_row($q)){
            $a[] = $r[0]; 
        }
        return $a;
    }
    // 取得数据库的表信息
    function tabinfo(){
        $dbname = $this->dbName;
        $q = mysqli_query($this->link, "SHOW TABLE STATUS");
        $a = array();
        while($r = mysqli_fetch_array($q,1)){
            $a[] = $r; 
        }
        return $a;
    }
    // 取得创建表sql
    function create($tab){ 
        $q = mysqli_query($this->link, "SHOW CREATE TABLE $tab");
        $r = mysqli_fetch_row($q); 
        return $r[1];
    }
    
    //返回quoteSql语句
    function quoteSql($sql){
        return mysqli_real_escape_string($this->link,$sql);
    }

    //输出错误信息
    function error($message='', $sql='') { 
        $sql = basDebug::hidInfo($sql,1);
        $sql = str_replace(array('<','>'),array('&lt;','&gt;'),$sql);
        $func = @$this->config['efunc'];
        if($func) return $func($message);
        @$error = (($this->link) ? mysqli_error($this->link) : mysqli_connect_error());
        @$errorno = intval(($this->link) ? mysqli_errno($this->link) : mysqli_errno());
        $msga = array(
            'Resume' => $message,
            'Detail' => $error,
            'sql' => $sql,
        );
        glbError::show($msga,$errorno); 
    }
    //释放结果内存
    function free($query) {
        return mysqli_free_result($query);
    }

    function close(){
        if($this->link) @mysqli_close($this->link);
    }
    function __destruct(){
        $this->close();
    }

}
