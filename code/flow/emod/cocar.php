<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 

$msg = ''; $tabext = '';

if($view=='clear'){
    $msg = lang('flow.dops_clearok');
    if($mod=='coitem'){
        $pids = glbDBExt::getKids('corder','title','1=1'); 
        $db->table($dop->tbid)->where("ordid NOT IN($pids)")->delete(); 
    }else{
        $db->table($dop->tbid)->where("atime<'".($_cbase['run']['stamp']-3*86400)."'")->delete(); 
    }
    $view = 'list';
}
 
if($view=='list'){

    if(!empty($bsend)){
        require dopFunc::modAct($_scdir,'list_do',$mod,$dop->type);
    } 
    
    $sbar = "\n".$so->Type(90,'-pKey-'); 
    $sbar .= "\n&nbsp; ".$so->Word(80,80,lang('flow.op0_filt'));
    $sbar .= "\n&nbsp; ".$so->Order(array('cid' => 'ID(D)','cid-a' => 'ID(A)',));
    $snav = admPFunc::fileNav($mod,'ordnav'); $msg = $msg ? "<span class='cF00'>$msg</span>" : ' '; 
    $so->Form($sbar,$dop->msgBar($snav,$msg),40);

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    basLang::inc('aflow', 'cocar');
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $cid = $idend = $r['cid'];
          if(empty($idfirst)) $idfirst = $cid;
          echo $cv->Select($cid);
          echo $cv->Field($r['pid']);
          echo $cv->Field($r['ordid'],1,15);
          echo $cv->Field($r['ordcnt']);
          echo $cv->Field($r['ordprice']);
          echo $cv->Field($r['auser']);
          echo $cv->Time($r['atime']);
          echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
          echo "</tr>"; 
        }
        $pg = $dop->pg->show($idfirst,$idend); 
        $op = "".basElm::setOption(lang('flow.dops_del'),'',lang('flow.op0_bacth')); //\nclear|清理
        dopFunc::pageBar($pg." &nbsp; <a href='$aurl[1]&view=clear'>".lang('flow.dops_clear')."</a>",$op);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='form'){
    if(!empty($bsend)){
        require dopFunc::modAct($_scdir,'form_do',$mod,$dop->type);
    }else{
        require dopFunc::modAct($_scdir,'form_show',$mod,$dop->type);
    }
}elseif($view=='xset'){
    ;//
}
