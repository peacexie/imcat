<?php 
require(dirname(__FILE__).'/_config.php');  

$cls = basReq::val('cls');
$act = basReq::val('act');
$now = '-';

if($act){
	$_cbase['db']['db_class'] = $act;
	$now = "act=$act";
	$db = glbDBObj::dbObj();
}

if($cls){
	$now = "cls=$cls";
	$class = "db_$cls";
	require_once(DIR_CODE."/adpt/dbdrv/{$class}.php");
	require DIR_CODE.'/cfgs/boot/cfg_db.php';
	$db = new $class(); $db->connect($_cfgs); 	
}

$code = basReq::val('code','');

?>

Core: <a href="?act=mysqli">mysqli</a> | <a href="?act=mysql">mysql</a> | <a href="?act=pdox">pdox</a> # Now(<?php echo $now; ?>) <br>
Class: <a href="?cls=mysqli">mysqli</a> | <a href="?cls=mysql">mysql</a> | <a href="?cls=pdox">pdox</a> # <a href="?">Clear</a>

<?php
echo "\n<pre>\n";

if($act){

	$list = $db->table('base_model')->field('kid,title')->limit(3)->select(); 
	print_r($list); 
	$list = $db->table('base_model')->field('kid,title')->limit(3)->find(); 
	print_r($list); 
	$count = $db->table('base_model')->where("pid='groups'")->count();  
	print_r($count);
	echo "<hr>";
	print_r($db->fields('xtest_keyid_ys'));
	print_r($db->tables());
}

if($cls){
	
	/*
	$sql = "SELECT kid,title FROM base_model_ys WHERE pid='groups' LIMIT 3";
	$list = $db->query($sql);
	print_r($list); die('aa');
	*/
	
	$time = date('Y-m-d H:i:s');
	$re1 = $db->run("INSERT INTO xtest_keyid_ys(kid,kno,content,atime) VALUES ('".basKeyid::kidTemp(4)."','1','测试内容$time','".time()."')");
	echo "\nre1="; print_r($re1);
	echo "\nlastID="; print_r($db->lastID);
	echo "\n\n";
	
	$sql = "SELECT kid,title FROM base_model_ys WHERE pid='groups' LIMIT 3";
	$list = $db->arr($sql);
	echo "<br>arr:"; print_r($list);
	$sql = "SELECT kid,title FROM base_model_ys WHERE pid='groups' LIMIT 3";
	$list = $db->row($sql);
	echo "<br>row:"; print_r($list);
	$sql = "SELECT title FROM base_model_ys WHERE pid='groups' LIMIT 3,1";
	$list = $db->val($sql);
	echo "<br>val:"; print_r($list);
	echo "<hr>";
	print_r($db->fields('xtest_keyid_ys'));
	print_r($db->tables());
}

echo "\n\n(End) $now: \n";
echo basDebug::runInfo();
echo "\n</pre>\n";
?>
