<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(dirname(__FILE__)).'/apis/_pub_cfgs.php');

$part = req('part','delog'); 
$ord4 = basLang::ucfg('cfgbase.admord4a');

if($part=='dbsql'){
	
	define('RUN_DBSQL',1);
	$cfg = array(
		'sofields'=>array('sql','page','tpl'),
		'soorders'=>$ord4,
		'soarea'=>array('used',lang('admin.rl_rtime')),
	);
	$dop = new dopExtra('logs_dbsql',$cfg); 
	
	// 删除操作
	if(!empty($bsend)){
		require(dirname(dirname(__FILE__)).'/binc/act_ops.php');
	} 
	
	$lnkset = "(<a href='?file=admin/ediy&part=edit&dkey=cfgs&dsub=&efile=boot/const.cfg.php' onclick='return winOpen(this,\"".lang('admin.rl_setpm')."\");'>".lang('flow.title_set')."</a>)";
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$links = admPFunc::fileNav('dbsql','logs');
	$dop->sobar("$links$lnkset $umsg",40,array());
	
	// 清理操作
	if(!empty($bsend)&&$fs_do=='dnow'){
		$msg = $dop->opDelnow();
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&part=$part&flag=v1");
	}
		
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>sql</th><th>tpl@tag - View - keyid / page</th><th>run(ms)/time</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $sql = basSql::fmtShow($r['sql'],2);
		  $ext = "$r[tag]@$r[tpl] # <a href='$r[page]' target=_blank>View</a> # $r[kid]";
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tl'><textarea cols=60 rows=3>$sql</textarea></td>\n";
		  echo "<td class='tc'>$ext<br><input type='text' value='$r[page]' class='txt w240'/></td>\n";
		  echo "<td class='tc'>$r[used]<br>".date('m-d H:i:s',$r['atime'])."</td>\n";
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod","part|$part"));

}else{
	
	if(empty($mod) || !in_array($mod,array('detmp','syact'))) $mod = 'detmp';
	$cfg = array(
		'sofields'=>array('act','note','aip','aua'),
		'soorders'=>$ord4,
		'soarea'=>array('used',lang('admin.rl_rtime')),
	);
	$dop = new dopExtra("logs_$mod",$cfg); 
	
	// 删除操作
	if(!empty($bsend)){
		require(dirname(dirname(__FILE__)).'/binc/act_ops.php');
	}  
	
	$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
	$links = admPFunc::fileNav($mod,'logs');
	$dop->sobar("$links$umsg",40,array());
	
	// 清理操作
	if(!empty($bsend)&&$fs_do=='dnow'){
		$msg = $dop->opDelnow();
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&part=$part&flag=v1");
	}
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>".lang('flow.title_select')."</th><th>act/run</th><th>page/ref</th><th>note</th><th>User Agent</th><th>time/kid</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  $note = basStr::filForm($r['note']);
		  $aua = basStr::filForm($r['aua']);
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'><a href='$r[page]' target=_blank>$r[act]<br>$r[used]</a></td>\n";
		  echo "<td class='tc'><input type='text' value='$r[page]' class='txt w240'/><br><input type='text' value='$r[pref]' class='txt w240'/></td>\n";
		  echo "<td class='tl'><textarea cols=30 rows=3>$note</textarea></td>\n";
		  echo "<td class='tl'><textarea cols=30 rows=3>$aua</textarea></td>\n";
		  echo "<td class='tc'>".date('m-d H:i:s',$r['atime'])."<br>$r[aip]</td>\n";
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));

}

?>
