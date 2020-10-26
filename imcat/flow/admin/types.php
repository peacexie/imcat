<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mod = empty($mod) ? 'china' : $mod;
$view = empty($view) ? 'glist' : $view;
$pid = empty($pid) ? '0' : $pid;
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@types.php'); 
$gbar = admAFunc::grpNav('types',$mod); 

$cfg = read($mod); 
$tabid = empty($cfg['etab']) ? 'types_common' : "types_$mod";

if($view=='glist'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        elseif($fs_do=='chord'){
            $msg = lang('admin.tp_ordok'); 
            $list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('`char`,kid')->select();
            if($list){ $bord = 124;
            foreach($list as $r){
                $bord += 4; $kid = $r['kid']; 
                $db->table($tabid)->data(array('top'=>$bord))->where("model='$mod' AND kid='$kid'")->update(); 
            }}
        }else{
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='upd'){ 
                    $fm[$id]['char'] = strtoupper(comConvert::pinyinMain($fm[$id]['title'],3,1));
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id'")->update(); 
                }elseif($fs_do=='del'){ 
                    if($db->table($tabid)->where("model='$mod' AND pid='$id'")->find()){
                        $msg = lang('admin.cat_dsub',$id); 
                    }else{
                        $db->table($tabid)->where("model='$mod' AND kid='$id'")->delete(); 
                        #comStore::delFiles('icon', $id);
                        $msg = lang('flow.msg_del');    
                    }
                }elseif($fs_do=='show'){ 
                     $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
                }elseif($fs_do=='stop'){ 
                     $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
                }elseif($fs_do=='frame'){ 
                     $db->table($tabid)->data(array('frame'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
                }elseif($fs_do=='nofrm'){ 
                     $db->table($tabid)->data(array('frame'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
                }elseif($fs_do=='chupd'){ 
                     $char = strtoupper(comConvert::pinyinMain($fm[$id]['title'],3,1));
                     $db->table($tabid)->data(array('char'=>$char))->where("model='$mod' AND kid='$id'")->update();
                }
            }
        }
        glbCUpd::upd_model($mod);
    } 

    $lnklay = admAFunc::typLay($cfg,$aurl,$pid);
    $lnkbak = $_groups[$cfg['pid']]['title'];
    $lnkadd = "<a href='$aurl[1]&view=gform&pid=$pid' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    if($pid && !isset($cfg['i'][$pid])) $lnkadd = "<i title='".lang('admin.cat_close')."'>".lang('flow.fl_addtitle')."&gt;&gt;</i>";
    $lnkchk = "<a href='$aurl[1]&view=check' onclick='return winOpen(this);'>".lang('admin.tp_chkinc')."</a>";
    if(empty($pid)) $lnklay = "$lnkchk | $lnklay";
    glbHtml::tab_bar("[$lnkbak] :: $gname<span class='span ph5'>|</span>$lnkadd<br>$lnklay",$gbar,35);
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th>";
    echo "<th>".lang('flow.title_char')."</th><th>".lang('flow.title_level')."</th><th>".lang('flow.title_frame')."</th><th>".lang('flow.title_subtype')."</th>";
    echo "<th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n";
    $list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('top,kid')->select();
    if($list){
    foreach($list as $r){
      $kid = $r['kid'];
      $u_sub = strstr($aurl[1],'pid=') ? basReq::getURep($aurl[1],'pid',$r['kid']) : "$aurl[1]&pid=$r[kid]";
      $f_deep = @$cfg['i'][$pid]['deep'] < $cfg['deep']-1;
      $s_cnt = count(comTypes::getSubs($cfg['i'],$r['kid']));
      $note = basStr::filTitle($r['note']);
      echo "<tr>\n";
      echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
      echo "<td class='tc'><a href='$aurl[1]&view=set&kid=$r[kid]' onclick='return winOpen(this,\"".lang('admin.fad_setid',$r['title'])."\",400,300);'>$r[kid]</a></td>\n";
      echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo "<td class='tc'>$r[char]</td>\n";
      echo "<td class='tc'>$r[deep]</td>\n"; 
      echo "<td class='tc'>".glbHtml::null_cell($r['frame'])."</td>\n";
      echo "<td class='tc'>".($f_deep ? "<a href='$u_sub'>$s_cnt</a>" : glbHtml::null_cell($s_cnt))."</td>\n";   
      echo "<td class='tc'><a href='$aurl[1]&view=gform&kid=$r[kid]&recbk=ref'>".lang('flow.title_edit')."</a></td>\n";
      echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$note' class='txt w120' /></td>\n";
      echo "</tr>"; 
    }} 
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    $opstr = basElm::setOption(lang('flow.op_op4')."\nframe|".lang('admin.cat_frame')."\nnofrm|".lang('admin.cat_xframe')."\nchupd|".lang('admin.tp_updchar')."\nchord|".lang('admin.tp_ordchar')."");
    echo "<td class='tr flgOpbar' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select class='w120 form-control' name='fs_do'>$opstr</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='gform'){ 

    $def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','frame'=>'0','deep'=>'1','cfgs'=>'',);
    if(!empty($bsend)){ 
        if($kid=='_isadd_'){
            $fm['char'] = strtoupper(comConvert::pinyinMain($fm['title'],3,1));
            foreach($def as $k=>$v){if(!isset($fm[$k])) $fm[$k] = $v;}
            if($db->table($tabid)->where("model='$mod' AND kid='$fm[kid]'")->find()){
                $msg = lang('flow.msg_exists',$fm['kid']);
            }else{
                $msg = lang('flow.msg_add');  
                $fm['deep'] = empty($fm['pid']) ? 1 : @$cfg['i'][$pid]['deep']+1;
                $db->table($tabid)->data(basReq::in($fm))->insert();
                $id = $fm['kid'];    
            }
        }else{
            $msg = lang('flow.msg_upd');
            unset($fm['kid']);
            $f = $cfg['f'];
            $mcfg = read($mod); 
            foreach($_POST['fm'] as $k=>$v){
            if(isset($f[$k])){ 
                $fm[$k] = dopFunc::svFmtval($f,$mod,$k,$v);
                if(isset($mcfg['f'][$k]) && in_array($mcfg['f'][$k]['type'],array('file','text'))){
                    $ishtml = $mcfg['f'][$k]['type']=='text';
                    $fm[$k] = comStore::moveTmpDir($fm[$k], 'icon', $mod, $ishtml);
                }
            } }
            $db->table($tabid)->data(basReq::in($fm))->where("model='$mod' AND kid='$kid'")->update();
        } 
        glbCUpd::upd_model($mod);
        basMsg::show($msg);    
    }else{
        if(empty($kid)){
            $kid = ''; $did = glbDBExt::dbNxtID($tabid,$mod,$pid);
        }else{
            $fm = $db->table($tabid)->where("model='$mod' AND kid='$kid'")->find();
        }
        foreach($def as $k=>$v){if(!isset($fm[$k])) $fm[$k] = $v;}

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "url='".PATH_BASE."?ajax-cajax&act=keyExists&mod=$mod&tab=$tabid' tip='".lang('admin.fad_tip21245')."'";
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:2-12' $vstr />$ienable");
        }
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21245')."' />$itop");
        glbHtml::fmae_row(lang('flow.fl_cfgtab'),"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea><br>".lang('flow.fl_cfgtip'));
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        empty($kid) || fldView::lists($mod,$fm);
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","fm[pid]|$pid","kid|".(empty($kid) ? '_isadd_' : $kid)));
    }

}elseif($view=='set'){
    require dirname(__DIR__).'/binc/set_id.php';
}elseif($view=='check'){
    $irep = devBase::typCheck($mod);
    dump(str_replace(";","\n",$irep),1);
}

?>
