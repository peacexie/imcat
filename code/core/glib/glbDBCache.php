<?php
//数据库-缓存类
class glbDBCache extends glbDBObj{

    public $cache = NULL; // 缓存对象

    function __construct($config=array()){
        parent::__construct($config); 
    }

    //执行原生sql语句，如果sql是查询语句，返回二维数组
    function query($sql,$func=''){ 
        if(empty($sql)) return false;
        $this->sql=$sql;
        //判断当前的sql是否是查询语句
        if($func){
            return parent::query($sql,$func);
        }elseif(strpos(trim(strtolower($sql)),'select')===0){ 
            $data=array();
            //读取缓存
            $data=$this->_dcGet();
            if(!empty($data)){ return $data; }
            //没有缓存，则查询数据库
            $this->connect();
            $data = $this->db->arr($this->sql);
            $this->runTimer('qSelect');
            $this->_dcPut($data);//写入缓存
            return $data;
        }else{ //不是查询条件，执行之后，直接返回
            return parent::query($sql,$func);
        }
    }

    //统计行数
    function count(){ // SELECT $func($field) AS $field FROM $tab WHERE kid='$job'
        $table=$this->options['table'];//当前表
        $field='count(*)';//查询的字段
        $where=$this->_parseCond();//条件
        $this->sql="SELECT $field FROM $table $where";
        //读取缓存
        $re = $this->_dcGet();
        if(!empty($re)){ return $re; }
        $this->connect();            
        $re = $this->db->val($this->sql);
        $this->runTimer('count');
        $this->_dcPut($re);//写入缓存
        return $re;
    }

    //只查询一条信息，返回一维数组    
    function find(){
        $table=$this->options['table'];//当前表
        $field=$this->options['field'];//查询的字段
        $this->options['limit']=1;//限制只查询一条数据
        $where=$this->_parseCond();//条件
        $this->options['field']='*';//设置下一次查询时，字段的默认值
        $this->sql="SELECT $field FROM $table $where";
        //读取缓存
        $data=$this->_dcGet();
        if(!empty($data)){ return $data; }
        $this->connect();
        $data = $this->db->row($this->sql);
        $this->runTimer('find');
        $this->_dcPut($data);//写入缓存
        return $data;
     }

    //查询多条信息，返回数组
    function select(){
        $table=$this->options['table'];//当前表
        $field=$this->options['field'];//查询的字段
        $where=$this->_parseCond();//条件
        $this->options['field']='*';//设置下一次查询时，字段的默认值
        $this->sql="SELECT $field FROM $table $where";
        $data=array();
        //读取缓存
        $data=$this->_dcGet();
        if(!empty($data)){ return $data; }
        //没有缓存，则查询数据库
        $this->connect();
        $data = $this->db->arr($this->sql);
        $this->runTimer('select');
        $this->_dcPut($data);//写入缓存
        return $data;
     }
    
    //初始化缓存类，如果开启缓存，则加载缓存类并实例化
    function _dcInit(){        
        if(is_object($this->cache)){
            return true;
        }elseif($this->config['dc_on']){
            $config = array();
            foreach ($this->config as $key => $val) {
                if($key=='dc_on') continue;
                $pre = substr($key,0,3);
                if($pre=='dc_') $config[substr($key,3)] = $val;
            }
            $this->cache = new extCache($config);
            return true;
        }else{
            return false;
        }
    }
    //读取缓存
    function _dcGet(){
        $key = $this->_dcKey();
        unset($this->options['cache']);
        $data = $key ? $this->cache->get($key) : '';
        if(!empty($data)){
            return $data;
        }else{
            return "";
        }

    }
    //写入缓存
    private function _dcPut($data){
        $key = $this->_dcKey(0);
        unset($this->options['cache']);
        if($key){
            $exp = $this->_dcKey('(exp)');
            return $this->cache->set($key[0],$data,$key[1]);
        }
        return false;    
    }
    private function _dcKey($resql=1){
        $expire = isset($this->options['cache']) ? $this->options['cache'] : $this->config['dc_exp'];
        if(empty($expire)) return false; // 缓存时间为0，不读取缓存
        if(!$this->_dcInit()){
            return false;  
        }
        $arr1 = array(
            'SELECT ','FROM ','WHERE ','AND ','OR ',
            'INSERT INTO ','UPDATE ','DELETE ','REPLACE INTO ', 
            'GROUP BY ','HAVING ','ORDER BY ','LIMIT ',
            ' ',
        ); // LEFT JOIN, RIGHT JOIN INNER JOIN
        $arr2 = array(
            '[s]','[f]','[w]','[a]','[o]',
            '[ins]','[upd]','[del]','[rep]',
            '[gby]','[hav]','[oby]','[m]',
            '',
        );
        $sql = str_replace($arr1,$arr2,$this->sql);
        return $resql ? $sql : array($sql,$expire);
    }

}
