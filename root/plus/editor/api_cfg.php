<?php 
require(dirname(dirname(dirname(__FILE__))).'/run/_paths.php'); 
glbHtml::head('js'); 
$lang = basReq::val('lang','cn'); 
$mod = basReq::val('mod','demo'); 
$kid = basReq::val('kid','');
//
echo "var edt_langType = '".@$lang."';\n"; 
echo "var edt_sysMod = '".@$mod."';\n"; 
echo "var edt_sysKid = '".@$kid."';\n"; 
echo basJscss::write(basJscss::imp('/plus/editor/_pub.js'))."\n";
//if(strpos()

// cfg_inc.php -=> config,
// _pub.js     --- DEL
// tpl_cfg.php --- DEL 


