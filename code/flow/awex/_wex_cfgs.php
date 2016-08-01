<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pmod','apiweixin');

$view = empty($view) ? 'list' : $view;
$fs_do = basReq::val('fs_do');
$fs = basReq::arr('fs'); 

$msg = ''; $cnt = 0; 

$wekid = basReq::val('wekid'); 
$wecfg = wysBasic::getConfig($wekid); 
$weapp = @$wecfg['appid']; 

$_cbase['run']['sobarnav'] = "<p class='tc pv5'>

 <a href='?file=awex/wex_menu&wekid=$wekid'>菜单</a>
 # <a href='?file=awex/wex_user&wekid=$wekid'>关注者</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid'>消息</a>
 # <a href='?file=awex/wex_rkey&wekid=$wekid'>关键字</a>
 
 # <a href='#?file=awex/wex_vmat&wekid=$wekid' class='c999'>微素材</a>
 # <a href='#?file=awex/wex_vweb&wekid=$wekid' class='c999'>微网站</a>
 # <a href='#?file=awex/wex_vshop&wekid=$wekid' class='c999'>微店铺</a>
 # <a href='#?file=awex/wex_vact&wekid=$wekid' class='c999'>微活动</a>
 
</p>";

//echo $wekid;
