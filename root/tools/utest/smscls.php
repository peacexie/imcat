<?php 
require(dirname(__FILE__).'/_config.php');  

$send = basReq::val('send');
$tel = basReq::val('tel','13537432147');

$cls = basReq::val('cls');
$act = basReq::val('act');
$now = '-';

$url = basReq::val('url');
if($act=='do_get'){
	print_r($_GET);	
	die();
}
if($url=='url_test'){
	$now = "url_test";	
}

if($act){
	$_cbase['sms']['cfg_api'] = $act;
	$now = "act=$act";
	$sms = new extSms(); 
}

if($cls){
	$now = "cls=$cls";
	$class = "sms_$cls";
	$_cfgs = glbConfig::read('sms','cfg');
	require(DIR_CODE."/adpt/smsapi/{$class}.php");
	$sms = new $class($_cfgs); 	
}

$code = basReq::val('code','');

?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>Demo-Class</title>
<script src="../../skin/jslib/jsbase.js"></script>
</head><body>
Core: <a href="?act=0test">0test</a> | <a href="?act=bucp">bucp</a> | <a href="?act=emhttp">emhttp</a> | <a href="?act=winic">winic</a> | <a href="?act=dxqun">dxqun</a> # Now(<?php echo $now; ?>) <br>
Class: <a href="?cls=0test">0test</a> | <a href="?cls=bucp">bucp</a> | <a href="?cls=emhttp">emhttp</a> | <a href="?cls=winic">winic</a> | <a href="?cls=dxqun">dxqun</a> # <a href="?">Clear</a> <br>
Url-Test: <a href="?url=url_test">url_test</a>
<?php

if($url=='url_test'){
	$s0 = '中文测试字符串十个字';
	$s1 = ''; $m1 = 20;
	for($i=0;$i<$m1;$i++){
		$s1 .= $s0;	
	} 
	$t0 = ':13537432147';
	$t1 = ''; $m2 = 100;
	for($i=0;$i<$m2;$i++){
		$t1 .= "$t0";	
	} 
	$a = array(
		'act'=>'do_get',
		'tel'=>$t1,
		'msg'=>$s1,
	);
	//$s = http_build_query($a);
	//$s = "act=do_get&tel=$t1&msg=$s1";
	$s = "act=do_get&tel=$t1&msg=".comParse::urlEncode($s1,1)."";
	$s = "?a=1&$s&b=2";
	
	echo "\n<hr>"; echo urlencode($s0);
	echo "\n<br>s1-len:".($m1*10)." 中文:".strlen($s1).'('.strlen(http_build_query(array($s1)));
	echo "\n<br>t1-len:{$m2}        号码:".strlen($t1).'('.strlen(http_build_query(array($t1)));
	echo "\n<br>s-len:".strlen($s);
	echo "\n<br>s=:".str_replace(array(",",":"),", ",$s);
	echo "\n<br><a href='$s'>Link(long)".(strlen($s))."</a>";
	$s2 = '';
	$s2 = comHttp::doGet("http://192.168.1.11/08tools/yscode/@test/cplus/smscls.php$s", 2);
	echo "\n\n<br>".str_replace(array(",",":"),", ",$s2)."<hr>";
	
	echo "\n<pre>\n";
	$a = array(
		'act'=>'do_get',
		'tel'=>'v-tel',
		'msg'=>'v-msg',
	);
	$b = $a;
	$b['test'] = 1;
	print_r($b);
	print_r($a);
	echo "\n</pre>\n";
}

echo "\n<pre>\n";

if($act){
	
	$r = $sms->isClosed();
	echo "\n isClosed="; var_export($r);
	print_r($sms);
	if($send=='Submit'){
		$msg = basReq::val('msg');
		$r = $sms->sendSMS($tel,$msg,5);
		echo "\n sendSMS=$r[1]($r[0])"; 	
	}else{
		$r = $sms->getBalance();
		echo "\n getBalance=$r[1]($r[0])"; 
	}
	echo "<form action='?' method='get'>".
		"tel:<textarea name='tel' cols='36' rows='3'>$tel</textarea><br>".
		"msg:<textarea name='msg' cols='36' rows='5' id='msg'></textarea><br>".
		"<input name='act' type='hidden' value='$act'>".
		"<input type='submit' name='send' id='send' value='Submit'>".
		" <span id='idn'>0</span> ".
		"<input type='button' value='set' onClick='_set()'>". 
		"13712133214,13537432147".
		"</form>";	
}

if($cls){
	
	$r = $sms->getBalance();
	echo "\n r1="; print_r($r);
	//$r2 = $sms->getBalance();
	//echo "\n r2="; print_r($r2);
	if(in_array($cls,array('0test'))){ 
		$r = $sms->chargeUp(2);
		echo "\n r2="; print_r($r);
	}
	$msg = $cls.date('Y-m-d H:i:s')."\n测\r试\r\nTest";
	$r = $sms->sendSMS('13537432147',$msg);
	echo "\n r4="; print_r($r);
	
}

echo "\n\n(End) $now: \n";
echo basDebug::runInfo();
basDebug::runLoad();
echo "\n</pre>\n";
?>
<script>
var omsg = jsElm.jeID('msg'); 
function _set() {
	var tmp = '<?php echo "$act:测试:".date('Y-m-d H:i:s'); ?>'; 
	//omsg.innerHTML = tmp; 
	omsg.value = tmp; 
	omsg.onblur();
}
omsg.onblur = function(){ jsElm.jeID('idn').innerHTML = omsg.value.length; }
</script>
</body></html>
