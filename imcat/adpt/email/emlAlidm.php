<?php
namespace imcat;

use Alidayu\Signature;

/**
 * ali邮件推送
 */
class emlAlidm{
    
    public $cfgs = [];

    // 初始化
    function __construct($cfgs=array()){
        $this->cfgs = $cfgs;
    }

    // 具体操作不会发短信
    function send($to, $title, $body, $vname=''){
        $params = array();
        $security = false; // fixme 必填：是否启用https
        $akId = $this->cfgs['akId'];
        $akSecret = $this->cfgs['akSecret'];
        $params["AccountName"] = $this->cfgs['account'];
        $params["ReplyToAddress"] = 'true';
        $params["AddressType"] = 1;
        $params["ToAddress"] = $to;
        $params["Subject"] = $title;
        $params["HtmlBody"] = $body;
        $params["FromAlias"] = $vname ?: $this->cfgs['re_name'];
        // *** 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new Signature();
        // 此处可能会抛出异常，注意catch
        $parsys = ["RegionId"=>"cn-hangzhou", "Action"=>"SingleSendMail", "Version"=>"2015-11-23"];
        $params = array_merge($params, $parsys);
        $json = $helper->request($akId, $akSecret, "dm.aliyuncs.com", $params, $security); 
        $res = json_decode($json, 1); #dump($json); dump($res);
        if(!$res){
            return "Unknow Error:[$json]";
        }elseif(empty($res['Code'])){
            return 'SentOK';
        }else{
            return "{$res['Message']}:{$res['Code']}";
        }
    }

}

