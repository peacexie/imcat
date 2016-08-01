<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');

//$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');
$tabid = 'wex_menu'; //$weapp
$mucfg = wysMenu::getMenuData($weapp); 

if($view=='list'){ 
	
	$flgmusave = basReq::val('musave');
	$flgcreate = basReq::val('create');
	$flggetmnu = basReq::val('getmnu');
	$flgdelete = basReq::val('delete');
	//echo "<pre>"; print_r($fm);
	
	if(!empty($flgmusave)){
		$whr = "`appid`='$weapp'"; //array('appid'=>$wecfg['appid']);
		foreach($fm as $k=>$v){
			if(empty($v['name'])){
				$db->table($tabid)->where("$whr AND `key`='$k'")->delete();
			}if(isset($mucfg[$k])){ 
				$db->table($tabid)->data(array('name'=>$v['name'],'val'=>$v['val']))->where("$whr AND `key`='$k'")->update();  
			}elseif(!empty($v['name'])){
				$v['kid'] = basKeyid::kidTemp(4).$k;
				$v['key'] = $k;
				$v['appid'] = $wecfg['appid'];
				$db->table($tabid)->data(basReq::in($v))->insert();
			}
		}
		$msg = "保存菜单配置 成功！";
		$mucfg = wysMenu::getMenuData($weapp); 
	}elseif(!empty($flgcreate)){
		$weixin = new wysMenu($wecfg); 
		$data = $weixin->create($mucfg); 
		$msg = $data['errcode'] ? "失败<br>([$data[errcode]]$data[errmsg])" : '成功！';
		die("<p class='tc'>创建微信菜单 : $msg<br>请关闭窗口<p>");
	}elseif(!empty($flggetmnu)){
		$weixin = new wysMenu($wecfg); 
		$data = $weixin->get(); 
		$menu = '';
		if(empty($data['errcode'])){
			foreach($data as $k=>$v){
				$title = "[$k]".$v['name']; //.''.$v['type'];
				$tiele = empty($v['val']) ? "<b>### $title</b>" : "$title (".$v['type'].")<br>".$v['val']."";
				$menu .= "\n $tiele";
			}
		}
		$msg = empty($data['errcode']) ? "成功！<pre>$menu</pre>" : "失败<br>([$data[errcode]]$data[errmsg])<br>";
		die("<p class='tc'>获取微信菜单 : $msg 请关闭窗口<p>");
	}elseif(!empty($flgdelete)){
		$weixin = new wysMenu($wecfg); 
		$data = $weixin->del(); 
		$msg = $data['errcode'] ? "失败<br>([$data[errcode]]$data[errmsg])" : '成功！';
		die("<p class='tc'>删除微信菜单 : $msg<br>请关闭窗口<p>");
	}
	
	echo basJscss::imp('/skin/a_jscss/weixin.js?v=1');
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	glbHtml::tab_bar("公众号[$wekid] : 菜单配置$umsg",$_cbase['run']['sobarnav'],40,'tl');
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>序号</th><th>标题</th><th>Key/Url</th>"; 
	echo "<th>操作</tr>\n";
	
	for($i=1;$i<=3;$i++){ for($j=0;$j<=5;$j++){
		$itemstr = ''; $mlen = 7; $imuid = "$i{$j}"; ///*[　][＋][－][｜][├][└]  */
		if($j==0 && $i<=2){
			$icon = "＋&nbsp;";
			$mlen = 4;
		}elseif($j==0 && $i==3){
			$icon = "＋&nbsp;";
			$mlen = 4;
		}elseif($i<=2){
			$icon = "｜ &nbsp; ";
			$icon .= "├-&nbsp;";
		}else{ //$i==3
			$icon = "　 &nbsp; ";
			$icon .= "├-&nbsp;";
		}
		$name = empty($mucfg[$imuid]['name']) ? '' : $mucfg[$imuid]['name'];
		$val = empty($mucfg[$imuid]['val']) ? '' : $mucfg[$imuid]['val'];
		$itemstr .= "<tr>";
		$itemstr .= "<td class='tc'>$imuid</td>\n";
		$itemstr .= "<td class='tl'>$icon<input name='fm[$imuid][name]' id='fm[$imuid][name]' value='$name' size='25' maxlength='".($j==0 ? 4 : 7)."' type='text'></td>\n";
		$itemstr .= "<td class='tl'><input name='fm[$imuid][val]' id='fm[$imuid][val]' value='$val' maxlength='240' type='text' class='w320'></td>";
		$itemstr .= "<td class='tc'><a id='cupick_$imuid' href='javascript:;' onClick=\"wxMenuClear($imuid)\">&lt;&lt;清空</a></td>\n"; //<a id='cupick_$imuid' href='javascript:;' onClick=\"wxMenuPickWin($imuid)\">&lt;&lt; 选取菜单项</a>
		$itemstr .= "</tr>";
		echo $itemstr;
	} }
	echo "
		<tr>
		<td>&nbsp;</td>
		<td colspan='2' class='tc' nowrap>
		<input name='musave' class='btn' type='submit' value='保存菜单配置' />
		&nbsp;
		<input name='create' class='btn' type='button' value='创建微信菜单' onclick=\"winOpen('$aurl[1]&create=1','创建微信菜单',360,240);\" />
		&nbsp;
		<input name='getmnu' class='btn' type='button' value='获取微信菜单' onclick=\"winOpen('$aurl[1]&getmnu=1','获取微信菜单',480,360);\" />
		&nbsp;
		<input name='delete' class='btn' type='button' value='删除微信菜单' onclick=\"winOpen('$aurl[1]&delete=1','删除微信菜单',360,240);\" />
		</td>
		<td>&nbsp;</td>
		</tr>";

	glbHtml::fmt_end(array("mod|$mod"));
		
}elseif($view=='form'){
	
}
?>
