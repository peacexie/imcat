<?php
namespace imcat;

use Alidayu\Signature;

/**
 * 阿里大鱼；
 */
class smsAlidy{
    
    public $userid; // 序列号
    public $userpw; // 密码
    public $cfgs = [];

    // 初始化
    function __construct($cfgs=array()){
        $this->userid = $cfgs['user'];
        $this->userpw = $cfgs['pass'];
        $this->cfgs = $cfgs;
    }

    // 具体操作不会发短信
    function sendTid($mobiles, $upars, $tid='', $sign=''){
        if(is_array($mobiles)) $mobiles = implode(',', $mobiles);
        $params = array();
        $security = false; // fixme 必填：是否启用https
        $akId = $this->userid;
        $akSecret = $this->userpw;
        $params["PhoneNumbers"] = $mobiles; // fixme 必填: 短信接收号码
        $params["SignName"] = $sign ?: $this->cfgs['cfg_pr4']; // fixme 必填: 短信签名，应严格按"签名名称"填写，
        $params["TemplateCode"] = $tid ?: $this->cfgs['cfg_pr3']; // fixme 必填: 短信模板Code，应严格按"模板CODE"填写,  
        $params['TemplateParam'] = $upars; // ["code"=>1234]; // fixme 设置模板参数
        // *** 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        // 初始化Signature实例用于设置参数，签名以及发送请求
        $helper = new Signature();
        // 此处可能会抛出异常，注意catch
        $parsys = ["RegionId"=>"cn-hangzhou", "Action"=>"SendSms", "Version"=>"2017-05-25"];
        $params = array_merge($params, $parsys);
        $json = $helper->request($akId, $akSecret, "dysmsapi.aliyuncs.com", $params, $security); 
        #dump($json);
        $res = json_decode($json);
        if($res->Code=='OK'){
            return array(1, "OK");
        }else{
            return array(-1, "{$res->Message}:{$res->Code}");
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



