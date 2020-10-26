<?php 
namespace imcat; $t1 = microtime(1);
(!defined('RUN_INIT')) && die('No Init');
#safComm::urlFrom(); 

$act = basReq::val('act','nav'); // nav, js, css
$tab = basReq::val('tab'); 
//$tpldir = basReq::val('tpldir'); 
//$lang = basReq::val('lang', $_cbase['sys']['lang']); 
//$skin = basReq::val('skin');
$rf = basReq::val('rf'); // mkv


# 导航测试
if($act=='nav'){
    $excss = 'now:book;base:alib/file-style;base:cssjs/stpub;ui:lunbo/lunbo';
    $exjs = 'now:book;base:alib/file-func;base:cssjs/adpush;ui:lunbo/lunbo';
    echo "<ul><li>act=$act, tab=$tab</li>\n";
    echo "<li><a href='?ajax-weys&act=css&tpldir=comm&skin=&tab=initCss;$excss&_r=5.3'>initCss</a></li>\n";
    echo "<li><a href='?ajax-weys&act=js&tpldir=comm&rf=home-cdata&tab=initJs;$exjs&_r=5.3'>initJs</a></li>\n";
    echo "</ul>\n";
    die();
} //echo "act=$act, rf=$rf, skin=$skin\n";


# head
$act = $act=='js'?'js':'css';
glbHtml::head($act);

# baseCss
if(strstr($tab,'initCss')){ //usleep(500*1000);
    echo basJscss::weysCss();
}

# baseJs
if(strstr($tab,'initJs')){
    echo basJscss::weysJs();
}

# ext-tab
$tab = str_replace(['initCss','initJs'], '', $tab);
#$tab = 'now:book;base:alib/file-style;base:cssjs/stpub;ui:lunbo/lunbo';
if($tab){
    echo basJscss::weysTab($tab, $act);
}


/*

* TODO:
  - css-IE11编译 : OK
  - css-Path替换 : OK
  - css-Skin ： OK
  - js-Lang自动 : OK
  - 文件合并 : OK

*/
