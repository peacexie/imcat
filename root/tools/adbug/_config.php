<?php
// 辅助调试工具，请用于合法用途，使用后请删除本文件或移动到网站目录之外！
//$_cbase['tpl']['tpl_dir'] = 'adm'; 
//$_cbase['run']['outer'] = 1;
//$_cbase['skip']['_sess_'] = true;
if(!session_id()) session_start();

include(dirname(dirname(dirname(__FILE__))).'/run/_paths.php');
include(DIR_CODE.'/cfgs/boot/cfg_adbug.php');
$sess_id = 'pmSessid_'.preg_replace("/[^\w]/", '', @$_cbase['safe']['safil']);

$qstr = $_SERVER['QUERY_STRING'];
$qstr || $qstr = 'binfo'; 
$_selfname = $_SERVER['PHP_SELF']; 
$allowb = array('binfo','phpinfo1','cookie','login','dologin','iframe','frame','fset');
$allowc = array('binfo');

if(strstr($_selfname,'start.php')){ 
	;//
}elseif(strstr($_selfname,'binfo.php') && in_array($qstr,$allowb)){ 
	;//
}elseif(strstr($_selfname,'check.php') && in_array($qstr,$allowc)){  
	;//
}else{
	bootPerm_ys('pstools','','<p><a href="binfo.php?login">login</a></p>');
	//else { $_isOut = 1; @include(dirname(__FILE__).'/devRun.php'); }
} 

function tadbugNave($path=''){
	if(empty($path)){
	echo "<tr class='tc'>
      <td class='tip'><a href='start.php'>&lt;&lt;首页</a></td>
      <th colspan='2'>调试/工具</th>
      <td class='tip'><a href='../setup/'>安装&gt;&gt;</a></td>";
	}
    echo "</tr><tr class='tc'>
      <td width='25%'><a href='binfo.php'>基础环境</a></td>
      <td width='25%'><a href='check.php'>环境检测</a></td>
	  <td width='25%'><a href='cscan.php'>Check/Scan</a></td>
      <td width='25%'><a href='reset.php'>系统重置</a></td>
    </tr>";
}

function fchkFuncs($name) { return function_exists($name)?FLAGYES.' - Support(支持) ':FLAGNO.' --- (X) ';}

function dfmtRemote($str,$method=''){
	$rem_cset = empty($_GET['rem_cset']) ? '' : $_GET['rem_cset'];
	if($rem_cset) $str = iconv($rem_cset,'utf-8',$str);
	$rem_show = empty($_GET['rem_show']) ? 'script,style' : $_GET['rem_show'];
	if(strstr($rem_show,'script')) $str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str);
	if(strstr($rem_show,'style')) $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str);
	if(strstr($rem_show,'tags')) $str = strip_tags($str);
	if(strstr($rem_show,'_null_')) {
		$str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str);
	}else{
		$str = nl2br($str);
		$str = htmlspecialchars($str); 
		$str = str_replace(array('&lt;br /&gt;','&amp;nbsp;'),array('<br />',' '),$str); 
	}
	return "<h1>$method</h1>\n$str";
}

