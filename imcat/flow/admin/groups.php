<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mod = empty($mod) ? 'groups' : $mod;
$view = empty($view) ? 'glist' : $view;
$tabid = 'base_model';
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@groups.php'); 
$gbar = admAFunc::grpNav('groups',$mod);
$advetabs = basLang::ucfg('cfglibs.advs_type');

if($view=='glist'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update(); 
                }elseif($fs_do=='del'){ 
                    $msg = admAFunc::modCopy($mod, $tabid, 'is_del', $id);
                }elseif($fs_do=='show'){ 
                    $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
                }elseif($fs_do=='stop'){ 
                    $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
                }
                if($mod!='groups' && $fs_do!='del') glbCUpd::upd_model($id);
            }
        }
        glbCUpd::upd_groups(); 
        if($fs_do!='del' && in_array($mod,array('score','sadm','smem','suser',))){ 
            glbCUpd::upd_paras($mod);
        }
        basMsg::show($msg,'Redir',"?$mkv&mod=$mod&flag=v1");
    } 

    $list = $db->table($tabid)->where("pid='$mod'")->order('top')->select(); 
    
    $lnkadd = "<a href='$aurl[1]&view=gform' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    glbHtml::tab_bar("[{$gname}]".lang('admin.fad_adset')."<span class='span ph5'>|</span>$lnkadd",$gbar,30);
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name').(in_array($mod,array('docs','coms','users')) ? '/'.lang('admin.fad_rmod') : '')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th>";
    $fhd3 = "<th>".lang('flow.title_fields')."</th><th>".lang('flow.title_set')."</th><th>".lang('flow.title_copy')."</th>";
    if($mod=='docs'){ 
        echo "<th>".lang('flow.title_cata')."</th>";
        echo $fhd3;
    }elseif($mod=='coms'){ 
        echo $fhd3;
    }elseif($mod=='users'){
        echo "<th>".lang('flow.title_grade')."</th>";
        echo $fhd3;
    }elseif($mod=='advs'){
        echo "<th>".lang('flow.title_cata')."</th>";
        echo "<th>".lang('flow.title_mode')."</th>";
        echo "<th>-".lang('flow.title_bei')."-</th>";
    }elseif($mod=='types'){ 
        echo "<th>".lang('flow.title_admin')."</th>";
        echo "<th>-".lang('flow.title_bei')."-</th>";
    }elseif($mod=='menus'){ 
        echo "<th>".lang('flow.title_admin')."</th>";
        echo "<th>-".lang('flow.title_bei')."-</th>";
    }elseif(in_array($mod,array('score','sadm','smem','suser'))){ 
        echo "<th>".lang('flow.title_admin')."</th>";
        echo "<th>".lang('flow.title_parset')."</th>";
    }elseif($mod=='plus'){
        echo "<th>-".lang('flow.title_bei')."-</th>";
    }else{ 
        echo "<th>-".lang('flow.title_bei')."-</th>";
        echo "<th>-".lang('flow.title_bei')."-</th>";
    }
    echo "<th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n"; 
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; $pstr = ''; 
      if(!empty($_groups[$kid]['pid']) && in_array($mod,array('types'))){
          $rmcfg = read($kid);
      }
      if($r['pmod'] && in_array($mod,array('docs','coms','users'))){
          $pname = @$_groups[$r['pmod']]['title'];
          $pstr = "/$pname($r[pmod])\n";
      }
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' />$pstr</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      $ftd3 = $cv->Url(lang('flow.title_fields'),1,"?admin-fields&mod=$r[kid]")."<td class='tc'>".lang('flow.title_set')."</td>\n";
      $ftd3 .= $cv->Url(lang('flow.title_copy'),1,"$aurl[1]&view=gform&cid=$r[kid]",lang('admin.grp_copyitem')." - $r[title]");
      if($mod=='docs'){ 
          echo $cv->Url(lang('flow.title_cata').'&gt;&gt;',1,"?admin-catalog&mod=$r[kid]",'frame');
          echo $ftd3;
      }elseif($mod=='coms'){ 
          echo $ftd3;
      }elseif($mod=='users'){
          echo $cv->Url(lang('flow.title_grade').'&gt;&gt;',1,"?admin-grade&mod=$r[kid]&frame=1",'frame');
          echo $ftd3;
      }elseif($mod=='advs'){
          echo $cv->Url(lang('flow.title_cata').'&gt;&gt;',1,"?admin-catalog&mod=$r[kid]",'frame');
          echo "<td class='tc'>".$advetabs[$r['etab']]."</td>\n";
          echo "<td class='tc'>-".lang('flow.title_bei')."-</td>\n";  
      }elseif($mod=='types'){ 
          if(strstr(@$rmcfg['cfgs'],'exdoc=1') && @$rmcfg['etab']){
              echo $cv->Url(lang('flow.title_fields'),1,"?admin-fields&mod=$r[kid]");  
          }else{
              echo "<td class='tc'>".lang('flow.title_fields')."</td>\n"; 
          }
          echo $cv->Url(lang('flow.title_admin'),1,"?admin-types&mod=$r[kid]",'frame');
      }elseif($mod=='menus'){ 
          echo $cv->Url(lang('flow.title_admin'),1,"?admin-menus&mod=$r[kid]",'frame');
          echo "<td class='tc'>-".lang('flow.title_bei')."-</td>\n"; 
      }elseif(in_array($mod,array('score','sadm','smem','suser'))){ 
          echo $cv->Url(lang('admin.fls_paritem'),1,"?admin-fields&mod=$r[kid]&ispara=1");
          echo $cv->Url(lang('flow.title_parset'),1,"?admin-paras&mod=$r[kid]");
      }elseif($mod=='plus'){
          echo "<td class='tc'>-".lang('flow.title_bei')."-</td>\n";  
      }else{ 
          echo "<td class='tc'>-".lang('flow.title_bei')."-</td>\n";
          echo "<td class='tc'>-".lang('flow.title_bei')."-</td>\n";  
      }
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=gform&kid=$r[kid]&recbk=ref","");
      echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$r[note]' class='txt w120' /></td>\n";
      echo "</tr>"; 
    }} 
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr flgOpbar' colspan='18'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select class='w120 form-control' name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='gform'){ 

    if(!empty($bsend)){
        if($kid=='is__add'){
            $msg = admAFunc::modCopy($mod, $tabid, $fm, $cid);
            $kid = $fm['kid'];
        }else{
            $msg = lang('flow.msg_upd');
            unset($fm['kid']);
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
        } 
        glbCUpd::upd_groups();
        if(in_array($mod,array('docs','coms','users'))){
            if(isset($_groups[$kid])){
                admAFunc::pmodSave($kid,@$fm['pmod']);
            }
        }
        if(in_array($mod,array('docs','users','types','coms','advs'))){
            glbCUpd::upd_model($kid); 
        }
        basMsg::show($msg);    //,'Redir'?$mkv&mod=$mod
    }else{

        if(!empty($cid)){ //copy
            $kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,@$pid);
            $fm = $db->table($tabid)->where("kid='$cid'")->find();
            $fm['title'] .= "_".lang('flow.title_copy');
            $fm['pmod'] = '';
        }elseif(!empty($kid)){
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,@$pid);
        }
        $def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','etab'=>'1','deep'=>'1','cfgs'=>'','pmod'=>'','crdel'=>'0','cradd'=>'0',);
        if($mod=='types'){ 
            $def['etab'] = 0; 
        } 
        foreach($def as $k=>$v){
            if(!isset($fm[$k])) $fm[$k] = $v;
        }
        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "url='".PATH_BASE."?ajax-cajax&act=modExists' tip='".lang('admin.fad_tip31245')."'";
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:3-12' $vstr />$ienable");
        }
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");
        if($mod=='advs'){ //'advs'=>'栏目级数',
            $ietab = " &nbsp; ".lang('admin.grp_dmode')."<select id='fm[etab]' name='fm[etab]' type='text' xxx=''>";
            $ietab .= basElm::setOption($advetabs,$fm['etab'])."</select>"; 
            glbHtml::fmae_row(lang('admin.grp_catlevel'),"<input name='fm[deep]' type='text' value='$fm[deep]' class='txt w80' maxlength='1' reg='n+i' tip='".lang('admin.fad_num')."' />(".lang('admin.grp_max').")$ietab");
            
        }elseif(in_array($mod,array('docs','types','menus'))){
            $_cfg = basLang::ucfg('cfglibs.model_deep'); 
            $ctitle = $_cfg[$mod];
            $ietab = " &nbsp; ".lang('admin.grp_extab');
            if(empty($kid)){
                $ietab .= " <input name='fm[etab]' type='hidden' value='0' /><input name='fm[etab]' type='checkbox' class='rdcb' value='1' ".($fm['etab']=='1' ? 'checked' : '')." />";
            }else{
                $ietab .= " <input name='fm_etab' type='checkbox' disabled='disabled' class='rdcb' value='1' ".($fm['etab']=='1' ? 'checked' : '')." />";
            }
            glbHtml::fmae_row($ctitle,"<input name='fm[deep]' type='text' value='$fm[deep]' class='txt w80' maxlength='1' reg='n+i' tip='".lang('admin.fad_num')."' />(".lang('admin.grp_max').")$ietab");
        }
        glbHtml::fmae_row(lang('flow.fl_cfgtab'),"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>".lang('flow.fl_cfgtip'));
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        if(in_array($mod,array('docs','coms','users'))){
            if($mod=='coms'){
                $arr = admPFunc::modList(array('docs','users','coms',),'relmod'); 
                $pmstr = basElm::setOption($arr,$fm['pmod']);
                $oldPid = "<input name='oldPid' type='hidden' value='{$fm['pmod']}' />";
                glbHtml::fmae_row(lang('admin.fad_rmod'),"<select name='fm[pmod]'>$pmstr</select><br>".lang('admin.grp_rmod')."");
            }
            $jifen = " &nbsp; ".lang('admin.grp_pdel')."<input name='fm[crdel]' type='text' value='$fm[crdel]' class='txt w80' maxlength='3' reg='n+i' tip='".lang('admin.fad_num')."' />";
            glbHtml::fmae_row(lang('admin.grp_pset'),lang('admin.grp_padd')."<input name='fm[cradd]' type='text' value='$fm[cradd]' class='txt w80' maxlength='3' reg='n+i' tip='".lang('admin.fad_num')."' />$jifen");
        }
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","fm[pid]|$mod","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
    }
    
}elseif($view=='sets'){
    //    
}

?>
