<?php
namespace imcat;

class mpxApi extends bextApi{
    
    function homeAct(){
        //
    }

    function weixinAct(){
        basDebug::log('mpx-weixin');
        $echostr = req('echostr');
        die($echostr);
    }



    /*
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if ($tmpStr == $signature ) {
            return true;
        } else {
            return false;
        }
    }*/


}
