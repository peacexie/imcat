<?php
(!defined('RUN_INIT')) && die('No Init'); 

$msg = ''; $tabext = '';
if($view=='list'){
    if(!empty($bsend)){
        require dopFunc::modAct($_scdir,'list_do',$mod,$dop->type);
    } //$dop->whrstr = " AND "; $_mpid,
    
    $dop->sobar($dop->msgBar($msg)); 
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_cata')."</th><th>".lang('flow.title_enable')."</th>";
    echo "<th>".lang('flow.log_atime')."</th><th>Users</th><th>Items</th>";
    echo "<th>".lang('flow.title_edit')."</th></tr>";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $did = $idend = $r['did'];
          if(empty($idfirst)) $idfirst = $did;
          echo "<tr>\n";
          echo $cv->Select($did);
          echo $cv->Title($r,1,'title',"");
          echo $cv->Types($r['catid']);
          echo $cv->Show($r['show']);
          echo $cv->Time($r['atime']);
          echo $cv->Field($r['auser']);
          echo $cv->Url('Items',1,"?file=dops/a&mod=votei&pid=$r[did]","");
          echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&did=$r[did]&recbk=ref","");
          echo "</tr>"; 
        }
        $dop->pgbar($idfirst,$idend);
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
}elseif($view=='set'){
    ;//utf-8编码
}
