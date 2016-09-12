<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)'); 

$view = basReq::val('view','uinfo'); //echo $view;
$bspw = basReq::val('bspw');
$mod = $user->uinfo['umods']; //$user->uperm['model'];
$tabid = "users_$mod";
$uid = $user->uinfo['uid'];

$title = $view=='uinfo' ? lang('admin.ui_uinfo') : lang('admin.ui_upass');
$msg = '';

if(!empty($bsend)){ 
	$db->table($tabid)->data(basReq::in($fm))->where("uid='$uid'")->update();
	$msg = ' &nbsp; '.lang('admin.ui_editok');	
}elseif(!empty($bspw)){
	$uname = $user->uinfo['uname'];
	$msg = dopUser::editPass($mod,$uname);
}
$fmo = $db->table($tabid)->where("uid='$uid'")->find(); 

// 个人资料 - 密码
$lnks = "<a href='?file=admin/uinfo&view=uinfo'>".lang('admin.ui_uinfo')."</a> | <a href='?file=admin/uinfo&view=passwd'>".lang('admin.ui_upass')."</a>";
glbHtml::tab_bar("$title $msg",$lnks,35);

if($view=='uinfo'){ //basDebug::varShow($user);

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	fldView::lists($mod,$fmo);
	glbHtml::fmae_send('bsend',lang('flow.dops_send'));
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='passwd'){
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	
	$paras = "type='password' value='' autocomplete='off' class='w240' maxlength='24' reg='str:6-24' tip='".lang('admin.ui_let624')."'";
	glbHtml::fmae_row(lang('admin.ui_oldpass'),"<input name='pwold' $paras/> ".lang('admin.ui_let624'));
	glbHtml::fmae_row(lang('admin.ui_newpass'),"<input name='pwnew' $paras/> ".lang('admin.ui_let624'));
	glbHtml::fmae_row(lang('admin.ui_again'),"<input name='pwrep' $paras/> ".lang('admin.ui_agtip'));
	
	glbHtml::fmae_send('bspw',lang('flow.dops_send'),'25');
	glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));

}

