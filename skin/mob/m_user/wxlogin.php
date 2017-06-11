<?php
//初始化
(!defined('RUN_INIT')) && die('No Init'); 
$db = db(); 

// === 获取配置
$kid = req('kid','admin');
$wecfg = wysBasic::getConfig($kid); 
$scene = req('scene','');

// === 验证code，codecheck
$code = req('code',''); 
$state = req('state',''); 
$openid = req('openid',''); //'oyDK8vjjcn2cFbxMLaMBhKEsYbCk'; 
$mname = req('mname',''); 
$codecheck = req('codecheck','');
$authkey = $_cbase['safe']['api']; 
$sflag = req('sflag',''); 
//basMsg::show("$kid,$code,$openid",'die');

//---Test*-/ 
// code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
if($code && empty($codecheck)){
    $oauth = new wmpOauth($wecfg); 
    $actoken = $oauth->getACToken($code); 
    if(!empty($actoken['errmsg'])){
        basMsg::show("[{$actoken['errcode']}]{$actoken['errmsg']}",'die');
    } 
    $openid = $actoken['result']['openid'];
    $codecheck = md5($code.$openid.$authkey); 
// 提交后用$codecheck认证信息
}elseif($code && !empty($codecheck) && !empty($openid)){
    // 防止恶意操作
    $codecfrom = md5($code.$openid.$authkey);
    if($codecfrom!==$codecheck){
        basMsg::show("验证失败，可能是重复提交！",'die');
    }
    // 防止重复操作(恶意)
    $row = $db->table('users_uppt')->where("pptuid='{$openid}' AND pptmod='weixin'")->find();
    if($row){ 
        basMsg::show("此微信号已经绑定会员！请直接登录！",'die');
    }
}else{ //避免后患，停止掉！(调试可屏蔽)
    basMsg::show("Error1: code=$code, state=$state, openid=$openid, codecheck=$codecheck",'die');
}
if($scene && in_array($state,array('binding','dologin','dogetpw','mbindDone','mbindChange','mbindExit'))){
    $timeNmin = $_cbase['run']['stamp']-(5*60*2); //10分钟 //saveState
    if(!$row = $db->table('wex_qrcode')->where("sid='$scene' AND sflag='$sflag' AND atime>'$timeNmin'")->find()){
        basMsg::show("超时了，请重新扫描！",'die');
    }
} 

// === 执行逻辑事务
// 扫描过来 : 带 scene,openid, 注意处理scene
// 扫描过来 : dogetpw
if($state=='dogetpw'){
    $msg = wysUser::resetPwd($openid,$scene,$mname);
    basMsg::show($msg,'die');
// 扫描过来 : dologin
}elseif($state=='dologin'){
    wysUser::setScanLogin($scene,$openid,$mname);
    $msg = "已经自动登录……，请留意屏幕跳转。";
    basMsg::show($msg,'die');

// 登录会员:扫描过来 : 点现在绑定
}elseif($state=='mbindDone'){ 
    $re = usrMember::bindUser($mname, 'weixin', $openid);
    $db->table('wex_qrcode')->data(array('atime'=>'0'))->where("sid='$scene'")->update();
    $msg = "已经绑定完成，请刷新或关闭窗口。";
    basMsg::show($msg,'die');
// 登录会员:扫描过来 : 点更换绑定
}elseif($state=='mbindChange'){ 
    $db->table('users_uppt')->data(array('uname'=>$mname))->where(array('pptuid'=>$openid,'pptmod'=>'weixin'))->update(); 
    $db->table('wex_qrcode')->data(array('atime'=>'0'))->where("sid='$scene'")->update();
    $msg = "已经更换绑定，请刷新或关闭窗口。";
    basMsg::show($msg,'die');
// 登录会员:扫描过来 : 点解除绑定
}elseif($state=='mbindExit'){ 
    $db->table('users_uppt')->where(array('pptuid'=>$openid,'pptmod'=>'weixin'))->delete(); 
    $db->table('wex_qrcode')->data(array('atime'=>'0'))->where("sid='$scene'")->update();
    $msg = "已经解除绑定，请刷新或关闭窗口。";
    basMsg::show($msg,'die');

// 扫描过来 : binding 
}elseif($state=='binding'){ 
    ;//这里为空,直接进入模版
// (点菜单,扫码,点链接过来) : 执行绑定
}elseif($state=='dobind'){ 
    
    $actys = req('actys','');
    $state_old = req('state_old',''); 
    $username = req('username',''); 
    $password = req('password',''); 
    $umod = req('umod',''); 
    //1. 快速新增帐号
    if($actys=='add'){  
        $re = wysUser::addUser($openid,$username,$password,$umod); 
        $msg = $re['msg']; //('mid'=>$mid, 'autocheck'=>$autocheck, 'msg'=>$msg);
        if($re['uid']){
            $msg .= "<br>您的登录帐号为：{$username}。";
            $msg .= "<br>您的登录密码为：{$password}。";
        } 
        $isok = $re['autocheck']==1 ? 1 : 0;
    //2. 绑定已有帐号
    }else{
        $re = wysUser::bindUser($openid,$username,$password);
        $msg = $re['msg']; //return array('res'=>$res, 'msg'=>$msg);
        $isok = $re['res'];
    }
    //3. 失败检查
    if(!$isok){
        basMsg::show("$msg, 请重新操作！",'die');
    }
    $url = wysBasic::fmtUrl(surl('user'));
    //4. 菜单过来,进入会员中心页
    if($state_old=='mlogin'){ 
        usrBase::setLogin('m',$username);
        header("Location:$url");
    //5. 扫码过来,更新数据库+提示信息+进入会员中心链接
    }else{ 
        wysUser::setScanLogin($scene,$openid,$username); // 重置扫码
        $msg .= "<br>电脑版本已经自动登录……，请留意屏幕跳转。";
        $msg .= "<br>点击进入:手机版<a href='$url'>用户中心</a> <br> ";
        basMsg::show($msg,'die');
    }
// 菜单过来 : 登录
}elseif($state=='mlogin'){
    $row = $db->table('users_uppt')->where("pptuid='{$openid}' AND pptmod='weixin'")->find();
    if($row){ //绑定了直接登录
        usrBase::setLogin('m',$row['uname']); 
        $url = wysBasic::fmtUrl(surl('user'));
        header("Location:$url");
    }else{ 
        //未绑定,进入模版
    }
// 积分签到
}elseif($state=='mjifen'){ //
    //wysUser::chkLogin($openid,$state);
    //积分+提示
    usrBase::setLogin('m',$mname);
    $url = wysBasic::fmtUrl(surl('user'));
    header("Location:$url");
}else{ //避免后患，停止掉！(调试可另处理)
    basMsg::show("Error2: code=$code, state=$state, openid=$openid, codecheck=$codecheck",'die');
}
//---Test*/

// === 获取用户信息
/*
 - 因为是管理多个公众号，用主站的公众号(snsapi_base)授权，获取openid
 - 但是(snsapi_base)授权不能获取用户详细信息，所以用各自的公众号获取用户信息
*/
$weuser = new wysUser($wecfg); 
$uinfo = $weuser->getUserInfo($openid); 
$mnamed = usrExtra::fmtUserName($uinfo);
$mpassd = substr($mnamed,0,3).'_'.basKeyid::kidRand(24,5); 


// sysvals:
$state_old = $state;
$state = 'dobind'; 
$umods = array();
foreach($_groups as $k=>$v){ if($v['pid']=='users') $umods[$k]=$v['title']; }
$karr = array('kid','scene','code','openid','codecheck','uinfo','mnamed','mpassd','state_old','state');

