<?php 
require(dirname(__FILE__).'/_config.php');  

// This script is only accessible from: <br>localhost (127.0.0.1, ::1)
// for your dir debug ... 
$list = comFiles::listDir(DIR_PROJ."/server"); 
$svlink = '';
foreach ($list['dir'] as $dir=>$ctime) {
	$svlink .= "<a href='?dir=$dir&part=server'>$dir</a> #";
}
echo "<meta charset='utf-8'>\n<style type='text/css'>p{margin:20px;line-height:150%;}</style>\n";
echo "<p>
	<a href='?dir=exdiy'>exdiy:扩展功能</a>
	# <a href='?dir=utest'>utest:测试代码</a>
	# <a href='?dir=vends'>vends:第三方</a> 
	# <a href='?dir=08data&part=08data'>08data:导数据</a> 
	<br> $svlink
</p>\n";

$part = basReq::val('part','(root)');
$dir = basReq::val('dir','utest');
$pcfg = array(
	'(root)' => array(dirname(dirname(__FILE__)), '..'),
	'server' => array(DIR_PROJ."/server", '../../../server'),
	'08data' => array(dirname(DIR_PROJ), '../../../..'),
);
$dbase = $pcfg[$part][0]; 

function listDir($dbase,$dir='',$part=''){
	global $pcfg;
	$list = comFiles::listDir("$dbase/$dir"); 
	if(empty($list['file'])) die('(null)');
	foreach ($list['file'] as $file => $val) {
		if(!strpos($file,'.php')) continue;
		if(in_array($file,array('index.php','_config.php'))) continue;
		$b2 = $pcfg[$part][1]; 
		echo " --- <a href='{$b2}/$dir/$file' target='_blank'>$file</a><br>\n";
	}
}

/*
$ex = new Exception();
$ex->__construct();
//error_get_last(); 
$trace = $ex->getTrace();
dump($trace);
//
@$s = 222/0;
//new glbError(); 
*/

echo "\n<p>";
if(!strstr($dir,'./')) listDir($dbase,$dir,$part);
echo "</p>\n";

