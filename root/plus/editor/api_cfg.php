<?php 
require '_config.php';
glbHtml::head('js'); 
$mod = req('mod','demo'); 
$kid = req('kid','');
//
echo "var edt_langType = '".@$lang."';\n"; 
echo "var edt_sysMod = '".@$mod."';\n"; 
echo "var edt_sysKid = '".@$kid."';\n"; 

$d1 = comFiles::get(DIR_ROOT."/plus/editor/_pub.js");
$d2 = comFiles::get(DIR_ROOT."/plus/editor/_pub-$lang.js");
echo "$d1\n\n//($lang)\n$d2";

// cfg_inc.php -=> config,
// _pub.js     --- DEL
// tpl_cfg.php --- DEL 


