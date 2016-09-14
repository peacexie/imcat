<?php
require(dirname(__FILE__).'/_config.php'); 

function re_icons($str){
	$aicons = array(
		'qing'       => '晴',
		'yin'        => '阴',
		'duoyun'     => '多云',
		'leizhenyu'  => '雷阵雨',
		'zhenyu'     => '阵雨',
	);
	$a1 = array(
		'tedabao'  => '特大暴',
		'dabao'    => '大暴',
		'bao'      => '暴',
		'da'       => '大',
		'zhong'    => '中',
		'xiao'     => '小',
	);
	$a2 = array('yu'=>'雨','xue'=>'雪');
	foreach($a1 as $k1=>$v1){
	foreach($a2 as $k2=>$v2){	
		$aicons["$k1$k2"] = "$v1$v2";
	} }
	foreach($aicons as $k=>$v){
		$str = str_replace("$k.png","($v)",$str); 	
	}
	return $str;	
}

$city = basReq::val('city'); $city = $city ? $city : '东莞';
$ret = basReq::val('ret');

// http://api.map.baidu.com/telematics/v3/weather?location=%E4%B8%9C%E8%8E%9E&ak=3GGtGlCtbAGa1GYK70XFX2Rb
$curl = urlencode($city);
$url = "http://api.map.baidu.com/telematics/v3/weather?location=$curl&ak=3GGtGlCtbAGa1GYK70XFX2Rb";
$str = comHttp::doGet($url); //file_get_contents($url); 

$wdate = basElm::getVal($str,array('<date>','</currentCity>'));
$wdata = basElm::getVal($str,array('<weather_data>','</weather_data>'));
$wpm25 = basElm::getVal($str,array('<pm25>','</pm25>'));
$_flag_ = (empty($wdate) || empty($wdata)) ? '<!--_apiError_-->' : '';

$wdata = str_replace(array("<date>","</temperature>"),array("\n<li>","</li>"),$wdata);  
$wdata = str_replace(array("<date>"),"<br>•\n",$wdata);  

$wstr = "<b>$wdate</b> ".($wpm25 ? "(pm25=$wpm25)" : '')." $wdata";
$wstr = strip_tags($wstr,"<br><b><li>"); 
$wstr = preg_replace("/\s(?=\s)/","\\1",$wstr); //多个连续空格只保留一个
              
$wstr = str_replace(array("http://api.map.baidu.com/images/weather"),'',$wstr); 
$wstr = str_replace(array("/day/","/night/"),array('白','夜'),$wstr); 
$wstr = re_icons($wstr);

if($ret=='ajax-text'){
	header("Access-Control-Allow-Origin: *");
	die("$wstr$_flag_");
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo "$city"; ?>天气</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php echo "$wstr$_flag_"; ?>
</body>
</html>