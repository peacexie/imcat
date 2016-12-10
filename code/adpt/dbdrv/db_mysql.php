<?php
//mysql数据库基类
class db_mysql {
	
	public $link;	
	public $lastID = 0;
	public $affRows = 0;
	public $dbName = '';
	public $config = array();
	
    // 初始化,是否支持mysqli
    function __construct(){
        if(!function_exists('mysql_connect')){
			glbError::show('mysql NOT SUPPERT!'); 
        } 
    }
	//连接数据库
	function connect($config=array()){ 
		$this->dbName = $config['db_name'];
		$this->config = $config; 
		if(!empty($config['db_conn'])){
			if(!$this->link = @mysql_pconnect($config['db_host'].':'.$config['db_port'], $config['db_user'], $config['db_pass'])){
				$this->error('mysql_connect Error!');
			}
		}else{
			if(!$this->link = @mysql_connect($config['db_host'].':'.$config['db_port'], $config['db_user'], $config['db_pass'])){
				$this->error('mysql_connect Error!');
			}
		}
		if($this->link){
			// if($this->version() > '5.0.1')
			mysql_query("SET character_set_connection=".$config['db_cset'].", character_set_results=".$config['db_cset'].", character_set_client=binary", $this->link);
			mysql_query("SET sql_mode=''", $this->link);
			return mysql_select_db($config['db_name'], $this->link);
		}
	}

	//查询sql语句// select
	function query($sql, $re='def'){
		if(!($res = mysql_query($sql, $this->link))){
			$this->error('MySQL Query Error', $sql);
		}
		if($re=='def'){ //关系数组
			$data = array();
			while($row=mysql_fetch_array($res, 1)) $data[]=$row;
			return $data;
		}else{ //MYSQL_NUM(2) : MYSQL_ASSOC(1) :MYSQL_BOTH(3) 
			$mode = intval($re); $mode = $mode==2 ? 2 : 1;
			return mysql_fetch_array($res, $mode);
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
		if(!($query = mysql_query($sql, $this->link))){
			$this->error('MySQL Query Error', $sql);
		}
		if($rTimer) return 0; // Timer Return
		$this->affRows = mysql_affected_rows($this->link);
		if(preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql)) {
			$this->lastID = ($id = mysql_insert_id($this->link)) >= 0 ? $id : 0;
		}
		return $this->lastID;
        //如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysql_insert_id() 返回的值将不正确。
        //可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
        //$rs = mysql_query("Select LAST_INSERT_ID() as lid",$this->linkID);
        //$row = mysql_fetch_array($rs);
        //return $row["lid"];
	}
	
	// 取得数据表的字段信息
	function fields($tab){
		$q = mysql_query("show full fields from $tab", $this->link);
		$a = array();
		while($r=mysql_fetch_assoc($q)){
			$k = $r['Field']; 
			$t['name'] = $k;
			$t['type'] = $r['Type'];
			$t['notnull'] = $r['Null']==='NO';
			$t['default'] = $r['Default'];
			$t['primary'] = $r['Key']==='PRI'; //!empty()
			$t['autoinc'] = $r['Extra']==='auto_increment';
			$t['Comment'] =  $r['Comment'];
			$a[$k] = $t;	
		}
		return $a;
	}
	// 取得数据库的表信息
	function tables(){
		$dbname = $this->dbName;
		$q = mysql_query("SHOW TABLES FROM $dbname", $this->link);
		$a = array();
		while($r = mysql_fetch_row($q)){ 
			$a[] = $r[0];
		}
		return $a;
	}
	// 取得数据库的表信息
	function tabinfo(){
		$dbname = $this->dbName;
		$q = mysql_query("SHOW TABLE STATUS", $this->link);
		$a = array();
		while($r = mysql_fetch_array($q,1)){
			$a[] = $r; 
		}
		return $a;
	}
	// 取得创建表sql
	function create($tab){
		$q = mysql_query("SHOW CREATE TABLE $tab", $this->link);
		$r = mysql_fetch_row($q);
		return $r[1];
	}
	
	//返回quoteSql语句
	function quoteSql($sql){
		return mysql_real_escape_string($sql);
	}
	
	//输出错误信息
	function error($message='', $sql='') {
		$sql = basDebug::hidInfo($sql,1);
		$func = @$this->config['efunc'];
		if($func) return $func($message);
		$error = (($this->link) ? mysql_error($this->link) : mysql_error());
		$errorno = intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
		$str= "	<i>Info</i>: $message<br>
				<i>SQL</i>: $sql<br>
				<i>Detail</i>: $error<br>
				<i>Code</i>:$errorno"; 
		glbError::show($str); 
	}
	//释放结果内存
	function free($query) {
		return mysql_free_result($query);
	}

	function close(){
		if($this->link) @mysql_close($this->link);
	}
	function __destruct(){
		$this->close();
	}
}

