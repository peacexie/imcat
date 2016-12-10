<?php
// PDO数据库驱动 
class db_pdox{

	public $pdo = null;
	public $lastID = 0;
	public $affRows = 0;
	public $dbName = '';
	public $config = array();
	public $dbType = ''; //默认mysql
	public $sql = '';

    // 初始化,是否支持PDO
    function __construct(){
        if(!class_exists('PDO')){
			glbError::show('PDO NOT SUPPERT!'); 
        } 
    }
    // 连接数据库
    function connect($config='') { 
		$this->dbType = empty($config['db_type']) ? 'mysql' : $config['db_type'];
		if(empty($config['db_dsn'])){
			$config['db_dsn'] = $this->dbType.':host='.$config['db_host'].';dbname='.$config['db_name'].';port='.$config['db_port'].'';
		} 
		// $this->dbName = $config['db_name'];
		$this->config = $config; 
	
		if(!empty($config['db_conn'])) {
			$config['params'][PDO::ATTR_PERSISTENT] = true;
		}
		try{
			@$this->pdo = new PDO( $config['db_dsn'], $config['db_user'], $config['db_pass']);
			@$this->pdo->exec('SET NAMES '.$config['db_cset']);
		}catch(PDOException $e){
			$this->error($e->getMessage());
		}
		/*
		if(!$this->pdo) {
			$e = new PDOException;
			$this->error($e->getMessage());
		}//*/
		
    }

