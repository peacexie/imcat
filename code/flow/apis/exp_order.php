<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$tabid = 'bext_paras';
$pid = empty($pid) ? 'paymode_cn' : $pid;

if($view=='list'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = "请选择操作项目！";
		if(empty($fs)) $msg = "请勾选操作项！";
		else{
			foreach($fs as $id=>$v){ //upd|更新\ndel|删除\nenable|启用\nstop|禁用
				$msg = "设置成功！";
				if($fs_do=='del'){ 
					$db->table($tabid)->where("kid='$id'")->delete();
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

	$linka = admPFunc::fileNav($pid,'ordcn'); $gname = admPFunc::fileNavTitle($pid,'ordcn');
	$linkb = admPFunc::fileNav($pid,'orden'); $gnamf = admPFunc::fileNavTitle($pid,'orden');

	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"增加条目-在[$gname]\");'>增加条目&gt;&gt;</a>";
	glbHtml::tab_bar("[{$gname}{$gnamf}]架设<span class='span ph5'>|</span>$lnkadd","$linka<br>$linkb",30);
	
	$list = $db->table($tabid)->where("pid='$pid'")->order('top')->select();
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>排序</th><th>启用</th><th>修改</th><th class='wp15'>备注</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo "<td class='tl'><input type='text' value='$r[detail]' class='txt w360' /></td>\n";
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='form'){
	
	if(!empty($bsend)){
		if($kid=='is__add'){
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

		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$kid = '';
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','detail'=>'','cfgs'=>'','numa'=>'0');
		foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= "启用<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; 顺序<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "tip='字母开头,允许字母数字下划线<br>允许4-12字符,建议5-8字符'"; //url='".PATH_ROOT."/plus/ajax/cajax.php?act=modExists' 
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
		} // paymode_, numa,  附加金额
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");

		$cfgs = strstr($pid,'paymode_') ? "图标地址<input name='fm[cfgs]' type='text' value='$fm[cfgs]' class='txt w150' maxlength='12' tip='图标文件名或css' />" : '';
		glbHtml::fmae_row('附加金额',"<input name='fm[numa]' type='text' value='$fm[numa]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' /> &nbsp; $cfgs");

		glbHtml::fmae_row('详细内容',"<textarea name='fm[detail]' rows='8' cols='50' wrap='off'>$fm[detail]</textarea>");
		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","fm[pid]|$pid","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
	}
}

?>
