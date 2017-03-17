<?php
/*
用于:Ftp存储中, 缩略图生成的参考文件
*/
die();

$_cbase['run']['subDirs'] = '1';
require(dirname(__FILE__).'/_config.php'); 
safComm::urlFrom();

$size = req('size'); 
$img = req('img');

// 仅本地测试,实际情况可自行设置
if(file_exists(DIR_STATIC.$img)){
    $orgd = DIR_STATIC.$img;
    $orgp = PATH_STATIC.$img;
}elseif(file_exists(DIR_URES.$img)){
    $orgd = DIR_URES.$img;
    $orgp = PATH_URES.$img;
}else{
    $orgd = '';
    $orgp = '';
}

// 缩略图大小
$csize = ';240x180,160x120,120x90,120x60;88x31,40x40,120x120;'; 
$csize .= '180x240,120x160,90x120,60x120;31x88,80x80,240x240;'; 

// 处理缩略图
if(!empty($size) && !empty($img) && strpos($csize,$size) && strpos($size,'x') && $orgd){
    $siza = explode('x',$size); 
    $objd = str_replace(array('.'),array("-$size."),$orgd);
    $objp = str_replace(array('.'),array("-$size."),$orgp);
    $res = comImage::thumb($orgd,$objd,$siza[0],$siza[1]);
    $objp = $res ? $objp : $orgp;
}else{
    $objp = PATH_STATIC."/icons/basic/$def";
}

if($objp){
    header('Location:'.$objp);
}

/*

*/
//thumb.php
