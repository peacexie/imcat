<?php
(!defined('RUN_MODE')) && die('No Init');

$msg = ''; $tabext = '';

if($view=='clear'){
	$msg = "清理成功！";
	/*if($mod=='coitem'){
		$pids = glbDBExt::getKids('corder','title','1=1'); 
		$db->table($dop->tbid)->where("ordid NOT IN($pids)")->delete(); 
	}else{
		$db->table($dop->tbid)->where("atime<'".($_cbase['run']['stamp']-3*86400)."'")->delete(); 
	}*/
	$view = 'list';
}

if($view=='list'){
	if(!empty($bsend)){
		
		$fs_do = basReq::val('fs_do');
		$fs = basReq::arr('fs'); 
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作记录！";
		$cnt = 0; $msgop = '';
		foreach($fs as $id=>$v){ 
			if(in_array($fs_do,array('show','hidden'))){ 
				$cnt += $dop->opShow($id,$fs_do);
				$msgop = $fs_do=='show' ? '审核' : '隐藏';
			}elseif($fs_do=='del'){ 
				$cnt += $dop->opDelete($id);
				$db->table('coms_coitem')->where("title='$id'")->delete(); 
				$msgop = '删除';
			}elseif(strstr($fs_do,'set_')){ 
				$v = basStr::filKey(str_replace('set_','',$fs_do),'_-.');
				$cnt += $db->table($dop->tbid)->data(array('ordstat'=>$v))->where("$dop->_kid='$id'")->update();
			}
		}
		$msg = "$cnt 条记录 $msgop 成功！";
	} 
	
	$sbar = "\n".$so->Type(90,'-pKey-'); 
	$sbar .= "\n&nbsp; ".$so->Word(80,80,'-筛选-');
	$sbar .= "\n&nbsp; ".$so->Field('ordstat',60);
	$sbar .= "\n&nbsp; ".$so->Order(array('cid' => '账号(降)','cid-a' => '账号(升)',));
	$snav = admPFunc::fileNav($mod,'ordnav'); $msg = $msg ? "<span class='cF00'>$msg</span>" : ' '; 
	$so->Form($sbar,$dop->msgBar($snav,$msg),40);

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选</th><th>订单号</th><th>状态</th><th>总额</th><th>数量</th><th>货品额</th>"; 
	echo "<th>跟踪号</th>"; 
	echo "<th>会员名称</th><th>电话</th>"; 
	echo "<th>添加</th><th>修改</th>\n</tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $cid = $idend = $r['cid'];
		  if(empty($idfirst)) $idfirst = $cid;
		  echo $cv->Select($cid);
		  //echo $cv->Field($r['title'],1,64);  
		  echo $cv->Url($r['title'],1,vopUrl::fout("chn:0",'')."?mkv=ocar-invoce&ordid=$cid","blank");
		  echo $cv->TKeys($r,1,'ordstat',12,'-');
		  echo $cv->Field($r['feetotle']);
		  echo $cv->Field($r['ordcnt']);
		  echo $cv->Field($r['feeamount']);
		  echo $cv->Field($r['trakeno'],1,64);
		  echo $cv->Field($r['mname']);
		  echo $cv->Field($r['mtel'],1,16);
		  echo $cv->Time($r['atime']);
		  echo $cv->Url('修改',1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
		  echo "</tr>"; 
		}
		$pg = $dop->pg->show($idfirst,$idend); 
		$op = "".basElm::setOption("del|删除".($cv->set_opts('ordstat'))."",'','-批量操作-'); //\ndnow|删除当前
		dopFunc::pageBar($pg." &nbsp; <a href='$aurl[1]&view=clear'>清理</a>",$op);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='form'){
	if(!empty($bsend)){
		require(dopFunc::modAct($_scdir,'form_do',$mod,$dop->type));
	}else{
		require(dopFunc::modAct($_scdir,'form_show',$mod,$dop->type));
	}
}elseif($view=='set'){
	;//
}
