<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mod = empty($mod) ? 'docs' : $mod;
$view = empty($view) ? 'list' : $view;
$ispara = req('ispara','0'); //1,0
$catid = req('catid','0'); $cawhr = ($catid) ? "AND catid='$catid'" : ""; 
$tabid = 'base_fields'; if($ispara) $tabid = 'base_paras'; if($catid) $tabid = 'bext_fields';
$title = lang('flow.title_field'); if($ispara || $catid) $title = lang('admin.fls_paritem');
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@fields.php'); 

if($view=='ftest'){ 
    
        $lnkbak = "<a href='?$mkv&mod=$mod&view=list&ispara=$ispara&catid=$catid'>&lt;&lt;".lang('admin.fls_backflist')."</a>";
        glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$gname]".lang('admin.fls_fmres'),'---',40);
    if(empty($bsend)){ 
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        fldView::lists($mod,array('title'=>'(test)'.lang('admin.fls_test').date('H:i:s'),'author'=>',a,b,'),$catid);
        glbHtml::fmae_send('bsend',lang('flow.dops_send'));
        glbHtml::fmt_end();
    }else{
        echo "<pre>";
        print_r($_GET);
        print_r($_POST);
        echo "</pre>";
    }

}elseif($view=='fmove'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            $msg = lang('flow.msg_set');
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='move'){ 
                    glbDBExt::moveOneField($mod,$id);
                }
            }
        }
        glbCUpd::upd_model($mod);
    }     
    glbHtml::tab_bar("<span class='span ph5'>|</span>[$gname]{$title} ".lang('admin.fls_list')."<span class='span ph5'>|</span>",'Move Fields...',40);
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th>";
    echo "<th>".lang('admin.fls_extab')."</th><th title='".lang('admin.fls_maxlen')."'>".lang('admin.fls_lettes')."</th>";
    echo "</tr>\n";   
    $list = $db->table($tabid)->where("model='$mod' AND enable=1 AND dbtype!='nodb'")->order('top')->select();
    foreach($list as $r){
      $kid = $r['kid'];
      echo "<tr>\n"; $dis = in_array($kid,['title','color']) ? 'disabled' : ''; 
      echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' $dis /></td>\n";
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /> ".(empty($ispara) ? '' : $r['key'])."</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".($r['dbtype']=='nodb' ? '---' : glbHtml::null_cell($r['etab']))."</td>\n";
      echo "<td class='tr'>".glbHtml::null_cell($r['vmax'],'')." | ".glbHtml::null_cell($r['dblen'],'')."</td>\n";
      echo "</tr>"; 
    }
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption("move|Move")."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='fadd'){
    
    if(empty($bsend)){ 
        eimp('/~base/alib/field-edit.js');
        $url = $aurl[1]; //basReq::getURep(,'view','form');
        $fmextra_bak = "\n<select id='fmextra_bak' name='fmextra_bak' style='display:none;' >".basElm::setOption(fldCfgs::viewPlugs(),'')."</select>";
        $field_from = "\n<input id='fm[from]' name='fm[from]' type='hidden' value='' />"; 
        $vtip = lang('admin.fad_tip31245'); 
        $vstr = "url='".PATH_BASE."?ajax-cajax&act=".($catid ? 'fieldCatid' : 'fieldExists')."&mod=$mod&catid=$catid' tip='$vtip'";
        if($catid) $vstr .= " readonly";
        
        glbHtml::fmt_head('fmlist',$url,'tbdata');
        $picks = $catid ? fldCfgs::addType($mod,$catid) : fldCfgs::addPick($mod);
        echo "<tr><td colspan='2' class='tl h100'>$picks</td></tr>";
        $ftypes = fldCfgs::viewTypes(); if(in_array($_groups[$mod]['pid'],array('coms'))||!empty($catid)){ unset($ftypes['file']); } //互动/评论/参数:不要附件, 
        glbHtml::fmae_row(lang('admin.fls_fieldtype'),"<select id='fm[type]' name='fm[type]' class='w150' reg='str:1-12' tip='".lang('admin.fls_selftype')."' onChange='gf_setfmType(this)'>".basElm::setOption($ftypes,'')."</select>"); 
        glbHtml::fmae_row(lang('admin.fls_fcontrol'),"<select id='fm[fmextra]' name='fm[fmextra]' class='w150'><option value=''>".basLang::show('core.opt_first')."</option></select>$fmextra_bak$field_from"); 
        if(in_array($_groups[$mod]['pid'],array('docs'))&&$_groups[$mod]['etab']&&empty($catid)){ //,'types'
            $ops = basElm::setOption("0|".lang('admin.fe_mtab')."\n1|".lang('admin.fe_extab')."",'');
            glbHtml::fmae_row(lang('admin.fls_dbtab'),"<select id='fm[etab]' name='fm[etab]' class='w150' reg='str:1-12' tip='".lang('admin.fls_seldbtab')."'>$ops</select>");
        }else{
            echo "<input name='fm[etab]' id='fm[etab]' type='hidden' value='' />";
        }
        glbHtml::fmae_row(lang('flow.fl_kflag'),"<input id='fm[kid]' name='fm[kid]' type='text' value='' class='txt w150' maxlength='12' reg='key:3-12' $vstr />");
        glbHtml::fmae_send('bsend',lang('flow.dops_send'));
        glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid),"mod|$mod")); 
    }else{
        $paras = "";
        foreach(fldCfgs::addParas() as $k){
            $kv = isset($fm[$k]) ? $fm[$k] : '';
            $paras .= "&fm[$k]=$kv";    
        }
        $url = basReq::getURep($aurl[1],'view','form').$paras; 
        die(basMsg::dir($url));
    }

}elseif($view=='form'){
    
    if(!empty($bsend)){ 
        if($kid=='_isadd_'){
            $kid = $fm['kid']; 
            if($db->table($tabid)->where("model='$mod' AND kid='$fm[kid]' $cawhr")->find()){
                $msg = lang('flow.msg_exists',$fm['kid']);
            }else{
                $fm['model'] = $mod; if($catid) $fm['catid'] = $catid;
                $db->table($tabid)->data(basReq::in($fm))->insert();
                $msg = lang('flow.msg_add'); 
            }
        }else{
            $msg = lang('flow.msg_upd');unset($fm['kid']); 
            if(@$fm_null=='nul') $fm['vreg'] = 'nul:'.$fm['vreg']; 
            $db->table($tabid)->data(basReq::in($fm))->where("model='$mod' AND kid='$kid' $cawhr")->update();
        } 
        if(empty($ispara) && empty($catid)){ 
            if(!empty($fm['dbtype']) && $fm['dbtype']!='nodb') glbDBExt::setOneField($mod,$kid,'check');
        } 
        glbCUpd::upd_model($mod);
        echo basJscss::Alert($msg);    
        
    }else{
        
        eimp('/~base/alib/field-edit.js'); 
        $fm = fldEdit::fmOrgData($tabid,$mod,$kid,$fm,$catid);
        
        $fedit = new fldEdit($mod,$fm);
        $aurl[1] = substr($aurl[1],0,strpos($aurl[1],'&fm[type]')); // 去掉-url的fm
        glbHtml::fmt_head('fmlist',$aurl[1],'tbdata');
        $fedit->fmTypeOpts();
        $fedit->fmPlusPara();
        $fedit->fmParaKeys();
        $fedit->fmKeyName();
        $fedit->fmDbOpts();
        $fedit->fmRegOpts();
        $fedit->fmViewOpts();
        $fedit->fmRemCfgs();
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send')); 
        glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid),"mod|$mod"));

    }

}elseif($view=='list'){
    
    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id' $cawhr")->update(); 
                }elseif($fs_do=='del'){ echo 'xxx'; echo $id;
                     if(!empty($ispara)){
                         $db->table($tabid)->where("issys='0' AND model='$mod' AND kid='$id' $cawhr")->delete(); 
                     }elseif(!empty($catid)){
                         $db->table($tabid)->where("model='$mod' AND kid='$id' $cawhr")->delete(); 
                     }else{
                         $tmp = array('title','company');
                         if(isset($tmp[$id])){
                             $msg = lang('admin.fls_sysfield');
                         }else{
                             if(empty($ispara)&&empty($catid)) glbDBExt::setOneField($mod,$id); //if($fm['dbtype']!='nodb') 
                             $msg = lang('flow.msg_del');
                         }
                     }
                }elseif($fs_do=='show'){ 
                     $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id' $cawhr")->update();  
                }elseif($fs_do=='stop'){ 
                     $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id' $cawhr")->update(); 
                }
            }
        }
        glbCUpd::upd_model($mod);
        basMsg::show($msg,'Redir',"?$mkv&mod=$mod&ispara=$ispara&catid=$catid&flag=v1");
    }     

    $lnkbak = "<a href='?admin-groups&mod=".$_groups[$mod]['pid']."'>&lt;&lt;".lang('admin.fls_backmod')."</a>"; //&view=list&ispara=$ispara
    $lnkcat = "<a href='?admin-catalog&mod=$mod'>&lt;&lt;".lang('admin.fls_backcat')."</a>";
    $lnkgrd = "<a href='?admin-grade&mod=$mod'>&lt;&lt;".lang('admin.fls_backgrade')."</a>";
    $lnkbak = $catid ? ($_groups[$mod]['pid']=='users'? $lnkgrd : $lnkcat) : $lnkbak;
    $lnkadd = "<a href='?$mkv&mod=$mod&view=fadd&ispara=$ispara&catid=$catid' onclick='return winOpen(this,\"".lang('admin.fls_addfield')."\")'>".lang('admin.fls_add')." $title&gt;&gt;</a>"; 
    $lnkform = $_groups[$mod]['pid']=='docs' ? "<a href='?$mkv&mod=$mod&view=fmove'>&lt;&lt;Move</a> | " : '';
    $lnkform .= "<a href='?$mkv&mod=$mod&view=ftest&ispara=$ispara&catid=$catid'>".lang('admin.fls_fmres')."&gt;&gt;</a>";
    glbHtml::tab_bar("$lnkbak<span class='span ph5'>|</span>[$gname]{$title} ".lang('admin.fls_list')."<span class='span ph5'>|</span>$lnkadd",$lnkform,40);
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th>";
    if(empty($ispara)&&empty($catid)) echo "<th>".lang('flow.title_enable')."</th><th>".lang('admin.fls_extab')."</th>";
    echo "<th>".lang('admin.fls_type')."</th><th>".(empty($ispara) ? lang('admin.fls_db') : lang('admin.fls_nowval'))."</th><th title='".lang('admin.fls_maxlen')."'>".lang('admin.fls_lettes')."</th>";    
    echo "<th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n";    
    if(!empty($catid)){
        $_dbmfields = $db->fields(glbDBExt::getTable($mod)); 
    }
    $list = $db->table($tabid)->where("model='$mod' $cawhr")->order('enable DESC,top')->select();
    if($list){
    foreach($list as $r){
      $kid = $r['kid'];
      $note = basReq::out($r['vreg']).' | '.basReq::out($r['vtip']);
      $note = $note==' | ' ? '' : $note;
      $types = fldCfgs::viewTypes();
      $plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$r['fmextra']]) ? ' ('.$plugs[$r['fmextra']].')' : '';
      if($ispara){
          $dbstr = "<input type='text' value='".basStr::filForm($r['val'])."' class='txt w80 disc' disabled='disabled' />";
      }else{ //basStr::cutWidth($r['dbdef'],3,'..')
          $dbstr = "$r[dbtype] ".(empty($r['dblen'])?'':"($r[dblen])").(strlen($r['dbdef'])?' ['.$r['dbdef'].']':'');
      }
      if(!empty($catid)){
            $exstr = (!empty($catid) && isset($_dbmfields[$kid])) ? '' : "<span class='cF00'>".lang('admin.fls_nofield')."</span>"; 
      }else{
          $exstr = '';
      }
      echo "<tr>\n";
      echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /> ".(empty($ispara) ? '' : $r['key'])."$exstr</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      if(empty($ispara)&&empty($catid)) echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      if(empty($ispara)&&empty($catid)) echo "<td class='tc'>".($r['dbtype']=='nodb' ? '---' : glbHtml::null_cell($r['etab']))."</td>\n";
      echo "<td class='tl'>".$types[$r['type']]." $plugstr</td>\n";
      echo "<td class='tc'>$dbstr</td>\n";
      echo "<td class='tr'>".glbHtml::null_cell($r['vmax'],'')." | ".glbHtml::null_cell($r['dblen'],'')."</td>\n";
      echo "<td class='tc'><a href='?$mkv&mod=$mod&view=form&kid=$r[kid]&ispara=$ispara&catid=$catid' onclick='return winOpen(this,\"".lang('flow.title_edit')." $title\")'>".lang('flow.title_edit')."</a></td>\n";
      echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
      echo "</tr>"; 
    }} 
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}

?>
