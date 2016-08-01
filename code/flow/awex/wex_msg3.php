<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');

//$mtab = basReq::val('mtab','get'); //get,send,form
$mtab = $view=='list' ? 'get' : $view; 
$tabid = "wex_msg$mtab"; //$weapp

$mlink = "<p class='tc pv5'>
   <a href='?file=awex/wex_msg3&wekid=$wekid&view=list'>接收消息</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid&view=send'>发送记录</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid&view=form' onclick=\"return winOpen(this,'群发信息',780,560);\">群发信息</a>
 
</p>";

//$types = array('test'=>'测试号','chking'=>'未认证','dingyue'=>'订阅号','fuwu'=>'服务号');

if($view=='list'){ 

	$cfg = array(
		'sofields'=>array('openid','detail'), //'appid',
		'soorders'=>array('kid' => 'kid(降)','kid-a' => 'kid(升)'),
		//'soarea'=>array('amount','数量'),
	);
	$dop = new dopExtra($tabid,$cfg); 
	$dop->so->whrstr .= " AND `appid`='$weapp'";
	$dop->order = $dop->so->order = basReq::val('order','kid'); //echo $dop->so->whrstr;
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！"; 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = "请勾选操作记录！";
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	$cnt += $dop->opDelete($id);
			  } 
		  }
		}
		$cnt && $msg = ($cnt>0) ? "$cnt 条记录 删除成功！" : "操作成功！";
	}
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$dop->sobar("$mlink 公众号[$wekid] : 接收消息管理 {$umsg}",40,array(),array('wekid'=>$wekid));
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>类型</th><th>信息内容</th>"; 
	echo "<th>openID</th><th>时间</th><th>回复状态</th><th>现在回复</th></tr>\n"; 
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $time = date('Y-m-d H:i',$r['atime']);
		  if($r['type']=='text'){ $type = '文本'; }
		  elseif($r['type']=='image'){ $type = '图片'; }
		  elseif($r['type']=='news'){ $type = '图文'; }
		  else{ $type = '---'; }
		  if($r['restate']=='Auto'){ $res = '自动'; }
		  elseif($r['restate']=='Kefu'){ $res = '客服'; }
		  else{ $res = '---'; }
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$type</td>\n";
		  echo "<td class='tc'>".basStr::filText(basStr::cutWidth($r['detail'],40))."</td>\n"; 
		  echo "<td class='tc'>$r[openid]</td>\n";
		  echo "<td class='tc'>$time</td>\n";
		  echo "<td class='tc'>$res</td>\n"; 
		  echo $cv->Url('现在回复',1,"?file=awex/wex_msg3&kid=$kid&view=form&wekid=$wekid","回复信息");
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend,"delete|*删除消息记录");
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='send'){ 

	$cfg = array(
		'sofields'=>array('openid','detail'), //'appid',
		'soorders'=>array('kid' => 'kid(降)','kid-a' => 'kid(升)'),
		//'soarea'=>array('amount','数量'),
	);
	$dop = new dopExtra($tabid,$cfg); 
	$dop->so->whrstr .= " AND `appid`='$weapp'";
	$dop->order = $dop->so->order = basReq::val('order','kid'); //echo $dop->so->whrstr;
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！"; 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = "请勾选操作记录！";
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	$cnt += $dop->opDelete($id);
			  } 
		  }
		}
		$cnt && $msg = ($cnt>0) ? "$cnt 条记录 删除成功！" : "操作成功！";
	}
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$dop->sobar("$mlink 公众号[$wekid] : 发送记录管理 {$umsg}",40,array(),array('wekid'=>$wekid));
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>类型</th><th>信息内容</th>"; 
	echo "<th>openID</th><th>时间</th><th>Url</th><th>media</th></tr>\n"; //media_id	picurl
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $time = date('Y-m-d H:i',$r['atime']);
		  if($r['type']=='text'){ $type = '文本'; }
		  elseif($r['type']=='news'){ $type = '图文'; }
		  else{ $type = '---'; }
		  if(!empty($r['media_id'])){
			  $detail = '媒体ID: '.$r['media_id'];
		  }elseif(!empty($r['subject'])){ 
			  $detail = '标题: '.$r['detail'];
		  }else{
		  	  $detail = $r['detail'];
		  }
		  $url = $r['url'] ? "<a href='$r[url]'>url</a>" : '---';
		  $media = $r['media_id'].$r['picurl']; 
		  $media  || $media = '---';
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$type</td>\n";
		  echo "<td class='tc'>".basStr::filText(basStr::cutWidth($r['detail'],40))."</td>\n"; 
		  echo "<td class='tc'>$r[openid]</td>\n";
		  echo "<td class='tc'>$time</td>\n";
		  echo "<td class='tc'>$url</td>\n";
		   echo "<td class='tc'>$media</td>\n";
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend,"delete|*删除消息记录");
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));
		
}elseif($view=='form'){

	$kid = basReq::val('kid',''); 
	$openid = basReq::val('openid','');
	$msgtype = basReq::val('msgtype',''); //print_r($fm);
	$groupid = basReq::val('groupid','');
	$doend = basReq::val('doend','');
	//echo "$kid,$openid";
	if(!empty($bsend)){	
		$detail = $fm['detail']; 
		$fm['atime'] = $_cbase['run']['stamp'];
		$fm['appid'] = $wecfg['appid'];
		$fm['kid'] = basKeyid::kidTemp();
		$fm['type'] = $msgtype;
		if($openid && $detail){
			$weixin = new wmpMsgsend($wecfg);
			$data = $weixin->sendText($openid,stripslashes($detail));
			$msg = "发送/回复信息".($data['errcode'] ? "失败<br>([$data[errcode]]$data[errmsg])" : '成功！');
			//记录... 
			if(empty($data['errcode'])){
				$kid && $db->table('wex_msgget')->data(array('restate'=>'Kefu'))->where("kid='$kid'")->update();
				$fm['openid'] = $openid;
				$db->table('wex_msgsend')->data(basReq::in($fm))->insert();
			}
		}elseif(!empty($groupid) && $detail){ 
			$weixin = new wmpMsgmass($wecfg);
			$g2id = $groupid==-1 ? 0 : $groupid; 
			$data = $weixin->sendText(stripslashes($detail),$g2id);
			$msg = "群发信息".($data['errcode'] ? "失败<br>([$data[errcode]]$data[errmsg])" : '成功！');
			//记录... 
			if(empty($data['errcode'])){
				$fm['openid'] = $groupid;
				$db->table('wex_msgsend')->data(basReq::in($fm))->insert();
			}
		}else{
			$msg = '缺少参数！';	
		}
		if($doend){
			basMsg::show("<p class='tc'>$msg<br>请关闭窗口<p>",'die');
		}else{
			basMsg::show($msg);
		}
	}else{

		if($kid){
			$rmsg = $db->table('wex_msgget')->where("kid='$kid'")->find();	
			$openid = $rmsg['openid'];
		}
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		
		if(!empty($openid)){
			define('WERR_RETURN',1);
			$weixin = new wmpUser($wecfg);
			$udata = $weixin->getUserInfo($openid); //print_r($udata);
			if(!empty($udata['errcode'])){
				$utitle = "获取用户信息失败：".$udata['errcode']." <br>(".$udata['message'];
			}else{
				$utitle = "[".@$udata['city']."]".@$udata['nickname']."";
			}
			glbHtml::fmae_row('客服回复',"$utitle");
		}else{
			$weixin = new wmpUser($wecfg); //print_r($wecfg);
			$data = $weixin->groupList(); $garr = array();
			foreach($data['groups'] as $k=>$v){
				$k2 = $v['id'] ? $v['id'] : '-1';
				$gname = $k ? "群发分组:$k2-{$v['name']}[{$v['count']}人]" : "群发所有用户:(含未分组:[{$v['count']}]人)";
				$garr[$k2] = $gname;
			} // [id] => 0 [name] => 未分组[count] => 1
			$item = "<select id='groupid' name='groupid' type='text' reg='str:1-12' tip='请选择'>";
			$item .= basElm::setOption($garr,0)."</select>"; 
			glbHtml::fmae_row('群发对象',"$item");
		}
		glbHtml::fmae_row('信息内容',"<textarea name='fm[detail]' rows='6' cols='50' wrap='wrap'></textarea>");
		if(!empty($kid)){
			glbHtml::fmae_row('原消息',"<textarea name='fm_org[detail]' rows='6' cols='50' wrap='wrap' readonly disabled>$rmsg[detail]</textarea>");
		}
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("kid|$kid","openid|$openid","msgtype|text","doend|$doend"));
		//echo basJscss::jscode('wxKwdsetSence(1,1);');
	}
}
?>
