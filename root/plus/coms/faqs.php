<?php
$_mod = basename(__FILE__,'.php'); 
require(dirname(__FILE__).'/_cfgdoc.php'); 

if(!empty($bsend)){
    
    $re2 = safComm::formCAll('fmdocfaqs');
    if(!empty($re2[0])){ 
        dopCheck::headComm();
        basMsg::show(lang('plus.coms_errvcode'),'die');
    }

    $dop->svPrep(); 
    $dop->svAKey();
    //$dop->svPKey('add');
    $dop->fmv['show'] = 0;
    $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    $db->table($dop->tbext)->data($dop->fmu)->insert(0);
    dopCheck::headComm();
    basMsg::show(lang('plus.coms_addok',$_groups[$mod]['title']),'prClose');
    
}else{
    
    dopCheck::headComm();
    $dop->fmo = $fmo = array();
    glbHtml::fmt_head('fmdocfaqs',"$aurl[1]",'tbdata'); 
    glbHtml::fmae_row(lang('flow.dops_icat'),$dop->fmType('catid').'');
    glbHtml::fmae_row(lang('flow.dops_ishow'),$dop->fmShow(),1);
    $vals = array();
    $skip = array('0','mpic','hinfo','jump','click','author','bugid','bugst');
    $mfields['detail']['fmsize'] = '480x18';
    foreach($mfields as $k=>$v){ 
        if(!in_array($k,$skip)){
            $item = fldView::fitem($k,$v,$vals);
            $item = fldView::fnext($mfields,$k,$vals,$item,$skip);
            glbHtml::fmae_row($v['title'],$item);
        }
    }
    $dop->fmAE3(1);
    glbHtml::fmae_row(lang('vcode'),"<script>fsInit('fmdocfaqs','5,-32','txt w80');</script>");
    glbHtml::fmae_send('bsend',lang('submit'),0,'tr');

}

/*

*/