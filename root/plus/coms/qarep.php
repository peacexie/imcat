<?php
$_mod = 'qarep';
require(dirname(__FILE__).'/_cfgcom.php'); 

$pinfo = dopFunc::getMinfo('faqs',$pid); //dump($pinfo);
if($pinfo['bugst']=='close'){
	basMsg::show("已关闭回复！请联系管理员.",'die');
}

if(!empty($bsend)){
	
	$re2 = safComm::formCAll('fmqarep');
	if(!empty($re2[0])){ 
		dopCheck::headComm();
		basMsg::show("认证码错误，增加失败！",'die');
	}

	$dop->svPrep(); 
	$dop->svAKey();
	$dop->svPKey('add');
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	dopCheck::headComm();
	basMsg::show("增加{$_groups[$mod]['title']}成功！",'prClose');
	
}else{

	dopCheck::headComm();
	$dop->fmo = $fmo = array();
	glbHtml::fmt_head('fmqarep',"$aurl[1]",'tbdata');
	fldView::lists($mod,$fmo);
	$dop->fmPKey(1,0,1);
	$dop->fmProp(0,1);
	glbHtml::fmae_row('认证码',"<script>fsInit('fmqarep');</script>");
	glbHtml::fmae_send('bsend','提交',0,'tr');
}


