<?php 
(!defined('RUN_INIT')) && die('No Init');

$url = '';

#echo "($uri)"; die();
if(strpos($uri,'chn.php')>0){
    $urt = str_replace("/chn.php", "/home.php", $uri);
    $url = "http://txjia.com/imcat{$urt}";
    #header("Location:$url");
    #die($url);
}

if(strpos($uri,'/book.php')===0){
    $urt = $uri; //str_replace("/book.php", "/home.php", $uri);
    $url = "http://txjia.com/custom{$urt}";
    #header("Location:$url");
    #die($url);
}

if($_host=='yscode.txjia.com'){
    $urt = $uri; //str_replace("/book.php", "/home.php", $uri);
    $url = "http://txjia.com/yscode{$urt}";
    #header("Location:$url");
    #die($url);
}

if(strpos($_host,'dongguan.')>0){
    $url = "http://txjia.com/qiye$uri";
    #header("Location:$url");
    #die($url);
}

/*

# http://txjia.com/custom/custom/custom/custom/custom/custom/custom/imcat/book.php
// http://txjia.com/imcat/book.php

*/

if(ob_get_contents()){ ob_clean(); }
// 本身是404页,可能存在404的head,所以以下跳转代码无效
if($url){
    header("location:$url");
    //die($url);
}
httpStatus(404);
