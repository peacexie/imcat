<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');
$ocfgs = glbConfig::read('outdb','ex');

if(in_array($view,array('list','set'))){
	$lnkadd = admPFunc::fileNav($view,'exd_share');
	$links = admPFunc::fileNav($file,'exd_psyn');
	glbHtml::tab_bar("[数据分享]<span class='span ph5'>#</span>$lnkadd","$links",50);
}
//echo $mod;

if($view=='set'){
	
	$vbak = $view;
	$view = 'set_a2';
	require(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
	$view = $vbak;

}elseif($view=='list'){

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>Key</th><th>名称</th><th>json分享</th><th>tpl调用</th><th class='wp15'>备注</th>\n";
	$gma = array('docs','users','coms'); $gmold = '';
	foreach($gma as $gm){ 
	foreach($_groups as $mod=>$gv){
	  $kid = "$mod"; if($gv['pid']!==$gm) continue;
	  $mcfg = glbConfig::read($mod); 
	  if($gmold!=$gv['pid']){
	  	echo "<tr><td class='tc fB' colspan='3'>{$_groups[$gv['pid']]['title']}</td></tr>";
	  }
	  echo "<tr>\n".$cv->Select($kid);
	  echo "<td class='tc'>$kid</td>\n";
	  echo "<td class='tc'>$gv[title]</td>\n";
	  echo $cv->Url('json分享',1,"?file=$file&view=json&mod=$mod","blank");
	  echo $cv->Url('tpl调用',1,"?file=$file&view=tpl&mod=$mod","blank");
	  echo "<td class='tl'><input type='text' value='".str_replace(array("\n","\r",";;"),array(";",";",";"),@$mcfg['cfgs'])."' class='txt w300' /></td>\n";
	  echo "</tr>";
	  $gmold = $gv['pid']; 
	}}
	echo "<tr>\n";
	echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
	echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>批量操作: (null) &nbsp; </td>\n";
	echo "</tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}elseif(in_array($view,array('json','tpl'))){
	
	$msg1 = $view=='json' ? '(如下参数cut/ret/tpl不用)' : '(可设置如下cut/ret/tpl显示参数)';
	if(in_array($_groups[$mod]['pid'],array('docs','users'))){
		$dop = new dopBase(glbConfig::read($mod)); 
		$ops = $dop->fmType('stype',150); $ops = str_replace(array("fm[stype]","reg='"),array("stype","'"),$ops); //
		$s_type = ($_groups[$mod]['pid']=='docs' ? '栏目' : '等级')."：$ops";
	}else{
		$s_type = "栏目/等级：---";
	}
	$stype = basReq::val('stype');
	$limit = basReq::val('limit',10);
	$order = basReq::val('order',substr($_groups[$mod]['pid'],0,1)."id:".($view=='json' ? 'ASC' : 'DESC'));
	$offset = basReq::val('offset');
	$cut = basReq::val('cut','title,compony');
	$clen = basReq::val('clen',48);
	$ret = basReq::val('ret','html');
	$tpl = basReq::val('tpl','','');
	$tpldef = $tpl ? $tpl : "<li><a href='{rhome}/run/chn.php?$mod.{kid}'>{title}</a></li>";
	$dis = $view=='json' ? 'disabled' : '';
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist'); //
		echo "\n<tr><th class='wp40'>数据条件</th>\n<th class='wp60'>显示设置</th></tr>\n";
		echo "\n<tr><td>资料模块：$mod {$_groups[$mod]['title']}</td>\n
		            <td>调用方式：{$view}调用 $msg1</td></tr>\n";
		echo "\n<tr><td>$s_type</td>\n
		            <td>cut：<input name='cut' type='text' value='$cut' class='txt w120' $dis/> &nbsp; clen：<input name='clen' type='text' value='$clen' class='txt w40' $dis/></td></tr>\n";
		echo "\n<tr><td>limit：<input name='limit' type='text' value='$limit' class='txt w150' /></td>\n
		            <td>ret：<input name='ret' type='text' value='$ret' class='txt w120'  $dis/>如：html,js</td></tr>\n";
		echo "\n<tr><td>order：<input name='order' type='text' value='$order' class='txt w150' /></td>\n
		            <td>tpl：<input name='tpl' type='text' value=\"$tpldef\" class='txt w320' $dis/></td></tr>\n";
		echo "\n<tr><td>offset：<input name='offset' type='text' value='$offset' class='txt w150' tip='如：2016-2e-1234' /></td>\n
		            <td class='tc'><input name='bsend' class='btn' type='submit' value='提交' /></td></tr>\n";
	if(!empty($bsend)){	
		// mod,stype,limit(1-100),order(did:DESC),offset,tpl,cut,clen,ret(html/js),
		$entpl = comParse::urlBase64($tpl); 
		$usign = exdBase::getJSign();
		$ptpl = $view=='json' ? '' : "&cut=$cut&clen=$clen&ret=$ret&tpl=".($tpl ? $entpl : '');
		$apiurl = $_cbase['run']['roots']."/plus/ajax/exdb.php?mod=$mod&act=".($view=='json' ? 'pull' : 'show')."";
		$apiurl .= "&stype=$stype&limit=$limit&order=$order&offset=$offset".$ptpl."&".$usign;
		echo "\n<tr><td colspan=2 style='border-top:5px solid #99F;'>调用地址：<input type='text' value=\"$apiurl\" class='txt w700' /></td></tr>\n";	
		if($view=='tpl') echo "\n<tr><td colspan=2>tpl转码：<input type='text' value=\"comParse::urlBase64(&quot;$tpl&quot;) = [$entpl]\" class='txt w700' /></td></tr>\n";	
		echo "\n<tr><td colspan=2>url签名：<input type='text' value=\"$usign\" class='txt w700' /></td></tr>\n";	
		echo "\n<tr><td colspan=2 style='border-top:5px solid #99F;'>调用结果：<iframe src='$apiurl' style='width:100%; height:640px; overflow-y:scroll; overflow-x:hidden;' frameBorder=0></iframe></td></tr>\n";	
	}

	glbHtml::fmt_end(); //array("xxx|xxx")
	
}else{
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	
	glbHtml::fmt_end(array("mod|$mod"));
		
}

?>
