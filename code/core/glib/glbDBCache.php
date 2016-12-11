<?php
//数据库-缓存类
class glbDBCache extends glbDBObj{

	public $cache = NULL; // 缓存对象

	function __construct($config=array()){
		parent::__construct($paras); 
	}

	//执行原生sql语句，如果sql是查询语句，返回二维数组
	function query($sql,$func=''){ 
		if(empty($sql)) return false;
		$this->sql=$sql;
		//判断当前的sql是否是查询语句
		if($func){
			return parent::($sql,$func)
		}elseif(strpos(trim(strtolower($sql)),'select')===0){ 
			$data=array();
			//读取缓存
			$data=$this->_dcGet('query');
			if(!empty($data)){ return $data; }
			//没有缓存，则查询数据库
			$this->connect();
			$data = $this->db->arr($this->sql);
			$this->runTimer('qSelect');
			$this->_dcPut($data,'query');//写入缓存
			return $data;
		}else{ //不是查询条件，执行之后，直接返回
			return parent::($sql,$func);
		}
	}

	//统计行数
	function count(){ // SELECT $func($field) AS $field FROM $tab WHERE kid='$job'
		$table=$this->options['table'];//当前表
		$field='count(*)';//查询的字段
		$where=$this->_parseCond();//条件
		$this->sql="SELECT $field FROM $table $where";
		$data="";
		//读取缓存
		$data=$this->_dcGet('count');
		if(!empty($data)){ return $data; }
		$this->connect();			
		$data['count(*)'] = $this->db->val($this->sql);
		$this->runTimer('count');
		$this->_dcPut($data['count(*)'],'count');//写入缓存
		return $data['count(*)'];
	}

	//只查询一条信息，返回一维数组	
	function find(){
		$table=$this->options['table'];//当前表
		$field=$this->options['field'];//查询的字段
		$this->options['limit']=1;//限制只查询一条数据
		$where=$this->_parseCond();//条件
		$this->options['field']='*';//设置下一次查询时，字段的默认值
		$this->sql="SELECT $field FROM $table $where";
		$data="";
		//读取缓存
		$data=$this->_dcGet('find');
		if(!empty($data)){ return $data; }
		$this->connect();
		$data = $this->db->row($this->sql);
		$this->runTimer('find');
		$this->_dcPut($data,'find');//写入缓存
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
		$data=$this->_dcGet('select');
		if(!empty($data)){ return $data; }
		//没有缓存，则查询数据库
		$this->connect();
		$data = $this->db->arr($this->sql);
		$this->runTimer('select');
		$this->_dcPut($data,'select');//写入缓存
		return $data;
	 }
	
	//初始化缓存类，如果开启缓存，则加载缓存类并实例化
	function _dcInit(){		
		if(is_object($this->cache)){
			return true;
		}elseif($this->config['dc_type']){
			require_once(DIR_CODE.'/adpt/cache/extCache.php');
			$config['DATA_CACHE_PATH']=DIR_DTMP.$this->config['dc_path'].'/';
			$config['DATA_CACHE_TIME']=$this->config['dc_tmout'];
			$config['DATA_CACHE_CHECK']=$this->config['dc_check'];		
			$config['DATA_CACHE_FILE']=$this->config['dc_file'];
			$config['DATA_CACHE_SIZE']=$this->config['dc_size'];
			$config['DATA_CACHE_FLOCK']=$this->config['dc_flock'];
			$this->cache=new extCache($config);
			return true;
		}else{
			return false;
		}
	}
	//读取缓存
	function _dcGet($cpre){
		$expire=isset($this->options['cache'])?$this->options['cache']:$this->config['dc_tmout'];
		//缓存时间为0，不读取缓存
		if($expire==0) return false;
		$data = "";	
		if($this->_dcInit()){
			 $data=$this->cache->get(md5($cpre.$this->sql));
		}
		if(!empty($data)){
			unset($this->options['cache']);
			return $data;
		}else{
			return "";
		}

	}
	//写入缓存
	private function _dcPut($data,$cpre){	
		$expire=isset($this->options['cache'])?$this->options['cache']:$this->config['dc_tmout'];
		unset($this->options['cache']);
		//缓存时间为0，不读取缓存
		if($expire==0) return false;
		if($this->_dcInit()){	
			return $this->cache->set(md5($cpre.$this->sql),$data,$expire);	
		}
		return false;	
	}

}
