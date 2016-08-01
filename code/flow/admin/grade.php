<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','admin/grade.php');

$mod = empty($mod) ? 'adminer' : $mod;
$view = empty($view) ? 'glist' : $view;
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end('参数错误:mod@grade.php'); 
$gbar = admAFunc::grpNav('users',$mod); 
$cfg = glbConfig::read($mod); 
$tabid = "base_grade";
//print_r(comTypes::getSubs($cfg['i'],'hn','3'));

if($view=='glist'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作项！";
		else{
			foreach($fs as $id=>$v){ //upd|更新\ndel|删除\nenable|启用\nstop|禁用
				$msg = "设置成功！";
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
 	
	$lnkadd = "<a href='$aurl[1]&view=gform' onclick='return winOpen(this,\"增加条目-在[$gname]\");'>增加条目&gt;&gt;</a>";
	glbHtml::tab_bar("[等级权限] :: $gname<span class='span ph5'>|</span>$lnkadd",$gbar,35);
	
	$_ex_paras = glbConfig::read('paras','ex');
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>排序</th><th>启用</th>";
	echo "<th>权限</th><th>修改</th>";
	if(in_array($mod,$_ex_paras['grade'])) echo "<th>参数</th>";
	echo "<th class='wp15'>备注</th>\n";
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
	  echo "<td class='tc'>".($kid=='supper' ? '设置' : "<a href='$aurl[1]&view=set&kid=$r[kid]'>设置</a>")."</td>\n";
	  echo "<td class='tc'><a href='$aurl[1]&view=gform&kid=$r[kid]' onclick='return winOpen(this,\"修改条目-$r[title]\");'>修改</a></td>\n";
	  if(in_array($mod,$_ex_paras['grade'])) echo "<td class='tc'>".("<a href='?file=admin/fields&mod=$mod&catid=$r[kid]'>设置</a>")."</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='gform'){

	if(!empty($bsend)){
		if($kid=='_isadd_'){
			if($db->table($tabid)->where("kid='$fm[kid]'")->find()){
				$msg = "该条目[$fm[kid]]已被占用！";
			}else{
				$msg = '添加成功！';  
				$db->table($tabid)->data(basReq::in($fm))->insert();
				$id = $fm['kid'];	
			}
		}else{
			$msg = '更新成功！'; 
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
		$ienable .= "启用<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; 顺序<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=keyExists&mod=&tab=$tabid' tip='字母开头,允许字母数字下划线<br>允许4-12字符,建议4-5字符'";
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
		}
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");
		glbHtml::fmae_row('配置数组',"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>格式:键=值,一行一个；");
		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","kid|".(empty($kid) ? '_isadd_' : $kid)));
	}

}elseif($view=='set'){

	$parts = basReq::val('parts','pmod'); 
	if(!empty($bsend)){
		$v = empty($fm['prmcb']) ? '' : implode(',',array_filter($fm['prmcb']));
		$db->table($tabid)->data(basReq::in(array($parts=>$v)))->where("kid='$kid'")->update();
		echo basJscss::Alert('更新成功！','Redir',$aurl[1]);
	}else{
		$row = $db->table($tabid)->where("kid='$kid'")->find(); 
		$title = $row['title']; //company,govern,apimail
		$lnkbak = "<a href='?file=$file&mod=$mod'>&lt;&lt;返回[$gname]等级列表</a>";
		$lpart1 = " | "; $lpart2 = " | ";
		$pcfg1 = array('pmod'=>'模块','padd'=>'增加','pdel'=>'删除','pcheck'=>'审核');
		foreach($pcfg1 as $k=>$v) { $lpart1 .= "<a href='?file=$file&mod=$mod&view=set&kid=$kid&parts=$k' ".(($parts==$k) ? 'class="cur"' : '').">$v</a> | "; }
		$pcfg2 = array('pmadm'=>'菜单','pmusr'=>'会员','pfile'=>'脚本','pextra'=>'附加');
		foreach($pcfg2 as $k=>$v) { $lpart2 .= "<a href='?file=$file&mod=$mod&view=set&kid=$kid&parts=$k' ".(($parts==$k) ? 'class="cur"' : '').">$v</a> | "; }
		glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$title]权限编辑","$lpart1<br>$lpart2",40); //-
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
			$_muadm = glbConfig::read('muadm'); 
			$a0 = $_muadm['i']; $a2 = array();
			foreach($a0 as $k2=>$v2){
				if($v2['pid']=='0') $a2[$k2] = $v2['title'];
			}
			glbHtml::fmae_row('顶级菜单',basElm::setCBox("prmcb",$a2,$pmstr));
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
			$_mumem = glbConfig::read('mumem'); 
			$a0 = $_mumem['i']; $i = 0;
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
			$_kmd = glbConfig::read(str_replace('_','',$_key)); 
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
		glbHtml::fmae_send('bsend','提交',in_array($parts,array('pmadm','pmusr')) ? ($parts=='pmusr' ? '20' : 25) : 15);
		glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","","kid|".(empty($kid) ? '_isadd_' : $kid)));
	}
}

?>
