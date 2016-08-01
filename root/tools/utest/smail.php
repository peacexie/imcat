<?php 
require(dirname(__FILE__).'/_config.php');  

$type = 'phpmailer'; //swiftmailer,phpmailer,basReq::val('type');
$s = 'v01-Mail:'.$type.' : 邮件 : '.date('Y-m-d H:i');
$c = '
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>Test邮件</title>
</head>
<body>
<h1>test 邮件</h1>
<div>Contents</div>
<div>sign</div>
</body>
</html>
'; 

if($type){
	$m = new extEmail($type);
	$re = $m->send('xpigeon@163.com',$s,$c,'fromName-测试-Test');
	echo "re=$re";
}

