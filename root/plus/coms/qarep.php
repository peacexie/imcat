<?php
namespace imcat;
$_mod = basename(__FILE__,'.php');
require dirname(__FILE__).'/_cfgcom.php'; 

$pinfo = dopFunc::getMinfo('faqs',$pid); 
if($pinfo['bugst']=='close'){
    basMsg::show(lang('plus.coms_closerep'),'die');
}

if(!empty($bsend)){
    
    $re2 = safComm::formCAll('fmqarep');
    if(!empty($re2[0])){ 
        dopCheck::headComm();
        basMsg::show(lang('plus.coms_errvcode'),'die');
    }

    $dop->svPrep(); 
    $dop->svAKey();
    $dop->svPKey('add');
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    dopCheck::headComm();
    basMsg::show(lang('plus.coms_addok',$_groups[$mod]['title']),'prClose');
    
}else{

    dopCheck::headComm();
    $dop->fmo = $fmo = array();
    glbHtml::fmt_head('fmqarep',"$aurl[1]",'tbdata');
    fldView::lists($mod,$fmo);
    $dop->fmPKey(1,0,1);
    $dop->fmProp(0,1);
    glbHtml::fmae_row(lang('vcode'),"<script>fsInit('fmqarep','5,-32','txt w80');</script>");
    glbHtml::fmae_send('bsend',lang('submit'),0,'tr');
}


