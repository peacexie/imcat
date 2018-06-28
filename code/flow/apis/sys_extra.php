<?php 
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$part = req('part','opcache'); //opcache,xxx
$act = req('act','view'); // view, clear
$msg = "$part : $act";

//$links = admPFunc::fileNav('send','sms'); //  class='cur'
$gap = "<span class='span ph5'>|</span>";
$bar = "<a href='?mkv=apis-sys_extra&part=opcache'>Opcache</a>";
$bar .= " : <a href='?mkv=apis-sys_extra&part=opcache&act=delete'>Clear</a>";
glbHtml::tab_bar($msg,$bar,25); // ($title,$cont,$w1=25,$css2='tc')

if($part=='opcache')

    if(!function_exists('opcache_get_status')){
        echo "<p class='f18 tc'>Opcache Disabled! Please set your php.ini</p>";
    }elseif($act=='view'){
        dump(opcache_get_status());
    }elseif($act=='delete'){
        opcache_reset();
        echo "<p>Clean Opcache OK!</p>";
        dump(opcache_get_status()); 
    }

?>
