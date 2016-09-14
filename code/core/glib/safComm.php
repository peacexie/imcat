<?php
(!defined('RUN_MODE')) && die('No Init');

/**
safBase : 常规-安全过滤(Safil=Safety Filter)
  formAuto,formRev,formCheck,
  urlFrom,urlScan,urlStamp
*/
class safComm{ // extends safBase
	
	// 综合认证：
	static function formCAll($mod,$path='',$timeout=3600){
		global $_cbase; 
		$re = '';
		//fromUrl 认证
		self::urlFrom($path);
		$rest = self::formCInit('x', $timeout);
		$code = basReq::val("{$mod}_{$rest[1]}");
		$revc = self::formCVimg($mod, $code, 'check', $timeout, 1);
		if(!empty($rest[0])){ 
			$re = lang('safcomm_vcoderr')."[$rest[0]]";
		}elseif(!empty($revc)){ //认证码 认证
			$re = lang('safcomm_vcoderr')."[$revc]";
		}
		return array($re,$code); //认证结果
	}
	
	// ---- 表单过滤项 ---------------------------------------
	
	// 检查表单项 --- formCVimg()
	static function formCVimg($mod, $vcode, $act, $timeout=3600, $clear=0){ 
		global $_cbase;
		$sform = $_cbase['safe']['safil'];
		if($act=='save'){
			$stamp = $_cbase['run']['stamp']; 
			$encode = $stamp.','.comConvert::sysEncode($sform.strtoupper($vcode),$stamp);
			comCookie::mset('vcodes',0,$mod,$encode); //echo $encode;
			return;
		}else{ //check
			$cookie = comCookie::mget('vcodes',$mod); 
			$stamp = substr($cookie,0,strpos($cookie,',')); 
			$encode = $stamp.','.comConvert::sysEncode($sform.strtoupper($vcode),$stamp);
			if(strlen($cookie)<24 || $cookie!=$encode){
				$re = "VCode-Error[1]！"; 
			}elseif(($_cbase['run']['stamp']-$stamp)>$timeout){
				$re = "VCode-Timeout[2]！";
			}else{
				$re = '';
			}
			$clear && comCookie::mset('vcodes',0,$mod,'null');
			return $re;
		}
	}
	
	// 检查表单项 --- 表单时间戳
	static function formCInit($act='init', $time=3600){  
		global $_cbase;
		$stamp = $_cbase['run']['stamp']; 
		$sform = $_cbase['safe']['safil'];
		$safix = $_cbase['safe']['safix'];
		$rdnum = $_cbase['safe']['rnum']; 
		$re = ''; $len1 = 34; $len2 = 16; //偶数
		$st = date('H',$stamp)>22 ? ($stamp + 5400) : $stamp; //23:xx -> 3600+1800
		$sdate = explode('_',date('m_d',$st));
		$dval  =	 (substr($rdnum,1,4)+substr($rdnum,4,4)*$sdate[0]); 
		$dval .= '_'.(substr($rdnum,6,4)+substr($rdnum,9,4)*$sdate[1]);
		if($act=='init'){
			$encode = comConvert::sysEncode($sform,$stamp,$len1);
			$restr = "<input type='hidden' name='{$safix}[dt]' value='$dval' />";
			$restr .= "<input type='hidden' name='{$safix}[tm]' value='$stamp' />";
			$restr .= "<input type='hidden' name='{$safix}[enc]' value='$encode' />";
			$fmid = basReq::val('fmid','');
			$css1 = basReq::val('css1','txt w60');
			$css2 = basReq::val('css2','fs_vimg');
			$tabi = basReq::val('tabi',19790);
			$senc = comConvert::sysEncode($sform,$stamp,$len2); 
			$vgap = '\\"'; 
			$vstr = "maxlength='5' reg='vimg:3-5' tip='".lang('core.safcomm_vcode')."' url='".PATH_ROOT."/plus/ajax/cajax.php?act=chkVImg&mod={$fmid}&key={$senc}'";
			$restr .= "<input id='{$fmid}_{$senc}' name='{$fmid}_{$senc}' tabindex='$tabi' type='text' class='$css1' onFocus={$vgap}fsCode('{$fmid}'){$vgap} $vstr />";
			$restr .= "<samp id='{$fmid}_vBox' class='$css2' style='display:none'></samp>"; //samp,span, style='width:50px;'
			return $restr;
		}else{
			$re_date = basReq::ark($safix,'dt');
			$re_stamp = basReq::ark($safix,'tm'); 
			$re_encode = basReq::ark($safix,'enc'); 
			$enc = comConvert::sysEncode($sform,$re_stamp,$len2); 
			if(empty($re_stamp) || empty($re_encode)){
				$re = 'Error-Null';
			}elseif($stamp-$re_stamp>$time){ 
				$re = 'Timeout';
			}elseif(!($re_encode==comConvert::sysEncode($sform,$re_stamp,$len1))){ 
				$re = 'Error-E';
			}elseif(!($re_date==$dval)){ 
				$re = 'Error-D';
			}
			return array($re,$enc);
		}
	}

	// QUERY-7参数检测
	static function urlQstr7($re=0){ 
		$q = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
		$q = urldecode($q);
		if($q!=str_replace(array('<','>','"',"'","\\","\r","\n"),'',$q)){
			if($re) return false;
			$msg = "[QUERY]Error!";
			basMsg::show($msg,'die'); 
		}
		return true;
	}
	
