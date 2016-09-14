<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');
require(DIR_CODE.'/tpls/umc/b_func/tex_faqs.php');

$tabid = 'bext_paras';
$view = empty($view) ? 'uset' : $view;

$linka = admPFunc::fileNav($view,'faqs'); $gname = admPFunc::fileNavTitle($view,'faqs');
glbHtml::tab_bar("{$gname}","$linka",30);

if($act){
	if($act=='_allt'){
		tex_faqs::statTypes('upd');	
	}else{
		tex_faqs::statTags('upd');
	}
	
}

glbHtml::fmt_head('fmlist',"?",'tblist');
$mcfg = glbConfig::read('faqs'); 
$ucfg = array('_allt'=>lang('flow.qa_alltype'),'_tags'=>lang('flow.qa_alltag'),);
$cfgs = tex_faqs::statTypes();
foreach($cfgs as $key=>$v){
	$title = isset($ucfg[$key]) ? $ucfg[$key] : $mcfg['i'][$key]['title'];
	$link = isset($ucfg[$key]) ? " &nbsp; -=> <a href='?file=$file&view=$view&act=$key'>".lang('flow.qa_reset')."</a>" : '';
	echo "\n<tr><td class='tc w150'>{$title}: </td>\n<td>$cfgs[$key] [$key] $link </td></tr>\n";
}
glbHtml::fmt_end(array("mod|$mod"));

if($view=='uset'){
	
}elseif($view=='list'){
	
}elseif($view=='form'){
	
}

?>