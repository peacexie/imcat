<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)'); 

$view = basReq::val('view','uinfo'); //echo $view;
$bspw = basReq::val('bspw');
$mod = $user->uinfo['umods']; //$user->uperm['model'];
$tabid = "users_$mod";
$uid = $user->uinfo['uid'];

$title = $view=='uinfo' ? '个人资料' : '个人密码';
$msg = '';

if(!empty($bsend)){ 
	$db->table($tabid)->data(basReq::in($fm))->where("uid='$uid'")->update();
	$msg = ' &nbsp; 修改成功！';	
}elseif(!empty($bspw)){
	$uname = $user->uinfo['uname'];
	$msg = dopUser::editPass($mod,$uname);
}
$fmo = $db->table($tabid)->where("uid='$uid'")->find(); 

// 个人资料 - 密码
$lnks = "<a href='?file=admin/uinfo&view=uinfo'>个人资料</a> | <a href='?file=admin/uinfo&view=passwd'>个人密码</a>";
glbHtml::tab_bar("$title $msg",$lnks,35);

if($view=='uinfo'){ //basDebug::varShow($user);

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	fldView::lists($mod,$fmo);
	glbHtml::fmae_send('bsend','提交');
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='passwd'){
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	
	$paras = "type='password' value='' autocomplete='off' class='w240' maxlength='24' reg='str:6-24' tip='6-24字符'";
	glbHtml::fmae_row('旧密码：',"<input name='pwold' $paras/> 6-24字符");
	glbHtml::fmae_row('新密码：',"<input name='pwnew' $paras/> 6-24字符");
	glbHtml::fmae_row('再一次：',"<input name='pwrep' $paras/> 再输一次新密码");
	
	glbHtml::fmae_send('bspw','提交','25');
	glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));

}

?>
