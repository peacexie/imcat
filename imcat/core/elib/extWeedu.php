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

    //static function smsgNewsArticle($agentId){}

    # ================================ 

    // 精简用户数据
    static function userMin(&$uinfo, $cut=1){
        $skip = ['extattr','order','external_profile','is_leader_in_dept','errcode','errmsg'];
        $keep = ['userid','name','mobile','email','avatar'];
        if($cut){
            foreach ($skip as $key) {
                unset($uinfo[$key]);
            } 
        }else{
            foreach ($uinfo as $key=>$val) {
                if(!in_array($key,$keep)){
                    unset($uinfo[$key]);
                }
            }
        }
    }

    // 从缓存获取:单个用户数据
    static function getUser($UserId='', $agentId=''){ // deps,utab,uone
        $wecfgs = read('wework', 'ex'); // DefAppID
        $agentId = $agentId ?: $wecfgs['DefAppID'];
        $fp = "/dtmp/wework/$UserId.cac_tab";
        if(!$UserId){
            $data = $wecfgs['utab']['(null)'];
        }elseif(isset($wecfgs['utab'][$UserId])){
            $data = $wecfgs['utab'][$UserId];
        }else{
            if(!file_exists(DIR_VARS.$fp)){
                self::updUser($UserId, $agentId);
            }
            $data = comFiles::get(DIR_VARS.$fp);
        }
        $uinfo = json_decode($data,1);
        if(!empty($uinfo)){ // 默认头像,调试权限
            if(empty($uinfo['avatar'])){ $uinfo['avatar']=PATH_STATIC.'/icons/basic/nouser2.png'; }
            $wecfgs = read('wework', 'ex');
            $uinfo['pdebug'] = $uinfo['userid'] && strstr($wecfgs['ucfg']['debug'],$uinfo['userid']);
        }
        return $uinfo;
    }

    static function oauth2Link($redirect, $scope='', $state='imcat_wxwork_login'){
        //;  
    }

    // ------------------------- user info ---------------------------------
    function getUserInfo($token){
        $url = "https://sso.qq.com/open/userinfo?access_token=$token";
        $data = comHttp::curlCrawl($url, [], []);
        $arr = json_decode($data, 1); 
        return $arr;
        // https://sso.qq.com/open/userinfo?access_token=581879d8ac77948
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