    // 执行查询 返回数据集// select
    function query($sql, $re='def'){ 
		$this->sql = $sql;
		$res = $this->pdo->query($sql); 
		//try{
		if($res){
			$res->setFetchMode($re=='1' ? PDO::FETCH_NUM : PDO::FETCH_ASSOC); 
		}else{ 
			$this->error();
		}
		return $re=='all' ? $res->fetchAll() : $res;
    }
	function arr($sql) { // fetch_array
		return $this->query($sql, 'all');
	}
	function row($sql) { // row
		//dump($sql);
		return $this->query($sql)->fetch();
	}
	function val($sql) { // val
		return $this->query($sql, 1)->fetchColumn();
	}
    // 执行语句(update, delete insert, other)
    function run($sql, $rTimer=0){ 
        $affRows = $this->pdo->exec($sql);
		if($rTimer) return 0; // Timer Return
		$this->affRows = $affRows;
		if(preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $sql)) {
			 switch($this->dbType) {
				case 'PGSQL':
				case 'SQLITE':
				case 'MSSQL':
				case 'SQLSRV':
				case 'IBASE':
				case 'mysql':
					$this->lastID = $this->pdo->lastInsertId();
					break;
				case 'ORACLE':
				case 'OCI':
					$sequenceName = $this->table;
					$vo = $this->pdo->query("SELECT {$sequenceName}.currval currval FROM dual");
					$this->lastID = $vo?$vo[0]["currval"]:0;
			}
		}
		return $this->affRows;
    }

	// 取得创建表sql
	function create($tab){ 
		$res = $this->query("SHOW CREATE TABLE $tab")->fetch();
		return $res['Create Table'];
	}
    // 取得数据表的字段信息
    function fields($tableName) { 
		switch(strtoupper($this->dbType)) {
			case 'MSSQL':
			case 'SQLSRV':
				$sql   = "SELECT   column_name as 'Name',   data_type as 'Type',   column_default as 'Default',   is_nullable as 'Null'
						  FROM    information_schema.tables AS t
						  JOIN    information_schema.columns AS c
						  ON  t.table_catalog = c.table_catalog
						  AND t.table_schema  = c.table_schema
						  AND t.table_name    = c.table_name
						  WHERE   t.table_name = '$tableName'";
				break;
			case 'SQLITE':
				$sql   = 'PRAGMA table_info ('.$tableName.') ';
				break;
			case 'ORACLE':
			case 'OCI':
				$sql   = "SELECT a.column_name \"Name\",data_type \"Type\",decode(nullable,'Y',0,1) notnull,data_default \"Default\",decode(a.column_name,b.column_name,1,0) \"pk\" "
				  ."FROM user_tab_columns a,(SELECT column_name FROM user_constraints c,user_cons_columns col "
				  ."WHERE c.constraint_name=col.constraint_name AND c.constraint_type='P' and c.table_name='".strtoupper($tableName)
				  ."') b where table_name='".strtoupper($tableName)."' and a.column_name=b.column_name(+)";
				break;
			case 'PGSQL':
				$sql   = 'select fields_name as "Name",fields_type as "Type",fields_not_null as "Null",fields_key_name as "Key",fields_default as "Default",fields_default as "Extra" from table_msg('.$tableName.');';
				break;
			case 'IBASE':
				break;
			case 'MYSQL':
			default:
				$sql   = 'DESCRIBE '.$tableName;//备注: 驱动类不只针对mysql，不能加``
		}dump($sql);
        $result = $this->query($sql);
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $val            =   array_change_key_case($val);
                $val['name']    =   isset($val['name'])?$val['name']:"";
                $val['type']    =   isset($val['type'])?$val['type']:"";
                $name           =   isset($val['field'])?$val['field']:$val['name'];
                $info[$name]    =   array(
                    'name'    => $name ,
                    'type'    => $val['type'],
                    // 'notnull' => (bool)(((isset($val['null'])) && ($val['null'] === '')) || ((isset($val['notnull'])) && ($val['notnull'] === ''))), // not null is empty, null is yes
                    'notnull' => $val['null']==='NO',
					'default' => isset($val['default'])? $val['default'] :(isset($val['dflt_value'])?$val['dflt_value']:""),
                    'primary' => isset($val['key'])?strtolower($val['key']) == 'pri':(isset($val['pk'])?$val['pk']:false),
                    'autoinc' => isset($val['extra'])?strtolower($val['extra']) == 'auto_increment':(isset($val['key'])?$val['key']:false),
					'Comment' => @$val['Comment'], //Comment, COLUMN_COMMENT
                );
				//$info[$name]    =   $val;
            }
        }
        return $info;
    }
    // 取得数据库的表信息
    function tables() {
		switch($this->dbType) {
		case 'ORACLE':
		case 'OCI':
			$sql   = 'SELECT table_name FROM user_tables';
			break;
		case 'MSSQL':
		case 'SQLSRV':
			$sql   = "SELECT TABLE_NAME	FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
			break;
		case 'PGSQL':
			$sql   = "select tablename as Tables_in_test from pg_tables where  schemaname ='public'";
			break;
		case 'IBASE':
			// 暂时不支持
			throw_exception(L('_NOT_SUPPORT_DB_').':IBASE');
			break;
		case 'SQLITE':
			$sql   = "SELECT name FROM sqlite_master WHERE type='table' "
					 . "UNION ALL SELECT name FROM sqlite_temp_master "
					 . "WHERE type='table' ORDER BY name";
			 break;
		case 'MYSQL':
		default:
			$sql    = 'SHOW TABLES ';
		}
        $result = $this->query($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }
	
    // 取得数据库的表信息(仅mysql)
    function tabinfo() {
		$result = $this->arr('SHOW TABLE STATUS');
        return $result;
	}

	//返回quoteSql语句
	function quoteSql($sql){
		return $this->pdo->quote($sql);
	}

	// 数据库错误信息
    function error($msg='', $sql=''){ 
		$sql = basDebug::hidInfo($sql,1);
		//$func = empty($this->config['efunc']) ? '' $this->config['efunc']: ;
		if(!$func = empty($this->config['efunc'])) return $func($message);
        if(empty($msg)){
			if(!empty($this->PDOStatement)) {
				$error = $this->PDOStatement->errorInfo();
				$this->error = '<i>Info</i>: '.$error[1].':'.$error[2].'<br>';
			}else{
				$this->error = '';
			}
			if(!empty($this->queryStr)){
				$this->error .= "<i>SQL</i>: ".$this->queryStr.'<br>';
			}else{
				$this->error .= "<i>SQL</i>: ".$this->sql.'<br>';
			}
		}else{
			$this->error = $msg;	
		}
		glbError::show($msg); 
    }
	
    // 释放查询结果
    function free() {
        $this->PDOStatement = null;
    }
    // 关闭数据库
    function close() {
        $this->pdo = null;
    }
	function __destruct(){
		$this->close();
	}

}
