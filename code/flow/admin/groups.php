<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','admin/groups.php');

$mod = empty($mod) ? 'groups' : $mod;
$view = empty($view) ? 'glist' : $view;
$tabid = 'base_model';
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end('参数错误:mod@groups.php'); 
$gbar = admAFunc::grpNav('groups',$mod);
$advetabs = array(1=>'文字连接',2=>'图片连接',3=>'信息区块',4=>'网址收藏',);

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
					 $msg = admAFunc::modCopy($mod, $tabid, 'is_del', $id);
				}elseif($fs_do=='show'){ 
					 $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
				}elseif($fs_do=='stop'){ 
					 $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
				}
				if($mod!='groups' && $fs_do!='del') glbCUpd::upd_model($id);
			}
		}
		glbCUpd::upd_groups(); 
		if($fs_do!='del' && in_array($mod,array('score','sadm','smem','suser',))){ 
			glbCUpd::upd_paras($mod);
		}
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
	} 

	$list = $db->table($tabid)->where("pid='$mod'")->order('top')->select(); 
	
	$lnkadd = "<a href='$aurl[1]&view=gform' onclick='return winOpen(this,\"增加条目-在[$gname]\");'>增加条目&gt;&gt;</a>";
	glbHtml::tab_bar("[{$gname}]架设<span class='span ph5'>|</span>$lnkadd",$gbar,30);
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称".(in_array($mod,array('docs','coms','users')) ? '/关联模型' : '')."</th><th>排序</th><th>启用</th>";
	$fhd3 = "<th>字段</th><th>设置</th><th>复制</th>";
	if($mod=='docs'){ 
		echo "<th>栏目</th>";
		echo $fhd3;
	}elseif($mod=='coms'){ 
		echo $fhd3;
	}elseif($mod=='users'){
		echo "<th>等级</th>";
		echo $fhd3;
	}elseif($mod=='advs'){
		echo "<th>栏目</th>";
		echo "<th>模式</th>";
		echo "<th>-备用-</th>";
	}elseif($mod=='types'){ 
		echo "<th>管理</th>";
		echo "<th>-备用-</th>";
	}elseif($mod=='menus'){ 
		echo "<th>管理</th>";
		echo "<th>-备用-</th>";
	}elseif(in_array($mod,array('score','sadm','smem','suser'))){ 
		echo "<th>管理</th>";
		echo "<th>参数设置</th>";
	}elseif($mod=='plus'){
		echo "<th>-备用-</th>";
	}else{ 
		echo "<th>-备用-</th>";
		echo "<th>-备用-</th>";
	}
	echo "<th>修改</th><th class='wp15'>备注</th>\n";
	echo "</tr>\n"; 
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; $pstr = ''; 
	  if($_groups[ $kid]['pid'] && in_array($mod,array('types'))){
	      $rmcfg = glbConfig::read($kid);
	  }
	  if($r['pmod'] && in_array($mod,array('docs','coms','users'))){
		  $pname = @$_groups[$r['pmod']]['title'];
		  $pstr = "/$pname($r[pmod])\n";
	  }
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' />$pstr</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  $ftd3 = $cv->Url('字段',1,"?file=admin/fields&mod=$r[kid]")."<td class='tc'>设置</td>\n";
	  $ftd3 .= $cv->Url('复制',1,"$aurl[1]&view=gform&cid=$r[kid]","复制条目 - $r[title]");
	  if($mod=='docs'){ 
		  echo $cv->Url('栏目&gt;&gt;',1,"?file=admin/catalog&mod=$r[kid]",'frame');
		  echo $ftd3;
	  }elseif($mod=='coms'){ 
		  echo $ftd3;
	  }elseif($mod=='users'){
		  echo $cv->Url('等级&gt;&gt;',1,"?file=admin/grade&mod=$r[kid]&frame=1",'frame');
		  echo $ftd3;
	  }elseif($mod=='advs'){
		  echo $cv->Url('栏目&gt;&gt;',1,"?file=admin/catalog&mod=$r[kid]",'frame');
		  echo "<td class='tc'>".$advetabs[$r['etab']]."</td>\n";
		  echo "<td class='tc'>-备用-</td>\n";  
	  }elseif($mod=='types'){ 
		  if(strstr(@$rmcfg['cfgs'],'exdoc=1') && @$rmcfg['etab']){
			  echo $cv->Url('字段',1,"?file=admin/fields&mod=$r[kid]");  
		  }else{
		      echo "<td class='tc'>字段</td>\n"; 
		  }
		  echo $cv->Url('管理',1,"?file=admin/types&mod=$r[kid]",'frame');
	  }elseif($mod=='menus'){ 
		  echo $cv->Url('管理',1,"?file=admin/menus&mod=$r[kid]",'frame');
		  echo "<td class='tc'>-备用-</td>\n"; 
	  }elseif(in_array($mod,array('score','sadm','smem','suser'))){ 
		  echo $cv->Url('参数项',1,"?file=admin/fields&mod=$r[kid]&ispara=1");
		  echo $cv->Url('参数设置',1,"?file=admin/paras&mod=$r[kid]");
	  }elseif($mod=='plus'){
		  echo "<td class='tc'>-备用-</td>\n";  
	  }else{ 
		  echo "<td class='tc'>-备用-</td>\n";
		  echo "<td class='tc'>-备用-</td>\n";  
	  }
	  echo $cv->Url('修改',1,"$aurl[1]&view=gform&kid=$r[kid]&recbk=ref","");
	  echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='18'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='gform'){

	if(!empty($bsend)){
		if($kid=='is__add'){
			$msg = admAFunc::modCopy($mod, $tabid, $fm, $cid);
			$kid = $fm['kid'];
		}else{
			$msg = '更新成功！'; 
			unset($fm['kid']);
			$db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
		} 
		glbCUpd::upd_groups();
		if(in_array($mod,array('docs','coms','users'))){
			if(isset($_groups[$kid])){
				admAFunc::pmodSave($kid,@$fm['pmod']);
			}
		}
		if(in_array($mod,array('docs','users','types','coms','advs'))){
			glbCUpd::upd_model($kid); 
		}
		basMsg::show($msg);	//,'Redir'?file=$file&mod=$mod
	}else{

		if(!empty($cid)){ //copy
			$kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,@$pid);
			$fm = $db->table($tabid)->where("kid='$cid'")->find();
			$fm['title'] .= "_复制";
			$fm['pmod'] = '';
		}elseif(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,@$pid);
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','etab'=>'1','deep'=>'1','cfgs'=>'','pmod'=>'','crdel'=>'0','cradd'=>'0',);
		if($mod=='types'){ 
			$def['etab'] = 0; 
		} 
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
			$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=modExists' tip='字母开头,允许字母数字下划线<br>允许3-12字符,建议4-5字符'";
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:3-12' $vstr />$ienable");
		}
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");
		if($mod=='advs'){ //'advs'=>'栏目级数',
			$ietab = " &nbsp; 内容模式<select id='fm[etab]' name='fm[etab]' type='text' xxx=''>";
			$ietab .= basElm::setOption($advetabs,$fm['etab'])."</select>"; 
			glbHtml::fmae_row('栏目级数',"<input name='fm[deep]' type='text' value='$fm[deep]' class='txt w80' maxlength='1' reg='n+i' tip='数字' />(最大)$ietab");
			
		}elseif(in_array($mod,array('docs','types','menus'))){
			$_cfg = array('docs'=>'栏目级数','types'=>'类别级数','menus'=>'菜单深度');
			$ctitle = $_cfg[$mod];
			if(empty($kid)){
				$ietab = " &nbsp; 启用扩展表<input name='fm[etab]' type='hidden' value='0' /><input name='fm[etab]' type='checkbox' class='rdcb' value='1' ".($fm['etab']=='1' ? 'checked' : '')." />";
			}else{
				$ietab = " &nbsp; 启用扩展表<input name='fm_etab' type='checkbox' disabled='disabled' class='rdcb' value='1' ".($fm['etab']=='1' ? 'checked' : '')." />";
			}
			glbHtml::fmae_row($ctitle,"<input name='fm[deep]' type='text' value='$fm[deep]' class='txt w80' maxlength='1' reg='n+i' tip='数字' />(最大)$ietab");
		}
		glbHtml::fmae_row('配置数组',"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>格式:键=值,一行一个；");
		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		
		if(in_array($mod,array('docs','coms','users'))){
			if($mod=='coms'){
				$arr = admPFunc::modList(array('docs','users','coms',),'relmod'); 
				$pmstr = basElm::setOption($arr,$fm['pmod']);
				$oldPid = "<input name='oldPid' type='hidden' value='{$fm['pmod']}' />";
				glbHtml::fmae_row('关联模型',"<select name='fm[pmod]'>$pmstr</select><br>此模型在[关联模型]下展示才有意义；如[新闻评论]关联模型为[新闻动态];");
			}
			$jifen = " &nbsp; 删除(-分)<input name='fm[crdel]' type='text' value='$fm[crdel]' class='txt w80' maxlength='3' reg='n+i' tip='数字' />";
			glbHtml::fmae_row('积分设置',"添加(+分)<input name='fm[cradd]' type='text' value='$fm[cradd]' class='txt w80' maxlength='3' reg='n+i' tip='数字' />$jifen");
		}
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","fm[pid]|$mod","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
	}
	
}elseif($view=='sets'){
	//	
}

?>
