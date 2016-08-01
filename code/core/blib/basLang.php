<?php

// basLang多语言类
class basLang{	
	
	static $_CACHES_LG = array();//将读取过的缓存暂存可重用
	
	// 多语言...
	static function get($key, $mod='core'){
		global $_cbase;
		$lang = $_cbase['sys']['lang'];
		if(isset(self::$_CACHES_LG[$mod])){
			$cfgs = self::$_CACHES_LG[$mod];
		}else{
			$cfgs = self::$_CACHES_LG[$mod] = include(DIR_CODE."/lang/$mod/$lang.php");	
		}
		return isset($cfgs[$key]) ? $cfgs[$key] : "($key)[$mod]";
	}
	
	// {lang(core.view_times,$click)}
	// {lang(core.sys_name)}
	static function show($mk, $val=''){
		$arr = explode('.',$mk); 
		$re = self::get($arr[1], $arr[0]);
		if(strlen($val)>0){
			$re = str_replace('{val}',$val,$re);
		} 
		return $re;
	}

}