	//检测是否外部提交过来的Url
	//expath : 路径匹配部分,可为空
	//die : 默认直接die, 如为空则返回用于判断
	//return : 默认直接die; false:不是外部提交来的地址; true(string):相关信息,表示是外部提交或直接输入网址过来
	//demo: if(xxx::urlFrom('',0)) die("不是来自{PATH_ROOT}的请求！");
	//demo: if(xxx::urlFrom('/dgpeace/_php_test.php'));
	static function urlFrom($expath='',$die=1){
		$re = '';
		$ref = empty($_SERVER["HTTP_REFERER"]) ? '' : $_SERVER["HTTP_REFERER"];
		if(empty($ref)){ //为空:(输入地址等)
			$re = 'Null'; 
		}else{
			$from = self::urlParse($ref); 
			$hnow = self::urlParse($_SERVER['HTTP_HOST']); 
			if(@$from['host']!==@$hnow['host']){ // 匹配:主机/域名+端口 
				$re = $from['host']; 
			}else{ // 匹配:路径
				$npath = PATH_PROJ; //cls_env::mconfig('cmsurl'); // 如:/house/
				if($expath) $npath = str_replace(array('///','//'),'/',"$npath/$expath"); 
				if(strlen($npath)>0 && !preg_match('/^'.preg_quote($npath,"/").'/i',$from['p'])){ 
					$re = $npath;	
				} 
			}
		}
		if($re && $die){ 
			safBase::Stop('urlFrom',$re); 
		}
		return $re; 
	}
	
	/*	获取: host(主域名+端口) 和 path(路径)
	--- Demo --- 
	$url1 = "http://m.txmao.com:808/shopinfo.d?m=fbyzm&mobile=13537432146&city=bj#aa=33";
	$url2 = "http://www.txmao.com:808/example/index.php/dir/test.php?aaa=bbb";
	$url3 = "http://192.168.1.11:888/house/dgpeace/_php_test.php?aaaa=bbb";
	$url4 = "http://[2001:410:0:1:250:fcee:e450:33ab]:8443/file.php/rrf?aa=bb"; 
	echo 'aab:<pre>'.var_dump(xxx::urlParse($url4)).'</pre><br>';
	*/
	static function urlParse($url){	
		$aurl = parse_url($url); //var_dump($aurl);
		$top = basEnv::TopDomain(@$aurl['host']);
		if(!empty($top)){ //IP(含ipv6)
			$aurl['host'] = $top;
		}
		$host = @$aurl['host'].(isset($aurl['port']) ? ':'.$aurl['port'] : '');
		$path = empty($aurl['path']) ? '' : $aurl['path'];
		return array('h'=>$host,'p'=>$path);
	}

	// 注入项扫描关键字
	static function urlScan(){  
		$filters  = "(and|or)\\b.+?(>|<|=|in|like)|<\\s*script\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
		$paras = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
		if(empty($paras)) return;
		if(preg_match("/".$filters."/is",$paras)){   
			safBase::Stop('urlScan');
		}	
	} // \\bEXEC\\b| , \\/\\*.+?\\*\\/| , '|
	
	// --- act=init,stop,flag
	static function urlStamp($act='init',$time=3600){  
		global $_cbase;
		$stamp = $_cbase['run']['stamp']; 
		$sform = $_cbase['safe']['safil'];
		$safix = $_cbase['safe']['safix'];
		if($act=='init'){
			$encode = comConvert::sysEncode($sform,$stamp);
			return "{$safix}[tm]=$stamp&{$safix}[enc]=$encode";
		}else{
			$flag = 0;
			$re_stamp = basReq::ark($safix,'tm');
			$re_encode = basReq::ark($safix,'enc'); 
			if(empty($re_stamp) || empty($re_encode)) $flag = 'empty';
			if($stamp-$re_stamp>$time) $flag = 'timeout';
			if(!($re_encode==comConvert::sysEncode($sform,$re_stamp))) $flag = 'encode';
			if($flag){
				return ($act=='flag') ? $flag : safBase::Stop('urlStamp');
			}
		}
	}
	
	// --- signVeryfy
	// 签名: data=array, keys='k1,k2'
	// 认证: data=timeout, keys='k1,k2'
	static function signVeryfy($data=60,$keys=''){ 
		global $_cbase;
		$stamp = $_cbase['run']['stamp']; 
		$snid = $_cbase['safe']['rnum'];
		$skey = $_cbase['safe']['api'];
		$safix = $_cbase['safe']['safix'];
		$udata = is_array($data) ? $data : $_GET;
		$keys || implode(',',array_keys($data));
		$akey = explode(',',$keys);
		$schk = ''; $sret = '';
		foreach($udata as $key=>$val){
			if(is_array($val)) continue;
			$sret .= "&$key=$val";
			if(in_array($key,$akey)){ 
				$schk .= "&$key=$val";
			}
		}
		if(is_array($data)){
			$encode = comConvert::sysEncode($schk,"$snid.$skey.$stamp");
			return "{$safix}[tm]=$stamp&{$safix}[enc]=$encode{$sret}";
		}else{
			$ustamp = basReq::ark($safix,'tm');
			if($stamp-$ustamp>intval($data)) return 'timeout';
			$usign = basReq::ark($safix,'enc'); 
			$encode = comConvert::sysEncode($schk,"$snid.$skey.$ustamp");
			return $encode==$usign ? '' : 'error';
		}
	}
	/*
	$act = basReq::val('act','sign');
	if($act=='sign'){
		$arr = array('act'=>'check','aa'=>'aa1','bb'=>'bb1',);
		$str = safComm::signVeryfy($arr,'act,aa');
		echo ":<a href='?$str' target='_blank'>$str</a>:";
	}elseif($act=='check'){
		$timeout = basReq::val('timeout','5');
		$res = safComm::signVeryfy($timeout,'act,aa');
		echo $res;
	}*/
	
	
	
		
	// --- End ----------------------------------------
	
}
