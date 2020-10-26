<?php
namespace imcat;

// 基础API类

class vopApi{

    // 显示
    static function view($data=array(), $die=1){
        if(empty($data['errno'])){ $data['errno'] = 0; }
        if(empty($data['errmsg'])){ $data['errmsg'] = ''; }
        $re = req('re', 'json');
        $debug = req('debug');
        if($debug){
            dump($data);
        }else{
            $re = req('re', 'json');
            $res = basOut::fmt($data, $re);
            header('content-type:application/json');
            echo $res;
        }
        $die && die();
    }

    // 错误
    static function error($msg='', $code=0){
        $msg = empty($msg) ? 'Error Message!' : $msg;
        $res['ercode'] = $code ? $code : 1;
        $res['ermsg'] = $msg;
        $res['ref'] = basEnv::serval('ref', '?');
        if($code==518){ // 518自定义特殊错误码(小程序中使用)
            $erhead = "518 {$res['errno']}:{$res['errmsg']}";
            header("HTTP/1.1 $erhead"); 
            header("Status: $erhead"); // 确保FastCGI模式下正常
        }elseif($code>200){
            glbHtml::httpStatus($code);
        }
        self::view($res);
    }

    // 快捷显示错误
    static function verr($msg='', $code=0){
        self::error($msg, $code);
    }

    // Action测试
    static function _defAct(){
        $res['ermsg'] = 'EmptyAction!';
        return self::view($res);
    }

    // Action测试
    static function test1Act(){
        $res['test'] = 'asisv:test1';
        return self::view($res);
    }
}

/*

*/
