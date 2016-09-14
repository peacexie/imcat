<?php
require(dirname(__FILE__).'/_config.php'); 
/*
vopRoute::init();
print_r(vopRoute::$org);
print_r(vopRoute::$par);
*/

$bfile = basename(__FILE__);
$scname = $_SERVER['SCRIPT_NAME'];

echo "$bfile\n";
echo "<a href='?'>[?Home]</a> # \n";
echo "<a href='$scname'>$bfile</a> # \n";
echo "<a href='$scname?quit'>$bfile?quit</a> # \n";
echo "<a href='$scname?quit=1'>$bfile?quit=1</a> <br> \n";
echo "<a href='$scname/quit'>$bfile/quit</a> # \n";
echo "<a href='$scname/quit?act'>$bfile/quit?act</a> # \n";
echo "<a href='$scname/quit?act=login'>$bfile/quit?act=login</a> # \n";

echo "<hr>-------bfile:".$bfile;
echo "<br>----PHP_SELF:".$_SERVER['PHP_SELF'];
echo "<br>-REQUEST_URI:".$_SERVER['REQUEST_URI'];
echo "<br>-SCRIPT_NAME:".$_SERVER['SCRIPT_NAME'];
echo "<br>QUERY_STRING:".$_SERVER['QUERY_STRING'];
basDebug::varShow($_GET);
echo "<hr>";

echo '
<form id="form1" name="form1" method="post" action="?">
  A
  <input name="kid[]" type="checkbox" id="kid1" value="A" />
  好<br />B
  <input name="kid[]" type="checkbox" id="kid2" value="Good" />
  很好<br />C
  <input name="kid[]" type="checkbox" id="kid3" value="3" />
  非常好<br />D
  <input name="kid[]" type="checkbox" id="kid4" value="中文" />
  噎<br />E
  <input name="kid[]" type="checkbox" id="kid5" value="Test" />
  Test测试<br />S1
  <input name="kid[]" type="checkbox" id="kid5" value="Test(&<>)(:/?=&#%)" />
  Test1<br />S2
  <input name="kid[]" type="checkbox" id="kid5" value="Test(/*?<>|)($[]{})" />
  Test1<br />
  <input name="btn" type="submit" value="提交" id="btn" />
</form>
';
if(isset($_REQUEST['kid'])){ 
	echo '<br>GET:'; print_r($_GET);
	echo '<br>POST:'; print_r($_POST);
	print_r($_REQUEST['kid']);
	echo '<br>tst1-a>s:<br>'; //print_r(basReq::getCBox($_REQUEST['kid']));
	//echo '<br>tst2-a>s:<br>'; print_r(getCBox(kid));
}
echo '<br>test-str:<br>((('; print_r(basReq::getCBox('kid'));
echo '<br>test-arr:<br>((('; print_r(basReq::getCBox('kid','a'));
echo '<br>:end:';


echo "<br><a href='?'>Home</a>";
//echo '<br>'.date('Y-m-d H:i:s');
echo '<br>';


echo '<br>key-N:'.basReq::val('key',888,'N');
echo '<br>key-D:'.basReq::val('key','2012-12-31','D');
echo '<br>key-S:'.basReq::val('key');
echo '<br>_deAct:'.basReq::val('_deAct');
$data = 'Test_123!<>;~!&*"@@##$43434343_Peace_Xie_Test';
echo '<br>'.basReq::fmt($data);
echo '<br>';





echo '<br>ip='.basEnv::userIP();
//del_dir('../tess');
echo '<br>cset:'.comConvert::autoCSet('$fContents','utf-8','gbk');
$timer = microtime(1); md5('123456');
echo '<br>'.(microtime(1)-$timer);
$timer = microtime(1);
echo '<br>sysEncode='.comConvert::sysEncode('123456-','-xx-',0,'sha1');
echo '<br>sysEncode='.comConvert::sysEncode('123456-','-xx-',0,'md5');
echo '<br>sysEncode='.comConvert::sysEncode('123452-','-xx-',0,'sha1,md5');
echo '<br>sysEncode='.comConvert::sysEncode('123456-','-xx-',0,'md5,sha1');
echo '<br>sysEncode=1234567890123456789012345678901234567890123456789012345678901234567890';
echo '<br>'.(microtime(1)-$timer);



echo "\n<hr>\n";
basDebug::varMain();
//bugStop("?_deTest=peaceTest");
echo "\n<hr>\n";


@$a = 1/0;
//print_r(basDebug::bugError());
print_r(basDebug::bugInfo());
basDebug::bugLogs('test',basDebug::bugPars('act'),'','show');
basDebug::bugLogs('test',basDebug::bugPars('act'),'','file');
basDebug::bugLogs('test',basDebug::bugPars('act'),'syact','db');
basDebug::bugLogs('test',basDebug::bugPars('act'),'detmp','db');
//bugLogs('file');
//bugLogs('file',array(),'./');

?>