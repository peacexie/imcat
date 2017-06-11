<?php
$_cbase['ucfg']['lang'] = '(auto)'; 
require dirname(dirname(dirname(__FILE__))).'/run/_init.php'; 
$lang = $_cbase['sys']['lang']; 

function pfcfgParts(){
    $re = basLang::ucfg('urparts'); 
    if(strlen(req('kid','','Key'))<10){ unset($re['now']); }
    return $re;
}
function pfcfgPars($arr=1){
    $re = array(); $allpars = "";
    foreach(array('act,form','fid,content','parts,comms','dir,logo','mod,demo','kid,,Key') as $t){
        $a = explode(',',$t);
        $v = req($a[0], $a[1], empty($a[2])?'Title':$a[2]);
        $re[$a[0]] = $v;
        $allpars .= "&$a[0]=$v";
    }
    $allpars = substr($allpars,1);
    if(empty($arr)) return $allpars;
    $re['allpars'] = substr($allpars,1);
    return $re;
}

function pfileHead($parts,$title=''){
    glbHtml::page($title,1);
    glbHtml::page('imadm',array('js'=>'/plus/file/funcs.js','css'=>'/plus/file/style.css',)); 
    glbHtml::page('body',' style="margin:0px 2px;"');
    $allpars = pfcfgPars(0);
    $cfg_parts = pfcfgParts();
    $cfg_dirs = read('urdirs','sy');
    $mod = req('mod','demo');
    $str = "\n<table class='file_bar'><tr>";
    $tmppars = basReq::getURep($allpars,'parts','{p}');
    foreach($cfg_parts as $k=>$v){ 
        $v0 = $k; $t0 = $v; 
        if(is_array($v)){ $v0 = $v[1]; $t0 = $v[0]; }
        $paras = basReq::getURep(str_replace('{p}',$k,$tmppars),'dir',$v0);
        $script = in_array($k,array('upbat','media')) ? $k : 'fview';
        $str .= "\n<td ".($parts==$k?'class="act"':'')."><a href='$script.php?".$paras."'>$t0</a></td>";
    } 
    $str .= "\n</tr></table>";
    echo $str;
}

$groups = read('groups');
extract(pfcfgPars(), EXTR_OVERWRITE); 

$user = user(array('Admin','Member'));
