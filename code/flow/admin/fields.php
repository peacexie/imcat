<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','admin/fields.php'); 

$mod = empty($mod) ? 'docs' : $mod;
$view = empty($view) ? 'list' : $view;
$ispara = basReq::val('ispara','0'); //1,0
$catid = basReq::val('catid','0'); $cawhr = ($catid) ? "AND catid='$catid'" : ""; //echo $cawhr;
$tabid = 'base_fields'; if($ispara) $tabid = 'base_paras'; if($catid) $tabid = 'bext_fields';
$title = '字段'; if($ispara || $catid) $title = '参数项';
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end('参数错误:mod@fields.php'); 

if($view=='ftest'){
	
		$lnkbak = "<a href='?file=$file&mod=$mod&view=list&ispara=$ispara&catid=$catid'>&lt;&lt;返回[字段列表]</a>";
		glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$gname]表单效果",'---',40);
	if(empty($bsend)){ 
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		fldView::lists($mod,array('title'=>'(test)测试'.date('H:i:s'),'author'=>',a,b,'),$catid);
		glbHtml::fmae_send('bsend','提交');
		glbHtml::fmt_end();
	}else{
		echo "<pre>";
		print_r($_GET);
		print_r($_POST);
		echo "</pre>";
	}

}elseif($view=='fadd'){
	
	if(empty($bsend)){ 
		echo basJscss::imp('/skin/a_jscss/fields.js');
		$url = $aurl[1]; //basReq::getURep(,'view','form');
		$fmextra_bak = "\n<select id='fmextra_bak' name='fmextra_bak' style='display:none;' >".basElm::setOption(fldCfgs::viewPlugs(),'')."</select>";
		$field_from = "\n<input id='fm[from]' name='fm[from]' type='hidden' value='' />"; 
		$vtip = "字母开头,允许字母数字下划线<br>允许3-12字符,建议4-5字符<br>不能使用sql关键词和mysql函数";
		$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=".($catid ? 'fieldCatid' : 'fieldExists')."&mod=$mod&catid=$catid' tip='$vtip'";
		if($catid) $vstr .= " readonly";
		
		glbHtml::fmt_head('fmlist',$url,'tbdata');
		$picks = $catid ? fldCfgs::addType($mod,$catid) : fldCfgs::addPick($mod);
		echo "<tr><td colspan='2' class='tl h100'>$picks</td></tr>";
		$ftypes = fldCfgs::viewTypes(); if(in_array($_groups[$mod]['pid'],array('coms'))||!empty($catid)){ unset($ftypes['file']); } //互动/评论/参数:不要附件, 
		glbHtml::fmae_row('字段类型',"<select id='fm[type]' name='fm[type]' class='w150' reg='str:1-12' tip='请选择[字段类型]' onChange='gf_setfmType(this)'>".basElm::setOption($ftypes,'')."</select>"); 
		glbHtml::fmae_row('字段控件',"<select id='fm[fmextra]' name='fm[fmextra]' class='w150'>".basElm::setOption('','')."</select>$fmextra_bak$field_from"); 
		if(in_array($_groups[$mod]['pid'],array('docs'))&&$_groups[$mod]['etab']&&empty($catid)){ //,'types'
			glbHtml::fmae_row('数据表',"<select id='fm[etab]' name='fm[etab]' class='w150' reg='str:1-12' tip='请选择[数据表]'>".basElm::setOption("0|主表\n1|扩展表",'')."</select>");
		}else{
			echo "<input name='fm[etab]' type='hidden' value='' />";
		}
		glbHtml::fmae_row('Key标识',"<input id='fm[kid]' name='fm[kid]' type='text' value='' class='txt w150' maxlength='12' reg='key:3-12' $vstr />");
		glbHtml::fmae_send('bsend','提交');
		glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid),"mod|$mod")); 
	}else{
		$paras = "";
		foreach(fldCfgs::addParas() as $k){
			$paras .= "&fm[$k]=$fm[$k]";	
		}
		$url = basReq::getURep($aurl[1],'view','form').$paras; 
		die(basMsg::dir($url));
	}

}elseif($view=='form'){
	
	if(!empty($bsend)){ 
		if($kid=='_isadd_'){
			$kid = $fm['kid']; 
			if($db->table($tabid)->where("model='$mod' AND kid='$fm[kid]' $cawhr")->find()){
				$msg = "该条目[$fm[kid]]已被占用！";
			}else{
				$fm['model'] = $mod; if($catid) $fm['catid'] = $catid;
				$db->table($tabid)->data(basReq::in($fm))->insert();
				$msg = '添加成功！'; 
			}
		}else{
			$msg = '更新成功！'; unset($fm['kid']); 
			if(@$fm_null=='nul') $fm['vreg'] = 'nul:'.$fm['vreg']; 
			$db->table($tabid)->data(basReq::in($fm))->where("model='$mod' AND kid='$kid' $cawhr")->update();
		} 
		if(empty($ispara) && empty($catid)){ 
			if(!empty($fm['dbtype']) && $fm['dbtype']!='nodb') glbDBExt::setOneField($mod,$kid,'check');
		} 
		glbCUpd::upd_model($mod);
		echo basJscss::Alert($msg);	
		
	}else{
		
		echo basJscss::imp('/skin/a_jscss/fields.js'); 
		$fm = fldEdit::fmOrgData($tabid,$mod,$kid,$fm,$catid);
		
		$fedit = new fldEdit($mod,$fm);
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		$fedit->fmTypeOpts();
		$fedit->fmPlusPara();
		$fedit->fmParaKeys();
		$fedit->fmKeyName();
		$fedit->fmDbOpts();
		$fedit->fmRegOpts();
		$fedit->fmViewOpts();
		$fedit->fmRemCfgs();
		
		glbHtml::fmae_send('bsend','提交'); 
		glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid),"mod|$mod"));

	}

}elseif($view=='list'){
	
	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作项！";
		else{
			foreach($fs as $id=>$v){ //upd|更新\ndel|删除\nenable|启用\nstop|禁用
				$msg = "设置成功！";
				if($fs_do=='upd'){ 
					$db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id' $cawhr")->update(); 
				}elseif($fs_do=='del'){ 
					 if(!empty($ispara)){
						 $db->table($tabid)->where("issys='0' AND model='$mod' AND kid='$id' $cawhr")->delete(); 
					 }elseif(!empty($catid)){
						 $db->table($tabid)->where("model='$mod' AND kid='$id' $cawhr")->delete(); 
					 }else{
						 $tmp = array('title','company');
						 if(isset($tmp[$id])){
							 $msg = "部分字段为系统字段,不能删除,但您可以禁用它!";
						 }else{
							 if(empty($ispara)&&empty($catid)) glbDBExt::setOneField($mod,$id); //if($fm['dbtype']!='nodb') 
							 $msg = "删除成功！";
						 }
					 }
				}elseif($fs_do=='show'){ 
					 $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id' $cawhr")->update();  
				}elseif($fs_do=='stop'){ 
					 $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id' $cawhr")->update(); 
				}
			}
		}
		glbCUpd::upd_model($mod);
	} 
 	
	$lnkbak = "<a href='?file=admin/groups&mod=".$_groups[$mod]['pid']."'>&lt;&lt;返回[模型列表]</a>"; //&view=list&ispara=$ispara
	$lnkcat = "<a href='?file=admin/catalog&mod=$mod'>&lt;&lt;返回[栏目列表]</a>";
	$lnkgrd = "<a href='?file=admin/grade&mod=$mod'>&lt;&lt;返回[等级列表]</a>";
	$lnkbak = $catid ? ($_groups[$mod]['pid']=='users'? $lnkgrd : $lnkcat) : $lnkbak;
	
	$lnkadd = "<a href='?file=$file&mod=$mod&view=fadd&ispara=$ispara&catid=$catid' onclick='return winOpen(this,\"增加字段\")'>增加$title&gt;&gt;</a>";
	$lnkform = " | <a href='?file=$file&mod=$mod&view=ftest&ispara=$ispara&catid=$catid'>表单效果&gt;&gt;</a>";
	glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$gname]{$title}列表<span class='span ph5'>|</span>$lnkadd",$lnkform,40);
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>排序</th>";
	if(empty($ispara)&&empty($catid)) echo "<th>启用</th><th>分表</th>";
	echo "<th>类型</th><th>".(empty($ispara) ? '数据库' : '当前值')."</th><th title='输入最大值 | 数据库长度'>字符数</th>";	
	echo "<th>修改</th><th class='wp15'>备注</th>\n";
	echo "</tr>\n";
	if(!empty($catid)){
		$_dbmfields = $db->fields(glbDBExt::getTable($mod)); //print_r($_dbmfields);
	}
	$list = $db->table($tabid)->where("model='$mod' $cawhr")->order('enable DESC,top')->select();
	if($list){
	foreach($list as $r){
	  $kid = $r['kid'];
	  $note = basReq::out($r['vreg']).' | '.basReq::out($r['vtip']);
	  $note = $note==' | ' ? '' : $note;
	  $types = fldCfgs::viewTypes();
	  $plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$r['fmextra']]) ? ' ('.$plugs[$r['fmextra']].')' : '';
	  if($ispara){
		  $dbstr = "<input type='text' value='".basStr::filForm($r['val'])."' class='txt w80 disc' disabled='disabled' />";
	  }else{ //basStr::cutWidth($r['dbdef'],3,'..')
		  $dbstr = "$r[dbtype] ".(empty($r['dblen'])?'':"($r[dblen])").(strlen($r['dbdef'])?' ['.$r['dbdef'].']':'');
	  }
	  if(!empty($catid)){
	  	  $exstr = (!empty($catid) && isset($_dbmfields[$kid])) ? '' : "<span class='cF00'>缺少字段,请手动添加</span>"; 
	  }else{
		  $exstr = '';
	  }
	  echo "<tr>\n";
	  echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /> ".(empty($ispara) ? '' : $r['key'])."$exstr</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  if(empty($ispara)&&empty($catid)) echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  if(empty($ispara)&&empty($catid)) echo "<td class='tc'>".($r['dbtype']=='nodb' ? '---' : glbHtml::null_cell($r['etab']))."</td>\n";
	  echo "<td class='tl'>".$types[$r['type']]." $plugstr</td>\n";
	  echo "<td class='tc'>$dbstr</td>\n";
	  echo "<td class='tr'>".glbHtml::null_cell($r['vmax'],'')." | ".glbHtml::null_cell($r['dblen'],'')."</td>\n";
	  echo "<td class='tc'><a href='?file=$file&mod=$mod&view=form&kid=$r[kid]&ispara=$ispara&catid=$catid' onclick='return winOpen(this,\"修改$title\")'>修改</a></td>\n";
	  echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}

?>
