<?php
(!defined('RUN_INIT')) && die('No Init');
// 语义理解接口
// 随微信规则更新 7000009:请求语义服务失败 

class wmpSemantic extends wmpBasic{
    
    private $yuyiUrl = 'https://api.weixin.qq.com/semantic/semproxy/search';
    
    function __construct($cfg=array()){
        parent::__construct($cfg); 
    }
    
    //
    function getResult($q, $city, $category, $uid){
        $url = $this->yuyiUrl."?access_token={$this->actoken}";
        $message = array();
        $message['q'] = $q;
        $message['city'] = $city;
        $message['category'] = $category;
        $message['uid'] = $uid;
        $data = comHttp::doPost($url, $message, 3); //{"ret":7000001}
        return wysBasic::jsonDecode($data,$this->yuyiUrl);
    }

}
