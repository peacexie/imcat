<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');
$_cbase['run']['sobarnav'] = '';

$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');
$cfg = array(
	'sofields'=>array('kid','type','appid','api'),
	'soorders'=>array('kid' => 'kid(降)','kid-a' => 'kid(升)'),
	//'soarea'=>array('amount','数量'),
);
$tabid = 'wex_apps';

if($view=='list'){ 

	$dop = new dopExtra($tabid,$cfg); 
	$dop->order = $dop->so->order = basReq::val('order','kid-a'); 
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！"; 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = "请勾选操作记录！";
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	wysBasic::clearCache($id);
			  	$cnt += $dop->opDelete($id);
			  } 
		  }elseif($fs_do=='clearact'){ 
			  foreach($fs as $id=>$v){
			  	wysBasic::clearCache($id);
			  } 
		  }elseif($fs_do=='clrqrtik'){ //atime<'".($_cbase['run']['stamp']-86400)."'
			  $db->table("wex_qrcode")->where("1=1")->delete(); //atime<'".($_cbase['run']['stamp']-5*60*144)."'
			  $cnt--;
		  }elseif(in_array($fs_do,array('locate','msgget','msgsend'))){ //432000=5day
			  $db->table("wex_$fs_do")->where("atime<'".($_cbase['run']['stamp']-432000)."'")->delete(); 
			  $cnt--;
		  } 
		  
		}
		$cnt && $msg = ($cnt>0) ? "$cnt 条记录 删除成功！" : "操作成功！";
	}
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$links = $cv->Url(' | 添加&gt;&gt;',0,"$aurl[1]&view=form","添加配置",480,360); //$links = admPFunc::fileNav('logs','sms');
	$dop->sobar("公众号管理 $links {$umsg}",50,array());
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>ID</th><th>类型</th><th>状态</th><th>appid:配置</th>"; //<th>活动时间</th>
	echo "<th>菜单</th><th>关注者</th><th>消息</th><th>关键字</th><th>调试</tr>\n"; //
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $typeu = $types[$r['type']]; 
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$kid</td>\n";
		  echo "<td class='tc' title='{$r['type']}'>$typeu</td>\n";
		  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
		  #echo "<td class='tc'>".date('Y-m-d H:i',$r['acexp'])."</td>\n";
		  //echo ;配置
		  echo $cv->Url($r['appid'],1,"$aurl[1]&view=form&kid=$kid","修改配置",480,360);
		  echo "<td class='tc'><a href='?file=awex/wex_menu&wekid=$r[kid]' target='_blank'>菜单</a></td>\n";
		  echo "<td class='tc'><a href='?file=awex/wex_user&wekid=$r[kid]' target='_blank'>关注者</a></td>\n";
		  echo "<td class='tc'><a href='?file=awex/wex_msg3&wekid=$r[kid]' target='_blank'>消息</a></td>\n";
		  echo "<td class='tc'><a href='?file=awex/wex_rkey&wekid=$r[kid]' target='_blank'>关键字</a></td>\n";
		  echo "<td class='tc'><a href='".PATH_ROOT."/a3rd/weixin_pay/wedebug.php?kid=$kid' target='_blank'>调试</a></td>\n";
		  echo "</tr>";
		}
		$exops = "\nlocate|清理地理位置\nmsgget|清理接收信息\nmsgsend|清理发送信息";
		$dop->pgbar($idfirst,$idend,$ops="clearact|清除act凭据\nclrqrtik|清除qrcode缓存$exops\ndelete|*删除appid配置");
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));
		
}elseif($view=='form'){

	if(!empty($bsend)){
		$appid = $fm['appid'];
		
		if($kid=='is__add'){
			$kid = $fm['kid'];
			if($db->table($tabid)->where("appid='$appid' OR kid='$kid'")->find()){
				$msg = "该条目[$appid/$fmkid]已被占用！";
			}else{
				$msg = '添加成功！'; //$fm['type'] = 'test';
				$db->table($tabid)->data(basReq::in($fm))->insert();
			}
		}else{
			unset($fm['kid']);
			$db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
			$msg = '更新成功！';
		} 
		basMsg::show($msg);	//,'Redir'?file=$file&mod=$mod
	}else{

		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$def = array('kid'=>'','type'=>'test','enable'=>'1','token'=>'','appid'=>'','appsecret'=>'','qrcode'=>'',);
			foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }		
		}
		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= "启用<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$ienable .= " &nbsp; 类型<select id='fm[type]' name='fm[type]' type='text'>";
		$ienable .= basElm::setOption($types,$fm['type'])."</select>";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "url='".PATH_ROOT."/plus/api/wechat.php?actys=kidExists' tip='字母开头,允许字母数字下划线<br>允许3-12字符,建议4-5字符'";
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='12' reg='key:3-12' $vstr />$ienable");
		}
		
		glbHtml::fmae_row('token',"<input name='fm[token]' type='text' value='$fm[token]' class='txt w320' maxlength='96' reg='str:3-96' tip='与公众平台一致' />");
		$vstr = "url='".PATH_ROOT."/plus/api/wechat.php?actys=appidExists&oldval=".@$fm['appid']."' tip='wx开头,与公众平台一致'";
		glbHtml::fmae_row('appid',"<input name='fm[appid]' type='text' value='$fm[appid]' class='txt w320' maxlength='96' reg='str:3-96' $vstr/>");
		glbHtml::fmae_row('appsecret',"<input name='fm[appsecret]' type='text' value='$fm[appsecret]' class='txt w320' maxlength='96' reg='str:3-96' tip='与公众平台一致' />");
		glbHtml::fmae_row('二维码',"<input name='fm[qrcode]' type='text' value='$fm[qrcode]' class='txt w320' maxlength='96' />");

		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));
	}
	
}
?>
