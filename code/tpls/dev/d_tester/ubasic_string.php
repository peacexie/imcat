
<?php
(!defined('RUN_MODE')) && die('No Init');

$s = '内页广告';
$s0 = comConvert::pinyinMain($s,3,0);
$s1 = comConvert::pinyinMain($s,3,1);
$s9 = comConvert::pinyinMain($s,3,9);
echo "<br>$s0<br>$s1<br>$s9";


echo '<br>'; var_export(basStr::isUtf8('string',''));
echo '<br>'; var_export(basStr::isUtf8('测试','')); 
echo '<br>'; var_export(basStr::isUtf8('琗琌ぃ',''));
echo '<br>'; var_export(basStr::isUtf8('$string',''));
echo '<br>'; var_export(basStr::isUtf8('に ぬ',''));
echo '<br>'; var_export(basStr::isUtf8("\xF1",''));
echo '<br>';
echo '<br>'; var_export(basStr::isUtf8('string'));
echo '<br>'; var_export(basStr::isUtf8('测试')); 
echo '<br>'; var_export(basStr::isUtf8('琗琌ぃ'));
echo '<br>'; var_export(basStr::isUtf8('$string'));
echo '<br>'; var_export(basStr::isUtf8('に ぬ'));
echo '<br>'; var_export(basStr::isUtf8("\xF1"));
echo '<br>';


echo "<br><a href='?'>Home</a>";
//echo '<br>'; var_export(Perm::chkUrl(''));
echo '<br>'; var_export(basStr::isMail('xys@163.com'));
echo '<br>'; var_export(basStr::isMail('xys@163.com.cn'));
echo '<br>'; var_export(basStr::isMail('xys@163'));
echo '<br>'; var_export(basStr::isMail('xys@163.3'));

echo '<br>'; var_export(basEnv::isRobot()); 
echo '<br>'; var_export(basEnv::isMobile()); 
echo '<br>';

$str = "尽test可能将定制内容集中存储到同一个目录";
for($i=3;$i<30;$i++){ 
	echo "\n<br> $i-a: "; print_r(basStr::cutCount($str,$i)); 
	echo "\n<br> $i-c: "; print_r(basStr::cutWidth($str,$i,'')); 
	//echo "\n<br> $i-b: ".basStr::cutWidth($str,$i,''); 
}

$sunit = "尽test可能将定制内容集中存储到同一个目录";
$str = "尽test可能 $sunit$sunit$sunit$sunit";
echo "\n<br> "; print_r(basStr::cutCount($str)); 

$sunit = "尽test可能将定制内容集中存储到同一个目录";
$str = "尽test可能 $sunit$sunit$sunit$sunit$sunit$sunit$sunit$sunit";
echo "\n<br> "; print_r(basStr::cutCount($str));

$sunit = "尽test可能将定制内容集中存储到同一个目录";
$str = "尽test可能 $sunit$sunit$sunit$sunit$sunit$sunit$sunit$sunit$sunit$sunit";
echo "\n<br> "; print_r(basStr::cutCount($str));



echo '<br>'.basStr::cutWidth($str);
echo '<br>'.basStr::cutWidth($str,4);
echo '<br>'.basStr::cutWidth($str,8);
echo '<br>'.basStr::cutWidth($str,12);
echo '<br>'.basStr::cutWidth($str,16);
echo '<br>123456789-123456789-123456789-123456789-123456789-';
echo '<br>';

echo '<br>'.basStr::showNumber(123,3);
echo '<br>'.basStr::showNumber(12345,2);
echo '<br>'.basStr::showNumber(1234767,1);
echo '<br>'.basStr::showNumber(12.3476789,5);
echo '<br>'.basStr::showNumber(123.476789,2);
echo '<br>'.basStr::showNumber(123,'Byte');
echo '<br>'.basStr::showNumber(12345,'Byte');
echo '<br>'.basStr::showNumber(1234767,'Byte');
echo '<br>'.basStr::showNumber(123476789,'Byte');
echo '<br>';

echo '<br>'.basStr::showState("Y;N;X;-","已审;未审;未过;未知",'');
echo '<br>'.basStr::showState("Y;N;X;-","已审;未审;未过;未知",'Y');
echo '<br>'.basStr::showState("Y;N;X;-","已审;未审;未过;未知",'N');
echo '<br>'.basStr::showState("Y;N;X;-","已审;未审;未过;未知",'-');
echo '<br>';

echo '<br>'.basStr::showColor($str,'#0FC');
echo '<br>'.basStr::showColor($str,'C0C');
echo '<br>'.basStr::showColor($str,'C0C');
echo '<br>';

$str = "
Test<b>String</b>'Test'Test End
";
echo '<br>:'.basStr::filText($str,0);
echo '<br>:'.basStr::filText($str);
echo '<br>:'.basStr::filHtml($str);
echo '<br>:'.basStr::filHText($str,20);
echo '<br>:'.basStr::filForm($str);
echo '<br>:'.basStr::filKey($str);
echo '<br>:'.basStr::filTitle($str);
echo '<br>';

$xStr = "
Test<b>String</b>'Test'Test End
";
echo '<br>:'.basJscss::Alert($xStr,'Redir','b.cn');
echo '<br>:'.basJscss::jsShow($xStr);
echo '<br>:'.basJscss::jsKey($xStr);
$str1 = basReq::in($xStr);
$str2 = basReq::out($str1);
echo "\n<hr>\n\n<br>:".$str1;
echo "\n<hr>\n\n<br>:".$str2;
echo '<br>';

//...

?>