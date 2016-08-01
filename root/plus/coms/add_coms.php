<?php
require(dirname(__FILE__).'/_cfgcom.php'); 

$adeel = dirname(__FILE__)."/{$mod}.php"; 
if(file_exists($adeel)){ die("It must deel with [{$mod}.php]." ); }
$aform = dirname(__FILE__)."/{$mod}_form.php";
$asave = dirname(__FILE__)."/{$mod}_save.php";
 
if(!empty($bsend)){
	
	$re2 = safComm::formCAll('fmcomadd');
	if(!empty($re2[0])){ 
		dopCheck::headComm();
		basMsg::show("认证码错误，增加失败！",'die');
	}
	
	if(file_exists($asave)){
		require($asave);
	}else{
		$dop->svPrep(); 
		$dop->svAKey();
		$dop->svPKey('add');
		$db->table($dop->tbid)->data($dop->fmv)->insert(); 
		dopCheck::headComm();
		basMsg::show("增加{$_groups[$mod]['title']}成功！",'prClose');
	}
	
}else{
	
	if(file_exists($aform)){
		require($aform);
	}else{
		dopCheck::headComm();
		$dop->fmo = $fmo = array();
		glbHtml::fmt_head('fmcomadd',"$aurl[1]",'tbdata');
		fldView::lists($mod,$fmo);
		$dop->fmPKey(1,0,1);
		$dop->fmProp(0,1);
		glbHtml::fmae_row('认证码',"<script>fsInit('fmcomadd');</script>");
		glbHtml::fmae_send('bsend','提交',0,'tr');
	}
}


