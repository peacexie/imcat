<?php
namespace imcat;
require __DIR__.'/_cfgcom.php'; 

$adeel = __DIR__."/{$mod}.php"; 
if(file_exists($adeel)){ die("It must deel with [{$mod}.php]." ); }
$aform = __DIR__."/{$mod}_form.php";
$asave = __DIR__."/{$mod}_save.php";

if(!empty($bsend)){
    
    $re2 = safComm::formCAll('fmcomadd');
    if(!empty($re2[0])){ 
        dopCheck::headComm();
        basMsg::show(lang('plus.coms_errvcode'),'die');
    }
    
    if(file_exists($asave)){
        require $asave;
    }else{
        $dop->svPrep(); 
        $dop->svAKey();
        $dop->svPKey('add');
        $db->table($dop->tbid)->data($dop->fmv)->insert(); 
        dopCheck::headComm();
        basMsg::show(lang('plus.coms_addok',$_groups[$mod]['title']),'prClose');
    }
    
}else{
    
    if(file_exists($aform)){
        require $aform;
    }else{
        dopCheck::headComm();
        $dop->fmo = $fmo = array();
        glbHtml::fmt_head('fmcomadd',"$aurl[1]",'tbdata');
        fldView::lists($mod,$fmo);
        $dop->fmPKey(1,0,1);
        $dop->fmProp(0,1);
        glbHtml::fmae_row(lang('vcode'),"<script>fsInit('fmcomadd','5,5','txt w80');</script>");
        glbHtml::fmae_send('bsend',lang('submit'),0,'tr');
    }
}


