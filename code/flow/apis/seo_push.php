<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$tabid = 'bext_paras'; 
$pid = empty($pid) ? 'seo_sitemap' : $pid;
$seo = new extSeo();

if(empty($dialog)){
	$gnarr = basLang::ucfg('cfgbase.seo_type'); 
	$gname = @$gnarr[$pid];
	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('flow.dops_add')."&gt;&gt;</a>";
	$lnkadd = in_array($pid,array('seo_sitemap')) ? "<span class='span ph5'>|</span>$lnkadd" : ''; 
	$linkp = admPFunc::fileNav($pid,'seo_push');
	$linkp = str_replace("&frame=1'","&frame=1' target='_blank'",$linkp);
	glbHtml::tab_bar("[$gname] $lnkadd","$linkp",50);	
}

if($pid=='create'){
	
	$job = req('job');
	$res = $seo->createSmap($job);
	$file = PATH_HTML."/map/$job";
	$str = $res ? "<a href='$file' target='_'>".lang('flow.pu_cfok')."</a>" : lang('flow.pu_cfng');
	echo "<p class='tc'><br>$str</p>";

}elseif($pid=='push'){
	
	$pfile = req('pfile');
	$plink = req('plink');
	if($pfile){ //echo "$pfile";
		$res = $seo->bpushRun($pfile);
	}elseif($plink){ //echo "$plink";
		$res = $seo->bpushRun(0,$plink);
	}
	$go = "<a href='?file=$file&pid=seo_pset'>".lang('flow.dops_back')."</a>";
	echo "<p class='tc'><br>".@$res['msg']."<br>$go</p>";

}elseif($pid=='seo_sitemap' && $view=='list'){

	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop');
		if(empty($fs)) $msg = lang('flow.msg_pkitem');
		else{
			foreach($fs as $id=>$v){
				$msg = lang('flow.msg_set');
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
	echo "<th>".lang('flow.title_select')."</th><th>".lang('flow.pu_mapfile')."</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.pu_cv2')."</th>\n";
	if($list){
	foreach($list as $r){
	  $kid = $r['kid']; 
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tc'>$r[title]</td>\n";
	  echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref",""); 
	  $url1 = $cv->Url(lang('flow.pu_create'),0,"?file=$file&pid=create&job=$kid",lang('flow.pu_create'));
	  $url2 = file_exists(DIR_HTML."/map/$kid") ? "<a href='".PATH_HTML."/map/$kid' target='_blank'>".lang('flow.pu_view')."</a>" : lang('flow.pu_view');
	  echo "<td class='tc'>$url1/$url2</td>\n";
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='7'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($pid=='seo_pset' && $view=='list'){
	
	$msg = '';	
	if(!empty($bsend)){
		if(empty($fs_do)) $msg = lang('flow.dops_setop');
		if(empty($fs)) $msg = lang('flow.msg_pkitem');
		else{
			foreach($fs as $id=>$v){
				$msg = lang('flow.msg_set');
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
	echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th class='wp15'>".lang('flow.pu_setv')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_edit')."</th>\n";
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
	  echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
	  echo "</tr>"; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

	echo "<div class='h02'>&nbsp;</div>";
	
	glbHtml::fmt_head('fmpush',"$aurl[1]",'tbdata');
	glbHtml::fmae_row(lang('flow.pu_ipush'),lang('flow.pu_slink')." : http://zhanzhang.baidu.com/linksubmit/index");
	glbHtml::fmae_row('API',"http://data.zz.baidu.com/urls?site=your_domain.com&token=your_token");
	
	$mcfg = array('baidu_push.txt'=>'baidu_push.txt'); //
	$topt = basElm::setOption($mcfg,'baidu_push.txt'); 
	glbHtml::fmae_row(lang('flow.pu_pfile'),"<select name='pfile' class='w150'>$topt</select>".lang('flow.pu_tip1')."");
	glbHtml::fmae_row(lang('flow.pu_plink'),"<textarea name='plink' rows='12' cols='50' style='white-space:nowrap; overflow:scroll;' ></textarea><br>".lang('flow.exd_iline').", eg:http://your_domain.com/html/123.htm；");
	glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
	glbHtml::fmae_row(lang('flow.pu_ldemo'),"<a href='http://www.onexin.net/sitemap-xml-to-the-major-method-of-search-engine-submission/' target='_blank'>".lang('flow.pu_tip2')."</a>");
	glbHtml::fmt_end(array("pid|push","dialog|1"));	

}elseif($pid=='seo_plog' && $view=='list'){
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th>\n";
	glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='form'){
	
	if(!empty($bsend)){
		if($kid=='is__add'){
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
		$ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
		$itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
		echo "<div class='h02'>&nbsp;</div>";
		glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
		if(!empty($kid)){
			glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
		}else{
			$vstr = "tip='eg:baidu_map.html, google_map.xml, baidu_push.txt'"; 
			glbHtml::fmae_row(lang('flow.pu_mfname'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='18' reg='key:7-18' $vstr />$ienable");
		} 
		glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");
		glbHtml::fmae_row(lang('flow.pu_modset'),"<textarea name='fm[note]' rows='5' cols='50' wrap='wrap'>$fm[note]</textarea><br>".lang('flow.exd_iline').", eg:news,limit,chn,monthly,0.5；");
		
		glbHtml::fmae_row(lang('flow.pu_itpl'),"<textarea name='fm[cfgs]' rows='5' cols='50' wrap='wrap'>$fm[cfgs]</textarea>");
		glbHtml::fmae_row(lang('flow.pu_ftpl'),"<textarea name='fm[detail]' rows='5' cols='50' wrap='wrap'>$fm[detail]</textarea>");
		
		$dcfgs = " *** ".lang('flow.pu_idemo')." *** <url>\n<loc>(url)</loc>\n<lastmod>(time)</lastmod>\n<changefreq>(freq)</changefreq>\n<priority>(priority)</priority>\n</url>\n";
		$dcfgs .= "\n*** ".lang('flow.pu_hdemo')." *** \n<!DOCTYPE html><html><head>\n<meta charset=\"utf-8\">\n<title>Baidu Sitemap</title>\n</head><body>\n(*)\n</body></html>";
		$dcfgs .= "\n *** ".lang('flow.pu_xdemo')." *** \n<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<urlset>\n(*)\n</urlset>";
		glbHtml::fmae_row(lang('flow.pu_fdemo'),"<textarea rows='5' cols='50' wrap='wrap'>$dcfgs</textarea>");
		
		glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
		glbHtml::fmt_end(array("mod|$mod","fm[pid]|$pid","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
	}
}

?>