<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');
$ocfgs = read('outdb','ex');
$tabid = 'exd_crawl';
$job = req("job"); 
$jcfg = exdBase::getJCfgs('crawl',$job); //print_r($jcfg);

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
					$db->table('exd_crlog')->where("kid='$id'")->delete();
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

	include(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
	$list = $db->table($tabid)->order('top')->select(); 
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_model')."</th><th>".lang('flow.title_field')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th>Url</th><th>Url</th><th>".lang('flow.dops_exeu')."</th><th>".lang('flow.title_copy')."</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  $mdname = $_groups[$r['mod']]['title'];
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'>$mdname</td>\n";
	  echo $cv->Url(lang('flow.title_cfg'),1,"?file=$file&mod=$r[mod]&view=fields&job=$r[kid]&recbk=ref","");
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo $cv->Url(lang('flow.title_set'),1,"$aurl[1]&view=urlset&job=$r[kid]&recbk=ref",'');
	  echo $cv->Url(lang('flow.oi_logs'),1,"$aurl[1]&view=urlist&job=$r[kid]&recbk=ref",''); 
	  echo $cv->Url(lang('flow.cw_crawl'),1,PATH_ROOT."/plus/ajax/exdb.php?act=crawl&mod=$r[mod]&job=$kid&".exdBase::getJSign(),'blank');
	  echo $cv->Url(lang('flow.title_copy'),1,"?file=binc/exd_copy&mod=exd_oimp&kid=$r[kid]&type=tabid&title=$r[title]",lang('flow.oi_copy'),480,360); 
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='11'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
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
			'mod'=>'news','stype'=>'','limit'=>'10',
		);
		foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }
		if($_groups[$fm['mod']]['pid']=='docs'){
			if(empty($fm['fskip']))	$fm['fskip'] = "color\nrel_doc\njump";
			//if(empty($fm['fdefs']))	$fm['fdefs'] = "catid=c1234";
		}elseif($_groups[$mod]['pid']=='users'){
			//if(empty($fm['fdefs']))	$fm['fdefs'] = "grade=g1234";
		} //print_r($_groups);
		
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
		$marr = admPFunc::modList(array('docs','users','coms',),'relmod'); 
		$mopt = basElm::setOption($marr,$fm['mod']);		
		$slimit = " &nbsp; limit<input name='fm[limit]' type='text' value='$fm[limit]' class='txt w60' maxlength='5' reg='n+i' tip='".lang('flow.cw_batch')."' />";
		glbHtml::fmae_row(lang('flow.title_model'),"<select name='fm[mod]' class='w150'>$mopt</select>$slimit");
		
		glbHtml::fmae_row(lang('flow.cw_fskip'),"<textarea name='fm[fskip]' rows='5' cols='50' wrap='wrap'>$fm[fskip]</textarea>");
		glbHtml::fmae_row(lang('flow.title_defval'),"<textarea name='fm[fdefs]' rows='5' cols='50' wrap='wrap'>$fm[fdefs]</textarea>");
		glbHtml::fmae_row(lang('flow.title_note'),"<textarea rows='3' cols='50' wrap='wrap'>".lang('flow.exd_skdef')."fieldname=fieldvalue</textarea>");
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
		glbHtml::fmt_end(array("mod|$mod","kid|".(empty($kid) ? 'is__add' : $kid)));
	}

}elseif(in_array($view,array('urlset'))){ 
	
	include(dirname(dirname(__FILE__)).'/binc/exd_inc1.php'); 
	
	if(!empty($bsend)){
		$msg = lang('flow.msg_upd');
		unset($fm['kid']); 
		exdBase::fldSave($fm,3);
		$db->table($tabid)->data(basReq::in($fm))->where("kid='$job'")->update();
		basMsg::show($msg,"Redir","?file=$file&view=$view&job=$job&mod=$mod&flag=v1"); 
	}

	echo "<div class='h02'>&nbsp;</div>";
	$fm = $jcfg; $fa = array('orgtg1','orgtg2','orgtg3',);
	foreach($fa as $k){ 
		$fm[$k] = basStr::filForm($fm[$k]); //echo "\n\n<br>$jcfg[$k]\n<br>$fm[$k]";
	}
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	echo "\n<tr><th class='tc w150'>$fm[title]</th>\n<th class='tl'>".lang('flow.cw_urlfrom')."</th></tr>\n";
	glbHtml::fmae_row(lang('flow.dops_itemname'),"$fm[title]");
	
	$url = PATH_ROOT."/plus/ajax/exdb.php?act=crawl&mod=$fm[mod]&job=$job&debug=links&".exdBase::getJSign();
	glbHtml::fmae_row(lang('flow.cw_demodetail'),"<input name='fm[odmp]' type='text' value='$fm[odmp]' class='txt w400' maxlength='240' reg='str:12-240' tip='Urleg: http://txjia.com/tip/?2016-1J-DE2G' />");
	glbHtml::fmae_row(lang('flow.cw_baselist'),"<input name='fm[ourl]' type='text' value='$fm[ourl]' class='txt w400' maxlength='240' reg='str:12-240' tip='Urleg: http://txjia.com/tip/?Page=(*)' />");
	glbHtml::fmae_row(lang('flow.cw_pgsrule'),"<input name='fm[opno]' type='text' value='$fm[opno]' class='txt w400' maxlength='240' reg='str:1-120' tip='eg: 1-23' />");
	glbHtml::fmae_row(lang('flow.cw_listdebug'),"<a href='".str_replace('(*)','1',$jcfg['ourl'])."' target='_blank'>".lang('flow.cw_baselist')."</a> # <a href='$url' target='_blank'>".lang('flow.cw_deetlist')."</a> ");
	glbHtml::fmae_row(lang('flow.cw_donelog'),"<textarea name='fm[oplog]' rows='3' cols='50' wrap='wrap'>$fm[oplog]</textarea>");
	
	echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>".lang('flow.cw_getlistrule')."</th></tr>\n";
	exdBase::fldForm($fm,3);

    glbHtml::fmae_row(lang('flow.cw_urlinc'),"<input name='fm[ohas]' type='text' value='$fm[ohas]' class='txt w400' maxlength='240' />");
	glbHtml::fmae_row(lang('flow.cw_urlnoi'),"<textarea name='fm[oskip]' rows='3' cols='50' wrap='wrap'>$fm[oskip]</textarea>");
	glbHtml::fmae_row(lang('flow.cw_urlrep'),"<textarea name='fm[orep]' rows='3' cols='50' wrap='wrap'>$fm[orep]</textarea>");
	glbHtml::fmae_row(lang('flow.cw_urlroot'),"<input name='fm[oroot]' type='text' value='$fm[oroot]' class='txt w400' maxlength='240' />");

	echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>".lang('flow.exd_reurl')."</th></tr>\n";
	$detail = lang('flow.exd_rutip')."{$_cbase['server']['txmao']}/dev.php?"; // xxxx 
	glbHtml::fmae_row(lang('flow.title_note'),"<textarea rows='3' cols='50' wrap='wrap'>$detail</textarea>");
	glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
	glbHtml::fmt_end(array("mod|$mod","job|$job"));
	
}elseif(in_array($view,array('urlist'))){ 
	
	include(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
	$cfg = array(
		'sofields'=>array('sysid','outurl'),
		'soorders'=>basLang::ucfg('cfgbase.ord_com2'),
		//'soarea'=>array('jifen','数量'),
		'kid'=>'sysid',
	);
	$dop = new dopExtra('exd_crlog',$cfg); //print_r($dop); 
	
	// 删除操作
	if(!empty($bsend)){
		$vbak = $view;
		$view = 'del_b3';
		require(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
		$view = $vbak;
	} 
	
	$umsg = $msg ? "<span class='cF00'>$msg</span>" : '';
	$dop->so->whrstr .= " AND `kid` ='$job'";
	$dop->sobar(lang('flow.cw_crlogs')." $msg",50,'-1',array('job'=>$job));

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>SysID</th><th>OutUrl</th><th>Done</th><th>atime</th><th>etime</th><th>".lang('flow.op_upd')."</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['sysid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$r[sysid]</td>\n";
		  echo $cv->Url('Link',1,"$r[outurl]",'blank');
		  echo "<td class='tc'>$r[done]</td>\n";
		  echo $cv->Time($r['atime'],$td=1);
		  echo $cv->Time($r['etime'],$td=1);
		  echo $cv->Url(lang('flow.op_upd'),1,PATH_ROOT."/plus/ajax/exdb.php?act=crawl&mod=$jcfg[mod]&job=$r[kid]&sysid=$r[sysid]&".exdBase::getJSign(),'blank');
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod","job|$job","view|$view"));
	
}elseif(in_array($view,array('fields','fset'))){ 
	include(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
}

?>
