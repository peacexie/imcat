<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','admin/types.php');

$mod = empty($mod) ? 'china' : $mod;
$view = empty($view) ? 'glist' : $view;
$pid = empty($pid) ? '0' : $pid;
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end('参数错误:mod@types.php'); 
$gbar = admAFunc::grpNav('types',$mod); 

$cfg = glbConfig::read($mod); //print_r($cfg);
$tabid = empty($cfg['etab']) ? 'types_common' : "types_$mod";

if($view=='glist'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作项！";
		elseif($fs_do=='chord'){
			$msg = "排序成功！";
			$list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('`char`,kid')->select();
			if($list){ $bord = 124;
			foreach($list as $r){
				$bord += 4; $kid = $r['kid']; 
				$db->table($tabid)->data(array('top'=>$bord))->where("model='$mod' AND kid='$kid'")->update(); 
			}}
		}else{
			foreach($fs as $id=>$v){ //upd|更新\ndel|删除\nenable|启用\nstop|禁用
				$msg = "设置成功！";
				if($fs_do=='upd'){ 
					$fm[$id]['char'] = strtoupper(comConvert::pinyinMain($fm[$id]['title'],3,1));
					$db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id'")->update(); 
				}elseif($fs_do=='del'){ 
					if($db->table($tabid)->where("model='$mod' AND pid='$id'")->find()){
						$msg = "{$id}该条目含有子类，请先删除子类！";
					}else{
						$db->table($tabid)->where("model='$mod' AND kid='$id'")->delete(); 
						vopStatic::clrFile($mod,$id);
						$msg = "删除成功！";	
					}
				}elseif($fs_do=='show'){ 
					 $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
				}elseif($fs_do=='stop'){ 
					 $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
				}elseif($fs_do=='frame'){ 
					 $db->table($tabid)->data(array('frame'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
				}elseif($fs_do=='nofrm'){ 
					 $db->table($tabid)->data(array('frame'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
				}elseif($fs_do=='chupd'){ 
					 $char = strtoupper(comConvert::pinyinMain($fm[$id]['title'],3,1));
					 $db->table($tabid)->data(array('char'=>$char))->where("model='$mod' AND kid='$id'")->update();
				}
			}
		}
		glbCUpd::upd_model($mod);
	} 

	$lnklay = admAFunc::typLay($cfg,$aurl,$pid);
	$lnkbak = $_groups[$cfg['pid']]['title'];
	$lnkadd = "<a href='$aurl[1]&view=gform&pid=$pid' onclick='return winOpen(this,\"增加条目-在[$gname]\");'>增加条目&gt;&gt;</a>";
	if($pid && !isset($cfg['i'][$pid])) $lnkadd = "<i title='[未启用]条目,不能增加'>增加条目&gt;&gt;</i>";
    $lnkchk = "<a href='$aurl[1]&view=check' onclick='return winOpen(this);'>检查ID包含</a>";
    if(empty($pid)) $lnklay = "$lnkchk | $lnklay";
	glbHtml::tab_bar("[$lnkbak] :: $gname<span class='span ph5'>|</span>$lnkadd<br>$lnklay",$gbar,35);
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>排序</th><th>启用</th>";
	echo "<th>字母</th><th>级别</th><th>结构</th><th>(子类)</th>";
	echo "<th>修改</th><th class='wp15'>备注</th>\n";
	echo "</tr>\n";
	$list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('top,kid')->select();
	if($list){
	foreach($list as $r){
	  $kid = $r['kid'];
	  $u_sub = strstr($aurl[1],'pid=') ? basReq::getURep($aurl[1],'pid',$r['kid']) : "$aurl[1]&pid=$r[kid]";
	  $f_deep = @$cfg['i'][$pid]['deep'] < $cfg['deep']-1;
	  $s_cnt = count(comTypes::getSubs($cfg['i'],$r['kid']));
	  echo "<tr>\n";
	  echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
	  echo "<td class='tc'><a href='$aurl[1]&view=set&kid=$r[kid]' onclick='return winOpen(this,\"设置条目-$r[title] (改ID/移动)\",400,300);'>$r[kid]</a></td>\n";
	  echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo "<td class='tc'>$r[char]</td>\n";
	  echo "<td class='tc'>$r[deep]</td>\n"; 
	  echo "<td class='tc'>".glbHtml::null_cell($r['frame'])."</td>\n";
	  echo "<td class='tc'>".($f_deep ? "<a href='$u_sub'>$s_cnt</a>" : glbHtml::null_cell($s_cnt))."</td>\n";   
	  echo "<td class='tc'><a href='$aurl[1]&view=gform&kid=$r[kid]&recbk=ref'>修改</a></td>\n";
	  echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
	  echo "</tr>"; 
	}} 
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用\nframe|结构\nnofrm|非结构\nchupd|更新字母\nchord|字母排序")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='gform'){ 

	if(!empty($bsend)){ 
		if($kid=='_isadd_'){
			$fm['char'] = strtoupper(comConvert::pinyinMain($fm['title'],3,1));
			if($db->table($tabid)->where("model='$mod' AND kid='$fm[kid]'")->find()){
				$msg = "该条目[$fm[kid]]已被占用！";
			}else{
				$msg = '添加成功！';  
				
				$fm['deep'] = empty($fm['pid']) ? 1 : @$cfg['i'][$pid]['deep']+1;
				$db->table($tabid)->data(basReq::in($fm))->insert();
				$id = $fm['kid'];	
			}
		}else{
			$msg = '更新成功！'; 
			unset($fm['kid']);
			$f = $cfg['f'];
			$mcfg = glbConfig::read($mod); 
			foreach($_POST['fm'] as $k=>$v){
			if(isset($f[$k])){ 
				$fm[$k] = dopFunc::svFmtval($f,$mod,$k,$v);
				if(isset($mcfg['f'][$k]) && in_array($mcfg['f'][$k]['type'],array('file','text'))){
					$ishtml = $mcfg['f'][$k]['type']=='text';
					$fm[$k] = comFiles::moveTmpDir($fm[$k],$mod,'',$ishtml);
				}
			} }
			$db->table($tabid)->data(basReq::in($fm))->where("model='$mod' AND kid='$kid'")->update();
		} 
		glbCUpd::upd_model($mod);
		basMsg::show($msg);	
	}else{
		if(empty($kid)){
			$kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,$pid);
		}else{
			$fm = $db->table($tabid)->where("model='$mod' AND kid='$kid'")->find();
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','frame'=>'0','deep'=>'1','cfgs'=>'',);
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
			$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=keyExists&mod=$mod&tab=$tabid' tip='字母开头,允许字母数字下划线<br>允许2-12字符,建议4-5字符'";
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:2-12' $vstr />$ienable");
		}
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");
		glbHtml::fmae_row('配置数组',"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>格式:键=值,一行一个；");
		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		empty($kid) || fldView::lists($mod,$fm);
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","fm[pid]|$pid","kid|".(empty($kid) ? '_isadd_' : $kid)));
	}

}elseif($view=='set'){
	require(dirname(dirname(__FILE__)).'/binc/set_id.php');
}elseif($view=='check'){
    $irep = devBase::typCheck($mod);
    echo "<pre> "; print_r(str_replace(";","\n",$irep)); echo "</pre>";
}

?>
