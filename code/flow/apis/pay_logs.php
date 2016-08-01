<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$cfg = array(
	'sofields'=>array('ordid','apino','api','expar'),
	'soorders'=>array('amount' => '金额(降)','amount-a' => '金额(升)','atime' => '操作时间(降)','atime-a' => '操作时间(升)'),
	'soarea'=>array('amount','金额'),
);
$dop = new dopExtra('plus_paylog',$cfg); 

// 删除操作
if(!empty($bsend)){
	require(dirname(dirname(__FILE__)).'/binc/act_ops.php');
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($view=='vcfgs' ? 'vcfgs' : 'vlist','pay');
$dop->sobar("$links$umsg",40,array());

if($view=='vcfgs'){

	$cfgs = exvOpay::getCfgs();
	//print_r($cfgs);
	$para = glbDBExt::getExtp('paymode_%');
	//print_r($para);
	
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>接口ID</th><th>方法</th><th>目录</th><th>说明</th><th>配置</th></tr>\n";
	foreach($cfgs as $key=>$r){ 
	  $title = isset($para[$key]['title']) ? $para[$key]['title'] : '---';
	  $detail = isset($para[$key]['detail']) ? $para[$key]['detail'] : '---';
	  echo "<td class='tc'>$key</td>\n";
	  echo "<td class='tc'>$r[method]</td>\n";
	  echo "<td class='tc'>{root}/a3rd/$r[dir]</td>\n";
	  echo "<td class='tc'>$title</td>\n";
	  echo "<td class='tc'>$detail</td>\n";
	  echo "</tr>";
	}
	echo "\n<tr><td colspan='5'>
	1. <a href='?file=apis/exp_order&pid=paymode_cn&frame=1' target='_blank'>付款配置</a> <br>
	2. 二次开发文档。
	
	</td></tr>\n";
	glbHtml::fmt_end(array("mod|$mod"));
	
}else{
	// 清理操作
	if(!empty($bsend)&&$fs_do=='dnow'){
		$msg = $dop->opDelnow();
		basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
	}
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
	echo "<th>选择</th><th>ordid</th><th>apino</th><th>amount</th><th>api</th><th>stat</th><th>操作时间</th></tr>\n";
	$idfirst = ''; $idend = '';
	if($rs=$dop->getRecs()){ 
		foreach($rs as $r){ 
		  $kid = $idend = $r['kid'];
		  if(empty($idfirst)) $idfirst = $kid;
		  echo "<tr>\n".$cv->Select($kid);
		  echo "<td class='tc'>$r[ordid]</td>\n";
		  echo "<td class='tc'>$r[apino]</td>\n";
		  echo "<td class='tc'>$r[amount]</td>\n";
		  echo "<td class='tc'>$r[api]</td>\n";
		  echo "<td class='tc'>$r[stat]</td>\n";
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
