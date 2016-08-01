<?php
(!defined('RUN_MODE')) && die('No Init'); 

$mcfg = glbConfig::read('adfavor');
$mtab = 'advs_adfavor';

$stype = empty($stype) ? basReq::val('stype','afadmin') : $stype;
$actstr = empty($actstr) ? @$mcfg['i'][$stype]['title'] : $actstr;
 
$stsub = array(); $stids = "'0'";
$nav = "\n<table border='1' class='tbdata favor'><tr>";
foreach($mcfg['i'] as $ik=>$iv){
	if($iv['pid']==$stype){ 
		$stsub[$ik] = $iv;
		$nav .= "\n<th width='%'><li class='right c999'>$actstr</li><li class='left'>$iv[title]</li></th>"; 
		$stids .= ",'$ik'";
	}
}
$nav .= "</tr><tr>"; $nw = count($stsub)==0 ? 100 : 100/count($stsub);
$nav = str_replace("width='%'","width='$nw%'",$nav); 

$list = $db->table($mtab)->where("catid IN($stids) $whrself AND `show`='1'")->order('top')->select();
if(!empty($list)){
    echo "<div class='h05'>&nbsp;</div>\n$nav";
    foreach($stsub as $ik=>$iv){
    	echo "\n<td valign='top'>";
    	foreach($list as $v2){ if($v2['catid']==$ik){
    		echo "<li><a href='$v2[url]' target='_blank'>$v2[title]</a></li>"; 
    	}}
    	echo "</td>"; 
    }
}
echo "\n</tr></table>";