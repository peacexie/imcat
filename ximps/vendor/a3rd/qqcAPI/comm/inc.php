<?php

//include DIR_ROOT.'/cfgs/excfg/ex_a3rd.php';
die();
 
$_cfgs = array(
	'appid' => $_cfgs['qqconn']['appid'],
	'appkey' => $_cfgs['qqconn']['appkey'],
	'callback' => $_cfgs['qqconn']['callback'],
	'scope' => 'get_user_info',
	'errorReport' => true,
	'storageType' => 'file',
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'root',
	'database' => 'test',
); 
return $_cfgs;
