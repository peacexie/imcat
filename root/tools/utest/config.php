<?php
$_cbase['tpl']['tpl_dir'] = 'adm';
require(dirname(__FILE__).'/_config.php'); 
//vopTpls::set('chn');

echo "<pre>";

$_cfg_load = glbConfig::read('load','cfg'); 
echo "\n<hr>"; print_r($_cfg_load);

$_va_home = glbConfig::vcfg('home');
echo "\n<hr>home1:"; print_r($_va_home);

$_vc_news = glbConfig::vcfg('news'); 
echo "\n<hr>news:"; print_r($_vc_news);

$_vc_indoc = glbConfig::vcfg('indoc'); 
echo "\n<hr>indoc:"; print_r($_vc_indoc);

$_sy_keepid = glbConfig::read('keepid','sy'); 
echo "\n<hr>"; print_r($_sy_keepid);

$_sy_sysids = glbConfig::read('sysids','sy'); 
echo "\n<hr>"; print_r($_sy_sysids);

$fsystem = basLang::ucfg('fsystem');
echo "\n<hr>"; print_r($fsystem); 

//$_demo = glbConfig::read('demo');
//echo "\n<hr>"; print_r($_demo); 

echo "\n<hr>"; print_r(array_keys(glbConfig::$_CACHES_YS));

print_r(get_defined_constants(1));

echo "</pre>";

define('K1','2131#2[]2_&*2');
$a = array(K1=>'v1','k2'=>'v2');
print_r($a);
echo $a[K1];

?>
