<?php
namespace imcat;

use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;

/**
 * 腾讯云短信
 */
class smsQcloud{
    
    public $userid; // 序列号
    public $userpw; // 密码
    public $cfgs = [];
    #public $bfile; // 余额文件

    // 初始化
    function __construct($cfgs=array()){
        $this->userid = $cfgs['user'];
        $this->userpw = $cfgs['pass'];
        $this->cfgs = $cfgs;
    }
    function sendSMS($mobiles, $content){
        $sms = new SmsMultiSender($this->userid, $this->userpw);
        $json = $sms->send(0, "86", $mobiles, $content, "", "");
        $res = json_decode($json, JSON_UNESCAPED_UNICODE);
        if($res['errmsg']=='OK'){
            return array(1, "OK");
        }else{
            return array(-1, "{$res['errmsg']}:{$res['result']}");
        }
    }

    // 余额查询 
    function getBalance(){
        $rnd = rand(12345, 98765);
        return array('1', $rnd); 
    }
    
    // 充值
    function chargeUp($count){
        $rnd = $count + rand(1,1000);
        return array('1', $cnt); 
    }

}

