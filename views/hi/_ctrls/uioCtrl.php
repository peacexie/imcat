<?php
namespace imcat\hi;

use imcat\basEnv;
use imcat\basElm;
use imcat\basOut;
use imcat\basStr;
use imcat\basSql;
use imcat\comSession;
use imcat\comCookie;
use imcat\comConvert;

use imcat\extCache;
use imcat\extSms;
use imcat\extWework;
use imcat\extWeedu;

use imcat\glbConfig; 
use imcat\glbDBExt;
use imcat\glbHtml;
use imcat\safComm;
use imcat\usrMember;

use imcat\wysBasic;
use imcat\wmpUser;
use imcat\wmpOauth;

use imcat\vopApi as api;

// user-login-logout
class uioCtrl{

    public $ucfg = array();
    public $vars = array();

    public $ckey = '';
    public $uflag = '0'; // 0,login(inmem,company,person) 
    public $uinfo = [];
    public $rlog = [];
    public $re = [];
    public $exp_day = 3; // 3天过期

    function __construct($ucfg=array(), $vars=array(), $bc=1){
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->uioBase($bc);
    }

    /*
        ### Apply #######################################################
    */

    function applyBase(){
        $re = &$this->re;
        $udefs = read('udefs', 'sy'); $umtab = [];
        $_canap = empty($udefs['_canap']) ? '^,^' : $udefs['_canap'];
        foreach ($this->re['vars']['umods'] as $gk=>$gv){ 
            if(strstr($_canap,$gk)) { $umtab[$gk] = $gv; }
        }
        $re['vars']['umopt'] = basElm::setOption($umtab, '', 0);
        $re['newtpl'] = 'login/apply'; 
        return $re;
    }
    function appdoBase(){
        $re = &$this->re;
        $re['vars']['errmsg'] = '申请成功';
        // chk-code
        $upass = req('upass'); $upchk = req('upchk');
        $vcode = req('vcode'); $umod = req('umod');
        if(!$upass || $upass!=$upchk){
            $vars = ['errno'=>'Error-password', 'errmsg'=>'密码不一致或为空！'];
            return api::view($vars, 'api');
        }
        $vcres = safComm::formCVimg('apply', $vcode, 'check', 3600);
        if(!empty($vcres)){
            $vars = ['errno'=>'Error-`vcode`', 'errmsg'=>'验证码错误！'];
            return api::view($vars, 'api');
        }
        // uname
        $uname = basStr::filKey(req('uname'),'_@.-'); // 
        if(!$uname || strlen($uname)<5){ 
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '用户名不合法'; 
            return api::v($re, 'api');
        }
        // 占用
        $unew = usrMember::addUname($uname, $umod);
        if($unew!=$uname){
            $re['vars']['errno'] = 'Error-userName'; 
            $re['vars']['errmsg'] = '用户已占用'; 
            return api::v($re, 'api');
        }
        // save
        $mname = req('mname');
        $row = ['uname'=>$uname, 'umod'=>$umod, 'mname'=>$mname, 'mpic'=>''];
        // $this->saveLogin(&$row, $mode=''); // usrMember::addUser($umod, $uname, $upass, $mname, '', '');
        usrMember::usvUser($row, 'idpwd', [], $upass);
        $row += $this->rlog;
        $tmp = db()->table('active_login')->data($row)->replace(0); 
        return $re;
    }

    /*
        ### 测试登录/登出/检查 #######################################################
    */

    // {surl(hi:login-setdf)}?jpmkv=comm:{=$this->mkv}&domkv=login-weedu&sec=full
    function setdfAct(){
        $tab = ['jpmkv']; // , 'bdkey', 'svmod', 
        foreach($tab as $key) {
            $$key = req($key, '', 'Title', 512);
            if($$key){ comCookie::oset($key, $$key); }
        }
        $domkv = req('domkv', $this->mod);
        $sec = req('sec'); if($sec){ $domkv = surl($this->mod)."?sec=$sec"; }
        api::v([], 'dir', $domkv);
    }
    // (本地)DelUser测试: xxx.php/login-locrs-locdemo
    function locduAct(){
        if(!basEnv::isLocal()){
            api::v([], 'die', 'Error-Local!');
        }
        $uname = $this->view;
        usrMember::delUser($uname, 0);
        usrMember::delUser($uname, 2);
        api::v([], 'dir', $this->mod);
    }

