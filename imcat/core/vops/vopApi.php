<?php
namespace imcat;

// 基础API类

class vopApi{

    // [], 'die', $msg
    // [], 'dir', $url
    // $data, ''
    static function v($data, $type='', $msg=''){
        if($type=='dir'){
            $msg = preg_match("/[^\w\-\.]+/i", $msg) ? $msg : surl($msg);
        }
        if($type=='die'){
            //
        }
        $retype = req('retype'); // retype
        if($retype || $type=='api'){ // type=api, re=json|jsonp
            $alp = '*'; glbHtml::dallow($alp);
            if(isset($data['vars'])){
                $vars = $data['vars']; unset($data['vars']);
                $_sys = $data;
                $data = $vars + ['_sys'=>$_sys, '_msg'=>$msg];
            }
            self::view($data);
        }elseif($type){ // dir, die
            if($type=='dir'){ header('Location:'.$msg); }
            if($type=='die'){ $msg = empty($msg) ? $data['vars']['errmsg'] : $msg; }
            die($msg);
        }else{ 
            return $data;
        }
    }

    // 显示
    static function view($data=array(), $die=1){
        if(empty($data['errno'])){ $data['errno'] = 0; }
        if(empty($data['errmsg'])){ $data['errmsg'] = ''; }
        $re = req('retype', 'json');
        $debug = req('debug');
        if($debug){
            dump($data);
        }else{
            $res = basOut::fmt($data, $re);
            header('content-type:application/json');
            echo $res;
        }
        $die && die();
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

    // 错误提示(小程序)
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

    // 错误提示(兼容tp)
    static function errEnd($ek, $tip='', $msg=''){
        if(empty($row)){
            $ere['errno'] = $ere['code'] ="Error-$ek"; // code
            $ere['errtip'] = $tip;
            $ere['errmsg'] = $ere['msg'] = $msg; // msg
            if(basEnv::isAjax()){
                return self::v($ere, 'api'); 
            }else{ 
                echo vopTpls::show('home/info', '', $ere);
            }
            die();
        }
    }

}

/*

*/
