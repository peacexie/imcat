<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 

$fm = basReq::arr('fm','Html'); 
$from = req('from');

if(req('umod')=='upd'){
    echo "<p class='tc'><a href='?$mkv&mod=$mod&view=list'>".lang('flow.dops_back')."</a></p>";
}

$msg = ''; $tabext = ''; 
if($view=='list'){
    
    if(!empty($bsend)){
        require dopFunc::modAct('list_do',$mod,$dop->type);
    } 

    $dop->sobar($dop->msgBar($msg));
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    basLang::inc('aflow', 'adpush_list', $dop->cfg);
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $aid = $idend = $r['aid'];
          if(empty($idfirst)) $idfirst = $aid;
          echo $cv->Select($aid);
          echo $cv->Field($r['aid']);
          $title = $cv->Title($r,1,'title',$dop->avLink($r),64);
          echo str_replace(array('[附]','[File]'),'[cfg]',$title);
          echo $cv->Types($r['catid']);
          echo $cv->Show($r['show']);
          echo $cv->Field($r['click']);
          echo $cv->Time($r['atime']); 
          echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&aid=$r[aid]&recbk=ref","");
          echo $cv->Url(lang('flow.ps_title'),1,"$aurl[1]&view=push&aid=$r[aid]&recbk=ref",lang('flow.ps_pinfo'),750);
          echo $cv->Url('Page',1,PATH_PROJ.$r['url']."home&adpush=load","blank");
          $mpic = "<input name='fm_[note]' type='text' value='$r[mpic]' class='txt w120' /> ";
          $detail = "<input name='fm_[detail]' type='text' value='$r[detail]' class='txt w60' />";
          echo "<td class='tl'>$mpic$detail</td>\n";
          echo "</tr>"; 
        }
        $dop->pgbar($idfirst,$idend);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='form'){
    
    if(!empty($bsend)){
        $dop->svPrep(); 
        //$org = $dop->fmv;
        $dop->fmv = basReq::in($dop->fmv);
        $dop->fmv['mpic'] = $fm['mpic'];
        $dop->fmv['detail'] = $fm['detail'];
        if(!empty($isadd)){ 
            $dop->svAKey();
            $dop->svPKey('add');
            //$dop->fmv['mpic'] = $fm['mpic'];
            $db->table($dop->tbid)->data($dop->fmv)->insert(); 
            $actm = lang('flow.dops_add');
            $aid = $dop->fmv['aid'];
        }else{ 
            $aid = $dop->svEKey();
            $dop->svPKey('edit');
            //$dop->fmv['mpic'] = $fm['mpic'];
            $db->table($dop->tbid)->data($dop->fmv)->where("aid='$aid'")->update();
            $actm = lang('flow.dops_edit');
        }
        basMsg::show("$actm".lang('flow.dops_ok'));    

    }else{

        if(!empty($aid)){
            $fmo = $db->table($dop->tbid)->where("aid='$aid'")->find(); 
            $isadd = 0;
        }else{
            $fmo = array();
            $isadd = 1;
        }
        $dop->fmo = $fmo; 
        $mfields = read('adpush.f'); 
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        $dop->fmPKey();
            $skip = array('mpic','url'); // url:where/paras:eg:hinfo>`0`
            foreach($mfields as $k=>$v){ 
                if(!in_array($k,$skip)){
                    if($k=='detail'){
                        $rapars = " id='fm[mpic]' name='fm[mpic]' rows='5' class='txt' style='width:480px'";
                        $itmd = "<textarea $rapars />".@$fmo['mpic']."</textarea>"; 
                        glbHtml::fmae_row('cfgs',$itmd);
                    }elseif($k=='click'){
                        $v['title'] = 'Max/Page';
                        $cext = " <input id='fm[url]' name='fm[url]' type='text' value='".@$fmo['url']."' class='txt w240' maxlength='255' tip='/dev.php?tpltag-adpush' >";
                        //glbHtml::fmae_row('cfgs',$itmd);
                    }
                    $item = fldView::fitem($k,$v,$fmo);
                    $item = fldView::fnext($mfields,$k,$fmo,$item,$skip);
                    glbHtml::fmae_row($v['title'],$item.@$cext);
                }
            }
        $dop->fmProp();
        glbHtml::fmae_send('bsend',lang('flow.dops_send'));
        glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));

    }

}elseif($view=='push' && !empty($aid)){ 
    
    if(!empty($bsend)){
        $data = comParse::jsonEncode($fm); 
        $db->table($dop->tbid)->data(basReq::in(array('detail'=>$data)))->where("aid='$aid'")->update();
        /*if(!empty($from)){ // omkv,otpl
            $url = "?ajax-cron&static=$omkv&tpldir=$otpl&act=mkv";
            echo "\n<iframe src='".PATH_BASE."$url"."' width='100%'></iframe>";
        }*/
        die(basJscss::Alert('Push OK!','prClose'));
    }

    $cfg = $db->table($dop->tbid)->where("aid='$aid'")->find(); 
    $fields = comParse::jsonDecode($cfg['mpic']); 
    $data = comParse::jsonDecode($cfg['detail']); 
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<tr>";
    foreach ($fields as $fn => $itm) {
        echo "<th width='$itm[w]'>$itm[t]</th>";
    }
    echo "<th>Pick</th></tr>";
    for($i=0;$i<$cfg['click'];$i++) {
        echo "<tr flag-row='fill-pic-row'>";
        foreach ($fields as $fn => $itm) {
            $val = empty($data[$i][$fn]) ? '' : $data[$i][$fn];
            $inp = "<input id='fm[$i][$fn]' name='fm[$i][$fn]' type='text' value='$val' style='width:100%' />";
            if(!empty($itm['tab'])){
                $ops = tagPush::opts($itm['tab'],$val);
                $inp = "<select id='fm[$i][$fn]' name='fm[$i][$fn]' style='width:100%'>$ops</select>";
            }
            echo "<td>$inp</td>";
        }
        $pick = "<a onclick=\"pickOpen('$cfg[pmod]','','fm[$i][pid]','fm[$i][title]',1)\">Pick</a>";
        $pick .= "<input id='fm[$i][pid]' name='fm[$i][pid]' type='hidden' value='' />";
        echo "<td class='tc' style='width:6%'>$pick</th></td>";
    }
    $submit = "<input name='bsend' type='submit' class='btn' value='".lang('flow.ps_send')."' />";
    echo "<tr><td class='tc' width='15%'>".lang('flow.ps_send')."</td>\n";
    echo "<td class='tc' colspan='".(count($fields))."'>$submit</td></tr>\n";
    glbHtml::fmt_end(array("mod|$mod","aid|$aid","view|$view"));

}