    // (本地)登录测试: 判断权限再登录...
    function locinAct(){
        if(!basEnv::isLocal()){
            api::v([], 'die', 'Error-Local!');
        }
        $this->locinBase();
    }
    // (本地)登录测试: xxx.php/{mod}-locin-locdemo, /{mod}-locin-wechat1?mode=wechat
    // 先判断权限，否则后果自负！！！
    function locinBase(){
        $udemo = read('udemo', 'sy'); $uname = $this->view;
        $tmp = db()->table('users_uacc')->where("uname='$uname'")->find();
        if(!empty($tmp)){ // 取会员信息
            $mtp = db()->table("users_$tmp[umods]")->where("uname='$uname'")->find(); // AND `show`='1'
            $exm = [ // mname   mpic
                'mname' => empty($mtp['mname']) ? "($uname)" : $mtp['mname'],
                'mpic' => empty($mtp['mpic']) ? "" : $mtp['mpic'],
            ]; // mname    grade   mfrom   mtel    memail  miui 
            $row = ['uname'=>$uname,'umod'=>$tmp['umods']] + $this->rlog + $exm;
            $tmp = db()->table('active_login')->data($row)->replace(0); 
        }elseif(isset($udemo[$uname])){ // 取预设信息
            $row = $this->rlog + $udemo[$uname];
            $row['mext'] = '';
            $mode = req('mode', 'locin');
            if($mode!='locin'){ $this->rlog['utype'] = $row['utype'] = $mode; }
            $this->saveLogin($row, $mode);
        }else{ 
            api::v([], 'die', 'Error-User!');
        } //dump($row);
        api::v($row, 'dir', $this->mod);
    }
    // 登出用户: xxx.php/login-logout[-dir]
    function logoutBase(){
        $cval = comCookie::oset($this->ckey, '');
        db()->table('active_login')->where("ckey='{$this->ckey}'")->delete();
        $vars = ['errno'=>0, 'errmsg'=>'登出OK'];
        return $vars;
    }
    // logoutAct:绑定操作(要重写)
    function logoutAct(){
        $res = $this->logoutBase();
        api::v($res, 'dir', $this->mod);
    }
    // 判断是否登录成功 ??? 
    function checkAct(){
        $vars = $this->re; // ['vars']
        //api::v($vars, 'die', 'Texst.die');
        api::v($vars, 'api');
    }
    static function getUnow($ucfg, $vars, $rk=0){
        $uio = new self($ucfg, $vars, $rk);
        //dump($uio->vars);
        return $uio->re['vars'];
    }
    // 错误提示信息
    function uioVInfo($msg=[]){
        $this->re['vars'] = $msg + $this->re['vars'];
        $this->re['newtpl'] = 'home/info';
        #return api::v($this->re);
        return $this->re;
    }
    
    /*
        ### 多端登录:wechat #######################################################
    */

