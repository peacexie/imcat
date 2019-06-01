<?php
namespace imcat;
$isMobile = basEnv::isMobile();
$vcfg = vopTpls::etr1('tpl'); unset($vcfg['base'],$vcfg['adm'],$vcfg['demo']);  
$title = $_cbase['sys_name'];
$icons = array('adm'=>'cog','rest'=>'exchange','umc'=>'user',
    'comm'=>'laptop','mob'=>'mobile','dev'=>'book','doc'=>'book',); // qrcode

$lang = $this->key ? $this->key : (empty($lang) ? 'cn' : $lang);

$_mdtab = array('about','start','tpdiy');
$texts = array();
if($this->key=='tips'){
    foreach ($_mdtab as $mdk) {
        $mlfile = vopTpls::tinc("home/tip-$mdk.md",0); 
        $mltext = comFiles::get($mlfile, 1); 
        $texts[$mdk] = extMkdown::pdext($mltext,0);  
    }
} 

