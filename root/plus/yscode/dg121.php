<?php
require(dirname(__FILE__).'/_config.php'); 

$url = 'http://www.dg121.com/default.asp';
$str = file_get_contents($url); //comHttp::doGet($url); //
$str = comConvert::autoCSet($str,'gb2312','utf-8');
$re = ''; //die($str);

//滚动通知栏目结束/分镇预报
$sa0 = basElm::getVal($str,array('<body onLoad="beginrefresh()">','townindex.asp')); 

//<div id=fyzy65 style="position:absolute; display:block;width:425px; height:245px; z-index:2; left: 350px; top: 400px; font-size: 18px; display:none;">
//<span class="t01" > <br /> 12小时内可能或者已经受台风影响，平均风力可达12级以上，或者已达12级以上并可能持续。  </span></div>
$sa0 = preg_replace("/\<div id=fyzy[^`]{24,1024}<\/span><\/div>/i",'',$sa0);

$sa0 = preg_replace("/<\!--.*?-->/si","",$sa0); //注释
$sa0 = preg_replace('~<([a-z]+?)\s+?.*?>~i','<$1>',$sa0); //去掉所有html标签的属性部分
$sa0 = preg_replace ('/width=[\'"](\d{1,5})[\'"]/iUs','',$sa0);
$sa0 = strip_tags($sa0,'<table> <tr> <td> '); 
//if($xLen) $xStr = self::showWidth($xStr,$xLen);
$sa0 = str_replace(array('&nbsp;'),' ',$sa0); //' ',"\r","\n","\t",
$sa0 = preg_replace("/\s(?=\s)/","\\1",$sa0); //多个连续空格只保留一个

//return $sa0; // nl2br($s);

//$sa0 = clsStr::filHText($sa0);
if(!empty($sa0)) $re = "\n$sa0<hr>\n"; //die($sa0);

/*
$a1 = clsElm::getArr($str,'<td class="tc1">(*)</td>',"[^>]{1,1200}");
if(!empty($a1[1][0])) $sa0 = $a1[1][0];
if(!empty($sa0)) $re = "\n$sa0<hr>\n";

$fa1 = 'bgcolor="F2F9FF"'; 
$fa2 = '<!--从这里开始是台风提示代码 -->'; 
$sa0 = clsElm::getVal($str,array($fa1,$fa2));
$sa0 = clsStr::filHText($sa0);
$sa0 = str_replace(array("\r","\n",'><br>','　',':<br>'),array('<br>','<br>','','',':'),$sa0);
if(!empty($sa0)) $re .= "\n$sa0\n";
*/
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Dongguan天气</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php echo "$re"; ?>
</body>
</html>