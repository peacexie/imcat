<?php
(!defined('RUN_MODE')) && die('No Init'); 
usrPerm::run('pfile','admin/paras.php');

$mod = empty($mod) ? 'prcore' : $mod;
//$ispara = 1; //1,0
$tabid = 'base_paras';
$title = '参数项';
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end('参数错误:mod@paras.php'); 
$gpid = $_groups[$mod]['pid'];

$aval = glbCUpd::upd_paras($gpid, '');
//print_r($aval);

if($view=='upd'){
	
	glbCUpd::upd_paras($gpid);
	echo "\n<hr>更新完毕！<hr>".basDebug::runInfo();

}elseif(!empty($bsend)){

	foreach($fm as $k=>$v){
		$db->table($tabid)->data(array('val'=>$v))->where("kid='$k'")->update(); 
		//echo "<br>".$db->sql;
	}
	glbCUpd::upd_paras($gpid);
	echo basJscss::Alert('修改完毕！','Redir',"$aurl[1]");
	
}else{

	$gbar = admAFunc::grpNav($gpid,$mod); 
	$lnkbak = "<a href='?file=admin/groups&mod=$gpid'>&lt;&lt;返回模型</a>";
	$lnkadd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"更新参数\",300,200);'>[更新参数]&gt;&gt;</a>";
	glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[{$gname}]设置<span class='span ph5'>|</span>$lnkadd",$gbar,40);

	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	fldView::lists($mod,$aval);
	glbHtml::fmae_send('bsend','提交');
	if($mod=='prsafe'){
		echo "<tr><th>随机码参考1</th><th class='tr'>---</th></tr>\n";
		echo "<tr><td class='tc'>['safe']['site']</td> <td class='f16 ffvs'>name"; for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('f',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['pass']</td> <td class='f16 ffvs'>pass"; for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('fs3',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['api']</td>  <td class='f16 ffvs'>api";  for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('f',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['js']</td>   <td class='f16 ffvs'>js";   for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('f',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['other']</td><td class='f16 ffvs'>other";for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('f',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['safil']</td><td class='f16 ffvs'>safil";for($i=0;$i<5;$i++) echo '-'.basKeyid::kidRand('fs3',5); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['safix']</td><td class='f16 ffvs'>_";    echo basKeyid::kidRand('0',3); echo " &nbsp; &nbsp; (_数字开头)</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['rnum']</td> <td class='f16 ffvs'>";     echo basKeyid::kidRand('0',24); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['rspe']</td> <td class='f16 ffvs'>spe";  for($i=0;$i<3;$i++) echo '-'.basKeyid::kidRand('fs1',8); echo "</td></tr>\n";
		echo "<tr><td class='tc'>['safe']['rndtab']</td> <td class='f16 ffvs'>";   echo basKeyid::kidRTable('f');                           echo "</td></tr>\n";
		//62个字符,任意换位置; 但不能重复,否则出错; 可用basKeyid::kidRTable('f')生成
		echo "<tr><th>随机码参考2</th><th class='tr'>---</th></tr>\n";
		foreach(array('0','a','k','30','24','s','1','2','3','f','fs','fs1','fs2','fs3','~else~') as $k){
			echo "<tr><td class='tc'>basKeyid::kidRand('$k',36)</td> <td class='f16 ffvs'>"; echo basKeyid::kidRand($k,36); echo "</td></tr>\n";
		}
	}
	glbHtml::fmt_end();
}


?>
