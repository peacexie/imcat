<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');
$ocfgs = glbConfig::read('outdb','ex');
$tabid = 'exd_psyn'; 
$job = basReq::val("job"); 
$jcfg = exdBase::getJCfgs('psyn',$job); //print_r($jcfg); 

if($view=='list'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop');
		if(empty($fs)) $msg = lang('flow.msg_pkitem');
		else{
			foreach($fs as $id=>$v){
				$msg = lang('flow.msg_set');
				if($fs_do=='del'){ 
					$db->table($tabid)->where("kid='$id'")->delete();
					$db->table('exd_pslog')->where("kid='$id'")->delete();
					$db->table('exd_sfield')->where("model='$id'")->delete();
				}elseif($fs_do=='show'){ 
					$db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
				}elseif($fs_do=='upd'){ 
					$db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update();
				}elseif($fs_do=='stop'){ 
					$db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
				}
			}
		}
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
	}

	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.sy_add')."\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
	$links = admPFunc::fileNav($file,'exd_psyn');
	glbHtml::tab_bar(lang('flow.sy_plan')."<span class='span ph5'>|</span>$lnkadd","$links",50);	
	$list = $db->table($tabid)->order('top')->select(); //->where("pid='$pid'")
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_model')."</th><th>api</th><th>limit</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th>".lang('flow.dops_exeu')."</th><th>".lang('flow.oi_logs')."</th><th>".lang('flow.title_copy')."</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  $mdname = $_groups[$r['mod']]['title'];
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'>$mdname</td>\n";
	  echo $cv->Url('ApiUrl',1,"$r[api]",'blank');
	  echo "<td class='tc'>$r[limit]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo $cv->Url(lang('flow.dops_exeu'),1,PATH_ROOT."/plus/ajax/exdb.php?act=psyn&mod=$r[mod]&job=$kid&".exdBase::getJSign(),'blank');
	  echo $cv->Url(lang('flow.oi_logs'),1,"$aurl[1]&view=loglist&job=$r[kid]&recbk=ref",''); 
	  echo $cv->Url(lang('flow.title_copy'),1,"?file=binc/exd_copy&mod=exd_oimp&kid=$r[kid]&type=tabid&title=$r[title]",lang('flow.oi_copy'),480,360); 
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='form'){
	
	if(!empty($bsend)){
		if($kid=='is__add'){
			if($db->table($tabid)->where("kid='$fm[kid]'")->find()){
				$msg = lang('flow.msg_exists',$fm['kid']);
			}else{
				$msg = lang('flow.msg_add');  
				$db->table($tabid)->data(basReq::in($fm))->insert();
				$id = $fm['kid'];	
			}
		}else{
			$msg = lang('flow.msg_upd');
			unset($fm['kid']); 
			$db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
		} 
		basMsg::show($msg);	
	}else{

		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$kid = '';
		}
		$def = array( //api	mod	stype	limit
			'kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','fskip'=>'','fdefs'=>'','cfgs'=>'',
			'api'=>$ocfgs['psyn']['server'],'mod'=>'news','stype'=>'','limit'=>'100',
		);
		foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }
		
		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "tip='".lang('admin.fad_chrsab','4-12')."'"; 
			glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
		} 
		
		glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");
		glbHtml::fmae_row(lang('flow.sy_apiurl'),"<input name='fm[api]' type='text' value='$fm[api]' class='txt w400' maxlength='240' reg='str:12-240' tip='".lang('flow.sy_tipapi')."' />");
		$marr = admPFunc::modList(array('docs','users','coms',),'relmod'); 
		$mopt = basElm::setOption($marr,$fm['mod']);		
		$slimit = " &nbsp; limit<input name='fm[limit]' type='text' value='$fm[limit]' class='txt w60' maxlength='5' reg='n+i' tip='".lang('flow.sy_limit')."' />";
		glbHtml::fmae_row(lang('flow.title_model'),"<select name='fm[mod]' class='w150'>$mopt</select>$slimit");
		if(!empty($kid)){ 
			$mcfg = glbConfig::read($fm['mod']); 
			$topt = comTypes::getOpt($mcfg['i'],$fm['stype'],'',0); 
			glbHtml::fmae_row(lang('flow.sy_type'),"<select name='fm[stype]' class='w150'>$topt</select> ".lang('flow.sy_tipnul'));
		}
		glbHtml::fmae_row(lang('flow.oi_skip'),"<textarea name='fm[fskip]' rows='5' cols='50' wrap='wrap'>$fm[fskip]</textarea>");
		glbHtml::fmae_row(lang('flow.title_defval'),"<textarea name='fm[fdefs]' rows='5' cols='50' wrap='wrap'></textarea>");
		glbHtml::fmae_row(lang('flow.title_note'),"<textarea rows='3' cols='50' wrap='wrap'>".lang('flow.exd_skdef')."fieldname=fieldvalue</textarea>");
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
		glbHtml::fmt_end(array("mod|$mod","kid|".(empty($kid) ? 'is__add' : $kid)));
	}

}elseif(in_array($view,array('loglist'))){ 
	
	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.sy_add')."\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
	$links = admPFunc::fileNav($file,'exd_psyn');
	glbHtml::tab_bar(lang('flow.sy_plan')."<span class='span ph5'>|</span>$lnkadd","$links",50);	
	$cfg = array(
		'sofields'=>array('sysid','outid'),
		'soorders'=>basLang::ucfg('cfgbase.ord_com2'),
		//'soarea'=>array('jifen','数量'),
		'kid'=>'sysid',
	);
	$dop = new dopExtra('exd_pslog',$cfg); //print_r($dop); 
	
	// 删除操作
	if(!empty($bsend)){
		$vbak = $view;
		$view = 'del_b3';
		require(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
		$view = $vbak;
	} 
	
	$umsg = $msg ? "<span class='cF00'>$msg</span>" : '';
	$dop->so->whrstr .= " AND `kid` ='$job'";
	$dop->sobar("[".lang('flow.sy_tlog')."] $msg",50,'-1',array('job'=>$job));

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>SysID</th><th>OutID</th><th>Done</th><th>atime</th><th>etime</th><th>".lang('flow.op_upd')."</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['sysid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$r[sysid]</td>\n";
		  echo "<td class='tc'>$r[outid]</td>\n"; 
		  echo "<td class='tc'>$r[done]</td>\n";
		  echo $cv->Time($r['atime'],$td=1);
		  echo $cv->Time($r['etime'],$td=1);
		  echo $cv->Url(lang('flow.op_upd'),1,PATH_ROOT."/plus/ajax/exdb.php?act=psyn&mod=$mod&job=$r[kid]&sysid=$r[sysid]&".exdBase::getJSign(),'blank');
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod","job|$job","view|$view"));

}

?>
