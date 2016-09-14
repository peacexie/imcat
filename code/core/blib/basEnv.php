<?php

// Environment基本环境处理类
class basEnv{	

	// 处理_pbase
	static function runPbase($_pbase){
		global $_cbase;
		// 加载runskip
		if(isset($_cbase['skip'])){
			include(DIR_CODE.'/cfgs/boot/bootskip.php'); 
		}
		// 全局系统配置
		if(!empty($_pbase)){ 
			if(!empty($_pbase)) $_cbase = basArray::Merge($_cbase, $_pbase);
		}
	}

	// 系统信息,魔术变量,时区
	static function runVersion(){
		global $_cbase;
		if(version_compare(PHP_VERSION,'5.4.0','<')) {
			ini_set('magic_quotes_runtime',0);
		}
		date_default_timezone_set($_cbase['sys']['tzcode']);		
	}
	// const,
	static function runConst(){
		global $_cbase;
		/*
		define('IS_CGI',	 substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
		define('IS_WIN',	 strstr(PHP_OS, 'WIN') ? 1 : 0 );
		define('IS_CLI',	 PHP_SAPI=='cli'? 1   :   0);
		//define('IN_APP',   0);
		define('IN_MOBILE',  self::isMobile());
		define('IN_ROBOT',   self::isRobot());
		*/
		define('KEY_TAB36',  '0123456789abcdefghijklmnopqrstuvwxyz'); // 极端情况下用
		define('KEY_TAB32',  '0123456789abcdefghjkmnpqrstuvwxy'); // - iloz (字形可能与数字012混淆)
		define('KEY_TAB30',  '123456789abcdfghjkmnpqrstuvwxy'); // - 0e + iloz (0字形,e读音易混淆)
		define('KEY_TAB24',  '3456789abcdfghjkpqstuvwxy'); // - 012eilmnorz(25) (去除字形读音易混淆者)
		define('KEY_NUM10',  '0123456789');
		define('KEY_NUM16',  '0123456789abcdef'); 
		define('KEY_CHR26',  'abcdefghijklmnopqrstuvwxyz');	

	}

	// 前置处理,运行时常用变量
	static function runCbase(){
		global $_cbase;
		// 运行时常用变量,
		$_cbase['run']['domain'] = $_SERVER['SERVER_NAME'];
		$_cbase['run']['dmtop'] = self::topDomain($_cbase['run']['domain']);
		$_cbase['run']['stamp'] = intval($_cbase['run']['timer']);
		$_cbase['run']['userag'] = self::userAG();
		$_cbase['run']['userip'] = self::userIP();
		$_cbase['run']['query'] = 0; //查询次数
		$_cbase['run']['qtime'] = 0; //查询时间
		$_cbase['run']['jsimp'] = ','; //imp-js:files
		$_cbase['run']['tplname'] = ''; //tpl:name
		$_cbase['run']['tplnow'] = ''; //tpl:now
		$_cbase['run']['tagnow'] = ''; //vopShow::tagParse()使用
		$_cbase['run']['tags'] = ''; //tag:files (未使用?)
		$_cbase['run']['tagjs'] = ''; //tag:files  (未使用?)
		$_cbase['run']['tmpFile'] = array();
		$_cbase['run']['jtype_mods'] = ''; //fldView::lists()使用
		$_cbase['run']['jtype_init'] = ''; //fldView::lists()使用
		$_cbase['run']['sobarnav'] = ''; //dopBSo->Form()使用,搜索条上的导航
		$_cbase['tpl']['tplpend'] = ''; //默认'',除非人工改变
		$_cbase['tpl']['tplpext'] = ''; //默认'',除非人工改变
		//$_cbase['mkv'] = array();
		$_cbase['run']['headed'] = '';
		self::sysHome(); //,topDomain,IP过滤
	}
	
	// 处理skips
	static function runSkips(){
		global $_cbase;
		// 错误处理类 
		if(!isset($_cbase['skip']['error'])){
			self::runError();
		}
		// *** robot
		if(isset($_cbase['skip']['robot'])){
			safBase::robotStop(); 
		}
		// 处理session
		if(!isset($_cbase['skip']['session'])){ 
			if(!session_id()) @session_start();
		}
	}

	// 加载错误处理类 
	static function runError(){
		global $_cbase;
		// 加载错误处理类 
		if(!isset($_cbase['skip']['error']) && $_cbase['debug']['err_hand']){
			ini_set('display_errors', 'On');
			if($_cbase['debug']['err_mode']){
				error_reporting(E_ALL); 
			}else{
				error_reporting(0); 
			}
			set_exception_handler('except_handler_ys'); //注册默认异常处理函数
			set_error_handler('error_handler_ys',E_ALL^E_WARNING^E_NOTICE); //注册默认错误处理函数
		}
		/*
		if(!empty($_cbase['handler']['shutdown'])){
			register_shutdown_function('shutdown_handler_ys');
		}*/
	}

	// 获取客户端软件信息
	static function userAG(){
		$ua = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
		//basStr::filTitle($_SERVER['HTTP_USER_AGENT'])
		$ua = str_replace(array("'","\\"),array("",""),$ua);
		return $ua;
	}
	
	// 获取客户端IP地址('::1','123.234.123.234, 127.0.0.1')(.:[ ])
	static function userIP($flag=0){
		$a = array('f'=>'HTTP_X_FORWARDED_FOR','a'=>'REMOTE_ADDR','c'=>'HTTP_CLIENT_IP'); //'r'=>'HTTP_X_REAL_FORWARDED_FOR',
		$ip = '';
		foreach($a as $k=>$v){
			$v = str_replace(' ','',$v);
			if(!empty($_SERVER[$v]) && !strstr($ip,$_SERVER[$v])){
				$ip .= ';'.($flag ? "$k," : '').$_SERVER[$v];
			}
		}
		if(basArray::inStr(array("'","\\"),$ip)) self::Stop('userIPError');
		$ip = substr($ip,1);
		return $ip;
	}

