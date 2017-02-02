<?php
(!defined('RUN_INIT')) && die('No Init'); 

$msg = ''; $tabext = '';

if($view=='clear'){
    $msg = lang('flow.dops_clearok');
    /*if($mod=='coitem'){
        $pids = glbDBExt::getKids('corder','title','1=1'); 
        $db->table($dop->tbid)->where("ordid NOT IN($pids)")->delete(); 
    }else{
        $db->table($dop->tbid)->where("atime<'".($_cbase['run']['stamp']-3*86400)."'")->delete(); 
    }*/
    $view = 'list';
}

if($view=='list'){
    if(!empty($bsend)){
        
        $fs_do = req('fs_do');
        $fs = basReq::arr('fs'); 
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.dops_setitem');
        $cnt = 0; $msgop = '';
        foreach($fs as $id=>$v){ 
            if(in_array($fs_do,array('show','hidden'))){ 
                $cnt += $dop->opShow($id,$fs_do);
                $msgop = $fs_do=='show' ? lang('flow.dops_checked') : lang('flow.dops_hide');
            }elseif($fs_do=='del'){ 
                $cnt += $dop->opDelete($id);
                $db->table('coms_coitem')->where("title='$id'")->delete(); 
                $msgop = lang('flow.dops_del');
            }elseif(strstr($fs_do,'set_')){ 
                $v = basStr::filKey(str_replace('set_','',$fs_do),'_-.');
                $cnt += $db->table($dop->tbid)->data(array('ordstat'=>$v))->where("$dop->_kid='$id'")->update();
            }
        }
        $msg = "$cnt ".lang('flow.dops_okn',$msgop);
    } 
    
    $sbar = "\n".$so->Type(90,'-pKey-'); 
    $sbar .= "\n&nbsp; ".$so->Word(80,80,lang('flow.op0_filt'));
    $sbar .= "\n&nbsp; ".$so->Field('ordstat',60);
    $sbar .= "\n&nbsp; ".$so->Order(array('cid' => 'ID(Desc)','cid-a' => 'ID(Asc)',));
    $snav = admPFunc::fileNav($mod,'ordnav'); $msg = $msg ? "<span class='cF00'>$msg</span>" : ' '; 
    $so->Form($sbar,$dop->msgBar($snav,$msg),40);

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    basLang::inc('aflow', 'corder');
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $cid = $idend = $r['cid'];
          if(empty($idfirst)) $idfirst = $cid;
          echo $cv->Select($cid);
          //echo $cv->Field($r['title'],1,64);  
          echo $cv->Url($r['title'],1,surl("chn:0",'')."?mkv=ocar-invoce&ordid=$cid","blank");
          echo $cv->TKeys($r,1,'ordstat',12,'-');
          echo $cv->Field($r['feetotle']);
          echo $cv->Field($r['ordcnt']);
          echo $cv->Field($r['feeamount']);
          echo $cv->Field($r['trakeno'],1,64);
          echo $cv->Field($r['mname']);
          echo $cv->Field($r['mtel'],1,16);
          echo $cv->Time($r['atime']);
          echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&cid=$r[cid]&recbk=ref","");
          echo "</tr>"; 
        }
        $pg = $dop->pg->show($idfirst,$idend); 
        $op = basElm::setOption("del,".lang('flow.dops_del').";".($cv->set_opts('ordstat'))."",'',lang('flow.op0_bacth')); //\ndnow|删除当前
        dopFunc::pageBar($pg." &nbsp; <a href='$aurl[1]&view=clear'>".lang('flow.dops_clear')."</a>",$op);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='form'){
    if(!empty($bsend)){
        require(dopFunc::modAct($_scdir,'form_do',$mod,$dop->type));
    }else{
        require(dopFunc::modAct($_scdir,'form_show',$mod,$dop->type));
    }
}elseif($view=='set'){
    ;//
}
