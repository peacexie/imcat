<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mod = empty($mod) ? 'muadm' : $mod;
$view = empty($view) ? 'glist' : $view;
$pid = empty($pid) ? '0' : $pid;
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@menus.php'); 
$gbar = admAFunc::grpNav('menus',$mod); 
$cfg = read($mod); 
$tabid = 'base_menu';

if(($mod=='mkvu' || $mod=='mkva') && $view=='glist'){
    
    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id'")->update(); 
                }elseif($fs_do=='show'){ 
                     $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
                }elseif($fs_do=='stop'){ 
                     $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
                }
            }
        }
        glbCUpd::upd_model($mod);
    } 

    $lnkupd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"".lang('admin.mu_addone')."-[$gname]\",300,200);'>".lang('admin.mu_upd')."</a>";
    $lnklay = admAFunc::typLay($cfg,$aurl,$pid);
    $lnkadd = "<a href='$aurl[1]&view=umcinit&pid=$pid' onclick='return winOpen(this,\"".lang('admin.mu_init')."-[$gname]\");'>".lang('admin.mu_init')."</a>";
    glbHtml::tab_bar(lang('admin.mu_navmenu')." :: $gname<span class='span ph5'>|</span>$lnkupd - $lnkadd<br>$lnklay",$gbar,35);
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th>";
    echo "<th>".lang('flow.title_level')."</th><th>".lang('flow.title_subtype')."</th>";
    echo "<th>".lang('flow.title_perm')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n";
    $pcfg = array('1'=>lang('admin.mu_setpm'),'.guest'=>lang('admin.mu_guestpm'),); // '0'=>'登录权限',
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
        echo "<td class='tc'>$r[kid]</td>\n";
        echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /></td>\n";
        echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
        echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
        echo "<td class='tc'>$r[deep]</td>\n"; 
        echo "<td class='tc'>".($f_deep ? "<a href='$u_sub'>$s_cnt</a>" : glbHtml::null_cell($s_cnt))."</td>\n";
        $popt = "<select name='fm[$kid][cfgs]'>".basElm::setOption($pcfg,$r['cfgs'],lang('admin.mu_loginpm'))."</select>";
        echo "<td class='tc'>$popt</td>\n";
        echo "<td class='tl'><input type='text' value='$note' class='txt w120' /></td>\n";
        echo "</tr>"; 
    }} 
    $ops = basElm::setOption("upd|".lang('flow.op_upd')."\nshow|".lang('flow.op_open')."\nstop|".lang('flow.op_close')."");
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr flgOpbar' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select class='w120 form-control' name='fs_do'>$ops</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='umcinit'){
    
    admAFunc::mkvInit($mod);
    basMsg::show(lang('admin.mu_initend'),'Redir',"?$mkv&mod=$mod&pid=$pid");

}elseif($view=='glist'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){ 
                $msg = lang('flow.msg_set');
                if($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("model='$mod' AND kid='$id'")->update(); 
                }elseif($fs_do=='del'){ 
                    if($db->table($tabid)->where("model='$mod' AND pid='$id'")->find()){
                        $msg = lang('admin.mu_delfirst');
                    }else{
                        $db->table($tabid)->where("model='$mod' AND kid='$id'")->delete(); 
                        $msg = lang('flow.msg_del');
                    }
                }elseif($fs_do=='show'){ 
                     $db->table($tabid)->data(array('enable'=>'1'))->where("model='$mod' AND kid='$id'")->update();  
                }elseif($fs_do=='stop'){ 
                     $db->table($tabid)->data(array('enable'=>'0'))->where("model='$mod' AND kid='$id'")->update(); 
                }
            }
        }
        glbCUpd::upd_model($mod);
    } 

    $lnkupd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"".lang('admin.mu_upd')."[$gname]\",300,200);'>".lang('admin.mu_upd')."</a>";
    $lnklay = admAFunc::typLay($cfg,$aurl,$pid);
    $lnkadd = "<a href='$aurl[1]&view=gform&pid=$pid' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('admin.mu_add')."&gt;&gt;</a>";
    if($msg && !strpos($msg,'<')) $msg = "<span class='cF00'>$msg</span>";
    $msg && $msg = $msg."<br>";
    glbHtml::tab_bar("$msg".lang('admin.mu_navmenu')." :: $gname<span class='span ph5'>|</span>$lnkupd - $lnkadd<br>$lnklay",$gbar,35);
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th>";
    echo "<th>".lang('flow.title_level')."</th><th>".lang('flow.title_subtype')."</th>";
    echo "<th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n";
    $list = $db->table($tabid)->where("model='$mod' AND pid='$pid'")->order('top,kid')->select();
    if($list){
    foreach($list as $r){
        $kid = $r['kid'];
        $u_sub = strstr($aurl[1],'pid=') ? basReq::getURep($aurl[1],'pid',$r['kid']) : "$aurl[1]&pid=$r[kid]";
        $f_deep = @$cfg['i'][$pid]['deep'] < $cfg['deep']-1;
        $s_cnt = count(comTypes::getSubs($cfg['i'],$r['kid']));
        $iconstr = empty($r['icon']) ? '(x)' : " <i class='fa fa-".$r['icon']."'></i>";
        $note = basStr::filTitle($r['note']);
        echo "<tr>\n";
        echo "<td class='tc'><input name='fs[$kid]' type='checkbox' class='rdcb' value='1' /></td>\n";
        echo "<td class='tc'><a href='$aurl[1]&view=set&kid=$r[kid]' onclick='return winOpen(this,\"".lang('admin.fad_setid',$r['title'])."\");'>$r[kid]</a></td>\n";
        echo "<td class='tl'><input name='fm[$kid][title]' type='text' value='$r[title]' class='txt w150' /> $iconstr</td>\n";
        echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
        echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
        echo "<td class='tc'>$r[deep]</td>\n"; 
        echo "<td class='tc'>".($f_deep ? "<a href='$u_sub'>$s_cnt</a>" : glbHtml::null_cell($s_cnt))."</td>\n";   
        echo "<td class='tc'><a href='$aurl[1]&view=gform&kid=$r[kid]' onclick='return winOpen(this,\"".lang('admin.mu_editone')."-$r[title]\");'>".lang('flow.title_edit')."</a></td>\n";
        echo "<td class='tl'><input name='fm[$kid][note]' type='text' value='$note' class='txt w120' /></td>\n";
        echo "</tr>"; 
    }} 
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr flgOpbar' colspan='10'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select class='w120 form-control' name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='gform'){

    if(!empty($bsend)){
        if($kid=='_isadd_'){
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
        $def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','deep'=>'1','cfgs'=>'','icon'=>'',);
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
            $vstr = "url='".PATH_BASE."?ajax-cajax&act=keyExists&mod=0&tab=$tabid' tip='".lang('admin.fad_tip21245')."'";
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:2-12' $vstr />$ienable");
        }
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21245')."' />$itop");
        $iconstr = empty($fm['icon']) ? '(null)' : "<i class='fa fa-".$fm['icon']."'></i>";
        $iconstr = " <a href='".PATH_VENDUI."/bootstrap/FontAwesome.htm' target='_blank'>$iconstr</a>";
        glbHtml::fmae_row('Icon',"<input name='fm[icon]' type='text' value='$fm[icon]' class='txt w150' maxlength='12' reg='tit:0-48' />$iconstr");
        glbHtml::fmae_row(lang('flow.fl_cfgtab'),"<textarea name='fm[cfgs]' rows='8' cols='50' wrap='off'>$fm[cfgs]</textarea>
        <br>".lang('admin.mu_fmta')."
        <br>1. ?admin-groups, ".lang('admin.mu_flagroot','{$root}')."
        <br>2. ".lang('admin.mu_tiptitle')."(!)link(!)frame|blank|jsadd, ".lang('admin.mu_tipline')."
        <br>3. &lt;a href=&quot;?admin-types&quot;&gt;".lang('admin.mu_typa')."&lt;/a&gt; - &lt;a href=&quot;#&quot; target=&quot;_blank&quot;&gt;".lang('admin.set')."&lt;/a&gt;；");
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","fm[model]|$mod","fm[pid]|$pid","kid|".(empty($kid) ? '_isadd_' : $kid)));
    }

}elseif($view=='upd'){
    
    glbCUpd::upd_menus($mod,$cfg); 
    echo "\n<hr>".lang('admin.mu_end')."<br>";

}elseif($view=='set'){
    
    require dirname(__DIR__).'/binc/set_id.php';

}

?>

