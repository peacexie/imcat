<?php
namespace imcat;
$_mod = basename(__FILE__,'.php');
require __DIR__.'/_cfgcom.php'; 

if(!empty($bsend)){
    
    $re2 = safComm::formCAll('fmcaddgbk');
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
    glbHtml::fmt_head('fmcaddgbk',"$aurl[1]",'tbdata');
        $vals = array();
        $skip = array('0','reply');
        foreach($mfields as $k=>$v){ 
            if(!in_array($k,$skip)){
                $item = fldView::fitem($k,$v,$vals);
                $item = fldView::fnext($mfields,$k,$vals,$item,$skip);
                glbHtml::fmae_row($v['title'],$item);
            }
        }
    $dop->fmPKey(1,0,1);
    $dop->fmProp(0,1);
    glbHtml::fmae_row(lang('vcode'),"<script>fsInit('fmcaddgbk','5,5','txt w80');</script>");
    $cls = basEnv::isMobile() ? 'tc' : 'tr';
    glbHtml::fmae_send('bsend',lang('submit'),0,$cls);

}