	// ---- 用户信息 判断 --------------------------------------- 
	
	// 是否搜索引擎来访
	static function isRobot($user_agent=''){
		$rbt = glbConfig::read('uachk','sy');
		$kw_spiders = $rbt['spname'];
		$kw_browsers = $rbt['browsers'];
		$user_agent || $user_agent = self::userAG();
		if(preg_match("/($kw_browsers)/i",$user_agent)){ //!strpos($user_agent,'://') && 
			return false;
		}elseif(preg_match("/($kw_spiders)/i",$user_agent)) return true;
		return false;
	}

	// 是否Weixin()
	static function isWeixin($key='',$ver=0){
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		$wxpos = strpos($uagent, 'MicroMessenger');
		if($ver){
			preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $uagent, $matches);
			return $wxpos ? $matches[2] : '';
		}else{
			return $wxpos;
		}
	}
	
	// 是否Mobile(奇迹方舟(imiku.com))
	static function isMobile($ckey=''){
		if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
			return true;
		}
		if(isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'],"wap")){
			return true;
		}
		$rbt = glbConfig::read('uachk','sy');
		$kw_spkeywd = $rbt['spkeywd'];
		if(preg_match("/($kw_spkeywd)/i", $_SERVER['HTTP_USER_AGENT'])){
			return true;
		}
		if(isset($_SERVER['HTTP_ACCEPT'])){ //协议法，因为有可能不准确，放到最后判断
			$fwap = strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml'); // 如果只支持wml并且不支持html那一定是移动设备
			$fhtm = strpos($_SERVER['HTTP_ACCEPT'], 'text/html'); // 如果支持wml和html但是wml在html之前则是移动设备
			if (($fwap !== false) && ($fhtm === false || ($fwap < $fhtm))) {
		 		return true;
			} 
		}
		return false;
	}

	// topDomain
	static function topDomain($host){
		if(strpos($host,':/')){
			$host = parse_url($host,PHP_URL_HOST);
			//IPv6，这里得到的host有问题
		}
		$arr = explode('.',$host);
		if(!strpos($host,'.')){ //主机名形式:localhost/pcname; IPv6形式:FE80::1, ::1, 2000::1:2345:6789:abcd
			return $host;
		}elseif(is_numeric($arr[count($arr)-1])){ //IPv4
			return $host;
		}else{ //域名
			$cnt = count($arr); 
			$part1 = $arr[$cnt-1]; $part2 = $arr[$cnt-2];
			$re = "$part2.$part1"; //默认
			if($cnt>=3){
				$tcfg = glbConfig::read('dmtop','sy');
				$t3p = '.com.net.org.edu.gov.int.mil.';
				$re3 = $arr[$cnt-3].".$re";
				if(!empty($tcfg[$re])){
					$re = $tcfg[$re]==3 ? $re3 : $re;
				}elseif(strlen($part2)==2 && strlen($part1)==2){ //2.2 www.dg.gd.cn, www.88.cn
					$re = preg_match('/[a-z]{2}/',$part2) ? $re3 : $re;
				}elseif(strlen($part2)==2 && strlen($part1)==3){ //2.3 www.fyh.cn.com, www.88.com
					$re = strpos($t3p,$part1) ? $re3 : $re;
				}elseif(strlen($part2)==3 && strlen($part1)==2){ //3.2 www.txm.cn, www.net.cn
					$re = strpos($t3p,$part2) ? $re3 : $re;
				}
			}
			return $re;
		} 
	}
	// sysHome // HTTP_HOST = SERVER_NAME : SERVER_PORT
	static function sysHome(){
		global $_cbase;
		$host = $_SERVER["HTTP_HOST"]; 
		$http = (@$_SERVER['HTTPS']==='on') ? 'https' : 'http';
		$_cbase['run']['rsite'] = "$http://$host"; 
		$_cbase['run']['rmain'] = "$http://$host".PATH_PROJ; 
		$_cbase['run']['roots'] = "$http://$host".PATH_ROOT; 
	}

	// 缓冲区obSave(...)
	static function obSave($msg=''){
		$msg || $msg = "Contents... ";
		$file = __FUNCTION__;
		echo 'flag1';
		ob_start(); 
		echo $msg.date('Y-m-d H:i:s');
		$data = ob_get_contents();
		ob_end_clean(); 
		comFiles::put(DIR_DTMP."/@temp/test_$file.txt",$data);
		echo('flag2');		
	}
	
	// 缓冲区Start, 替代ob_start(...)
	static function obStart(){
		!ini_get('output_buffering') && ob_start();
	}
	// 缓冲区Clean, 替代ob_end_clean(),ob_clean()
	static function obClean($start=1){
		$obList = ob_list_handlers();
		$obLen = count($obList);
		while($obLen>0){
			ob_clean();
			//ob_end_clean(); //$type=='gzip'
			$obLen--;
		};
		if(!empty($start)) self::obStart();
	}
	// 缓冲区调试
	static function obDebug($start=1){
		$obList = ob_list_handlers();
		$obLen = count($obList);
		$str = "\n<hr>";
		while($obLen>0){
			$c = ob_get_contents();
			$str .= "Debug($obLen):$c<br>\n";
			ob_flush();
			$obLen--;
		};
		echo "$str<hr>";
	}
	static function obShow($data='obData',$die=1){
		basEnv::obClean();
		echo "$data";
		$die && die();
	}
}

