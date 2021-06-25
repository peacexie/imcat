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
use imcat\basDebug;
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

// 微信相关登录
trait uioTrait{

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
            return api::v($vars);
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
        if(empty($wecfg['enable'])){
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
            $CorpId = $wecfg['CorpId']; $agentId = 'AppAB';
            if(empty($wecfg['isOpen'])){
                die('请配置:[ex_wework.php]:isOpen=1');
            }
            $api = new \CorpAPI($CorpId, $agentId);
            try {
                $ures = $api->GetUserInfoByCode($code, 1); 
                if(empty($ures['UserId'])){ 
                    $vars = ['errno'=>$ures['errcode'], 'errmsg'=>$ures['errmsg']];
                    $vars = $this->uioVInfo($vars);
                    if(!empty($wecfg['ucfg']['debug'])){ 
                        basDebug::bugLogs('weworkAct', $ures, 'u-err', 'db');
                    }
                    return api::v($vars);
                }else{ 
                    $user = $api->GetUserById($ures['UserId']); 
                    if(!empty($wecfg['ucfg']['debug'])){ 
                        basDebug::bugLogs('weworkAct', [$ures,$user], 'u-ok', 'db');
                    }
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
                    return api::v($urow, 'dir', surl($this->mod));
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


}

/*

*/
