<?php
/*
公共模板扩展函数
*/ 
class tex_base{
	
	//protected $xxx = array();
	
	static function init($obj){
		$user = usrBase::userObj('Member'); 
		return $user;
	}
	
	static function pend(){
		global $_cbase;
		$base = $_cbase['tpl']['tplpend']; 
		$ext = $_cbase['tpl']['tplpext'];
		$base || $base = 'jstag,menu'; 
		$js = "setTimeout(\"jcronRun()\",3700);\n";
		strstr($base,'jstag') && $js .= "jtagSend();\n";
		strstr($base,'menu') && $js .= "jsactMenu();\n";
		echo basJscss::jscode($js)."\n";	
	}

}
