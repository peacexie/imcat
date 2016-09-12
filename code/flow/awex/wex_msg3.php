<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');

//$mtab = basReq::val('mtab','get'); //get,send,form
$mtab = $view=='list' ? 'get' : $view; 
$tabid = "wex_msg$mtab"; //$weapp

$cmsg = basLang::ucfg('cfgbase.wx_nmsg');
$tmsg = basLang::ucfg('cfgbase.wx_tmsg');

$mlink = "<p class='tc pv5'>
   <a href='?file=awex/wex_msg3&wekid=$wekid&view=list'>$cmsg[get]</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid&view=send'>$cmsg[send]</a>
 # <a href='?file=awex/wex_msg3&wekid=$wekid&view=form' onclick=\"return winOpen(this,'$cmsg[ms]',780,560);\">$cmsg[ms]</a>
 
</p>";

if($view=='list'){ 

	$cfg = array(
		'sofields'=>array('openid','detail'), //'appid',
		'soorders'=>array('kid' => 'kid(D)','kid-a' => 'kid(A)'),
		//'soarea'=>array('amount','数量'),
	);
	$dop = new dopExtra($tabid,$cfg); 
	$dop->so->whrstr .= " AND `appid`='$weapp'";
	$dop->order = $dop->so->order = basReq::val('order','kid'); //echo $dop->so->whrstr;
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop'); 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = lang('flow.dops_setitem');
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	$cnt += $dop->opDelete($id);
			  } 
		  }
		}
		$cnt && $msg = ($cnt>0) ? "$cnt ".lang('flow.dops_delok') : lang('flow.dops_opok');
	} 
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$dop->sobar("$mlink ".lang('awex.appid')."[$wekid] : ".lang('awex.m3_getlogs')." {$umsg}",40,array(),array('wekid'=>$wekid));

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>".lang('flow.title_type')."</th><th>$cmsg[ct]</th>"; 
	echo "<th>openID</th><th>".lang('awex.m3_time')."</th><th>$cmsg[st]</th><th>$cmsg[nrep]</th></tr>\n"; 
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $time = date('Y-m-d H:i',$r['atime']);
		  if($r['type']=='text'){ $type = $tmsg['text']; }
		  elseif($r['type']=='image'){ $type = $tmsg['image']; }
		  elseif($r['type']=='news'){ $type = $tmsg['news']; }
		  else{ $type = '---'; }
		  if($r['restate']=='Auto'){ $res = $tmsg['Auto']; }
		  elseif($r['restate']=='Kefu'){ $res = $tmsg['Kefu']; }
		  else{ $res = '---'; }
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$type</td>\n";
		  echo "<td class='tc'>".basStr::filText(basStr::cutWidth($r['detail'],40))."</td>\n"; 
		  echo "<td class='tc'>$r[openid]</td>\n";
		  echo "<td class='tc'>$time</td>\n";
		  echo "<td class='tc'>$res</td>\n"; 
		  echo $cv->Url($cmsg['nrep'],1,"?file=awex/wex_msg3&kid=$kid&view=form&wekid=$wekid",$cmsg['rep']);
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend,"delete|*".lang('awex.m3_delmsg'));
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='send'){ 

	$cfg = array(
		'sofields'=>array('openid','detail'), //'appid',
		'soorders'=>array('kid' => 'kid(D)','kid-a' => 'kid(A)'),
		//'soarea'=>array('amount','数量'),
	);
	$dop = new dopExtra($tabid,$cfg); 
	$dop->so->whrstr .= " AND `appid`='$weapp'";
	$dop->order = $dop->so->order = basReq::val('order','kid'); //echo $dop->so->whrstr;
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop'); 
		if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = lang('flow.dops_setitem');
		$cnt = 0; 
		if(empty($msg)){
		  if($fs_do=='delete'){ 
			  foreach($fs as $id=>$v){ 
			  	$cnt += $dop->opDelete($id);
			  } 
		  }
		}
		$cnt && $msg = ($cnt>0) ? "$cnt ".lang('flow.dops_delok') : lang('flow.dops_opok');
	}
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$dop->sobar("$mlink ".lang('awex.appid')."[$wekid] : ".lang('awex.m3_mslogs')." {$umsg}",40,array(),array('wekid'=>$wekid));
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>".lang('flow.title_type')."</th><th>$cmsg[ct]</th>"; 
	echo "<th>openID</th><th>".lang('awex.m3_time')."</th><th>Url</th><th>media</th></tr>\n"; //media_id	picurl
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $time = date('Y-m-d H:i',$r['atime']);
		  if($r['type']=='text'){ $type = $tmsg['text']; }
		  elseif($r['type']=='news'){ $type = $tmsg['news']; }
		  else{ $type = '---'; }
		  if(!empty($r['media_id'])){
			  $detail = lang('awex.m3_mdid').': '.$r['media_id'];
		  }elseif(!empty($r['subject'])){ 
			  $detail = lang('awex.m3_title').': '.$r['detail'];
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
		$dop->pgbar($idfirst,$idend,"delete|*".lang('awex.m3_delmsg'));
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
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
			$msg = lang('awex.m3_srep').($data['errcode'] ? lang('awex.fail')."<br>([$data[errcode]]$data[errmsg])" : lang('awex.success'));
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
			$msg = lang('awex.m3_msend').($data['errcode'] ? lang('awex.fail')."<br>([$data[errcode]]$data[errmsg])" : lang('awex.success'));
			//记录... 
			if(empty($data['errcode'])){
				$fm['openid'] = $groupid;
				$db->table('wex_msgsend')->data(basReq::in($fm))->insert();
			}
		}else{
			$msg = lang('awex.m3_mparam');	
		}
		if($doend){
			basMsg::show("<p class='tc'>$msg<br>".lang('awex.close')."<p>",'die');
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
				$utitle = lang('awex.m3_guing').$udata['errcode']." <br>(".$udata['message'];
			}else{
				$utitle = "[".@$udata['city']."]".@$udata['nickname']."";
			}
			glbHtml::fmae_row($cmsg['srep'],"$utitle");
		}else{
			$weixin = new wmpUser($wecfg); //print_r($wecfg);
			$data = $weixin->groupList(); $garr = array();
			foreach($data['groups'] as $k=>$v){
				$k2 = $v['id'] ? $v['id'] : '-1';
				$gname = $k ? lang('awex.m3_togroup',"$k2-{$v['name']}[{$v['count']}]") : lang('awex.m3_toall',$v['count']);
				$garr[$k2] = $gname;
			} // [id] => 0 [name] => 未分组[count] => 1
			$item = "<select id='groupid' name='groupid' type='text' reg='str:1-12' tip='".lang('awex.m3_select')."'>";
			$item .= basElm::setOption($garr,0)."</select>"; 
			glbHtml::fmae_row(lang('awex.m3_msto'),"$item");
		}
		glbHtml::fmae_row($cmsg['ct'],"<textarea name='fm[detail]' rows='6' cols='50' wrap='wrap'></textarea>");
		if(!empty($kid)){
			glbHtml::fmae_row(lang('awex.m3_orgmsg'),"<textarea name='fm_org[detail]' rows='6' cols='50' wrap='wrap' readonly disabled>$rmsg[detail]</textarea>");
		}
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
		glbHtml::fmt_end(array("kid|$kid","openid|$openid","msgtype|text","doend|$doend"));
		//echo basJscss::jscode('wxKwdsetSence(1,1);');
	}
}
?>	
