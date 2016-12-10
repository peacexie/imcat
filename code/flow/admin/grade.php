<?php
(!defined('RUN_INIT')) && die('No Init');
usrPerm::run('pfile','admin/grade.php');

$mod = empty($mod) ? 'adminer' : $mod;
$view = empty($view) ? 'glist' : $view;
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@grade.php'); 
$gbar = admAFunc::grpNav('users',$mod); 
$cfg = read($mod); 
$tabid = "base_grade";
//print_r(comTypes::getSubs($cfg['i'],'hn','3'));

if($view=='glist'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop');
		if(empty($fs)) $msg = lang('flow.msg_pkitem');
		else{
			foreach($fs as $id=>$v){
				$msg = lang('flow.msg_set');
				if($fs_do=='upd'){ 
					$db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update(); 
				}elseif($fs_do=='del'){ 
					//if($db->table($tabid)->where("pid='$id'")->find()){
						//$msg = "{$id}该条目含有子类，请先删除子类！";
					//}else{
						 	
					//}
					$db->table($tabid)->where("kid='$id'")->delete();
				}elseif($fs_do=='show'){ 
					 $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
				}elseif($fs_do=='stop'){ 
					 $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
				}
			}
		}
		glbCUpd::upd_model($mod);
	} 
 	
	$lnkadd = "<a href='$aurl[1]&view=gform' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
	glbHtml::tab_bar(lang('admin.grd_gperm')." :: $gname<span class='span ph5'>|</span>$lnkadd",$gbar,35);
	
	$_ex_paras = read('frame.expars','sy'); 
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th>";
	echo "<th>".lang('flow.title_perm')."</th><th>".lang('flow.title_edit')."</th>";
	if(in_array($mod,$_ex_paras['grade'])) echo "<th>".lang('flow.title_param')."</th>";
	echo "<th class='wp15'>".lang('flow.title_note')."</th>\n";
	echo "</tr>\n";
	$list = $db->table($tabid)->where("model='$mod'")->order('top,kid')->select();
	if($list){
	foreach($list as $r){
	  $kid = $r['kid'];
	  echo "<tr>\n";
	  echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";  
	  echo "<td class='tc'>".($kid=='supper' ? lang('admin.grd_set') : "<a href='$aurl[1]&view=set&kid=$r[kid]'>".lang('flow.title_set')."</a>")."</td>\n";
	  echo "<td class='tc'><a href='$aurl[1]&view=gform&kid=$r[kid]' onclick='return winOpen(this,\"".lang('admin.grd_edit')."-$r[title]\");'>".lang('flow.title_edit')."</a></td>\n";
	  if(in_array($mod,$_ex_paras['grade'])) echo "<td class='tc'>".("<a href='?file=admin/fields&mod=$mod&catid=$r[kid]'>".lang('flow.title_set')."</a>")."</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='gform'){

	if(!empty($bsend)){
		if($kid=='_isadd_'){
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
		glbCUpd::upd_model($mod);
		basMsg::show($msg);	
	}else{
		if(empty($kid)){
			$kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,@$pid);
		}else{
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','cfgs'=>'',);
		foreach($def as $k=>$v){
			if(!isset($fm[$k])) $fm[$k] = $v;
		}
		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=keyExists&mod=&tab=$tabid' tip='".lang('admin.fad_tip41245')."' ";
			glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
		}
		glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");
		glbHtml::fmae_row(lang('flow.fl_cfgtab'),"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>".lang('flow.fl_cfgtip'));
		glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
		glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","kid|".(empty($kid) ? '_isadd_' : $kid)));
	}

}elseif($view=='set'){

	$parts = req('parts','pmod'); 
	if(!empty($bsend)){
		$v = empty($fm['prmcb']) ? '' : implode(',',array_filter($fm['prmcb']));
		$db->table($tabid)->data(basReq::in(array($parts=>$v)))->where("kid='$kid'")->update();
		echo basJscss::Alert(lang('flow.msg_upd'),'Redir',$aurl[1]);
	}else{
		$row = $db->table($tabid)->where("kid='$kid'")->find(); 
		$title = $row['title']; //company,govern,apimail
		$lnkbak = "<a href='?file=$file&mod=$mod'>&lt;&lt;".lang('admin.grd_back')."[$gname]".lang('admin.grd_glist')."</a>";
		$lpart1 = " | "; $lpart2 = " | ";
		$pcfg1 = array('pmod'=>lang('admin.grd_mod'),'padd'=>lang('flow.dops_add'),'pdel'=>lang('flow.dops_del'),'pcheck'=>lang('flow.dops_checked'));
		foreach($pcfg1 as $k=>$v) { $lpart1 .= "<a href='?file=$file&mod=$mod&view=set&kid=$kid&parts=$k' ".(($parts==$k) ? 'class="cur"' : '').">$v</a> | "; }
		$pcfg2 = basLang::ucfg('cfglibs.grset_types');  
		foreach($pcfg2 as $k=>$v) { $lpart2 .= "<a href='?file=$file&mod=$mod&view=set&kid=$kid&parts=$k' ".(($parts==$k) ? 'class="cur"' : '').">$v</a> | "; }
		glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$title]".lang('admin.grd_pedit')."","$lpart1<br>$lpart2",40); //-
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		$pmstr = $row[$parts];  
		if(in_array($parts,array('pmod','padd','pdel','pcheck'))){
			$a0 = array('docs','coms','users','advs','plus');
			foreach($a0 as $k){
				$a2 = array();
				foreach($_groups as $k2=>$v2){
					if($v2['pid']==$k) $a2[$k2] = $v2['title']."($k2)";
				}
				glbHtml::fmae_row($_groups[$k]['title'],basElm::setCBox("prmcb",$a2,$pmstr,5));
			}
		}elseif(in_array($parts,array('pmadm'))){
			$a0 = read('muadm.i'); 
			$a2 = array();
			foreach($a0 as $k2=>$v2){
				if($v2['pid']=='0') $a2[$k2] = $v2['title'];
			}
			glbHtml::fmae_row(lang('admin.grd_tmenu'),basElm::setCBox("prmcb",$a2,$pmstr));
			$i = 0;
			foreach($a0 as $k2=>$v2){ 
			if($v2['deep']=='2'){ $i++; 
				$def = strstr(",$pmstr,",",$k2,") ? " checked='checked' " : '';
				$s2 = "\n<label>".$a0[$v2['pid']]['title']." - <input type='checkbox' class='rdcb' name='fm[prmcb][]' id='fm_prmcb_$i' value='$k2' $def>$v2[title]($k2)</label>";
				$s3 = ''; $j = 0;
				foreach($a0 as $k3=>$v3){ 
				if($v3['pid']==$k2){ $i++; $j++;  
					$def = strstr(",$pmstr,",",$k3,") ? " checked='checked' " : '';
					if($j>4) { $s3 .= "<br>"; $j=1; }
					$s3 .= "\n<label><input type='checkbox' class='rdcb' name='fm[prmcb][]' id='fm_prmcb_$i' value='$k3' $def>$v3[title]($k3)</label>";
				}}
				glbHtml::fmae_row($s2,$s3);
			}}
		}elseif(in_array($parts,array('pmusr'))){
			$a0 = read('mumem.i'); 
			$i = 0;
			foreach($a0 as $k2=>$v2){ 
			if($v2['deep']=='1'){ $i++; 
				$def = strstr(",$pmstr,",",$k2,") ? " checked='checked' " : '';
				$s2 = "\n<label><input type='checkbox' class='rdcb' name='fm[prmcb][]' id='fm_prmcb_$i' value='$k2' $def>$v2[title]($k2)</label>";
				$s3 = ''; $j = 0;
				foreach($a0 as $k3=>$v3){ 
				if($v3['pid']==$k2){ $i++; $j++;  
					$def = strstr(",$pmstr,",",$k3,") ? " checked='checked' " : '';
					if($j>4) { $s3 .= "<br>"; $j=1; }
					$s3 .= "\n<label><input type='checkbox' class='rdcb' name='fm[prmcb][]' id='fm_prmcb_$i' value='$k3' $def>$v3[title]($k3)</label>";
				}}
				glbHtml::fmae_row($s2,$s3);
			}}
		}elseif(in_array($parts,array('pfile','pextra'))){
			$_key = $parts=='pfile' ? '_mupfile' : '_mupext';
			$_kmd = read(str_replace('_','',$_key)); 
			$a0 = $_kmd['i']; $i = 0;
			foreach($a0 as $k2=>$v2){ 
				$s3 = ''; $i++; $j = 0;
				$a3 = basElm::text2arr($v2['cfgs']); 
				foreach($a3 as $k3=>$v3){ $i++; $j++;  
					$def = strstr(",$pmstr,",",$k3,") ? " checked='checked' " : '';
					if($j>3) { $s3 .= "<br>"; $j=1; }
					$s3 .= "\n<label><input type='checkbox' class='rdcb' name='fm[prmcb][]' id='fm_prmcb_$i' value='$k3' $def>$v3($k3)</label>";
				}
				glbHtml::fmae_row("$v2[title]",$s3);
			}
		}
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),in_array($parts,array('pmadm','pmusr')) ? ($parts=='pmusr' ? '20' : 25) : 15);
		glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","","kid|".(empty($kid) ? '_isadd_' : $kid)));
	}
}

?>
