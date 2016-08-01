<?php
(!defined('RUN_MODE')) && die('No Init');

if(in_array($view,array('list','fields','urlset','urlist','loglist'))){ //'fdefs',
	$jname = $tabid=='exd_crawl' ? '数据采集' : '数据导入';
	$lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"增加[$jname]\");'>增加条目&gt;&gt;</a>";
	$lnkre = "<a href='$aurl[1]&view=list'>&lt;&lt;返回</a>";
	$links = admPFunc::fileNav($file,'exd_oimp');
	glbHtml::tab_bar("[$jname]排程<span class='span ph5'>|</span>".($view=='list' ? $lnkadd : $lnkre),"$links",50);
}

if($view=='set_a2'){
	
	glbHtml::fmt_head('fmlist',"?",'tblist');
	echo "\n<tr><th class='tc w150'></th>\n<th>分享配置：</th></tr>\n";
	if(empty($ocfgs)){
		echo "\n<tr><td class='tc w180'>提示：</td>\n<td>
			当前无配置；<br>请配置文件{code}/cfgs/excfg/ex_outdb.php
		</td></tr>\n";
	}else{
		echo "\n<tr><td class='tc w150'>配置文件：</td>\n<td>{code}/cfgs/excfg/ex_outdb.php，
		<a href='?file=admin/ediy&part=edit&dkey=cfgs&dsub=&efile=excfg/ex_outdb.php' onclick=\"return winOpen(this,'修改配置',780,560);\">修改</a></td></tr>\n";
		echo "\n<tr><td class='tc'>['psyn']['server']</td>\n<td>{$ocfgs['psyn']['server']}/plus/ajax/exdb.php</td></tr>\n";
		echo "\n<tr><td class='tc'>['sign']['sapp']</td>\n<td>{$ocfgs['sign']['sapp']}</td></tr>\n";
		echo "\n<tr><td class='tc'>['sign']['skey']</td>\n<td>{$ocfgs['sign']['skey']}</td></tr>\n";
		//echo "\n<tr><td class='tc'>说明：</td>\n<td>['psyn']['server']：如果是本地地址：则此网站数据可分享给其他网站；如果是外部地址：则可调用配置地址对应网站的数据。</td></tr>\n";
		echo "\n<tr><td class='tc'>文档：</td>\n<td><a href='{$_cbase['server']['txmao']}/dev.php?dev2nd-exdata'>二次开发:数据扩展(系统工具)</a></td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));

}if($view=='del_b3'){
	
	if(empty($fs_do)) $msg = "请选择操作项目！";
	if(empty($fs)) $msg = "请勾选操作记录！";
	$cnt = 0; 
	if(empty($msg)){
	  foreach($fs as $id=>$v){ 
		  if($fs_do=='dele'){ 
			  $db->table($dop->tbid)->where("kid='$job' AND sysid='$id'")->delete(); 
			  $cnt++;
		  }elseif($fs_do=='xxx'){ 
			  ;///
		  }
	  } 
	}
	$cnt && $msg = "$cnt 条记录 删除成功！";
	
	/*/ 清理操作
	if(!empty($bsend)&&$fs_do=='dnow'){
		$msg = $dop->opDelnow();
		basMsg::show($msg,'Redir',"?file=$file&view=$view&job=$job&mod=$mod&flag=v1");
	}*/
	
}if($view=='fset'){

	if(!empty($bsend)){
		$msg = '更新成功！'; 
		$fm['kid'] = $kid; 
		$fm['model'] = $job; //->where("kid='$job'")
		$fm['dealfmts'] = implode(',',$fm['dealfmts']); 
		if($tabid=='exd_crawl') exdBase::fldSave($fm,5);
		$db->table('exd_sfield')->data(basReq::in($fm))->replace();
		//basMsg::show("$msg","Redir","$aurl[1]");
		echo basJscss::Alert("$msg","Redir","$aurl[1]",1);	 
	}

	echo "<div class='h02'>&nbsp;</div>";
	$fm = $db->table('exd_sfield')->where("model='$job' AND kid='$kid'")->find(); 
	$fa = array(
		"orgtg1","orgtg2","orgtg3","orgtg4","orgtg5",
		"dealtabs","dealfmts","dealconv","dealfunc","dealfunp","defval","defover",
	);
	foreach($fa as $k){ 
		$fm[$k] = @basStr::filForm($fm[$k]); //echo "\n\n<br>$jcfg[$k]\n<br>$fm[$k]";
	}
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	echo "\n<tr><th class='tc w150'>$jcfg[title]</th>\n<th class='tl'>字段[$kid]配置 ：</th></tr>\n";
	glbHtml::fmae_row('项目名称',"$jcfg[title] --- 字段[$kid]配置");
	if($tabid=='exd_crawl'){
		$url = PATH_ROOT."/plus/ajax/exdb.php?act=crawl&mod=$jcfg[mod]&job=$job&debug=field&field=$kid&url=".urlencode($jcfg['odmp'])."&".exdBase::getJSign();
		glbHtml::fmae_row('样例详情页',"<input name='fm_odmp' type='text' value='$jcfg[odmp]' class='txt w400' maxlength='240' readonly />");
		glbHtml::fmae_row('调试规则',"<a href='$jcfg[odmp]' target='_blank'>打开样例详情页</a> # <a href='$url' target='_blank'>调试采集[$kid]字段</a> ");
		echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>提取原始内容规则：</th></tr>\n";
		exdBase::fldForm($fm,5);
	}else{
		$odbname = $ocfgs['list'][$jcfg['odb']]; 
		glbHtml::fmae_row('来源数据',"$jcfg[odb] --- $odbname");
		glbHtml::fmae_row('来源字段',"<input name='fm[orgtg1]' type='text' value='$fm[orgtg1]' class='txt w400' maxlength='48' reg='key:1-24' />");
	}
	
	echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>内容结果处理：</th></tr>\n";
	glbHtml::fmae_row('替换原始内容',"<textarea name='fm[dealtabs]' rows='3' cols='50' wrap='wrap'>$fm[dealtabs]</textarea>");
	$cbarr = array('note'=>'清除备注','html'=>'清除Html','blank'=>'清除空白','strtotime'=>'转化为时间戳',);
	$cbstr = basElm::setCBox('dealfmts',$cbarr,$fm['dealfmts'],6);
	glbHtml::fmae_row('综合处理内容',"$cbstr");
	glbHtml::fmae_row('内容替换表',"<textarea name='fm[dealconv]' rows='3' cols='50' wrap='wrap'>$fm[dealconv]</textarea>");
	$iparas = " &nbsp; 参数<input name='fm[dealfunp]' type='text' value='$fm[dealfunp]' class='txt w150' maxlength='24' />";
	glbHtml::fmae_row('结果处理函数',"<input name='fm[dealfunc]' type='text' value='$fm[dealfunc]' class='txt w180' maxlength='24' />$iparas");
	//$idefover = " &nbsp; <input name='fm[defover]' type='hidden' value='0' /><input name='fm_defover' type='hidden' value='$fm[defover]' />";
	//$idefover .= "启用<input name='fm[defover]' type='checkbox' class='rdcb' value='1' ".($fm['defover']=='1' ? 'checked' : '')." />";
	glbHtml::fmae_row('为空时默认值',"<input name='fm[defval]' type='text' value='$fm[defval]' class='txt w400' maxlength='255' />"); //$idefover

	echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>测试/设置说明/提取网址：</th></tr>\n";
	$detail = "网址含有,网址不含,网址替换：一行一个；\n[网址替换]默认值格式：oldval=newval";
	$detail .= "\n详情见文档：{$_cbase['server']['txmao']}/dev.php?advset-exdata#s_fields";
	glbHtml::fmae_row('备注',"<textarea rows='3' cols='50' wrap='wrap'>$detail</textarea>");
	glbHtml::fmae_send('bsend','提交','25');
	glbHtml::fmt_end(array("mod|$mod","job|$job"));

}if($view=='fields'){

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>Key</th><th>名称</th><th>排序</th>";
	echo "<th>启用</th><th>分表</th>";
	echo "<th>类型</th><th>配置</th><th>".(empty($ispara) ? '数据库' : '当前值')."</th><th title='输入最大值 | 数据库长度'>字符数</th>";	
	echo "<th class='wp15'>备注</th>\n";
	echo "</tr>\n";
	$list = $db->table('base_fields')->where("model='$mod'")->order('enable DESC,top')->select();
	$fskip = basElm::line2arr($jcfg['fskip']); 
	if($list){
	foreach($list as $r){
	  $kid = $r['kid'];
	  $note = basReq::out($r['vreg']).' | '.basReq::out($r['vtip']);
	  $note = $note==' | ' ? '' : $note;
	  $types = fldCfgs::viewTypes();
	  $plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$r['fmextra']]) ? ' ('.$plugs[$r['fmextra']].')' : '';
	  $dbstr = "$r[dbtype] ".(empty($r['dblen'])?'':"($r[dblen])").(strlen($r['dbdef'])?' ['.$r['dbdef'].']':'');
	  echo "<tr>\n";
	  echo "<td class='tc'>$r[kid]</td>\n";
	  echo "<td class='tl'>$r[title]</td>\n";
	  if($r['dbtype']=='nodb' || in_array($kid,$fskip)){
		  echo "<td class='tc c999' colspan=5>-- 排出字段 --</td>\n";
	  }else{
	 	  echo "<td class='tc'>$r[top]</td>\n";
	 	  echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
	  	  echo "<td class='tc'>".($r['dbtype']=='nodb' ? '---' : glbHtml::null_cell($r['etab']))."</td>\n";
	  	  echo "<td class='tl'>".$types[$r['type']]." $plugstr</td>\n";
		  echo "<td class='tc'><a href='?file=$file&mod=$mod&view=fset&job=$job&kid=$r[kid]' onclick='return winOpen(this,\"字段配置\")'>配置</a></td>\n";
	  }
	  echo "<td class='tc'>$dbstr</td>\n";
	  echo "<td class='tr'>".glbHtml::null_cell($r['vmax'],'')." | ".glbHtml::null_cell($r['dblen'],'')."</td>\n";
	  echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
	  echo "</tr>"; 
	}} 
	if($_groups[$mod]['pid']=='docs'){
		$dop->fext['catid'] = array('title'=>"栏目",'dbtype'=>"varchar (12)");
	}elseif($_groups[$mod]['pid']=='users'){
		$dop->fext['grade'] = array('title'=>"等级",'dbtype'=>"varchar (12)");
	} //print_r($_groups);
	echo "<tr style='border:1px solid #00CCFF'><td class='tc' colspan=10></td></tr>";
	foreach($dop->fext as $fk=>$fv){
	  if(!in_array($fk,array('atime','catid','grade'))) continue;
	  echo "<tr>\n";
	  echo "<td class='tc'>$fk</td>\n";
	  echo "<td class='tl'>$fv[title]</td>\n";
	  echo "<td class='tc c999'>---</td>\n";
	  echo "<td class='tc'>Y</td>\n";
	  echo "<td class='tc c666' colspan=2>-- 附加字段 --</td>\n";
	  echo "<td class='tc'><a href='?file=$file&mod=$mod&view=fset&job=$job&kid=$fk' onclick='return winOpen(this,\"配置\")'>配置</a></td>\n";
	  echo "<td class='tc'>$fv[dbtype]</td>\n";
	  echo "<td class='tr'>--</td>\n";
	  echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
	  echo "</tr>"; 
	}
	glbHtml::fmt_end(array("mod|$mod"));
	
}elseif($view=='fdefs'){
	echo 'fdefs - no use.';
}