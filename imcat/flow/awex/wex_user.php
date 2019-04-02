<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require __DIR__.'/_wex_cfgs.php';

$tabid = 'wex_menu'; //$weapp
$mucfg = wysMenu::getMenuData($weapp); 

if($view=='list'){ 

    eimp('/~base/cssjs/weixin.js?v=1');
    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    glbHtml::tab_bar(lang('awex.appid')."[$wekid] : ".lang('awex.follows')."$umsg",$_cbase['run']['sobarnav'],40,'tl');
    
    echo "<style type='text/css'>li.cF0F a { color: #F0F; }</style>";
    echo "<div id='tip_errors' class='pa5 ma5 h150 f14' style='display:none'></div>";
    glbHtml::fmt_head('fmlist',"?",'tblist');
    basLang::inc('uless', 'wex_user');
    for($i=1;$i<=50;$i++){
        echo "\n<tr id='wu_row$i' style='display:none'><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td></tr>";    
    }
    $tips = "<div class='pa5 ma5 h150 f14'>".lang('awex.tip_uload')."</div>";
    echo "\n<tr><td colspan='8'><div class='pg_bar tc'>$tips</div></td></tr>";
    glbHtml::fmt_end();
    
    $weixin = new wmpUser($wecfg); 
    $data = $weixin->getUserInfoList();
    $jscode = "var wu_total=$data[total], wu_count=$data[count], wu_kid='{$wecfg['kid']}', wu_next='$data[next_openid]', wu_page=1, 
        wu_msgurl='?awex-wex_msg3&wekid=$wekid&view=form&doend=1&openid=', wu_urlbase='".PATH_ROOT."/plus/api/wechat.php?', 
        wu_list='".(implode(',',$data['data']['openid']))."'; 
        \nwxGetUserPage(); wxGetPageBar(wu_page);";
    echo basJscss::jscode($jscode);
        
}elseif($view=='form'){
    
}
?>
