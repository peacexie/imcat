<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)');

$view = empty($view) ? 'list' : $view;
$tabid = 'bext_relat';
$list = $db->table($tabid)->order('top,kid')->select(); 
$gbar = ''; $ggap = ''; $cfg = array();
foreach($list as $r){
	$gbar .= "$ggap<a href='?file=$file&view=set&parts=$r[kid]' ".($parts==$r['kid'] ? 'class="cur"' : '').">$r[title]</a>";
	$ggap = ' | ';
	if($parts==$r['kid']){ 
		$cfg = $r;
		$restr = $cfg['cfgs']; $rearr = basElm::text2arr($restr); 
	}
} //print_r($cfg); //echo $aurl[1];

if($view=='upd'){
	$re = glbCUpd::upd_relat();
	print_r($re);	
	die();
}elseif($view=='list'){
	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作项！";
		foreach($fs as $id=>$v){ //upd|更新\ndel|删除\nenable|启用\nstop|禁用
			$msg = "设置成功！";
			if($fs_do=='upd'){ 
				$db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update(); 
			}elseif($fs_do=='del'){ 
				$db->table($tabid)->where("kid='$id'")->delete(); 	
			}elseif($fs_do=='show'){ 
				 $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
			}elseif($fs_do=='stop'){ 
				 $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
			}elseif($fs_do=='chupd'){ 
				 $char = strtoupper(comConvert::pinyinMain($fm[$id]['title'],3,1));
				 $db->table($tabid)->data(array('char'=>$char))->where("kid='$id'")->update();
			}
		}
		//glbCUpd::upd_model($mod);
		basMsg::show($msg,'Back','');	
	} 
	$lnkupd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"更新\",320,240);'>更新</a>";
	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"增加条目\");'>增加条目&gt;&gt;</a>";
	glbHtml::tab_bar("$lnkupd<span class='span ph5'>|</span>关联项目管理<span class='span ph5'>|</span>$lnkadd",$gbar,25);
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>排序</th><th>启用</th>";
	echo "<th>类别/栏目(1)</th><th>类别/栏目(2)</th><th>设置</th>";
	echo "<th>修改</th><th class='wp15'>备注</th>\n";
	echo "</tr>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  $u_sub = strstr($aurl[1],'pid=') ? basReq::getURep($aurl[1],'pid',$r['kid']) : "$aurl[1]&pid=$r[kid]";
	  echo "<tr>\n";
	  echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";   
	  echo "<td class='tc'>".@$_groups[$r['mod1']]['title']."</td>\n";
	  echo "<td class='tc'>".@$_groups[$r['mod2']]['title']."</td>\n";
	  echo "<td class='tc'><a href='?file=$file&view=set&parts=$r[kid]'>设置</a></td>\n";
	  echo "<td class='tc'><a href='$aurl[1]&view=form&kid=$r[kid]' onclick='return winOpen(this,\"修改条目\");'>修改</a></td>\n"; //
	  echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end();	
}elseif($view=='form'){
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
		//glbCUpd::upd_model($mod);
		basMsg::show($msg);	
	}else{
		if(empty($kid)){
			$kid = ''; $did = glbDBExt::dbNxtID($tabid,'relat',0);
		}else{
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','cfgs'=>'','mod1'=>'','mod2'=>'',);
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
			$vstr = "tip='字母开头,允许字母数字下划线<br>允许2-12字符,建议4-5字符'"; //url='".PATH_ROOT."/plus/ajax/cajax.php?act=keyExists&mod=$mod&tab=$tabid' 
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:2-12' $vstr />$ienable");
		}
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");	
		$pold = '(x)'; $b = array('docs'=>'文档','types'=>'类别');
		foreach($_groups as $k=>$v){ 
		if(in_array($v['pid'],array('docs','types'))){
			if($pold!=$v['pid']) $a["^group^$v[pid]"] = $b[$v['pid']];
			$a[$k] = ' &nbsp; &nbsp; '.$v['title'];
			$pold = $v['pid'];
		} }
		$s1 = "<select name='fm[mod1]' reg='str:2-12' tip='请选择'>".basElm::setOption($a,$fm['mod1'])."</select>";
		$s2 = "<select name='fm[mod2]' reg='str:2-12' tip='请选择'>".basElm::setOption($a,$fm['mod2'])."</select>";
		glbHtml::fmae_row('类别/栏目(1)',$s1);
		glbHtml::fmae_row('类别/栏目(2)',$s2);
		glbHtml::fmae_row('配置数组',"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>格式:键=值,一行一个；");
		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid)));
	}
}elseif($view=='set'){
	$lnkbak = "<a href='?file=$file&view=list'>&lt;&lt;返回列表</a>";
	glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$cfg[title]]设置<span class='span ph5'>|</span>",$gbar,25);
	echo "<div class='h02'>&nbsp;</div>";
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	$cfg1 = glbConfig::read($cfg['mod1']); 
	$cfg2 = glbConfig::read($cfg['mod2']); 
	$mod1 = ''; $restr = $cfg['cfgs']; $rearr = basElm::text2arr($restr);
	echo "<th>Key</th><th>名称</th><th>关联项目</th><th>设置</th></tr>\n";
	foreach($cfg1['i'] as $k=>$v){
		$fix = $v['deep']<'2' ? "" : str_repeat("&nbsp;&nbsp;",$v['deep']);
		$arr = explode(',',@$rearr[$k]); $str = ''; 
		foreach($arr as $v2){
			$str .= (empty($str) ? '' : ',&nbsp; ').@$cfg2['i'][$v2]['title'];	
		}
		echo "<tr>\n";
		echo "<td class='tc w80'>$k</td>";
		echo "<td class='tl w200'><input type='text' value='$fix$v[title]' class='txt w180' /></td>";
		echo "<td class='tl'>$str</td>";
		echo "<td class='tc w40'><a href='?file=$file&view=sone&parts=$parts&kid=$k' onclick='return winOpen(this,\"[".$v['title']."]关联的[".@$_groups[$cfg['mod2']]['title']."]\");'>设置</a></td>\n";
		echo "</tr>";	
	}
	glbHtml::fmt_end(array("parts|$parts"));
}elseif($view=='sone'){
	if(!empty($bsend)){	
		$v = empty($fm['rel']) ? '' : implode(',',array_filter($fm['rel']));
		$rearr[$kid] = $v; $restr = '';
		foreach($rearr as $k=>$v){ $restr .= "$k=,$v,\n"; } 
		$restr = str_replace(array(",,,",",,"),array(",",","),$restr); 
		$db->table($tabid)->data(basReq::in(array('cfgs'=>$restr)))->where("kid='$parts'")->update();
		basMsg::show('更新成功！');	
	}else{ 
		$cfg2 = glbConfig::read($cfg['mod2']); 
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		$istr = @$rearr[$kid]; $wcell = 15;
		/*if($cfg2['deep']<2 && count($cfg2['i'])<12){
			echo "\n<tr><td colspan='2' class='tl'>";
			foreach($cfg2['i'] as $k2=>$v2){ 
				$def = strstr(",$istr,",",$k2,") ? " checked='checked' " : '';
				echo "\n\n<label><input type='checkbox' class='rdcb' name='fm[rel][]' id='fm_rel_$k2' value='$k2' $def>$v2[title]</label>";
			} 
			echo "</td></tr>";
		}else*/if($cfg2['deep']<2){
			$ctab = array();
			foreach($cfg2['i'] as $k2=>$v2){
				$char = $v2['char']; if($char<'A') $char = '-';
				$ctab[$char][$k2] = $v2;
			}
			foreach($ctab as $char=>$v1){
				$s2 = ''; 
				foreach($v1 as $k2=>$v2){ 
					$def = strstr(",$istr,",",$k2,") ? " checked='checked' " : '';
					$s2 .= "\n\n<label><input type='checkbox' class='rdcb' name='fm[rel][]' id='fm_rel_$k2' value='$k2' $def>$v2[title]</label>";
				} 
				glbHtml::fmae_row(" &nbsp;[$char]&nbsp; ",$s2);
				$wcell = '';
			}
		}else{
			foreach($cfg2['i'] as $k2=>$v2){ 
			if($v2['deep']=='1'){ $s3 = ''; $j = 0; 
				$def = strstr(",$istr,",",$k2,") ? " checked='checked' " : '';
				$def = empty($v2['frame']) ? "<input type='checkbox' class='rdcb' name='fm[rel][]' id='fm_rel_$k2' value='$k2' $def>" : '';
				$s2 = "\n<label>$def$v2[title]</label>";
				foreach($cfg2['i'] as $k3=>$v3){ 
				if($v3['pid']==$k2){
					//$i++; $j++;  
					$def = strstr(",$istr,",",$k3,") ? " checked='checked' " : '';
					//if($j>4) { $s3 .= "<br>"; $j=1; }
					$s3 .= "\n\n<label><input type='checkbox' class='rdcb' name='fm[rel][]' id='fm_rel_$k3' value='$k3' $def>$v3[title]</label>";
				} }
				glbHtml::fmae_row($s2,$s3);
			} }
		}
		glbHtml::fmae_send('bsend','提交',$wcell);
		glbHtml::fmt_end(array("parts|$parts"));
	}
}

?>
