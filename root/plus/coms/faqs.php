<?php
$_mod = basename(__FILE__,'.php'); 
require(dirname(__FILE__).'/_cfgdoc.php'); 

if(!empty($bsend)){
	
	$re2 = safComm::formCAll('fmdocfaqs');
	if(!empty($re2[0])){ 
		dopCheck::headComm();
		basMsg::show("认证码错误，增加失败！",'die');
	}

	$dop->svPrep(); 
	$dop->svAKey();
	//$dop->svPKey('add');
	$db->table($dop->tbid)->data($dop->fmv)->insert(); 
	dopCheck::headComm();
	basMsg::show("增加{$_groups[$mod]['title']}成功！",'prClose');
	
}else{
	
	dopCheck::headComm();
	$dop->fmo = $fmo = array();
	glbHtml::fmt_head('fmdocfaqs',"$aurl[1]",'tbdata'); 
	glbHtml::fmae_row('所在栏目',$dop->fmType('catid').'');
	glbHtml::fmae_row('显示',$dop->fmShow(),1);
	$vals = array();
	$skip = array('0','mpic','hinfo','jump','click','author','bugid','bugst');
	$mfields['detail']['fmsize'] = '480x18';
	foreach($mfields as $k=>$v){ 
		if(!in_array($k,$skip)){
			$item = fldView::fitem($k,$v,$vals);
			$item = fldView::fnext($mfields,$k,$vals,$item,$skip);
			glbHtml::fmae_row($v['title'],$item);
		}
	}
	$dop->fmAE3(1);
	glbHtml::fmae_row('认证码',"<script>fsInit('fmdocfaqs');</script>");
	glbHtml::fmae_send('bsend','提交',0,'tr');

}

/*

*/