<?php 
require('_config.php');
glbHtml::head('js'); 
$mod = basReq::val('mod','demo'); 
$kid = basReq::val('kid','');
//
echo "var edt_langType = '".@$lang."';\n"; 
echo "var edt_sysMod = '".@$mod."';\n"; 
echo "var edt_sysKid = '".@$kid."';\n"; 
basLang::jimp("/plus/editor/_pub.js",'',$lang,1);
#echo basJscss::write(basJscss::imp("/plus/editor/_pub.js"))."\n";
#echo basJscss::write(basJscss::imp("/plus/editor/_pub-$lang.js"))."\n";

//if(strpos()

// cfg_inc.php -=> config,
// _pub.js     --- DEL
// tpl_cfg.php --- DEL 


