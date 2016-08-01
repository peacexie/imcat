
<?php
require(dirname(__FILE__).'/_config.php'); 

$str = "《 !\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~";
echo '<br>'.$str;
$str = comConvert::sysRevert($str,0,'peace1234543');
echo '<br>'.$str;
$str = comConvert::sysRevert($str,1,'peace1234543');
echo '<br>'.$str;
echo '<br>';

//$user = new usrMember();
//$user->login('admindemo','123456');

$user = new usrAdmin();
$user->login('peace','admin123');

echo "<pre>";
print_r($user);


//safBase::Stop('ipStop');
//safBase::Stop('test');

//glbCUpd::upd_grade();

$epw = comConvert::sysPass('peace','admin123','adminer');
echo "peace=$epw<hr>";
$epw = comConvert::sysPass('memberdemo','demo123','company');
echo "memberdemo=$epw<hr>";

//echo date('Y-m-d H:i:s',2147483647);
//*
$user = new usrMember();


$user->login('admindemo','123456');

$a1 = $user->uget_perms('auser');
$a2 = usrBase::uget_perms('aframe');

echo "\n:::<pre>";

$umod = $user->uget_minfo('admindemo','171a3054ecad338c95a59db3655aa3f11ca939a02b454656');
print_r($umod); echo "\n a1,a2<br>";
print_r($a1);
print_r($a2);
print_r($user->uinfo);
echo "\n:::xxx</pre>";
//die();
//*/

for($i=100;$i<120;$i++){
	$v = str_pad("ckval_{$i}_",190,".");
	//comCookie::oset("c$i",$v,12345);
}

$guid = comSession::guid('safil','sessid');
$om = 'xpigeon@163.com';
$oa = 'xieys';

$sid = $guid['sid'];
$sphp = @$_COOKIE['PHPSESSID']; //'3ueeujprk8ofnlcde4aj6j9f33';
$stime = $_cbase['run']['stamp'];
$scode = comConvert::sysEncode($stime,$_cbase['safe']['safil'],32);

$ea = comConvert::sysRevert($oa,0,$sid.$scode); //Guest/
$em = comConvert::sysRevert($om,0,$sid.$scode); //null/
$da = comConvert::sysRevert($ea,1,$sid.$scode);
$dm = comConvert::sysRevert($em,1,$sid.$scode);

$sess = "member=$em\nmadmin=$ea\nstime=$stime\nscode=$scode";
echo $sess;

$o = '1422265497';
$e = comConvert::sysRevert($o,0,'');
$d = comConvert::sysRevert($e,1,'');
echo "<br>o=".$o;
echo "<br>e=".$e;
echo "<br>f=".preg_replace("/[^0-9A-Za-z]/",'_',$e);
echo "<br>d=".$d;
// +   /  =
// _1,_2,_3

?>
