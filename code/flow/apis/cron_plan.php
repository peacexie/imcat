<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$tabid = 'bext_cron';

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
				}elseif(in_array($fs_do,array('runp','rinc'))){ 
					$upd = $fs_do=='runp' ? 1 : 0;
					$cron = new extCron($id);
				}
			}
		}
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
	}

	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"增加[计划任务]\");'>增加条目&gt;&gt;</a>";
	$links = admPFunc::fileNav($file,'cron_plan');
	glbHtml::tab_bar("[计划任务]排程<span class='span ph5'>|</span>$lnkadd","$links",50);
	
	$ucfg = array('m'=>'月', 'w'=>'周', 'd'=>'天', 'h'=>'时',);
	$list = $db->table($tabid)->order('top')->select(); //->where("pid='$pid'")
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key/文件名</th><th>名称</th><th>间隔周期</th><th>上次执行</th><th>下次执行</th><th>执行分秒</th><th>排序</th><th>启用</th><th>修改</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'>$r[excycle]".@$ucfg[$r['excunit']]."</td>\n";
	  echo $cv->Time($r['exlast'],$td=1);
	  echo $cv->Time($r['exnext'],$td=1);
	  echo "<td class='tc'>$r[exsecs]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='9'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用\nrinc|*直接执行\nrunp|*排程运行")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='form'){
	
	if(!empty($bsend)){
		$fm['exnext'] = strtotime($fm['exnext'].":00");
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
		basMsg::show($msg);	
	}else{

		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$kid = '';
		}
		$def = array(
			'kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','cfgs'=>'',
			'excycle'=>'1','excunit'=>'d','exlast'=>'0','exnext'=>'946526400','extime'=>'0','exskip'=>'','exsecs'=>date('i:s'),
		);
		foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= "启用<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; 顺序<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "tip='计划任务对应的文件名<br>不含.php'"; //url='".PATH_ROOT."/plus/ajax/cajax.php?act=modExists' 
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='tit:4-12' $vstr />$ienable");
		} 
		glbHtml::fmae_row('任务名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");

		$excunit = basElm::setOption(array('w'=>'周','d'=>'天','h'=>'时',),$fm['excunit']);
		$excunit = " &nbsp; 单位<select name='fm[excunit]' class='w90'>$excunit</select>";
		glbHtml::fmae_row('间隔周期',"<input name='fm[excycle]' type='text' value='$fm[excycle]' class='txt w90' maxlength='3' reg='n+i' tip='1-120数字' />$excunit");

		echo basJscss::imp('/My97DatePicker/WdatePicker.js','vui'); 
		$slast = empty($fm['exlast']) ? '-' : date('Y-m-d H:i',$fm['exlast']);
		$slast = " &nbsp; 上次：$slast";
		$iinp = "<input id='fm[exnext]' name='fm[exnext]' type='text' value='".date('Y-m-d H:i',$fm['exnext'])."' class='txt w130' />";
		$item = "$iinp<span class='fldicon fdate' onClick=\"WdatePicker({el:'fm[exnext]',dateFmt:'yyyy-MM-dd HH:mm'})\" /></span>";
		glbHtml::fmae_row('下次执行',"$item $slast");
		
		glbHtml::fmae_row('执行分秒',"<input name='fm[exsecs]' type='text' value='$fm[exsecs]' class='txt w150' maxlength='5' reg='str:5-5' tip='00:00~59:59，即1小时内的秒数' />00:00~59:59");

		glbHtml::fmae_row('备注',"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
		
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","kid|".(empty($kid) ? 'is__add' : $kid)));
	}
}

?>
