<?php
/*
公共模板扩展函数
*/ 
class tex_base{
	
	//protected $xxx = array();
	
	static function base1($show,$a=''){ 
		echo "<br>base1::";
	}
	
	static function init($obj){
		$user = usrBase::userObj();
		return $user;	
	}
	
	static function pend(){
		global $_cbase;
		$base = $_cbase['tpl']['tplpend'];
		$ext = $_cbase['tpl']['tplpext'];
		$base || $base = 'jstag';
		$js = "setTimeout(\"jcronRun()\",3700);\n";
		strstr($base,'jstag') && $js .= "jtagSend();\n";
		$ext && $js .= "$ext;\n";
		echo basJscss::jscode($js)."\n";
	}

}
