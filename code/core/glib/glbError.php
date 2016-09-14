<?php
//cp错误异常类
class glbError extends Exception {

	private $erMsg='';
	private $erFile = '';
	private $erLine = 0;
	private $erCode = 0;
	private $erLevel = 0;
 	private $trace = '';
	//static $die = 0; //,$die=1

	//抛出错误信息，用于外部调用
	static function show($msg="",$code=0) {
		 //self::$die = $die;
		 new self($msg,$code); 
	}

	function __construct($erMsg='',$erCode=0,$erFile='',$erLine=0) { 
		//echo "[$erCode,$erMsg]";
		parent::__construct($erMsg,$erCode);
		$msgmy = $msgphp = ''; //echo "$erMsg='',$erCode=0,$erFile='',$erLine=0";
		if(empty($erCode)){ //0:自动找错误
			/*if(mysqli_errno()){
				$msgmy .= "<i>MySql Error</i>: ".basDebug::hidInfo(mysqli_error(),1)." (".mysqli_errno().")<br>";
				$this->erCode = mysqli_errno();
			}*/
			if(empty($this->erCode)){
				$errs = error_get_last(); 
				if(!empty($errs)){
					$msgphp .= "<i>Message</i>: ".$errs['message']."<br>";
					$this->erFile = basDebug::hidInfo($errs['file']);
					$this->erLine = $errs['line'];
					$this->erCode = $errs['type'];
				}
			}
		}elseif($erCode>0){ //>0
			$this->erCode = $this->getCode();
			$this->erFile = basDebug::hidInfo($this->getFile());
			$this->erLine = $this->getLine();			
		}
		if(empty($erMsg)){
			$erMsg = $msgmy.$msgphp;
		} //echo $msg;	
 		$this->trace = $this->trace();
		$this->erOutput($erMsg); 
	}
	
	//获取trace信息
	protected function trace() {
		$trace = $this->getTrace();
		$tInfo = '';
		foreach($trace as $t) {
			$class = isset($t['class']) ? $t['class'] : '';
			$type = isset($t['type']) ? $t['type'] : '';
			$function = isset($t['function']) ? $t['function'] : '';
			$tInfo .= @$t['file'] . ' (' . @$t['line'] . ') ';
			$tInfo .= $class . $type . $function . "() @ <br />\r\n";
		}
		return $tInfo ;
	}
	
	//错误等级
	protected function getLevel() {
	   $arr = array(	
			1=> '(E_ERROR)',
			2 => '(E_WARNING)',
			4 => '(E_PARSE)',  
			8 => '(E_NOTICE)',  
			16 => 'E_CORE_ERROR',  
			32 => 'E_CORE_WARNING',  
			64 => '(E_COMPILE_ERROR)', 
			128 => '(E_COMPILE_WARNING)',  
			256 => '(E_USER_ERROR)',  
			512 => '(E_USER_WARNING)', 
			1024 => '(E_USER_NOTICE)',  
			2047 => 'E_ALL', 
			2048 => 'E_STRICT'
		);
		return isset( $arr[$this->erCode] ) ? $arr[$this->erCode] : $this->erCode;
	}
	
	//输出错误信息
	 protected function erOutput($message=''){
		global $_cbase; 
		//if(defined('DEBUG') && false == DEBUG){ exit; } 
		@header("HTTP/1.1 404 Not Found");
		$sCode = empty($this->erCode) ? '[RUN]' : $this->erCode;
		$sLevel = $this->getLevel();
		$sLevel = empty($sLevel) ? '[RUN]' : $this->getLevel();
		basMsg::init("Error-$sCode",1);
		$html = '';
		if($this->erFile){
			$html .= "\n<strong> ------ File</strong>: ".$this->erFile.' (Line:'.$this->erLine.')<br>';
		} 
		$html .= "\n<strong> ------ Info</strong>: (Level:$sLevel)<br>".basDebug::hidInfo($message).'<br>';
		$html .= "\n<strong> ------ Trace</strong>: (Time:".date("Y-m-d H:i:s",$_cbase['run']['stamp']).")<br>".basDebug::hidInfo($this->trace).'<br>';
		$html .= "\n<strong> ------ Debug Info</strong>: ".basDebug::runInfo().'<br>';
		$html .= "\n<strong> ------ Now you can</strong>: <a href='{$_SERVER['PHP_SELF']}'>Retry</a> , <a href='javascript:history.back()'>Return</a> OR <a href='".PATH_ROOT."'>Go Homepage</a><br>";
		if(defined('RUN_AJAX')){ $html = strip_tags($html); } 
		die(glbHtml::page('end',$html));
	}
}