<?php
(!defined('RUN_INIT')) && die('No Init');
usrPerm::run('pmod','apiweixin');

$view = empty($view) ? 'list' : $view;
$fs_do = req('fs_do');
$fs = basReq::arr('fs'); 

$msg = ''; $cnt = 0; 

$wekid = req('wekid'); 
$wecfg = wysBasic::getConfig($wekid); 
$weapp = @$wecfg['appid']; 

$cnav = basLang::ucfg('cfgbase.wx_nav');

$_cbase['run']['sobarnav'] = "<p class='tc pv5'>

 <a href='?file=awex/wex_menu&wekid=$wekid'>$cnav[menu]</a>
 # <a href='?file=awex/wex_user&wekid=$wekid'>$cnav[user]</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid'>$cnav[msg]</a>
 # <a href='?file=awex/wex_rkey&wekid=$wekid'>$cnav[kw]</a>
 
 # <a href='#?file=awex/wex_vmat&wekid=$wekid' class='c999'>$cnav[wres]</a>
 # <a href='#?file=awex/wex_vweb&wekid=$wekid' class='c999'>$cnav[wweb]</a>
 # <a href='#?file=awex/wex_vshop&wekid=$wekid' class='c999'>$cnav[wshop]</a>
 # <a href='#?file=awex/wex_vact&wekid=$wekid' class='c999'>$cnav[wact]</a>
 
</p>";

//echo $wekid;
