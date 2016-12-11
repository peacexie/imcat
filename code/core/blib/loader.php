<?php 
/**
 * 类的自动加载
 * spl_autoload_register('func_name'); //可以多个...
 * spl_autoload_register(array('autoLoad_ys','load')); 	
 */
class autoLoad_ys{
	
	static $acdirs = array();
	static $cfgpr4 = array();
	static $cfgnsp = array();
	static $cfgmap = array();
	
	static $upath = array();
	
	// autoLoad_ys::ureg('/adpt/wechat'); //code目录下
	// autoLoad_ys::ureg('/a3rd/wechat',0); //root目录下
	static function ureg($upath,$pcode=1){
		$key = $pcode ? 'code' : 'root';
		if(empty(self::$upath[$key]) || !in_array($upath,self::$upath[$key])){ 
			self::$upath[$key][] = $upath;
		}
		spl_autoload_register(array(__CLASS__,'uload'));
	}
	static function uload($name){ 
		foreach(self::$upath as $key=>$kpath){ 
			foreach($kpath as $path){ 
				self::doinc($path."/$name.php",constant("DIR_".uppercase($key)));
			}
		}
	}
	
	static function init(){ 
		require DIR_CODE.'/cfgs/boot/cfg_load.php';
		self::$acdirs = $_cfgs['acdir'];
		self::$cfgpr4 = $_cfgs['acpr4'];
		self::$cfgnsp = $_cfgs['acnsp']; 
		self::$cfgmap = $_cfgs['acmap'];
		spl_autoload_register(array(__CLASS__,'cload')); 
		spl_autoload_register(array(__CLASS__,'vload')); 
	}
	// 核心类库
	static function cload($name){ 
		$path = ''; 
		if(isset(self::$cfgmap[$name])){ // 自定义-class-map
			if(substr(self::$cfgmap[$name],0,1)=='~'){
				$base = DIR_CODE;
				$file = substr(self::$cfgmap[$name],1);
			}else{
				$base = DIR_VENDOR;
				$file = self::$cfgmap[$name];
			} 
			return self::doinc($file,$base);
		}else{ // 按[前缀-目录]对照加载
			$fx3 = substr($name,0,3); //echo "<br>$name";
			foreach(self::$acdirs as $dir=>$fixs){ //
				if(strstr($fixs,$fx3)){ 
					return self::doinc("/$dir/$name.php",DIR_CODE);
				}
			}
		}
	}
	// 第三方
	static function vload($name){ 
		// -pr4规范
		foreach(self::$cfgpr4 as $k=>$v){ 
			if(!strstr($name,$k)) continue; //strpos比file_exists快多了吧？
			$file = str_replace('\\', '/', str_replace($k, $v[0].'/', $name)).'.php'; 
			return self::doinc($file,DIR_VENDOR);
		}
		// -namespace规范
		foreach(self::$cfgnsp as $k=>$v){ 
			if(!strstr($name,$k)) continue; //strpos比file_exists快多了吧？
			$file = str_replace('\\', '/', $v[0].'/'.$name).'.php'; 
			return self::doinc($file,DIR_VENDOR);
		}
		return ''; 
	}
	// inc
	static function doinc($file,$base=''){
		global $_cbase; 
		if(file_exists($base.$file)){ 
			$_cbase['run']['aclass'][] = $file;
			require($base.$file); 
		}
	}
}

/**
 * 权限判断函数
 * 用于未加载核心类库场合
 */
function bootPerm_ys($key='',$re='0',$exmsg=''){
	global $_cbase; // 不能用cfg()
	$tid = preg_replace("/[^\w]/", '', $_cbase['safe']['safil']); 
	$sid = 'pmSessid_'.$tid; //echo $sid;
	if($re=='sid') return $sid;
	$sval = @$_SESSION[$sid];
	if(empty($key)){
		$msg = empty($sval) ? '-' : '';
	}else{ 
		$msg = strstr($sval,$key) ? '' : "$key";
	} 
	if($msg){
		if(!empty($re)) return $msg;
		$exmsg && $exmsg = "<hr>$exmsg";
		die("NO Permission :<br>\n$msg$exmsg"); 
	}
}

/**
 * 一组别名函数（使用Symfony的dump后添加的）
 * 如果与其它程序一起使用，发现有如下函数冲突，请设置[run.outer]参数即可
 * 设置[run.outer]后，如需要用本系统方法，请用autoLoad_ys::init()加载即可
 * 输出:xml,json,jsonp
 */
if(empty($_cbase['run']['outer'])){ 
	// dump(格式化输出：变量，数组，Object)
	function dump($var){  
		basDebug::varShow($var);
	}
	// cfg(读取cbase配置) cfg('sys.cset');
	function cfg($key,$def=''){
		global $_cbase; 
		$org = $_cbase; $re = $def;
		$ak = explode('.',$key);
		foreach ($ak as $k) {
			$org = $re = isset($org[$k]) ? $org[$k] : $def;
		}
		return $re;
	}
	// lang(显示语言标识)
	function lang($mk, $val=''){
		if($val===0){ 
			echo basLang::show($mk, $val===0?'':$val);
		}else{
			return basLang::show($mk, $val);
		} // ??? basLang::ucfg('cfgbase.uinfstate'); 
	}
	// read(读取缓存)
	function read($file,$dir='modcm'){
		// $file:支持格式:domain.dmacc
		if(strpos($file,'.')){
			$tmp = explode('.',$file);
			$file = $tmp[0];
		}
		$res = glbConfig::read($file,$dir);
		return empty($tmp[1]) ? $res : $res[$tmp[1]];
	}
	// req(获得get/post参数)
	function req($key,$def='',$type='Title',$len=255){
		return basReq::val($key,$def,$type,$len);
	}
	// db(获得db对象)
	function db($config=array()){
		return glbDBObj::dbObj($config);
	}
	// user(获得user对象)
	function user($uclass=''){
		return usrBase::userObj($uclass);
	}
	// 格式化url输出
	function surl($mkv='',$type='',$host=0){
		return vopUrl::fout($mkv,$type,$host);
	}
	// cmod模块关闭
	function cmod($key=''){
		$_groups = read('groups'); 
		return !isset($_groups[$key]);
	}
}

/**
 * 一组handler函数
 */
/*
function uerr_handler($msg='') {  
	return $msg; 
}
*/
// 默认异常处理函数
function except_handler_ys($e) {  
	throw new glbError($e); 
}
// 默认错误处理函数
function error_handler_ys($Code,$Message,$File,$Line) {  
	throw new glbError(@$Code,$Message,$File,$Line); 
}
// 当php脚本执行完成,或者代码中调用了exit ,die这样的代码之后：要执行的函数
function shutdown_handler_ys() {  
	//echo "(shutdown)";
	basDebug::bugLogs('handler',"[msg]","shutdown-".date('Y-m-d').".debug",'file');
}

//(!function_exists('intl_is_failure'))
