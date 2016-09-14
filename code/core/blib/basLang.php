<?php

// basLang多语言类
class basLang{	
	
	static $_CACHES_LG = array();//将读取过的缓存暂存可重用

	// ('fsystem'), ('cfglibs.upload');
	static function ucfg($file, $key=0){
		if(strpos($file,'.')){
			$mk = explode(".",$file);
			$file = $mk[0];
			$key = $mk[1];
		} 
		$re = self::getCfgs($key, $file, 'ucfgs');
		return $re;
	}

	// {lang(core.view_times,$click)}
	// {lang(core.sys_name)}
	static function show($mk, $val='', $dir='kvphp'){
		$mk = str_replace("'",'',$mk);
		if(!strpos($mk,'.')) $mk = "core.$mk";
		$arr = explode('.',$mk); 
		$re = self::getCfgs($arr[1], $arr[0], $dir);
		if(strlen($val)>0){
			$re = str_replace('{val}',$val,$re);
		} 
		return $re;
	}
	
	// 多语言...
	static function getCfgs($key, $mod='core', $dir='kvphp'){
		global $_cbase; 
		$lang = $_cbase['sys']['lang']; 
		if(isset(self::$_CACHES_LG[$mod])){
			$cfgs = self::$_CACHES_LG[$mod];
		}else{
			$flang = DIR_CODE."/lang/$dir/$mod-$lang.php"; 
			$cfgs = self::$_CACHES_LG[$mod] = file_exists($flang) ? include($flang) : array();	
		}
		if(empty($key)) return $cfgs;
		return isset($cfgs[$key]) ? $cfgs[$key] : '{'."$mod.$key".'}';
	}

	// {linc(file.part)} <大段文本>
	static function inc($file, $part='', $uarr=array()){
		global $_cbase;
		$lang = $_cbase['sys']['lang']; 
		$flang = DIR_CODE."/lang/ptinc/$file-$lang.php";
		include($flang); 
		if(isset($reinc[$part])){
			return $reinc[$part];
		}
	}	

	// 字段从数组中选个语言键值
	static function pick($key, $vals=array()){
		global $_cbase;
		if(!is_array($vals)){
			return $vals;
		}
		$lang = $_cbase['sys']['lang']; 
		if($key && isset($vals[$key])){
			return $vals[$key];
		}elseif(isset($vals[$lang])){
			return $vals[$lang];
		}else{
			return reset($vals);
		}
	}	
	
	// 前置处理,ucfg.lang
	// $_cbase['ucfg']['lang'] = '(auto)'; 
	static function auto(){
		global $_cbase; 
		if(empty($_cbase['ucfg']['lang'])) return;
		if($_cbase['ucfg']['lang']=='(auto)'){
			$ulang = basReq::val('lang');
			if(!empty($ulang)){
				$_cbase['sys']['lang'] = $ulang;
				return;
			}
			$lang = comCookie::oget('lang');
			if(!empty($lang)){
				$_cbase['sys']['lang'] = $lang;
				return;
			}
			$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
			$_cbase['sys']['lang'] = $lang=='zh' ? 'cn' : 'en';
		} //dump($_cbase['sys']['lang']);
	}

	// links
	static function links($dir=''){
		global $_cbase;
		$vopfmt = glbConfig::read('vopfmt','ex');
		$url = PATH_ROOT."/plus/ajax/redir.php?lang:{key}";
		if(empty($dir)){
			$tpl = "<a href='$url' title='{title}'>{mini}</a>";
		}elseif(strpos($dir,'</')){
			$tpl = str_replace('{url}',$url,$dir);
		}else{ // lang:cn:&recbl=redir
			$url .= ":&recbk=$dir"; 
			$tpl = "<a href='$url' title='{title}'>{mini}</a>";
		}
		$res = '';
		foreach ($vopfmt['langs'] as $key => $val) {
			$res .= "\n".str_replace(array('{key}','{title}','{mini}'),array($key,$val[0],$val[1]),$tpl);
		}
		return $res;

	}	

	// jimp
	static function jimp($path,$base='',$lang='(auto)',$injs=0){
		global $_cbase; //basLang::auto();
		if($lang=='(auto)') $lang = $_cbase['sys']['lang']; 
		$js1 = basJscss::imp($path,$base,'js')."\n";
		$pajs = str_replace('.js',"-$lang.js",$path);
		$js2 = basJscss::imp($pajs,$base,'js')."\n";
		$res = "$js1$js2";
		if($injs){
			$res = basJscss::write($res);
		}
		echo $res;
	}

}

