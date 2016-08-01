<?php
require(dirname(__FILE__).'/_cfgall.php'); 

$act = basReq::val('act','view');
$mod = empty($mod) ? (empty($_mod) ? '' : $_mod) : $mod;
if(!$mod || !isset($_groups[$mod]) || $_groups[$mod]['pid']!='coms'){ 
	glbHtml::end("参数错误:{$mod}");
}

$_cfg = glbConfig::read($mod); 
$dop = new dopComs($_cfg,@$_cfg['cfgs']);
$mfields = $_cfg['f'];
unset($_cfg);

