<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$tabid = 'bext_paras'; 
$pid = empty($pid) ? 'seo_sitemap' : $pid;
$seo = new extSeo();

if(empty($dialog)){
	$gnarr = array('seo_sitemap'=>'Sitemap','seo_pset'=>'Push设置','seo_plog'=>'Push记录'); 
	$gname = @$gnarr[$pid];
	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"增加条目-在[$gname]\");'>增加&gt;&gt;</a>";
	$lnkadd = in_array($pid,array('seo_sitemap')) ? "<span class='span ph5'>|</span>$lnkadd" : ''; 
	$linkp = admPFunc::fileNav($pid,'seo_push');
	$linkp = str_replace("&frame=1'","&frame=1' target='_blank'",$linkp);
	glbHtml::tab_bar("[$gname] $lnkadd","$linkp",50);	
}

if($pid=='create'){
	
	$job = basReq::val('job');
	$res = $seo->createSmap($job);
	$file = PATH_HTML."/map/$job";
	$str = $res ? "<a href='$file' target='_'>成功生成！</a>" : "生成失败。";
	echo "<p class='tc'><br>$str</p>";

}elseif($pid=='push'){
	
	$pfile = basReq::val('pfile');
	$plink = basReq::val('plink');
	if($pfile){ //echo "$pfile";
		$res = $seo->bpushRun($pfile);
	}elseif($plink){ //echo "$plink";
		$res = $seo->bpushRun(0,$plink);
	}
	$go = "<a href='?file=$file&pid=seo_pset'>返回</a>";
	echo "<p class='tc'><br>".@$res['msg']."<br>$go</p>";

}elseif($pid=='seo_sitemap' && $view=='list'){

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
	
	$list = $db->table($tabid)->where("pid='seo_sitemap'")->order('top')->select();
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>map文件</th><th>名称</th><th>排序</th><th>启用</th><th>修改</th><th class='wp15'>生成/查看</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref",""); 
	  $url1 = $cv->Url('生成',0,"?file=$file&pid=create&job=$kid","生成");
	  $url2 = file_exists(DIR_HTML."/map/$kid") ? "<a href='".PATH_HTML."/map/$kid' target='_blank'>查看</a>" : '查看';
	  echo "<td class='tc'>$url1/$url2</td>\n";
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='7'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($pid=='seo_pset' && $view=='list'){
	
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
					$fm['push_time']['detail'] = strtotime($fm['push_time']['detail']);
					$db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update();
				}elseif($fs_do=='stop'){ 
					$db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
				}
			}
		}
		basMsg::show($msg,'Redir',"?file=$file&pid=$pid");
	}
	
	$list = $seo->bpushCfg();
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th class='wp15'>设置值</th><th>排序</th><th>修改</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  if($kid=='push_time'){
	  	$r['detail'] = empty($r['detail'])? '2005-12-31' : date('Y-m-d H:i:s',$r['detail']);
	  }
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
	  echo "<td class='tl'><input name='fm[$kid][detail]' type='text' value='$r[detail]' class='txt w240' /></td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo $cv->Url('修改',1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>批量操作: <select name='fs_do'>".basElm::setOption("upd|更新\ndel|删除\nshow|启用\nstop|禁用")."</select> <input name='bsend' class='btn' type='submit' value='执行操作' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));
	
	echo "<div class='h02'>&nbsp;</div>";
	
	glbHtml::fmt_head('fmpush',"$aurl[1]",'tbdata');
	glbHtml::fmae_row('主动推送',"百度链接提交 : http://zhanzhang.baidu.com/linksubmit/index");
	glbHtml::fmae_row('API',"http://data.zz.baidu.com/urls?site=your_domain.com&token=your_token");
	
	$mcfg = array('baidu_push.txt'=>'baidu_push.txt'); //
	$topt = basElm::setOption($mcfg,'baidu_push.txt'); 
	glbHtml::fmae_row('推送文档',"<select name='pfile' class='w150'>$topt</select> (或手动在如下框填写地址提交)");
	glbHtml::fmae_row('推送连接',"<textarea name='plink' rows='12' cols='50' style='white-space:nowrap; overflow:scroll;' ></textarea><br>一行一个，如：http://your_domain.com/html/123.htm；");
	glbHtml::fmae_send('bsend','提交','25');
	glbHtml::fmae_row('连接参考',"<a href='http://www.onexin.net/sitemap-xml-to-the-major-method-of-search-engine-submission/' target='_blank'>向各大搜索引擎提交SITEMAP.XML的方法</a>");
	glbHtml::fmt_end(array("pid|push","dialog|1"));	

}elseif($pid=='seo_plog' && $view=='list'){
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th>\n";
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
		basMsg::show($msg);	
	}else{

		if(!empty($kid)){
			$fm = $db->table($tabid)->where("kid='$kid'")->find();
		}else{
			$kid = '';
		}
		$def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','detail'=>'','note'=>'','cfgs'=>"");
		foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

		$ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
		$ienable .= "启用<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; 顺序<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row('Key标识',"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "tip='如：baidu_map.html, google_map.xml, baidu_push.txt'"; 
			glbHtml::fmae_row('Map文件名',"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='18' reg='key:7-18' $vstr />$ienable");
		} 
		glbHtml::fmae_row('条目名称',"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' />$itop");
		glbHtml::fmae_row('模型配置',"<textarea name='fm[note]' rows='5' cols='50' wrap='wrap'>$fm[note]</textarea><br>一行一个，如：news,limit,chn,monthly,0.5；");
		
		glbHtml::fmae_row('item模版',"<textarea name='fm[cfgs]' rows='5' cols='50' wrap='wrap'>$fm[cfgs]</textarea>");
		glbHtml::fmae_row('file模版',"<textarea name='fm[detail]' rows='5' cols='50' wrap='wrap'>$fm[detail]</textarea>");
		
		$dcfgs = " *** item参考 *** <url>\n<loc>(url)</loc>\n<lastmod>(time)</lastmod>\n<changefreq>(freq)</changefreq>\n<priority>(priority)</priority>\n</url>\n";
		$dcfgs .= "\n*** html参考(file模版) *** \n<!DOCTYPE html><html><head>\n<meta charset=\"utf-8\">\n<title>Baidu Sitemap</title>\n</head><body>\n(*)\n</body></html>";
		$dcfgs .= "\n *** xml参考(file模版) *** \n<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<urlset>\n(*)\n</urlset>";
		glbHtml::fmae_row('file参考',"<textarea rows='5' cols='50' wrap='wrap'>$dcfgs</textarea>");
		
		glbHtml::fmae_send('bsend','提交','25');
		glbHtml::fmt_end(array("mod|$mod","fm[pid]|$pid","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
	}
}

?>