    // 微信授权：统一先跳转到此地址
    function wecdirAct(){
        $state = req('state'); $stmp = explode('^',$state);
        if(!empty($this->uinfo)){ // 已经登录
            if($stmp[0]=='set' && !empty($stmp[1])){
                $vars = $this->saveSet($this->uinfo, $stmp, 'wecdir'); 
                return $this->uioVInfo($vars);
            }
            if($stmp[0]=='bind' && !empty($stmp[1]) && !empty($stmp[2])){
                usrMember::bindUser($stmp[2], 'wechat', $user['openid']);
                $vars = ['errtip'=>'绑定成功', 'errmsg'=>'请刷新网页'];
                $vars = $this->uioVInfo($vars);
                return api::v($vars);
            }
            if($stmp[0]=='scan' && !empty($stmp[1])){
                $urow = $this->uinfo;
                $urow['ckey'] = $stmp[1];
                $this->saveLogin($urow, 'wechat'); 
            }
            $vars = ['errtip'=>'登录成功', 'errmsg'=>'已登录，请刷新网页'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars, 'api');
        }else{ // 未登录,跳转去授权
            $scope = req('scope', 'snsapi_userinfo');
            $reurl = surl($this->mod."-wechat", '', 1); 
            $wecfg = wysBasic::getConfig('admin');
            $wea = new wmpOauth($wecfg);
            $oaurl = $wea->getCode($reurl, $scope, $state); // echo "($aur)";
            header('Location:'.$oaurl);
            die();
        }
    }
    // 点微信授权链接: {mod}-wechat + state=(dir:mkv:ext)
    // 扫(授权)码登录: {mod}-wechat + state=(scan^rnd24^ext)
    // 绑定 ?
    function wechatAct(){
        $wecfg = wysBasic::getConfig('admin'); 
        if(empty($this->cfg['enable'])){
            return ['errno'=>'notOpen', 'errmsg'=>'请设置参数'];
        }
        $oauth = new wmpOauth($wecfg);
        $code = req('code');
        $state = req('state'); $stmp = explode('^',$state);
        if($code){
            $acc = $oauth->getACToken($code);
            if(!empty($acc['errcode'])){ // {"errcode":40003,"errmsg":" invalid openid "}
                $vars = ['errno'=>$acc['errcode'], 'errmsg'=>$acc['errmsg']];
                $vars = $this->uioVInfo($vars);
                return api::v($vars);
            }
            $user = $oauth->getUserInfo($acc['result']['access_token'], $acc['result']['openid']);
            if(!empty($user['errcode'])){ // {"errcode":40003,"errmsg":" invalid openid "}
                $vars = ['errno'=>$user['errcode'], 'errmsg'=>$user['errmsg']];
                $vars = $this->uioVInfo($vars);
                return api::v($vars);
            }else{
                if($stmp[0]=='bind' && !empty($stmp[1]) && !empty($stmp[2])){
                    usrMember::bindUser($stmp[2], 'wechat', $user['openid']);
                    $vars = ['errtip'=>'绑定成功', 'errmsg'=>'请刷新网页'];
                    $vars = $this->uioVInfo($vars);
                    return api::v($vars);
                }
                $ext = "sex={$user['sex']}".(empty($user['unionid']) ? '' : "\nunionid={$user['unionid']}");
                $utmp = ['pptuid'=>$user['openid'], 'mname'=>$user['nickname'], 'mpic'=>$user['headimgurl'], 'mext'=>$ext];
                $urow = $utmp + $this->rlog;
                $this->saveLogin($urow, 'wechat');
                if($stmp[0]=='set' && !empty($stmp[1])){
                    $vars = $this->saveSet($urow, $stmp, 'wechat'); 
                    $vars = $this->uioVInfo($vars);
                    return api::v($vars);
                }
                if($stmp[0]=='scan' && !empty($stmp[1])){
                    $urow['ckey'] = $stmp[1];
                    $this->saveLogin($urow, 'wechat');
                    $vars = ['errtip'=>'登录成功', 'errmsg'=>'已登录，请刷新网页'];
                    $vars = $this->uioVInfo($vars);
                    return api::v($vars);
                }else{
                    return api::v($urow, 'dir', surl($this->mod));
                }
            }
        }else{
            $vars = ['errno'=>'Empty `code`', 'errmsg'=>'缺少code参数'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }
        #return api::v($this->re);
    }

    /*
        ### 多端登录:wework #######################################################
    */

    // 企业微信授权：统一先跳转到此地址
    function wewdirAct(){
        $state = req('state'); $stmp = explode('^',$state);
        $retype = req('retype'); $rex = $retype ? "?retype=json" : '?_redef=def';
        if(!empty($this->uinfo)){ // 已经登录
            if($stmp[0]=='scan' && !empty($stmp[1])){
                $urow = $this->uinfo;
                $urow['ckey'] = $stmp[1];
                $this->saveLogin($urow, 'wework'); 
            }
            $vars = ['errtip'=>'登录成功', 'errmsg'=>'已登录，请刷新网页'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }else{ // 未登录,跳转去授权
            $reurl = surl($this->mod."-wework", '', 1).$rex;
            $scope = req('scope', 'snsapi_userinfo');
            $oaurl = extWework::oauth2Link($reurl, $scope, $state);
            header('Location:'.$oaurl);
            die();
        }
    }
    // 点企业微信授权链接: {mod}-wework + state=(dir:mkv:ext)
    // 企业微信扫(授权)码登录: {mod}-wework + state=(scan^rnd24^ext)
    // 绑定 ?
    function weworkAct(){
        $code = req('code');
        $state = req('state'); $stmp = explode('^',$state);
        if($code){
            $wecfg = read('wework', 'ex');
            $CorpId = $wecfg['CorpId']; $agentId = 'AppCS';
            if(empty($wecfg['isOpen'])){
                die('请配置:[ex_wework.php]:isOpen=1');
            }
            include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
            $api = new \CorpAPI($CorpId, $agentId);
            try {
                $ures = $api->GetUserInfoByCode($code, 1); 
                if(empty($ures['UserId'])){ 
                    $vars = ['errno'=>$ures['errcode'], 'errmsg'=>$ures['errmsg']];
                    $vars = $this->uioVInfo($vars);
                    return api::v($vars);
                }else{
                    $user = $api->GetUserById($ures['UserId']); 
                } //dump($ures); dump($user);
                $ext = "gender={$user['gender']}".(empty($user['mobile']) ? '' : "\nmtel={$user['mobile']}");
                $utmp = ['pptuid'=>$user['userid'], 'mname'=>$user['name'], 'mpic'=>$user['avatar'], 'mext'=>$ext];
                $urow = $utmp + $this->rlog;
                $this->saveLogin($urow, 'wework');
                if($stmp[0]=='scan' && !empty($stmp[1])){
                    $urow['ckey'] = $stmp[1];
                    $this->saveLogin($urow, 'wework');
                    $vars = ['errtip'=>'登录成功', 'errmsg'=>'已登录，请刷新网页'];
                    $vars = $this->uioVInfo($vars);
                    return api::v($vars);
                }else{
                    header('Location:'.surl($this->mod));
                }
            } catch (Exception $e) {
                $vars = ['errno'=>'errNowUser', 'errmsg'=>$e->getMessage()];
                $this->re['vars'] = $vars + $this->re['vars'];
            }
        }else{
            $vars = ['errno'=>'Empty `code`', 'errmsg'=>'缺少code参数'];
            $vars = $this->uioVInfo($vars);
            return api::v($vars);
        }
        #return api::v($this->re);
    }

    /*

        ### 多端登录:idpwd #######################################################
    */

    // 账密登录：
    function idpwdAct(){
        #safComm::urlFrom();
        $row['uname'] = $uname = req('uname'); $upass = req('upass'); $vcode = req('vcode');
        $vcres = safComm::formCVimg('idpwd', $vcode, 'check', 3600);
        if(!empty($vcres)){
            $vars = ['errno'=>'Error-`vcode`', 'errmsg'=>'验证码错误！'];
            api::view($vars);
        }
        $this->re['vars']['uimod'] = $uimod = $this->getUser($row, 'idpwd', 1);
        if(empty($row['upass']) || empty($uimod['show'])){
            $vars = ['errno'=>'Error-`userid-password(1)`', 'errmsg'=>'账密错误1！'];
            api::view($vars);
        }else{
            $dbpass = $row['upass'];
            unset($row['upass']);
        } //dump($uimod); dump($row);
        if($dbpass!==comConvert::sysPass($uname,$upass,$row['umod'])){
            $vars = ['errno'=>'Error-`userid-password(2)`', 'errmsg'=>'账密错误2！'];
        }else{ 
            $vars = ['errmsg'=>'登录成功!'];
            $row['mname'] = $uimod['mname'];
            db()->table('active_login')->data($this->rlog+$row)->replace(0);
            comCookie::mset('vcodes',0,'idpwd','null');
        } //dump($row); dump($this->rlog);
        api::view($vars);
    }

    /*
        ### 多端登录:mobvc #######################################################
    */

    // 发短信
    function mobsmAct(){
        #safComm::urlFrom();
        $vcode = req('vcode'); $mtel = req('mtel');
        $vcres = safComm::formCVimg('mobvc', $vcode, 'check', 3600);
        if(!empty($vcres)){
            $vars = ['errno'=>'Error-`vcode`', 'errmsg'=>'图片码错误！'];
        }else{ // send-msg
            $sms = new extSms(); $code = rand(100123, 999876); //sendTid($mobiles, $tid='', $params=[], $cfgs=[])
            $res = $sms->sendTid($mtel, '', ['code'=>$code], ['pid'=>'mobvc:'.$code]);
            if(isset($res[1]) &&$res[1]=='OK'){
                $vars = ['errno'=>0, 'errmsg'=>'短信已发送，请留意接收！'];
                comCookie::mset('vcodes',0,'mobvc','null');
            }else{
                $vars = ['errno'=>'Error-API', 'errmsg'=>$res[1]];
            }
        }
        api::view($vars);
    }

    // 短信登录：
    function mobvcAct(){
        global $_cbase;
        #safComm::urlFrom();
        $mcode = req('mcode'); $mtel = req('mtel');
        // db-check
        $stamp = $_cbase['run']['stamp']-600; 
        $rdb = db()->table('plus_smsend')->where("tel='$mtel' AND pid='mobvc:$mcode' AND atime>='$stamp' ")->find();
        if(!empty($rdb['res']) && $rdb['res']=='1:OK'){
            db()->table("plus_smsend")->data(array('pid'=>"ok-vcode:$mcode"))->where("kid='{$rdb['kid']}'")->update(0);
            $row = $this->rlog + ['mname'=>$mtel, 'mext'=>'', 'pptuid'=>$mtel];
            $this->saveLogin($row, 'mobvc');
            comCookie::oset('mobvc',''); // clear.cookie
            $vars = ['errno'=>0, 'errmsg'=>'登录成功，请刷新！'];
        }else{
            $vars = ['errno'=>'Error-VODE', 'errmsg'=>'短信码错误'];
        }
        api::v($vars, 'api');
    }

    /*
        ### 多端登录:eduid #######################################################
    */

    // 教育号登录
    function eduidBase(){
        global $_cbase; $_cbase['sys_name'] = '教育号开发';
        $appid = req('appid'); $code = req('code'); // ?code=xxx32bitxxx&appid=700439
        if($appid && $code){
            $this->wxedu = new extWeedu($appid);
            $token = $this->wxedu->getAccessToken($code, $reurl='');
            $ures = $this->wxedu->getUserInfo($token); 
            if(!empty($ures['code'])){ 
                $vars = ['uflag'=>'0', 'errno'=>'Error:'.$ures['code'], 'errmsg'=>$ures['msg'].'/'.$ures['data']];
                return $this->uioVInfo($vars);
            }elseif(is_array($ures['data'])){
                $ur = $ures['data']; $ex = json_decode($ur['ext_data'], 1); //dump($ex);
                $mext .= "coid=".$ur['corpid']."\nuserid=".$ur['userid']."\nappid=".$appid; //$ur['suite_id'];
                $mext .= "\nsid=".$ur['source_id']."\nastyle=".$ur['manage_style']."\ncorp=".$ex['org_name']."\ntoken=$token";
                $row = ['utype'=>'eduid', 'umod'=>$ur['role_id'], 'uname'=>'', 'pptuid'=>$ur['corpid'].'_'.$ur['userid'], 
                    'mname'=>$ur['user_name'], 'mpic'=>$ur['avatar'], 'mext'=>$mext] + $this->rlog;
                $this->saveLogin($row);
                header('Location:'.surl($this->ucfg['mkv']));
                die();
            }
        }
        if(empty($this->uinfo)){
            $vars = ['uflag'=>'0', 'errno'=>'Eduid-Timeout', 'errmsg'=>'智慧校园登录超时，请重新从智慧校园登录'];
            return $this->uioVInfo($vars);
        }
        // 
        $apptab = read('weedu.AppsConfig', 'ex');
        $roles = ['11'=>'学生', '12'=>'老师', '13'=>'家长'];
        $uinfo = $this->uinfo; $umod = $uinfo['umod']; 
        $appid = empty($uinfo['mexa']['appid']) ? '~' : $uinfo['mexa']['appid'];
        $exinfo = [
            'apname' => isset($apptab[$appid]['name']) ? $apptab[$appid]['name'] : '(未知应用)',
            'school' => str_replace(['电子科技','有限公司'], ['','...'], $uinfo['mexa']['corp']),
            'title' => isset($roles[$umod]) ? $roles[$umod] : '(未知身份)',
        ]; //dump($exinfo);
        $this->re['vars']['uinfo'] = $this->uinfo += $exinfo;
        // 
        $this->re['newtpl'] = $this->mod.'/eduid'; 
    }
    // 扩展
    function eduidAct(){
        $this->eduidBase();
        return $this->re;
    }

    /*
        ### bindAct #######################################################
    */

    function bindBase($cfg=[]){
        $row['mode'] = $mode = req('mode'); // 登录模式/idpwd
        $row['umod'] = $umod = req('umod'); // 模型
        $row['uname'] = req('uname'); $row['passwd'] = req('passwd');
        $row['pptuid'] = req('pptuid'); $row['mname'] = req('mname');
        if(!empty($this->uinfo) && $this->uinfo['umod']=='(bind)'){
            $clog = ['umod'=>$umod];
            db()->table('active_login')->data($clog)->where("ckey='{$this->ckey}'")->update();
        }
        {
            $clog = ['umod'=>$umod];
            db()->table('active_login')->data($clog)->where("pptuid='{$row['pptuid']}' AND umod='(bind)'")->update();
        }
        $udefs = read('udefs','sy');
        $cfgs = isset($udefs[$mode]) ? $udefs[$mode] : ['grade'=>'','show'=>'0'];
        $cfgs = $cfg + $cfgs;
        return usrMember::usvUser($row, $mode, $cfgs, $row['passwd']); // ($row, $mode, $cfgs, $upass='')
    }

    // bindAct:绑定操作(要重写)
    function bindAct(){
        if(!empty($this->uinfo)){
            $res = $this->bindBase();
            #comCookie::oset('svmod', '');
            //comCookie::oset('jpmkv', '');
            //comCookie::oset('bdkey', '');
            api::view($res, 'api');
        }
        api::v($this->re['vars'], 'api');
    }

    /*
        ### init/check #######################################################
    */

    function uioBase($bc=1){
        global $_cbase; $run = $_cbase['run'];
        $this->mod = $this->ucfg['mod']; $this->key = $this->ucfg['key']; $this->view = $this->ucfg['view']; 
        if($this->mod=='uio'){ die('Error-Url(uioBase)!'); }
        $this->ckey = usrMember::getCkey($this->key); //echo "a:$this->ckey<br>";
        $this->re['vars'] = ['errno'=>0, 'errmsg'=>'', 'uflag'=>$this->uflag, 'ckey'=>$this->ckey]; 
        $this->rlog = ['ckey'=>$this->ckey, 'utype'=>$this->key, 'atime'=>$run['stamp'], 'aip'=>$run['userip']];
        #if(in_array($this->key,['logout'])){ return; } // 'locin',
        // uinfo
        $whr = "ckey='{$this->ckey}' AND atime>='".($run['stamp']-$this->exp_day*86400)."'"; 
        $row = db()->table('active_login')->where($whr)->find(); //dump($row);
        if(!empty($row['mext'])){
            $row['mexa'] = basElm::text2arr($row['mext']);
        } 
        if(empty($row)){ 
            $this->re['vars']['uinfo'] = $this->uinfo = []; 
            $bc && $this->bindCheck($row);
        }else{
            if($run['stamp']-$row['atime']>3600){
                $row['atime'] = $data['atime'] = $run['stamp'];
                db()->table('active_login')->data($data)->where("ckey='{$this->ckey}'")->update();
            }
            $this->re['vars']['uinfo'] = $this->uinfo = $row;
            $this->re['vars']['utype'] = $row['utype'];
            $this->re['vars']['uflag'] = $row['umod']; 
            $this->re['vars']['last_upd'] = $row['atime']; // 上次登录
            $bc && $this->bindCheck($row); 
        }
        $unameMod = empty($this->re['vars']['uimod']['uname']) ? '(null)' : $this->re['vars']['uimod']['uname'];
        $this->re['vars']['uname'] = empty($this->uinfo['uname']) ? $unameMod : $this->uinfo['uname'];
        // udefs,udebug
        $this->re['vars']['udefs'] = $udefs = read('udefs', 'sy'); 
        $this->re['vars']['udebug'] = empty($udefs['_debug']) ? '-' : $udefs['_debug'];
        // vars/env
        $udemo = read('udemo', 'sy');
        $this->re['vars']['null'] = $udemo['null']; // 为空的用户信息
        $this->re['vars']['urlBase'] = surl('base:0', '', 1);
        $this->re['vars']['urlNow'] = surl('0', '', 1);
        $this->re['vars']['isMobile'] = basEnv::isMobile();
        $this->re['vars']['isWexin'] = basEnv::isWeixin();
        $this->re['vars']['isWework'] = basEnv::isWework();
        // 
        if(!empty($this->set_newtpl)){
            $this->re['newtpl'] = $this->mod.'/mhome'; // 'tplnull'=>1 设置不需要模板
        }
        //
    }
    // bind-check:绑定检查(基础)
    function bindCheckBase(&$row){
        // modname, umods
        $this->re['vars']['modname'] = '(未知模型)';
        $umods = []; 
        $gps = read('groups'); // $this->view;
        foreach ($gps as $um => $uval) {
            if($uval['pid']=='users'){ 
                $umods[$um] = $uval['title']; 
                if(!empty($row['umod']) && $row['umod']==$um){
                    $this->re['vars']['modname'] = $uval['title'];
                    unset($umods[$um]);
                }
            }
        }
        $this->re['vars']['umods'] = $umods; 
        if(empty($row)){ return; }
        // 
        $this->re['vars']['errmsg'] = '已登录';
        $mode = $row['utype'];
        $udefs = read('udefs','sy');
        if(isset($row['umod'])){ // 已知模型
            $uimod = $this->getUser($row, $mode);
        }elseif(!isset($udefs[$mode]['umod'])){ // 无配置:存cookie
            $cac = new extCache(); // get-cookie
            $ukey = empty($row['uname']) ? $row['uname'] : $row['uname'];
            $uimod = $cac->get($ukey);
        }elseif(!empty($udefs[$mode]['umod'])){ // 指定模型:存会员
            $uimod = $this->getUser($row, $mode);
            if(empty($uimod)){
                $uimod = [];
            }
            // TODO:show=0
        }else{ // 会员模型为空:待绑定/已绑定
            if($row['umod']!='(bind)'){
                $nomod = empty($row['umod']);
                $uimod = $this->getUser($row, $mode); //dump($row['umod']);
                if($nomod && !empty($row['umod'])){
                    $this->uflag = $row['umod']; 
                }
            }else{ 
                $uimod = [];
            }
            // TODO:show=0
        }
        $this->re['vars']['uimod'] = $uimod;
        // 
        if($this->uinfo['umod']=='(bind)'){
            $umarr = explode(',', $udefs[$mode]['umtab']);
            $umtab = [];
            foreach ($umarr as $gk){
                $umtab[$gk] = isset($umods[$gk]) ? $umods[$gk]['title'] : "【{$gk}】";
            }
            $this->re['vars']['umtab'] = $umtab;
            $this->re['vars']['umopt'] = basElm::setOption($umtab, '', 0);
        }
    }
    // bind-check:绑定检查(要重写)
    function bindCheck(&$row){ 
        $this->bindCheckBase($row);
    }

    /*
        ### 公共方法 #######################################################
    */

    // 强行绑定（可切换用户模型）
    function saveSetBase($row, $stmp=[], $key='wecdir'){
        $org = db()->table('active_login')->where("ckey='{$stmp[1]}' AND uname='(set)'")->find();
        $ex2 = empty($org['mext']) ? [] : (is_string($org['mext']) ? basElm::text2arr($org['mext']) : []);
        $_enc = comConvert::sysRevert(empty($ex2['_enc'])?'':$ex2['_enc'], 1);
        if(empty($org) || $_enc!=$stmp[1] || time()-$org['atime']>($this->exp_day+1)*86400){
            $ermsg = empty($org) ? "绑定错误(rec)！" : ($_enc!=$stmp[1]?"绑定错误(enc)！":"绑定错误(exp)！");
            return ['errno'=>'Error-saveSetBase', 'errmsg'=>'绑定错误！'];
        }
        //
        $udefs = read('udefs'); $not_ex = empty($udefs['_not_exmod']) ? 'inmem' : $not_ex;
        $mold = $row['umod']; $row['umod'] = $org['umod'];
        if(strstr($not_ex,$mold)){ 
            $ermsg = empty($org) ? "绑定错误(rec)！" : ($_enc!=$stmp[1]?"绑定错误(enc)！":"绑定错误(exp)！");
            return ['errno'=>'Error-Action', 'errmsg'=>'操作错误，此用户不支持切换模型！'];
        }
        $mexa = empty($row['mext']) ? [] : basElm::text2arr($row['mext']); 
        $mexa = $ex2 + $mexa; $exfalg = 1; $res = $d3x = []; 
        if(empty($mold) || $mold=='(bind)'){ // 直接增加到指定模型
            usrMember::usvUser($row, $row['utype'], $mexa);
            $res = ['uflag'=>$org['umod'], 'errno'=>'0', 'errmsg'=>'绑定成功'];
        }elseif($mold!=$org['umod']){ // 更换模型 
            $d3x = usrMember::uexUser($row['uname'], $org['umod'], 0, $mexa);
            if(empty($d3x)){ 
                usrMember::usvUser($row, $row['utype'], $mexa);
            }
            $res = ['uflag'=>$org['umod'], 'errno'=>'0', 'errmsg'=>"由{$row['umod']}切换至{$org['umod']}成功"];
        }else{ // Update?
            $res = ['uflag'=>$org['umod'], 'errno'=>'0', 'errmsg'=>"未变更`{$org['umod']}`模型"];
            $exfalg = 0;
        }
        if($exfalg){
            unset($row['mexa']);
            $tmp = db()->table('active_login')->data($row)->replace(0); 
        }
        return $res+$d3x;
    }
    // 扩展
    function saveSet($row, $stmp=[], $key='wecdir'){
        $res = $this->saveSetBase($row, $stmp, $key);
        return $res;
        //$org = $db->table('active_login')->where("ckey='{$stmp[1]}' AND uname='(set)'")->delete();
    }

    // 保存登录信息
    function saveLogin(&$row, $mode=''){ 
        $udefs = read('udefs','sy');
        $ukey = !empty($row['pptuid']) ? $row['pptuid'] : $row['uname']; // TODO:黑名单...
        $cac = new extCache(); $caexp = ($this->exp_day*2).'d';
        if(!isset($udefs[$mode]['umod'])){ // 无配置:存cookie
            $row['umod'] = empty($row['umod']) ? 'person' : $row['umod'];
            $cac->set($ukey, $row, $caexp);
        }elseif(!empty($udefs[$mode]['umod'])){ // 指定模型:存会员
            $cfgs = $udefs[$mode];
            $row['umod'] = $cfgs['umod']; // 指定模型优先
            $uimod = $this->getUser($row, $mode);
            if(empty($uimod)){
                usrMember::usvUser($row, $mode, $cfgs, '');
            }else{
                // update?
            }
        // TODO:url-指定模型
        }else{ // 会员模型为空:待绑定/已绑定
            $uimod = $this->getUser($row, $mode); 
            if(empty($uimod)){
                $cac->set($ukey, $row, $caexp);
                $row['umod'] = '(bind)';
            }else{ 
                #$row['umod'] = $uimod['umods'];
                // update?
            }
        }
        // save-login
        unset($row['mexa']);
        $tmp = db()->table('active_login')->data($row)->replace(0); 
    }

    /*
        ### xxx #######################################################
    */

    // locin, idpwd, mobvc, email, wechat, wework
    static function getUser(&$row, $mode='idpwd', $exacc=0){
        $db = db(); 
        if($mode && !in_array($mode,['locin','idpwd'])){
            $tmp = $db->table('users_uppt')->where("pptuid='{$row['pptuid']}'")->find();
            if(empty($tmp)){ return []; }
            $uname = $row['uname'] = $tmp['uname'];
        }else{
            $uname = $row['uname'];
        }
        if(empty($row['umod'])){ 
            $uacc = $db->table('users_uacc')->where("uname='$uname'")->find();
            if(empty($uacc)){ return []; }
            $umod = $row['umod'] = $uacc['umods']; 
            if($exacc){ $row['upass'] = $uacc['upass']; }
        }else{
            $umod = $row['umod'];
        } 
        $_groups = glbConfig::read('groups');
        if(!isset($_groups[$umod]) || $_groups[$umod]['pid']!='users'){
            return [];
        }
        $uimod = $db->table("users_$umod")->where("uname='$uname'")->find(); // AND `show`='1'
        if(empty($uimod)) return array();
        return $uimod;
    }

    function qrUrls($urmkv, $scope='', $exmkv='0'){
        if(!strpos($urmkv,'/')) $urmkv = $this->re['vars']['urlNow']."/$urmkv";
        $scope = $scope ? $scope : req('scope', 'snsapi_userinfo'); // snsapi_userinfo,snsapi_base
        $exmkv = $exmkv ? $exmkv : req('exmkv', '0.0.0'); // like:vmod^mkv^exstr
        $re['qrUrl']   = "$urmkv?scope=$scope&state=scan^{$this->ckey}^$exmkv&_v=".time();
        $re['linkUrl'] = str_replace('=scan^', '=dir^', $re['qrUrl']);
        $re['scanUrl'] = PATH_BASE."?ajax-vimg&mod=qrShow&data=".str_replace(['?','&'],['%3F','%26'],$re['qrUrl']);
        return $re;
    }

}

/*

*/
