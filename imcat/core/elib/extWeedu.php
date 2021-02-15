<?php
namespace imcat;

class extWeedu{ // extends extWework
    
    public $appid = '';
    public $acfg = [];
    public $reqs = [];
    public $wxcpt = null;

    # ================================ 

    function __construct($appId){
        // cfgs
        $this->appid = $appId;
        $this->wecfg = read('weedu', 'ex');
        if(!isset($this->wecfg['AppsConfig'][$appId])){
            die('Error AppID!');
        }else{
            $this->acfg = $this->wecfg['AppsConfig'][$appId];
        }
        include_once(DIR_WEKIT."/sv-api/callback/WXBizMsgCrypt.php");
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
    }

    function getMsg($encMesg){
        // params
        $reqs['sMsgSign'] = req('msg_signature'); 
        $reqs['sTimeStamp'] = req('timestamp');
        $reqs['sNonce'] = req('nonce');
        $this->reqs = $reqs;
        $this->wxcpt = new \WXBizMsgCrypt($this->acfg['Token'], $this->acfg['EncodingAESKey'], $this->appid);

        $res = ['errCode'=>0, 'strMsg'=>''];
        $reqs = $this->reqs;
        //$encMesg = file_get_contents("php://input")
        // "<xml><ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt>encStr</Encrypt><AgentID><![CDATA[218]]></AgentID></xml>";
        $strMsg = "";  // 解析之后的明文
        $errCode = $this->wxcpt->DecryptMsg($reqs['sMsgSign'], $reqs['sTimeStamp'], $reqs['sNonce'], $encMesg, $strMsg);
        if ($errCode == 0) { // 解密成功，sMsg即为xml格式的明文
            #basDebug::bugLogs("getMsg-check", $strMsg, "msgOK.log", 'db');
            $res = ['errCode'=>0, 'strMsg'=>$strMsg]; // var_dump($strMsg); // TODO: 对明文的处理
        } else {
            #basDebug::bugLogs("getMsg-check", $encMesg, "errMsg.log", 'db');
            //print("ERROR: " . $errCode . "\n\n");
            $res = ['errCode'=>$errCode, 'strMsg'=>$strMsg]; 
        }
        return $res;
    }

    function chkUrl($sVerifyEchoStr){
        $reqs = $this->reqs;
        $sEchoStr = "";
        //dump([$this->acfg['Token'], $this->acfg['EncodingAESKey'], $receiveid]);
        $errCode = $this->wxcpt->VerifyURL($reqs['sMsgSign'], $reqs['sTimeStamp'], $reqs['sNonce'], $sVerifyEchoStr, $sEchoStr);
        $data = [$errCode, $sVerifyEchoStr, $sEchoStr];
        if(empty($errCode)){ // ==0
            basDebug::bugLogs("chkUrl-check", $data, "chkUrl-urlOK.log", 'db');
            echo $sEchoStr;
        } else {
            basDebug::bugLogs("chkUrl-check", $data, "chkUrl-errUrl.log", 'db');
            print("ERROR: " . $errCode . "\n\n");
        }
    }

    # ================================ 

    // 部门管理
    static function getDeplist($token='', $id='', $type=''){
        $url = "https://oapi.epaas.qq.com/school/department/list?access_token=$token&id=$id&department_type=$type";
        $data = comHttp::curlCrawl($url, [], []);
        $arr = json_decode($data, 1); 
        return $arr;
    }

    // 获取机构凭证 (auth_code )
    static function xxx_getCorpActoken($sid, $sactoken=''){
        $url = "https://oapi.epaas.qq.com/service/get_corp_token?suite_access_token=$sactoken";
        $wecfg = read('weedu.AppsConfig', 'ex');
        $skey = isset($wecfg[$sid]['SuiteKey']) ? $wecfg[$sid]['SuiteKey'] : '';
        $json = "{
            \"auth_corpid\":\"$sid\" ,
            \"permanent_code\": \"$skey\"
        }"; echo $json;
        $data = comHttp::curlCrawl($url, $json, ['type'=>'json']); echo $data;
        $arr = json_decode($data, 1); 
        return $arr;
    }

    //"auth_corpid": "auth_corpid_value",
    //"permanent_code": "code_value"

    // 获取第三方应用凭证 (sticket - 回调保存)
    static function getSuiteActoken($sid, $sticket=''){
        $url = "https://oapi.epaas.qq.com/service/get_suite_token";
        $wecfg = read('weedu.AppsConfig', 'ex');
        $skey = isset($wecfg[$sid]['SuiteKey']) ? $wecfg[$sid]['SuiteKey'] : '';
        $json = "{
            \"suite_id\":\"$sid\" ,
            \"suite_secret\": \"$skey\", 
            \"suite_ticket\": \"$sticket\" 
        }"; echo $json;
        $data = comHttp::curlCrawl($url, $json, ['type'=>'json']); echo $data;
        $arr = json_decode($data, 1); 
        return $arr;
    }

    // 2. 获取用户可见架构ID列表
    static function getUserDeps($token=''){
        $url = "https://sso.qq.com/open/get_can_see_departments?access_token=$token";
        $data = comHttp::curlCrawl($url, [], []);
        $arr = json_decode($data, 1); 
        return $arr;
    }

    // 1. 查询用户信息
    function getUserInfo($token){
        $url = "https://sso.qq.com/open/userinfo?access_token=$token";
        $data = comHttp::curlCrawl($url, [], []);
        $arr = json_decode($data, 1); 
        return $arr;
    }

    // ------------------------- access token ---------------------------------
    function getAccessToken($code, $reurl=''){
        $this->accessToken = '';
        $this->GetAccessTokenRemote($code, $reurl);
        /*
        $tkarr = \imcat\extCache::tkGet($this->appid); // 取缓存
        if(!empty($tkarr['token'])){
            $this->accessToken = $tkarr['token'];
        }
        if(empty($this->accessToken)){ 
            $this->GetAccessTokenRemote($code, $reurl);
        }*/
        return $this->accessToken;
    }

    function getAccessTokenRemote($code, $reurl=''){
        $urlb = 'https://sso.qq.com/open/access_token';
        $url = "$urlb?appid={$this->appid}&secret={$this->acfg['SuiteKey']}&code=$code&redirect_uri=$reurl&grant_type=authorization_code";
        $data = comHttp::curlCrawl($url, [], []);
        $arr = json_decode($data, 1); 
        if(is_array($arr['data']) && isset($arr['data']['access_token'])){
            $tkarr = ['token'=>$arr['data']["access_token"], 'exp'=>$arr['data']["expires_in"]];
            #\imcat\extCache::tkSet($this->appid, $tkarr, 90);
            $this->accessToken = $tkarr["token"];
        }
        // https://sso.qq.com/open/access_token?appid=xxxx&secret=265b2b7&code=581879d8ac77948&redirect_uri=https://xxx.html&grant_type=authorization_code
    }

}

/*


*/
