<?php
namespace imcat;

class extWework{
    
    public $reqs = [];
    public $wxcpt = null;
    public $acfg = [];

    # ================================ 

    function __construct($appId){
        // params
        $reqs['sMsgSign'] = req('msg_signature'); 
        $reqs['sTimeStamp'] = req('timestamp');
        $reqs['sNonce'] = req('nonce');
        $this->reqs = $reqs;
        // cfgs
        $this->wecfg = read('wework', 'ex');
        if(!isset($this->wecfg['AppsConfig'][$appId])){
            die('Error AppID!');
        }else{
            $this->acfg = $this->wecfg['AppsConfig'][$appId];
        }
        include_once(DIR_WEKIT."/sv-api/callback/WXBizMsgCrypt.php");
        $this->wxcpt = new \WXBizMsgCrypt($this->acfg['Token'], $this->acfg['EncodingAESKey'], $this->wecfg['CorpId']);
    }

    function getMsg($encMesg){
        $res = ['errCode'=>0, 'strMsg'=>''];
        $reqs = $this->reqs;
        //$encMesg = file_get_contents("php://input")
        // "<xml><ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt>encStr</Encrypt><AgentID><![CDATA[218]]></AgentID></xml>";
        $strMsg = "";  // 解析之后的明文
        $errCode = $this->wxcpt->DecryptMsg($reqs['sMsgSign'], $reqs['sTimeStamp'], $reqs['sNonce'], $encMesg, $strMsg);
        if ($errCode == 0) { // 解密成功，sMsg即为xml格式的明文
            basDebug::bugLogs("getMsg-check", $strMsg, "msgOK.log", 'db');
            $res = ['errCode'=>0, 'strMsg'=>$strMsg]; // var_dump($strMsg); // TODO: 对明文的处理
        } else {
            basDebug::bugLogs("getMsg-check", $encMesg, "errMsg.log", 'db');
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

    static function smsgCard($agentId, $msg=[], $to=[]){
        $wecfg = read('wework', 'ex');
        $to['uids'] = array_unique(array_filter($to['uids']));
        if(empty($to['uids']) && empty($to['party']) && empty($to['tag'])){
            basDebug::bugLogs("msg-send", $to, "msg-null.log", 'file');
            return ['errcode'=>'82001', 'errmsg'=>"指定的成员/部门/标签全部为空"]; 
        }
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $api = new \CorpAPI($wecfg['CorpId'], $wecfg['AppsConfig'][$agentId]['Secret']);
        try { 
            $message = new \Message();
            {
                $message->sendToAll = false;
                if(!empty($to['uids'])) $message->touser = $to['uids']; // array("PeaceXie", "ShengbenZhu");
                if(!empty($to['party'])) $message->toparty = $to['party']; // array(1, 2, 1111, 3333);
                if(!empty($to['tag'])) $message->totag = $to['tag']; // array(3, 4, 22233332, 33334444);
                $message->agentid = $wecfg['AppsConfig'][$agentId]['AgentId'];
                $message->safe = 0;
                $message->messageContent = new \TextCardMessageContent( 
                    $msg['title'], //"售后单[$row[did]]{$acmsg}", // 请(加急)处理,提到了您,完成了...
                    $msg['des'], //"<div class=\"gray\">".date('Y-m-d H:i:s')."</div> <div class=\"normal\">$row[title]</div><div class=\"highlight\">单号 $row[did] $mfmsg</div>", 
                    $msg['url'], //$url, 
                    $msg['btntxt'] //"查看详情"
                );
            }
            $invalidUserIdList = null;
            $invalidPartyIdList = null;
            $invalidTagIdList = null;
            $api->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
            //var_dump($agentId, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
            $dlog = [$message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList];
            basDebug::bugLogs("msg-send", $dlog, "msg-end.log", 'file');
            return [$invalidUserIdList, $invalidPartyIdList, $invalidTagIdList];
        } catch (Exception $ex) {
            //var_dump($invalidPartyIdList);
            return ['errcode'=>'errMsgSend', 'errmsg'=>$ex->getMessage()];
        }
    }

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
        $fp = "/dtmp/weixin/$UserId.cac_tab";
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
        if(empty($uinfo['avatar'])){ $uinfo['avatar']=PATH_STATIC.'/icons/basic/nouser2.png'; }
        return $uinfo;
    }
    // 更新:单个用户数据 > 保存到缓存
    static function updUser($UserId='', $agentId=''){ // deps,utab,uone
        $wecfgs = read('wework', 'ex'); // DefAppID
        $agentId = $agentId ?: $wecfgs['DefAppID'];
        $fp = "/dtmp/weixin/$UserId.cac_tab";
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $CorpId = $wecfgs['CorpId']; //read('wework.CorpId', 'ex');
        $api = new \CorpAPI($CorpId, $agentId);
        $uinfo = $api->GetUserById($UserId);  
        // save
        if(!empty($uinfo['userid'])){
            extWework::userMin($uinfo); 
            $data = comParse::jsonEncode($uinfo);
            comFiles::put(DIR_VARS.$fp,$data);
            //unset($res[$key]);
        }
        return $uinfo;
    }

    // 从缓存获取:部门/用户列表数据
    static function getContacts($act='deps'){ // deps,utab
        $key = $act=='deps' ? 'department' : 'userlist';
        $fp = "/dtmp/weixin/_$key.cac_tab";
        if(!file_exists(DIR_VARS.$fp)){
            self::updContacts($act);
        }
        $data = comFiles::get(DIR_VARS.$fp);
        return json_decode($data,1);
    }
    // 更新:部门/用户列表数据 > 保存到缓存
    static function updContacts($act='deps'){ // deps,utab
        $key = $act=='deps' ? 'department' : 'userlist';
        $fp = "/dtmp/weixin/_$key.cac_tab";
        $res = ['errno'=>'','errmsg'=>'更新成功'];
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $wework = read('wework', 'ex');
        // getData
        $api = new \CorpAPI($wework['CorpId'], $wework['TxlSecret']);
        if($act=='deps'){
            $res = $api->DepartmentList(null, 1);
        }elseif($act=='utab'){
            $res = $api->userSimpleList(1, 1, 1);
            $tmp = [];
            if(!empty($res[$key])){
                foreach ($res[$key] as $no=>$row) {
                    $tmp[$row['userid']] = $row;
                }
            }
            $res[$key] = $tmp;
        }
        // save
        if(!empty($res[$key])){
            $data = comParse::jsonEncode($res[$key]);
            comFiles::put(DIR_VARS.$fp,$data); //dump($data);
            unset($res[$key]);
        }
        return $res;
    }

    // 判断是否企业微信
    static function isWework($ver=0){
        //return 1;
        $wxpos = strpos(basEnv::userAG(), 'wxwork/');
        if($ver){
            preg_match('/.*?(wxwork\/([0-9.]+))\s*/', $uagent, $matches);
            return $wxpos ? $matches[2] : '';
        }else{
            return $wxpos;
        }
    }

    // 判断...是否iOS-act
    static function fixAct(){
        return 'onclick';
        //$isMob = basEnv::isMobile();
        $isiOS = strpos($_SERVER['HTTP_USER_AGENT'],'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
        $act = $isiOS ? 'ontouchstart' : 'onclick';
        return $act;
    }
    // if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){

}

/*

PC-Chrome:
  - "(Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36"
PC-Qiye
  - "(Windows NT 6.2; WOW64) Chrome/53.0.2785.116 Safari/537.36 wxwork/3.0.16 (MicroMessenger/6.2) WindowsWechat"
PC-Wechat
  - "(Windows NT 10.0; WOW64) Chrome/53.0.2875.116 Safari/537.36 NetType/WIFI MicroMessenger/7.0.5 WindowsWechat"
Mob-Wechat
  - "(Linux; Android 7.0; SLA-TL10 Build/HUAWEISLA-TL10; wv) MMWEBID/4318 MicroMessenger/7.0.12.1620(0x27000C34) Process/tools NetType/WIFI Language/zh_CN ABI/arm32"
Mob-Qiye
  - "(Linux; Android 7.0; SLA-TL10 Build/HUAWEISLA-TL10; wv) wxwork/3.0.14 MicroMessenger/7.0.1 NetType/WIFI Language/zh"

*/
