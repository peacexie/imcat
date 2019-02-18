<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$_locfp = '/ximp/utabs/spword.imp_txt';
if(!is_file(DIR_STATIC.$_locfp)){
    $_dowurl = 'https://github.com/peacexie/imcat/raw/patches/excode/imp_files.rar';
    die("Please down the file `$_locfp` <br>\nfrom `$_dowurl`<br>\n");
}

include_once(DIR_STATIC.'/ximp/class/Splitword.cls_php'); 

/* Demo *************************************************************************************
	$str = ''; //��֤gbk/gb2312���룬��������ת��������뻹ԭ��
	$a_split = new SplitWord();
	$str = preg_replace("/&#?\\w+;/", ',', strip_tags($str));
	$str = $a_split->GetIndexText($a_split->SplitRMM($str),100);
************************************************************************************* ******/

class extSpword extends \Splitword{
	
	static function main($str,$len=-1,$cset='utf-8'){
		if(!$str) return '';
		static $a_split;
		if(empty($a_aplit)){
			$a_split = new self();
		}
		$len = $len>0 ? $len*($cset=='utf-8' ? 3 : 2) : $len;
		$str = preg_replace("/&#?\\w+;/", ',', strip_tags($str));
		if(!in_array($cset,array('gb2312','gbk'))) $str = comConvert::autoCSet($str,$cset,'gbk');
		$str = $a_split->GetIndexText($a_split->SplitRMM($str),$len);
		if(!in_array($cset,array('gb2312','gbk'))) $str = comConvert::autoCSet($str,'gbk',$cset);
		return str_replace(' ',',',$str);
	}
	
}
