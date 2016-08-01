<?php
(!defined('RUN_MODE')) && die('No Init'); 

if(!empty($bsend)){
	
	if(empty($fm['op'])){ 
		basMsg::show('请选择操作！');
		glbHtml::end();
	}
	if($fm['op']=='reid' && $fm['kre']!=$kid){
		$db->table($tabid)->data(array('pid'=>$fm['kre']))->where("model='$mod' AND pid='$kid'")->update();
		$db->table($tabid)->data(array('kid'=>$fm['kre']))->where("model='$mod' AND kid='$kid'")->update();
		$msg = "改ID[{$fm['kre']}]成功"; 
	}elseif($fm['op']=='move'){  
		$deep = empty($fm['pid']) ? '1' : $cfg['i'][$fm['pid']]['deep']+1; 
		$dorg = $cfg['i'][$kid]['deep'];
        $pid = empty($fm['pid']) ? '0' : $fm['pid'];
		$db->table($tabid)->data(array('pid'=>$pid,'deep'=>$deep))->where("model='$mod' AND kid='$kid'")->update();
		if(!(intval($deep)==intval($dorg))){
			$dmov = $dorg - $deep;
			$a = comTypes::getSubs($cfg['i'],$kid);
			$kids = '';
			foreach($a as $k=>$v){
				$kids .= (empty($kids) ? '' : ',')."'$k'";
			}
			$res2 = (intval($dmov)>0 ? '-' : '+').abs($dmov);
			$kids && $db->query("UPDATE {$db->pre}$tabid{$db->ext} SET deep=deep$res2 WHERE model='$mod' AND kid IN($kids)"); 
		}
		$msg = '移动成功'; 
	} 
	glbCUpd::upd_model($mod);
	basMsg::show($msg);	
	
}else{ 

	echo "<div class='h02'>&nbsp;</div>";
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />");
	glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='".$cfg['i'][$kid]['title']."' class='txt w150 disc' disabled='disabled' />");
	glbHtml::fmae_row('操作',basElm::setRadio('op',"reid=改ID\nmove=移动"));

	$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=keyExists&mod=$mod&tab=$tabid&old_val=$kid' tip='字母开头,允许字母数字下划线<br>允许2-12字符,建议4-5字符'";
	glbHtml::fmae_row('新ID',"<input name='fm[kre]' type='text' value='$kid' class='txt w150' maxlength='12' reg='key:2-12' $vstr />");
	
	$ops = comTypes::getOpt(comTypes::getPars($cfg['i'],$cfg['deep']));
	$ops = str_replace('-请选择-','-顶级-',$ops); 
	glbHtml::fmae_row('父条目',"<select name='fm[pid]'>$ops</select> (移动操作生效)");
	glbHtml::fmae_send('bsend','提交','25');
	glbHtml::fmt_end(array("mod|$mod","pid|$pid","kid|$kid"));
}
