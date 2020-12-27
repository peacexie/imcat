<?php
namespace imcat\base;

use imcat\basEnv;
use imcat\basElm;
#use imcat\basOut;
use imcat\basSql;
use imcat\comSession;
use imcat\comCookie;
use imcat\comConvert;

use imcat\extCache;
use imcat\extSms;
use imcat\extWework;

use imcat\glbConfig; 
use imcat\glbDBExt;
use imcat\safComm;
use imcat\usrMember;
use imcat\vopApi;

use imcat\wysBasic;
use imcat\wmpUser;
use imcat\wmpOauth;

// user-login-logout
class uioCtrl{

    public $ucfg = array();
    public $vars = array();

    protected $ckey = '';
    protected $uflag = '0'; // 0,login(inmem,company,person) 
    protected $uinfo = [];
    protected $rlog = [];
    protected $re = [];
    protected $exp_day = 3; // 3天过期

    function __construct($ucfg=array(), $vars=array()){
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->initBase();
    }

    /*
        ### 测试登录/登出/检查 #######################################################
    */

    function setdfAct(){
        // svmod-保存模型(person-pcom-1), jpmkv-跳转mkv(comm:news.2020-12.abcd), bdkey-绑定key(xxxxx)
        $tab = ['svmod', 'jpmkv', 'bdkey'];
        foreach($tab as $key) {
            $$key = req($key, '', 'Title', 512);
            if($$key){
                comCookie::oset($key, $$key);
            }else{
                comCookie::oset($key, ''); // clear;
            }
        }
        header('Location:'.surl($this->mod));
        die();
    }
    // (本地)DelUser测试: xxx.php/login-locrs-locdemo
    function locduAct(){
        if(!basEnv::isLocal()){
            die('Error-Local!');
        }
        $uname = $this->view;
        usrMember::delUser($uname, 0);
        usrMember::delUser($uname, 2);
        header('Location:'.surl($this->mod));
        die();
    }
    // (本地)登录测试: xxx.php/{mod}-locin-locdemo, /{mod}-locin-wechat1?mode=wechat
    function locinAct(){
        if(!basEnv::isLocal()){
            die('Error-Local!');
        }
        $uname = $this->view;
        $udemo = read('udemo', 'sy');
        if(empty($udemo[$uname])){ 
            die('Error-User!');
        }else{
            $row = $this->rlog + $udemo[$uname];
            $row['mext'] = '';
            $mode = req('mode', 'locin');
            if($mode!='locin'){ $this->rlog['utype'] = $row['utype'] = $mode; }
            $this->saveLogin($row, $mode);
        } //dump($row);
        header('Location:'.surl($this->mod));
        die();
    }
    // 登出用户: xxx.php/login-logout[-dir]
    function logoutAct(){
        $cval = comCookie::oset($this->ckey, '');
        db()->table('active_login')->where("ckey='{$this->ckey}'")->delete();
        $vars = ['errno'=>0, 'errmsg'=>'登出OK'];
        if($this->ucfg['view']=='dir'){
            header('Location:'.surl($this->mod));
            die();
        }else{
            vopApi::view($vars);
        }
    }
    // 判断是否登录成功 ??? 
    function checkAct(){
        vopApi::view($this->re['vars']);
    }

    /*
        ### 多端登录:wechat #######################################################
    */

