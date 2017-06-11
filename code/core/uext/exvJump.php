<?php

// 显示相关函数; 单独函数可先用new exvJump();自动加载
class exvJump{

    static $jcfg = array();

    // 获得多语言-跳转地址
    static function getLang(){
        $langs = self::getCfgs('langs');
        $_def = self::getCfgs('_defs');
        $nkey = $_def['lang']; //未找到地区时的默认网站
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
        $lang = 'en'; // zh,cn,en
        foreach($langs as $key=>$kname){
            if($lang==$key){
                $nkey = $kname;
                break;        
            }
        }
        $nurl = vopUrl::fout("$kname:0"); 
        return $nurl;
    }

    // 获取ip对应地址
    static function getAddr($userip){
        $_def = self::getCfgs('_defs');
        $api = $_def['api']; 
        $ipObj = new extIPAddr($api);
        $addr = $ipObj->addr($userip);
        #echo "$api,$userip,$addr";
        return $addr;
    }

    // 获得分站-跳转地址
    static function getDurl($uaddr){
        $jcfg = self::getCfgs();
        $nkey = $jcfg['_defs']['site']; //未找到地区时的默认网站
        foreach($jcfg['sites'] as $key=>$kname){
            if(strstr($uaddr,$kname)){
                $nkey = $key;
                break;        
            }
        }
        $nurl = "http://$nkey.{$jcfg['_defs']['domain']}/"; // 组装完整url
        return $nurl;
    }


    // 获得ujump配置
    static function getCfgs($key=''){
        if(empty(self::$jcfg)){
            self::$jcfg = glbConfig::read('vjump','ex');
        }
        return $key && isset(self::$jcfg[$key]) ? self::$jcfg[$key] : self::$jcfg;
    }

}
