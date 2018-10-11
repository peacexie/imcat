<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mcfg = read('adfavor');
$mtab = 'advs_adfavor';

$stype = empty($stype) ? req('stype','afadmin') : $stype;
$actstr = empty($actstr) ? @$mcfg['i'][$stype]['title'] : $actstr;
 
$stsub = $titles = array(); $stids = "'0'";
//$nav = "\n<div class='row'>";
foreach($mcfg['i'] as $ik=>$iv){
    if($iv['pid']==$stype){ 
        $stsub[$ik] = $iv;
        $titles[$ik] = "\n<li class='list-group-item'><div class='right c999'>$actstr</div>$iv[title]</li>\n"; 
        $stids .= ",'$ik'";
    }
}
//$nav .= "</div>\n<div class='row'>"; $nw = count($stsub)==0 ? 100 : 100/count($stsub);
//$nav = str_replace("width='%'","width='$nw%'",$nav); 

$list = $db->table($mtab)->where("catid IN($stids) $whrself AND `show`='1'")->order('top')->select();
echo "<div class='row'>";
if(!empty($list)){
    //echo "<div class='h05'>&nbsp;</div>\n";
    foreach($stsub as $ik=>$iv){
        echo "\n<div class='col-md-3'>";
        echo "<ul class='list-group'>$titles[$ik]\n";
        foreach($list as $v2){ if($v2['catid']==$ik){
            echo "<li class='list-group-item'><a href='$v2[url]' target='_blank'>$v2[title]</a></li>\n"; 
        }}
        echo "</ul></div>"; 
    }
}
echo "\n</div>";