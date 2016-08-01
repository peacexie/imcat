<?php 
require(dirname(__FILE__).'/_cfgall.php');

if(!empty($mod) && !empty($kid)){

	$tab = $mod=='qarep' ? 'coms_qarep' : 'dext_faqs';
	$fid = $mod=='qarep' ? 'cid' : 'did'; 
	$info = $db->table($tab)->field('detail')->where("$fid='$kid'")->find(); 
	$detail = basStr::filForm(@$info['detail']);
	echo "<textarea cols='' rows='26' style='width:100%'>$detail</textarea>";

}

