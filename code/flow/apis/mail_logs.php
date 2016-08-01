<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$cfg = array(
	'sofields'=>array('title','ufrom','uto','detail','stat'),
	'soorders'=>array('atime' => '操作时间(降)','atime-a' => '操作时间(升)'),
	//'soarea'=>array('amount','金额'),
);
$dop = new dopExtra('plus_emsend',$cfg); 

// 删除操作
if(!empty($bsend)){
	require(dirname(dirname(__FILE__)).'/binc/act_ops.php');
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($view=='vcfgs' ? 'vcfgs' : 'vlist','mail');
$dop->sobar("$links$umsg",40,array());

if($view=='vcfgs'){
	
	$cfgs = glbConfig::read('mail','ex');
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	if(empty($cfgs)){
		echo "\n<tr><td class='tc w180'>提示：</td>\n<td>
			当前无配置；<br>请复制文件{code}/cfgs/excfg/ex_mail.php-demo为ex_mail.php；<br>并修改参数完成配置
		</td></tr>\n";
	}else{ 
		echo "\n<tr><td class='tc w150'>当前配置：</td>\n<td>当前配置文件{code}/cfgs/excfg/ex_mail.php，
		<a href='?file=admin/ediy&part=edit&dkey=cfgs&dsub=&efile=excfg/ex_mail.php' onclick=\"return winOpen(this,'修改配置',780,560);\">修改</a></td></tr>\n";
		foreach($cfgs as $key=>$v){
			echo "\n<tr><td class='tc'>{$key}：</td>\n<td>$v</td></tr>\n";
		}
	}
	glbHtml::fmt_end(array("mod|$mod"));
	
}else{
	// 清理操作
	if(!empty($bsend)&&$fs_do=='dnow'){
		$msg = $dop->opDelnow();
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
	}  
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>title</th><th>ufrom</th><th>uto</th><th>api</th><th>stat</th><th>aip</th><th>操作时间</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$r[title]</td>\n";
		  echo "<td class='tc'>$r[ufrom]</td>\n";
		  echo "<td class='tc'>$r[uto]</td>\n";
		  echo "<td class='tc'>$r[api]</td>\n";
		  echo "<td class='tc'>$r[stat]</td>\n";
		  echo "<td class='tc'>$r[aip]</td>\n";
		  echo "<td class='tc'>".date('Y-m-d H:i',$r['atime'])."</td>\n";
		  echo "</tr>";
		}
		$dop->pgbar($idfirst,$idend);
	}else{
		echo "\n<tr><td class='tc' colspan='15'>无资料！</td></tr>\n";
	}
	glbHtml::fmt_end(array("mod|$mod"));	
}

?>
