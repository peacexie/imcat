<?php

// 显示相关函数; 单独函数可先用new exvJump();自动加载
class exvJump{

	static $jcfg = array();

	// 获得多语言-跳转地址
	static function getLang(){
	    $jcfg = self::getCfgs();
	    $nkey = $jcfg['deflang']; //未找到地区时的默认网站
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
		//$_cbase['sys']['lang'] = $lang=='zh' ? 'cn' : 'en';
	    foreach($jcfg['langs'] as $key=>$kname){
	        if($lang==$key){
	            $nkey = $kname;
	            break;        
	        }
	    }
	    $nurl = vopUrl::fout("$kname:0"); 
	    return $nurl;
	}

	// 获得分站-跳转地址
	static function getDir($uaddr){
	    $jcfg = self::getCfgs();
	    $nkey = $jcfg['defsite']; //未找到地区时的默认网站
	    foreach($jcfg['sites'] as $key=>$kname){
	        if(strstr($uaddr,$kname)){
	            $nkey = $key;
	            break;        
	        }
	    }
	    $nurl = "http://$nkey.{$jcfg['domain']}/"; // 组装完整url
	    return $nurl;
	}

	// 获得分站数据，供各分站调用
	static function getSites(){
	    $data = array();
	    foreach($sub_sites as $key=>$kname){
	        $data[$key] = array('name'=>$kname, 'url'=>"http://$key.{$jcfg['domain']}/");
	    }
	    $data = comParse::jsonEncode($data);
	    return $data;
	}

	// 获得ujump配置
	static function getCfgs($key=''){
		if(empty(self::$jcfg)){
			self::$jcfg = glbConfig::read('ujump','ex');
		}
		return $key && isset(self::$jcfg[$key]) ? self::$jcfg[$key] : self::$jcfg;
	}


}
