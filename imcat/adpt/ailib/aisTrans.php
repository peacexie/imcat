<?php
namespace imcat;

# http://api.fanyi.baidu.com/api/trans/product/apidoc
# 每月翻译字符低于200万，当月免费，如超过200万字符，需按照49元人民币/百万字符支付当月全部翻译字符数费用（包括之前免费的200万字符）。
# 请将单次请求长度控制在 6000 bytes以内。（汉字约为2000个）

class aisTrans{

    static $api = "http://api.fanyi.baidu.com/api/trans/vip/translate"; 
    static $cfgs = array();

    //翻译入口
    static function main($data, $from, $to, $cache=30)
    {
        // config
        self::cfgi();
        $appid = self::$cfgs['id'];
        $sekey = self::$cfgs['as'];
        // array -> string
        if(is_array($data)){
            $query = '';
            foreach($data as $val) {
                $query .= ($query?"\n":'').$val;
            }
        }else{
            $query = $data;
        }
        // args
        $args = array(
            'q' => $query,
            'appid' => $appid,
            'salt' => rand(10000,99999),
            'from' => $from,
            'to' => $to,

        );
        $args['sign'] = self::sign($query, $appid, $args['salt'], $sekey);
        $param = self::conv($args);
        $qurl = self::$api."?$param";
        $svfp = comHttp::fpCache('__trans', "$query-$from-$to");
        $data = extCache::cfGet("/remote/".$svfp, $cache, 'vars', 'str');
        if($data===false){
            $data = comHttp::curlPost($qurl); 
            extCache::cfSet("/remote/".$svfp, $data, 'vars');
        } 
        $data = json_decode($data, true);
        return $data; 
    }

    // conv
    static function conv(&$args){
        $data = '';
        foreach($args as $key=>$val){
            if(is_array($val)){
                foreach ($val as $k=>$v){
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }else{
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }

    // sign
    static function sign($query, $appID, $salt, $secKey){
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }

    // cfg-init
    static function cfgi()
    {
        if(empty(self::$cfgs)){
            $cfgs = read('ais','ex');
            $cfy = $cfgs['fanyi'];
            //$cyy = $cfgs['yuyan']; 
            self::$cfgs = $cfy;
        }
    }

}

/*

$q = "测试成功(1)";
$res1 = apiTrans::main($q, 'ch', 'en');
dump($res1); 

$p[] = "测试成功(1)";
$p[] = "测试是被(2)";
$res2 = apiTrans::main($p, 'ch', 'en');
dump($res2); 

*/
