<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');

//$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');
$tabid = 'wex_menu'; //$weapp
$mucfg = wysMenu::getMenuData($weapp); 

if($view=='list'){ 

	echo basJscss::imp('/skin/a_jscss/weixin.js?v=1');
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	glbHtml::tab_bar("公众号[$wekid] : 关注者管理$umsg",$_cbase['run']['sobarnav'],40,'tl');
	
	echo "<style type='text/css'>li.cF0F a { color: #F0F; }</style>";
	echo "<div id='tip_errors' class='pa5 ma5 h150 f14' style='display:none'></div>";
	glbHtml::fmt_head('fmlist',"?",'tblist');
	echo "\n<tr><th>昵称</th><th>OpenId</th><th>分组ID</th><th>城市</th><th>头像</th><th>性别</th><th>关注时间</th><th>发信息</th></tr>";
	for($i=1;$i<=50;$i++){
		echo "\n<tr id='wu_row$i' style='display:none'><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td><td class='tc'></td></tr>";	
	}
	$tips = "<div class='pa5 ma5 h150 f14'>加载中……<br>如果出现错误，请检查公众号配置或刷新页面；<br>如果翻页中出现错误，请重新点翻页。</div>";
	echo "\n<tr><td colspan='8'><div class='pg_bar tc'>$tips</div></td></tr>";
	glbHtml::fmt_end();
	
	$weixin = new wmpUser($wecfg); 
	$data = $weixin->getUserInfoList();
	$jscode = "var wu_total=$data[total], wu_count=$data[count], wu_kid='{$wecfg['kid']}', wu_next='$data[next_openid]', wu_page=1, 
		wu_msgurl='?file=awex/wex_msg3&wekid=$wekid&view=form&doend=1&openid=', wu_urlbase='".PATH_ROOT."/plus/api/wechat.php?', 
		wu_list='".(implode(',',$data['data']['openid']))."'; 
		\nwxGetUserPage(); wxGetPageBar(wu_page);";
	echo basJscss::jscode($jscode);
		
}elseif($view=='form'){
	
}
?>
