<?php
namespace imcat;
require __DIR__.'/_cfgall.php'; 

$act = req('act','view');
$mod = empty($mod) ? (empty($_mod) ? '' : $_mod) : $mod;
if(!is_string($mod)){
    glbHtml::end("Error:mod=",var_export($mod,1));
}
if(!$mod || !isset($_groups[$mod]) || $_groups[$mod]['pid']!='coms'){ 
    glbHtml::end(lang('flow.dops_parerr').":{$mod}");
}
$stops = read('coms.stops','sy');
if(in_array($mod,$stops)){
    glbHtml::end(lang('flow.dops_parerr').":{$mod}");
}

$_cfg = read($mod); 
$dop = new dopComs($_cfg,@$_cfg['cfgs']);
$mfields = $_cfg['f'];
unset($_cfg);