    // 微信授权：统一先跳转到此地址
    function wecdirAct(){
        $state = req('state'); $stmp = explode('^',$state);
        if(!empty($this->uinfo)){ // 已经登录
            if($stmp[0]=='scan' && !empty($stmp[1])){
                $urow = $this->uinfo;
                $urow['ckey'] = $stmp[1];
                $this->saveLogin($urow, 'wechat'); 
            }
            $this->re['vars']['errtip'] = '登录成功';
            $this->re['vars']['errmsg'] = '已登录，请刷新网页';
            $this->re['newtpl'] = 'home/info';
            return $this->re;
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
        $oauth = new wmpOauth($wecfg);
        $code = req('code');
        $state = req('state'); $stmp = explode('^',$state);
        if($code){
            $acc = $oauth->getACToken($code); //dump($acc);
            $user = $oauth->getUserInfo($acc['result']['access_token'], $acc['result']['openid']);
            if(!empty($user['errcode'])){ // {"errcode":40003,"errmsg":" invalid openid "}
                $vars = ['errno'=>$user['errcode'], 'errmsg'=>$user['errcode']];
                $this->re['vars'] = $vars + $this->re['vars'];
            }else{
                $ext = "sex={$user['sex']}".(empty($user['unionid']) ? '' : "\nunionid={$user['unionid']}");
                $utmp = ['pptuid'=>$user['openid'], 'mname'=>$user['nickname'], 'mpic'=>$user['headimgurl'], 'mext'=>$ext];
                $urow = $utmp + $this->rlog;
                $this->saveLogin($urow, 'wechat');
                if($stmp[0]=='scan' && !empty($stmp[1])){
                    $urow['ckey'] = $stmp[1];
                    $this->saveLogin($urow, 'wechat');
                    $this->re['vars']['errtip'] = '登录成功';
                    $this->re['vars']['errmsg'] = '已登录，请刷新网页';
                    $this->re['newtpl'] = 'home/info';
                    return $this->re;
                }else{
                    header('Location:'.surl($this->mod));
                }
            }
        }else{
            $vars = ['errno'=>'Empty `code`', 'errmsg'=>'缺少code参数'];
            $this->re['vars'] = $vars + $this->re['vars'];
        }
        return $this->re;
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
            vopApi::view($vars);
        }
        $this->re['vars']['uimod'] = $uimod = $this->getUser($row, 'idpwd', 1);
        if(empty($row['upass']) || empty($uimod['show'])){
            $vars = ['errno'=>'Error-`userid-password(1)`', 'errmsg'=>'账密错误1！'];
            vopApi::view($vars);
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
        vopApi::view($vars);
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
        vopApi::view($vars);
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
        vopApi::view($vars);
    }

    /*
        ### 多端登录:wework #######################################################
    */

    // 企业微信授权：统一先跳转到此地址
    function wewdirAct(){
        $state = req('state'); $stmp = explode('^',$state);
        if(!empty($this->uinfo)){ // 已经登录
            if($stmp[0]=='scan' && !empty($stmp[1])){
                $urow = $this->uinfo;
                $urow['ckey'] = $stmp[1];
                $this->saveLogin($urow, 'wework'); 
            }
            $this->re['vars']['errtip'] = '登录成功';
            $this->re['vars']['errmsg'] = '已登录，请刷新网页';
            $this->re['newtpl'] = 'home/info';
            return $this->re;
        }else{ // 未登录,跳转去授权
            $reurl = surl($this->mod."-wework", '', 1);
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
            include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
            $agentId || $agentId = 'AppCS';
            $CorpId = read('wework.CorpId', 'ex');
            $api = new \CorpAPI($CorpId, $agentId);
            try {
                $user = $api->GetUserInfoByCode($code, 1);
                $ext = "gender={$user['gender']}".(empty($user['mobile']) ? '' : "\nmobile={$user['mobile']}");
                $utmp = ['pptuid'=>$user['userid'], 'mname'=>$user['name'], 'mpic'=>$user['avatar'], 'mext'=>$ext];
                $urow = $utmp + $this->rlog;
                $this->saveLogin($urow, 'wework');
                if($stmp[0]=='scan' && !empty($stmp[1])){
                    $urow['ckey'] = $stmp[1];
                    $this->saveLogin($urow, 'wework');
                    $this->re['vars']['errtip'] = '登录成功';
                    $this->re['vars']['errmsg'] = '已登录，请刷新网页';
                    $this->re['newtpl'] = 'home/info';
                    return $this->re;
                }else{
                    header('Location:'.surl($this->mod));
                }
            } catch (Exception $e) {
                $vars = ['errno'=>'errNowUser', 'errmsg'=>$e->getMessage()];
                $this->re['vars'] = $vars + $this->re['vars'];
            }
        }else{
            $vars = ['errno'=>'Empty `code`', 'errmsg'=>'缺少code参数'];
            $this->re['vars'] = $vars + $this->re['vars'];
        }
        return $this->re;
    }

    /*
        ### bindAct #######################################################
    */

    function bindBase($cfg=[]){
        $row['mode'] = $mode = req('mode'); // 登录模式/idpwd
        $row['umod'] = $umod = req('umod'); // 模型
        if(!empty($this->uinfo) && $this->uinfo['umod']=='(bind)'){
            $clog = ['umod'=>$umod];
            db()->table('active_login')->data($clog)->where("ckey='{$this->ckey}'")->update();
        }
        $row['uname'] = req('uname');
        $row['passwd'] = req('passwd');
        $row['pptuid'] = req('pptuid');
        $row['mname'] = req('mname');
        $udefs = read('udefs','sy');
        $cfgs = isset($udefs[$mode]) ? $udefs[$mode] : ['grade'=>'','show'=>'0'];
        $cfgs = $cfg + $cfgs;
        return $this->addUser($row, $mode, $cfgs, $row['passwd']); // ($row, $mode, $cfgs, $upass='')
    }

    // bindAct:绑定操作(要重写)
    function bindAct(){
        if(!empty($this->uinfo)){
            $res = $this->bindBase();
            #comCookie::oset('svmod', '');
            //comCookie::oset('jpmkv', '');
            comCookie::oset('bdkey', '');
            return $res;
        }
        vopApi::view($this->re['vars']);
    }

    /*
        ### init/check #######################################################
    */

    function initBase(){
        global $_cbase; $run = $_cbase['run'];
        if($_cbase['tpl']['vdir']=='base'){ die('Error-Url!'); }
        $this->mod = $this->ucfg['mod']; $this->key = $this->ucfg['key']; $this->view = $this->ucfg['view']; 
        $this->ckey = comSession::getCook('msin_sessid'); // getSess
        $this->re['vars'] = ['errno'=>0, 'errmsg'=>'未登录', 'uflag'=>$this->uflag]; 
        $this->rlog = ['ckey'=>$this->ckey, 'utype'=>$this->key, 'atime'=>$run['stamp'], 'aip'=>$run['userip']];
        if(in_array($this->key,['locin','logout'])){ return; } 
        // uinfo
        //$cval = comCookie::oget($this->ckey); $cval = comConvert::sysRevert($cval, 1, 'ck'); 
        $whr = "ckey='{$this->ckey}' AND atime>='".($run['stamp']-$this->exp_day*86400)."'"; 
        $row = db()->table('active_login')->where($whr)->find(); //dump($row);
        if(!empty($row['mext'])){
            $row['mexa'] = basElm::text2arr($row['mext']);
        } 
        $udemo = read('udemo', 'sy');
        $this->re['vars']['null'] = $udemo['null']; // 为空的用户信息
        if(empty($row)){ 
            $this->re['vars']['uinfo'] = $this->uinfo = []; 
        }else{
            if($run['stamp']-$row['atime']>3600){
                $row['atime'] = $data['atime'] = $run['stamp'];
                db()->table('active_login')->data($data)->where("ckey='{$this->ckey}'")->update();
            }
            $this->re['vars']['uinfo'] = $this->uinfo = $row;
            $this->re['vars']['utype'] = $row['utype'];
            $this->bindCheck($row); 
            $this->re['vars']['uflag'] = $row['umod']; 
            $this->re['vars']['last_upd'] = $row['atime']; // 上次登录
        }
        // env
        $this->re['vars']['urlBase'] = surl('base:0', '', 1);
        $this->re['vars']['urlNow'] = surl('0', '', 1);
        $this->re['vars']['isMobile'] = basEnv::isMobile();
        $this->re['vars']['isWexin'] = basEnv::isWeixin();
        $this->re['vars']['isWework'] = basEnv::isWework();
        $this->re['newtpl'] = $this->mod.'/mhome'; // 'tplnull'=>1 设置不需要模板
    }
    // bind-check:绑定检查(基础)
    function bindCheckBase(&$row){
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
            $groups = read('groups');
            $umarr = explode(',', $udefs[$mode]['umtab']);
            $umtab = [];
            foreach ($umarr as $gk){
                $umtab[$gk] = isset($groups[$gk]) ? $groups[$gk]['title'] : "【{$gk}】";
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

    // 保存登录信息
    function saveLogin($row, $mode=''){ 
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
                $this->addUser($row, $mode, $cfgs, '');
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
                $row['umod'] = $uimod['umods'];
                // update?
            }
        }
        // save-login
        $tmp = db()->table('active_login')->data($row)->replace(0); 
    }
    static function addUser($row, $mode, $cfgs, $upass=''){
        $umod = $row['umod']; 
        $_groups = glbConfig::read('groups');
        if(!isset($_groups[$umod]) || $_groups[$umod]['pid']!='users'){
            $re = ['errno'=>"User-Model-Error!",'errmsg'=>"model[$umod]Error!"];
            vopApi::view($re);
        }
        $grade = empty($row['grade']) ? $cfgs['grade'] : $row['grade'];
        $show = empty($row['show']) ? $cfgs['show'] : $row['show'];
        if(empty($row['uid'])){
            $tmp = usrMember::addUid();
            $uid = $tmp['uid']; $uno = $tmp['uno'];
        }else{
            $uid = $row['uid']; $uno = 1;
        }
        if(empty($row['uname']) &&$mode && !in_array($mode,['locin','idpwd'])){
            $row['uname'] = usrMember::addUname($row['pptuid'], $umod);
        }
        //
        $dbpass = $upass ? comConvert::sysPass($row['uname'],$upass,$umod) : '(reset)';
        $acc = array('uid'=>$uid,'uno'=>$uno,'uname'=>$row['uname'],'upass'=>$dbpass,'umods'=>$umod,); 
        $mcfg = read("$umod.f"); $dex = basSql::logData();
        db()->table('users_uacc')->data($acc+$dex)->insert(); 
        $umd = array('uid'=>$uid,'uname'=>$row['uname'],'grade'=>$grade,'mname'=>$row['mname'],'show'=>$show,);
        foreach($mcfg as $fk=>$fr) {
            if(!isset($umd[$fk]) && $fr['dbtype']=='varchar'){
                $umd[$fk] = req($fk); // umd 默认字段
            }
        } 
        db()->table("users_$umod")->data($umd+$dex)->insert();
        // pptuid
        if($mode && $mode!='idpwd'){
            usrMember::bindUser($row['uname'], $mode, $row['pptuid']);
        }
        return $acc + $umd + $row;
    }

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

    /*
        ### 测试区域 #######################################################
    */

    function qrUrls($urmkv, $scope='', $exmkv='0'){ // $reurl, $scope, $state
        if(!strpos($urmkv,'/')) $urmkv = $this->re['vars']['urlNow']."/$urmkv";
        $scope = $scope ? $scope : req('scope', 'snsapi_userinfo'); // snsapi_base
        $exmkv = $exmkv ? $exmkv : req('exmkv', '0.0.0'); // like:vmod^mkv^exstr
        $re['qrUrl']   = "$urmkv?scope=$scope&state=scan^{$this->ckey}^$exmkv&_v=".time();
        $re['linkUrl'] = str_replace('=scan^', '=dir^', $re['qrUrl']);
        $re['scanUrl'] = PATH_BASE."?ajax-vimg&mod=qrShow&data=".str_replace(['?','&'],['%3F','%26'],$re['qrUrl']);
        return $re;
    }

    function tb1Act(){
        die('tb1Act');
    }


}

/*

    // untie / pptuid
    function untieAct(){
        // db()->table('active_login')->where("ckey='{$this->ckey}'")->delete();
        vopApi::view($this->re['vars']);
    }
    function infoAct(){
        vopApi::view($this->re['vars']);
    }

*/
