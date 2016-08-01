<?php
define('RUN_JSHOW', 1);
$_cbase['skip']['.none.'] = true;
require(dirname(__FILE__).'/_config.php');
vopTpls::set(basReq::val('tpldir'));
glbHtml::head();

$q = $_SERVER['QUERY_STRING'];
parse_str($q,$a);
$_cbase['run']['mkv'] = basReq::val('mkv');

$sfie = array(); $scnt = array();
$stag = '';  $sadv = ''; 
foreach($a as $k=>$v){
	//$k = basStr::filKey($k,"-._@:");
	//echo "\n<hr>$k:$v<hr>\n\n";
	if(strstr($k,'jsid_tags_')){ 
		$re = tagCache::jsTag($k,$_cbase['run']['mkv'],$v);
		$stag .= "jtagRep('$k','$re');\n"; 
	}elseif(strstr($k,'jsid_advs_')){
		$sadv .= "jsElm.jeID('$k').innerHTML='".tagCache::showAdv($v)."';\n";
	}elseif(strstr($k,'jsid_field_')){ 
		$sfie[substr($k,11)] = $v;	
	}elseif(strstr($k,'jsid_count_')){
		$scnt[substr($k,11)] = $v;
	}else{ // _cbase.run.mkv,_cbase.run.csname,_rnd
		//;
	}
}

$rfie = vopCell::jsFields($sfie);
$rcnt = vopCell::jsCounts($scnt);

echo $rfie;
echo $rcnt;
echo $stag;
echo $sadv;

$jsrun = ''; //静态控制

/*
注意格式:<i id="jsid_field_news:2013-ff-dcyq:etime">0</i>中
要用[:]不能用[.] 测试代码：
$q = 'aa.bb=1&aa:bb=2'; //&path/file=3
parse_str($q,$b); print_r($b);
Array(
    [aa_bb] => 1
    [aa:bb] => 2
) */

?>