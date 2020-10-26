<?php
(!defined('RUN_INIT')) && die('No Init');

use imcat\basEnv;
use imcat\basOut;
use imcat\comSession;
use imcat\comCookie;
use imcat\comConvert;
use imcat\comHttp;
use imcat\extGbuy;
use imcat\vopApi;

//global $_cbase; 
$act = req('act');
$base = 'http://192.168.31.19/peace/imcat/catmain';

// 限本地调试
$isloc = basEnv::isLocal();
if(!$isloc){
    die('');
}
// 
if($act=='zoko'){
    $url = 'http://api.gdzhuoke.com/api/product/detail';
    $data = '{"token":"e100fee9daeca090ebf8d105fed60e2d","sku":"ECS01000348","product_extend_attributes":""}';
    $opt = [];
    $res = comHttp::curlCrawl($url, $data, $opt);
    dump($res);
}
// 
$acp['username'] = 'abc'; // {"username":"zhuoke","password":"ds...rN","timestamp":"2020-08-31 13:02:31","sign":"92...d8"};
$acp['password'] = '123'; 
$acp['timestamp'] = date('Y-m-d H:i:s'); 
$acp['sign'] = md5("abc123{$acp['timestamp']}123"); 
$actoks = extGbuy::getActoken($acp, 1);
$access_token = $actoks['access_token'];
#dump($actoks); # die();
// 
if($act=='token'){
    $url = "$base/root/run/vapi.php/gbuy-actoken";
    $data = json_encode($acp); // '{"token":"$access_token","sku":"ECS1000271","product_extend_attributes":""}';
    $opt = [];
    $res = comHttp::curlCrawl($url, $data, $opt);
    echo($res); die();
}
if($act=='pres'){
    echo "\n\n:->post"; dump($this->post);
    echo "\n\n:_POST"; dump($_POST);
    echo "\n\n:_REQUEST"; dump($_REQUEST);
    echo "\n\n"; 
}
if($act=='post'){
    $url = "$base/root/run/vapi.php/gbuy-testp?act=pres";
    $data = '{"token":"'.$access_token.'","sku":"Test-271","product_extend_attributes":""}';
    $opt = [];
    $res = comHttp::curlCrawl($url, $data, $opt);
    dump($res);
}
