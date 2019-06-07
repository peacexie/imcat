<?php

$hi = empty($_GET['hi']) ? 'Hi, It works!' : $_GET['hi']; 
$hi .= ' '.date('H:i:s');

$base = $_SERVER['SCRIPT_NAME'];
$base = substr($base,0,strrpos($base,'/')); 

$nav = '';
$arr = array('/entry1.php','/entry1.php?hi=Test1!','/entry1.php/pinfo?q=hi','/entry1/news.id1234.htm','/entry1/news-subx.htm','/entry1/news-a.htm?hi=abc');
foreach ($arr as $key) {
    $nav .= " # <a href='$base$key'>$key</a>\n";
}
$nav = "$nav<br>".str_replace('entry1','entry2',$nav);
$nav = str_replace(array('>/entry1.php/','>/entry2.php/','>/entry1/','>/entry2/'),'>/',$nav);

?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>
<meta name='viewport' content='width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no'>
<meta name='robots' content='noindex, nofollow'>
<title>Rewrite Tester</title>
</head>

<body>
</body>
</html>

<p>Rewrite配置参考</p>
<a href='http://imcat.txjia.com/chn.php?faqs.2017-9h-4bq1'>Rewrite配置(Apache/Nginx/iis7+)参考</a>

<nav>
    <p>nav: Rewrite TEST Links</p>
    <?=$nav?>
</nav>

<?php
echo "<p>hi = $hi<p>\n";

$tab = array(
    'PHP_SELF', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 
    'REQUEST_URI', 'QUERY_STRING', 
    'REDIRECT_URL', 'REDIRECT_QUERY_STRING', 
    'ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 
    'argv', 
);
echo "<pre>";
foreach ($tab as $key){
    $val = isset($_SERVER[$key]) ? $_SERVER[$key] : '<span style="color:#CCC">null</span>';
    echo str_pad("$key ",30,".")." = [$val]\n";
}
echo "</pre>";
?>

