<?php
// 对接本系统基本函数和配置
// 如果本系统修改,就改这个文件，不用改wmp*文件

class wysBasic{
    
    static $cfgs = array();
    static $tiks = array();
    
    static $cache_path = array(
        'actik' => "/weixin/actik_(appid).cac_txt",
        'jstik' => "/weixin/jstik_(appid).cac_txt",
    );
    
    //替换地址
    static function fmtUrl($url, $enc=0){ 
        global $_cbase;
        $a1 = array("{svrtxmao}","{svrtxcode}","{svrtxjia}");
        $a2 = array($_cbase['server']['txmao'],$_cbase['server']['txcode'],$_cbase['server']['txjia']);
        $url = str_replace($a1,$a2,$url);
        $root = PATH_PROJ;
        if(!strpos($url,'?')) $url .= '?'; 
        $from = array('{root}','?&',);
        $to = array($root,'?');
        $url = str_replace($from, $to, $url);
        if(!strstr($url,'://')){
            $home = $_cbase['run']['rsite']; 
            $url = "$home$url";
        }
        $enc && $url = str_replace(array("&","#"),array("%26","%23"),$url);
        return $url;
    }

    static function debugError($msg='',$arr=array(),$url='',$die=0){
        if(is_array($arr) && !empty($arr['errcode'])){
            $msg = wmpError::errGet($arr['errcode']);
            if(strpos($msg,'(unKnow)') && !empty($arr['errmsg'])){
                $msg = '['.$arr['errcode'].']'.$arr['errmsg'];
            }
            $msg = "$url<br>$msg";
        }elseif(is_object($arr) || is_array($arr)){ 
            $msg .= "$url<br>".var_export($arr,1); 
        }else{
            $msg .= "$url<br>".$arr;
        } 
        $msg && $msg = "$msg<br>";
        $debug = cfg('weixin.debug');
        if(defined('WERR_RETURN') && empty($die)){ 
            $arr['message'] = $msg;
            $arr['url'] = $url;
            return $arr; 
        }else{ 
            if(defined('RUN_WECHAT')){
                basDebug::bugLogs('weixin',$msg,'detmp','db');
            }
            if(defined('RUN_WECHAT') && empty($debug)){
                die(''); //这个回复微信服务器
            }else{
                if(defined('RUN_MOB')){ 
                    echo "\n<meta name='viewport' content='width=device-width, initial-scale=1'>";
                }
                die($msg); //这个给人看的
            }
        }
    }
    
    //把数据转成JSON格式（让支持中文显示）
    static function jsonEncode($datas){ 
        return comParse::jsonEncode($datas);
    }
    
    static function jsonDecode($data,$url=''){ 
        if(empty($data)) return self::debugError($url.'<br>[Remote]获取远程数据错误，请检查php扩展和服务器环境<br>','');
        $arr = json_decode($data,1); 
        if(!empty($arr['errcode'])){
            return self::debugError($arr['errcode'],$arr,$url);
        }else{
            return $arr;    
        }
    }
    
    // 返回系统配置/参数：(说明)
    // -> $key=kid,appid
    static function getConfig($val='admin', $key='kid'){
        $ckey = "$val-$key"; 
        if(isset(self::$cfgs[$ckey])){ 
            return self::$cfgs[$ckey]; 
        }
        $recfg = db()->table('wex_apps')->where("$key='$val'")->find();
        return empty($recfg) ? array() : $recfg;
    }
    
    // 返回缓存文件路径
    static function getCfpath($key, $type='actik'){
        //comFiles::chkDirs('weixin','dtmp',0);
        $cfile = DIR_DTMP.str_replace('(appid)',$key,self::$cache_path[$type]);
        return $cfile;
    }
    
    // clearCache
    static function clearCache($id){
        $cfg = self::getConfig($id);
        foreach(self::$cache_path as $_pk=>$path){
          $cfile = DIR_DTMP.str_replace('(appid)',$cfg['appid'],$path);
          @unlink($cfile);
        }
    }
    
}
