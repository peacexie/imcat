<?php 
//$aincfile = '/root/run/_init.php';
//$aincfile = '/cfgs/stinc/err404.php';

function autoInc_ys($file=''){
	if(empty($file)) return $file;
	$path = ''; // -- вт╤╞╪Л╡Бб╥╬╤
	// /html/model/collum1/collum2/collum2/yyyy/m/d/id/
	for($i=0;$i<12;$i++){
		$path = empty($path) ? dirname(__FILE__) : dirname($path);
		$full = "$path$file";
		if(file_exists($full)){
			return $full;	
			break;
		} 
	}
	return '';
}

if(!empty($aincfile) && $aincpath=autoInc_ys($aincfile)){
	include($aincpath);
}
//echo DIR_ROOT;
