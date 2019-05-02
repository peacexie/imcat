<?php
namespace imcat;

class aisBdapi{

    #static $clients = array();
    static $cfgs = array();
    #static $ckey = '';

    // 远程API数据
    static function remote($url, $data, $conv=0)
    {
        // url
        $cfg = self::token();
        $token = $cfg['access_token'];
        $url = "$url?access_token=$token";
        // remote
        if(is_array($data)) $data = json_encode($data);
        comHttp::setCache(30);
        $re0 = comHttp::curlPost($url, $data);
        if($conv) $re0 = iconv("GB2312", "UTF-8//IGNORE", $re0);
        $res = json_decode($re0, 1); 
        return $res;
    }

    // cfg-init
    static function init($class, $recfg=0)
    {
        if(empty(self::$cfgs)){
            $cfgs = read('ais','ex');
            self::$cfgs = $cfgs;
        }
        $cfg = self::$cfgs[static::$ckey];
        if($recfg) return $cfg;
        return self::$clients[$class];
    }

    // cfg-token
    static function token(){
        $cfg = self::init('0', 1);
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $data['grant_type'] = 'client_credentials';
        $data['client_id'] = $cfg['ak'];
        $data['client_secret'] = $cfg['as'];
        $pms = "";
        foreach($data as $k => $v) {
            $pms .= "$k=$v&"; 
        }
        $pms = substr($pms,0,-1);
        comHttp::setCache(21600); // 1440min=1day, 21600min=15day
        $res = comHttp::curlPost($url, $pms);
        return json_decode($res, 1);
    }
}

/*
    if(empty(self::$clients[$class])){
        include_once DIR_VENDOR."/bdAip/$class.php";
        $cfull = "\\$class"; 
        self::$clients[$class] = new $cfull($cfg['id'], $cfg['ak'], $cfg['as']);
    }
    /* 使用SDK
    $client = self::init('AipNlp');
    $ops = array();
    $title && $ops["title"] = $title;
    $res = $client->newsSummary($content, 240, $ops);
*/
