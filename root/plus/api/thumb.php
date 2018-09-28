<?php
#namespace imcat;


/*
用于:Ftp存储中, 缩略图生成的参考文件
*/
die();


# 配置: 目录前缀, 查看前缀
$dpre = __DIR__."/imcat"; // /$img
$vpre = 'http://ftp.txjia.com/imcat';


$_cbase['run']['subDirs'] = '1';
require dirname(__DIR__).'/php/root/run/_init.php';
#safComm::urlFrom();


$img = req('img');
$size = req('size'); 
$def = req('def');
$debug = req('debug');

$img = str_replace('{ftproot}/', '', $img);

// 缩略图大小
$csize = ';240x180,160x120,120x90,120x60;88x31,40x40,120x120;'; 
$csize .= '180x240,120x160,90x120,60x120;31x88,80x80,240x240;'; 

if(file_exists("$dpre/$img")){
    $objp = "$vpre/$img";
    if(!empty($size) && strpos($csize,$size)){
        $siza = explode('x',$size); 
        $szfp = str_replace('.', "-$size.", $img);
        $res = comImage::thumb("$dpre/$img", "$dpre/$szfp", $siza[0], $siza[1]);
        if($res) $objp = "$vpre/$szfp";
    }
}else{
    $objp = "$vpre/defs/$def";
}

if($objp){
    if($debug){
        echo "<br>$img<br>$dpre<br>$vpre<br>\n";
        die($objp);
    }
    header('Location:'.$objp);
}


/*
http://ftp.txjia.com/thumb.php
  ?img={ftproot}/cargo/2018/9s-g477/2018-9s-gd7s.jpg
  &size=160x120&def=demo_nop300x200.jpg&debug=1&_v=4.4
*/
