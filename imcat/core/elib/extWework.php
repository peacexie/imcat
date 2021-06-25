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
        if(empty($this->wecfg['isOpen'])){
            die('请配置:[ex_wework.php]:isOpen=1');
        }else{
            include_once(DIR_WEKIT."/sv-api/callback/WXBizMsgCrypt.php");
            $this->wxcpt = new \WXBizMsgCrypt($this->acfg['Token'], $this->acfg['EncodingAESKey'], $this->wecfg['CorpId']);
        }
        if(!isset($this->wecfg['AppsConfig'][$appId])){
            die('Error AppID!');
        }else{
            $this->acfg = $this->wecfg['AppsConfig'][$appId];
        }
    }

    # Edu:家校沟通-Start -------

    static function getEdutPid($ures){ 
        if(!empty($ures['parent_userid'])){ 
            return $ures['parent_userid'];
        }
        if(!empty($ures['UserId'])){
            $uin = self::getUser($ures['UserId']);
            if(isset($uin['mobile'])){ 
                //return $uin['mobile'];
                $rdb = db()->table('exd_edu')->field('pid')->where("mob='$uin[mobile]'")->find(); 
                if(!empty($rdb['pid'])){ return $rdb['pid']; }
            }
        }
        return '';
    }

    // 更新班级:学生-家长:对应关系；cid=班级id
    static function updEduClass($cid='0'){ 
        $cdata = is_int($cid) ? self::getEduTabs($cid) : $cid;
        if(is_array($cdata)){
        foreach($cdata as $cr){
            $sid = $cr['student_userid'];
            foreach($cr['parents'] as $pr){
                $pid = $pr['parent_userid']; 
                $eid = empty($pr['external_userid']) ? '' : $pr['external_userid'];
                $rr = ['sid'=>$sid, 'pid'=>$pid, 'mob'=>$pr['mobile'], 'eid'=>$eid];
                db()->table('exd_edu')->data($rr)->replace(0); 
                //echo "$sid - $pid - $mob - $eid<br>\n";
            }
        } }
    }

    // 从缓存获取:学校:部门/用户列表数据
    static function getEduTabs($act='deps', $upd=0){ // deps,utab
        $key = $act=='deps' ? 'departments' : 'students';
        $fp = "/dtmp/wework/edu_$act.cac_tab";
        if(!file_exists(DIR_VARS.$fp) || $upd){
            self::updEduTab($act);
        }
        $data = comFiles::get(DIR_VARS.$fp);
        return json_decode($data,1);
    }
    // 更新:学校:部门/用户列表数据 > 保存到缓存
    static function updEduTab($act='deps'){ // deps,utab
        $key = $act=='deps' ? 'departments' : 'students';
        $fp = "/dtmp/wework/edu_$act.cac_tab";
        $res = ['errno'=>'','errmsg'=>'更新成功'];
        $wecfg = read('wework', 'ex');
        $jiaSecret = $wecfg['JiaSecret'];
        if(empty($wecfg['isOpen'])){
            return ['errno'=>'!isOpen','errmsg'=>'请配置:[ex_wework.php]:isOpen=1'];
        }
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        // getData
        $api = new \CorpAPI($wecfg['CorpId'], $jiaSecret);
        if($act=='deps'){
            $res = $api->EduDepartLists();
        }else{ // if($act=='utab')
            $res = $api->EduDepartUsers($act); //dump($res);
        }
        // save
        if(!empty($res[$key])){
            $data = comParse::jsonEncode($res[$key]);
            comFiles::put(DIR_VARS.$fp,$data); //dump($data);
            unset($res[$key]);
        }
        return $res;
    }
    // 从缓存获取:家校:用户信息
    static function getEduUser($uid, $upd=0){ // deps,utab
        $fp = "/dtmp/wework/eu_$uid.cac_tab";
        if(!file_exists(DIR_VARS.$fp) || $upd){
            $res = ['errno'=>'','errmsg'=>'Get.成功'];
            $wecfg = read('wework', 'ex');
            $jiaSecret = $wecfg['JiaSecret'];
            if(empty($wecfg['isOpen'])){
                return ['errno'=>'!isOpen','errmsg'=>'请配置:[ex_wework.php]:isOpen=1'];
            }
            include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
            // getData
            $api = new \CorpAPI($wecfg['CorpId'], $jiaSecret);
            $res = $api->EdutUserInfo($uid); 
            // 
            $type = empty($res['user_type']) ? 0 : $res['user_type'];
            $tabs = ['','student','parent']; $key = $tabs[$type];
            if(!empty($res[$key])){
                $tmp = ['type'=>$key] + $res[$key];
                $data = comParse::jsonEncode($tmp);
                comFiles::put(DIR_VARS.$fp,$data); //dump($data);
                unset($res['user_type']);
            }
        }
        $data = comFiles::get(DIR_VARS.$fp);
        return json_decode($data,1);
    }
    // 从缓存获取:外部:用户信息
    static function getExtUser($uid, $upd=0){ // deps,utab
        $fp = "/dtmp/wework/out_$uid.cac_tab";
        if(!file_exists(DIR_VARS.$fp) || $upd){
            $res = ['errno'=>'','errmsg'=>'Get.成功'];
            $wecfg = read('wework', 'ex');
            $jiaSecret = $wecfg['JiaSecret'];
            if(empty($wecfg['isOpen'])){
                return ['errno'=>'!isOpen','errmsg'=>'请配置:[ex_wework.php]:isOpen=1'];
            }
            include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
            // getData
            $api = new \CorpAPI($wecfg['CorpId'], $jiaSecret);
            $res = $api->EXTUserInfo($uid); 
            if(!empty($res['external_contact'])){
                $data = comParse::jsonEncode($res['external_contact']);
                comFiles::put(DIR_VARS.$fp,$data); //dump($data);
            }
        }
        $data = comFiles::get(DIR_VARS.$fp);
        return json_decode($data,1);
    }

    # Edu:家校沟通-End -------

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

    static function smsgExtNews($agentId, $arts=[], $to=[]){
        $wecfg = read('wework', 'ex');
        //
        $to['pids'] = empty($to['pids']) ? [] : array_unique(array_filter($to['pids']));
        $to['sids'] = empty($to['sids']) ? [] : array_unique(array_filter($to['sids']));
        $to['part'] = empty($to['part']) ? [] : array_unique(array_filter($to['part']));
        if(empty($to['pids']) && empty($to['sids']) && empty($to['part'])){
            basDebug::bugLogs("smsgExtNews", $to, "smsgExtNews-null.log", 'file');
            return ['errcode'=>'82001', 'errmsg'=>"指定的发送对象"]; 
        }
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php");
        $api = new \CorpAPI($wecfg['CorpId'], $agentId); // $agentId,$wecfg['JiaSecret']
        // 
        $data = [
            'to_external_user' => [],
            'to_parent_userid' => $to['pids'],
            'to_student_userid' => $to['sids'],
            'to_party' => $to['part'],
            'toall' => 0,
            'msgtype' => 'news',
            'agentid' => $wecfg['AppsConfig'][$agentId]['AgentId'],
            'news' => ['articles' => $arts],
            'enable_id_trans' => 0,
            'enable_duplicate_check' => 0,
            'duplicate_check_interval' => 1800
        ];

        try {
            $errPlist = $errSlist = $errDlist = [];
            $api->ExtMessageSend($data, $errPlist, $errSlist, $errDlist);
            if(!empty($errPlist) || !empty($errSlist) || !empty($errDlist)){
                $dlog = [$errPlist, $errSlist, $errDlist];
                basDebug::bugLogs("smsgExtNews", $dlog, "smsgExtNews-nul2.log", 'file');
            }
            return [$errPlist, $errSlist, $errDlist];
        } catch (Exception $ex) {
            return ['errcode'=>'smsgExtNews', 'errmsg'=>$ex->getMessage()];
        }
    }

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
        $fp = "/dtmp/wework/$UserId.cac_tab";
        if(!$UserId){
            $data = $wecfgs['utab']['(null)'];
        }elseif(isset($wecfgs['utab'][$UserId])){
            $data = $wecfgs['utab'][$UserId];
        }else{
            if(!file_exists(DIR_VARS.$fp)){
                $utmp = self::updUser($UserId, $agentId);
                if(!empty($utmp['errcode'])){
                    return $utmp;
                }
            }
            $data = comFiles::get(DIR_VARS.$fp);
        }
        $uinfo = json_decode($data,1);
        if(!empty($uinfo)){ // 默认头像,调试权限
            if(empty($uinfo['avatar'])){ $uinfo['avatar']=PATH_STATIC.'/icons/basic/nouser2.png'; }
            $wecfgs = read('wework', 'ex');
            //$uinfo['pdebug'] = $uinfo['userid'] && strstr($wecfgs['ucfg']['debug'],$uinfo['userid']);
        }
        return $uinfo;
    }
    // 更新:单个用户数据 > 保存到缓存
    static function updUser($UserId='', $agentId=''){ // deps,utab,uone  
        $wecfg = read('wework', 'ex');
        $CorpId = $wecfg['CorpId']; //$agentId = 'AppCS';
        if(empty($wecfg['isOpen'])){
            return ['errno'=>'!isOpen','errmsg'=>'请配置:[ex_wework.php]:isOpen=1'];
        }
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $agentId = $agentId ?: $wecfg['DefAppID'];
        $fp = "/dtmp/wework/$UserId.cac_tab";
        $api = new \CorpAPI($CorpId, $agentId);
        $uinfo = $api->GetUserById($UserId); 
        // save
        if(!empty($uinfo['errcode'])){
            $uinfo = ['uname'=>$UserId, 'mname'=>"($UserId)", 'mpic'=>''] + $uinfo;
        }elseif(!empty($uinfo['userid'])){
            extWework::userMin($uinfo); 
            $data = comParse::jsonEncode($uinfo);
            comFiles::put(DIR_VARS.$fp,$data);
            //unset($res[$key]);
        }
        return $uinfo;
    }

    // 从缓存获取:部门/用户列表数据
    static function getContacts($act='deps', $secret='AppAB'){ // deps,utab
        $key = $act=='deps' ? 'department' : 'userlist';
        $fp = "/dtmp/wework/_$key.cac_tab";
        if(!file_exists(DIR_VARS.$fp)){
            self::updContacts($act, $secret);
        }
        $data = comFiles::get(DIR_VARS.$fp);
        return empty($data) ? [] : json_decode($data,1);
    }
    static function getDpuids($dpid=1){
        $utab = self::getContacts('utab');
        $res = [];
        foreach($utab as $uk => $ur) {
            if(in_array($dpid,$ur['department'])){
                $res[$uk] = $ur['name'];
            }
        }
        return $res;
    }
    // 更新:部门/用户列表数据 > 保存到缓存
    static function updContacts($act='deps', $secret='AppAB'){ // deps,utab
        $key = $act=='deps' ? 'department' : 'userlist';
        $fp = "/dtmp/wework/_$key.cac_tab";
        $res = ['errno'=>'','errmsg'=>'更新成功'];
        $wecfg = read('wework', 'ex');
        if(empty($wecfg['isOpen'])){
            return ['errno'=>'!isOpen','errmsg'=>'请配置:[ex_wework.php]:isOpen=1'];
        }
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        // getData
        $api = new \CorpAPI($wecfg['CorpId'], ($secret?$secret:$wecfg['TxlSecret']));
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

    static function mapLink($pos=''){
        $tmp = explode(',',$pos);
        $title = '打卡位置';
        $url = "https://map.qq.com/?type=marker&isopeninfowin=1&markertype=1&pointx={$tmp[0]}&pointy={$tmp[1]}&name=$title&zoomLevel=16";
        return $url;
    }

    static function oauth2Link($redirect, $scope='', $state='imcat_wxwork_login'){
        //$reuri = $iss.urlencode($iss.surl('user-login','',1));

        $redirect = str_replace(array("?&","&","#"),array("?","%26","%23"),$redirect);
        $scope || $scope = 'snsapi_base';

        $CorpId = read('wework.CorpId', 'ex');
        $urlBase = '';
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$CorpId&redirect_uri=$redirect&response_type=code&scope=$scope&state=$state#wechat_redirect";
        return $url;

    }

    static function wecfgs(){
        $wecfgs = read('wework', 'ex'); 
        $tab1 = ['CorpId','TxlSecret','CHECKIN_APP_SECRET','APPROVAL_APP_SECRET'];
        $tab2 = ['Secret','Token','EncodingAESKey'];
        foreach($tab1 as $dek){ unset($wecfgs[$dek]); }
        foreach($wecfgs['AppsConfig'] as $ak=>&$av){ 
            foreach($tab2 as $dek){ unset($wecfgs['AppsConfig'][$ak][$dek]); }
        }
        return $wecfgs;
    }

    // 按pid格式化
    static function lv1Dept($data, &$res, $pid=0, $deep=0){
        #$cr = $data[1];
        #$res[1] = ['name'=>$cr['name'], 'deep'=>0, 'pid'=>0];
        foreach($data as $ck=>$cr){
            if($cr['parentid']==$pid){
                $res[$ck] = ['name'=>$cr['name'], 'deep'=>$deep, 'pid'=>$pid];
                self::lv1Dept($data, $res, $ck, $deep+1);
            }
        }
    }

    // 部门结构格式化:健值是班级id
    static function fmtDept($class=''){
        $data = $class ? self::getEduTabs('deps') : self::getContacts('deps');
        $res = [];
        foreach($data as $ck=>$cr){ // .':'.$cr['id']
            $res[$cr['id']] = $cr; //['name'];
        }
        return $res;
    }

    // xxx
    static function fmtMychs($deps, $userp){
        if(empty($userp['children'])){ return []; }
        $tcs = $userp['children'];
        $chs = []; 
        foreach($tcs as $tk=>$tr){ 
            $chs[$tk]['sid'] = $sid = $tr['student_userid']; $ti = extWework::getEduUser($sid); 
            $chs[$tk]['sid'] = $sid;
            $chs[$tk]['rel'] = $tr['relation'];
            $chs[$tk]['name'] = $ti['name'];
            $chs[$tk]['cid'] = $cid = $ti['department'][0]; 
            $chs[$tk]['pid'] = $pid = $deps[$cid]['parentid'];
            $chs[$tk]['cname'] = $deps[$pid]['name'].$deps[$cid]['name'];
            $aid = '';
            foreach($deps[$cid]['department_admins'] as $ar){
                if($ar['type']==3){
                    $chs[$tk]['aid'] = $aid = $ar['userid']; 
                    $au = extWework::getUser($aid); //dump($au);
                    $chs[$tk]['aname'] = $au['name'];
                    break;
                }
            }
        }
        return $chs;
    }

    static function fmtMyprs($ustu){
        if(is_string($ustu)){
            $ustu = extWework::getEduUser($ustu); 
        }
        $prtab = $ustu['parents'];
        $prs = []; 
        foreach($prtab as $tk=>$tr){ 
            $prs[$tk]['pid'] = $tr['parent_userid']; 
            $prs[$tk]['rel'] = $tr['relation'];
            $prs[$tk]['mtel'] = $tr['mobile'];
            $prs[$tk]['isub'] = $tr['is_subscribe'];
        }
        return $prs;
    }

    //parents

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
