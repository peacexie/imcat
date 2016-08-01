<?php
$_mod = 'gbook';
require(dirname(__FILE__).'/_cfgcom.php'); 

if(!empty($bsend)){
	
	$re2 = safComm::formCAll('fmcaddgbk');
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
	glbHtml::fmt_head('fmcaddgbk',"$aurl[1]",'tbdata');
		$vals = array();
		$skip = array('0','reply');
		foreach($mfields as $k=>$v){ 
			if(!in_array($k,$skip)){
				$item = fldView::fitem($k,$v,$vals);
				$item = fldView::fnext($mfields,$k,$vals,$item,$skip);
				glbHtml::fmae_row($v['title'],$item);
			}
		}
	$dop->fmPKey(1,0,1);
	$dop->fmProp(0,1);
	glbHtml::fmae_row('认证码',"<script>fsInit('fmcaddgbk');</script>");
	glbHtml::fmae_send('bsend','提交',0,'tr');

}

