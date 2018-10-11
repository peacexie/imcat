<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 

if(!empty($bsend)){
    
    if(empty($fm['op'])){ 
        basMsg::show(lang('flow.dops_setop'));
        glbHtml::end();
    }
    if($fm['op']=='reid' && $fm['kre']!=$kid){
        $db->table($tabid)->data(array('pid'=>$fm['kre']))->where("model='$mod' AND pid='$kid'")->update();
        $db->table($tabid)->data(array('kid'=>$fm['kre']))->where("model='$mod' AND kid='$kid'")->update();
        $msg = lang('admin.sid_eid')."[{$fm['kre']}]".lang('admin.sid_ok'); 
    }elseif($fm['op']=='move'){  
        $deep = empty($fm['pid']) ? '1' : $cfg['i'][$fm['pid']]['deep']+1; 
        $dorg = $cfg['i'][$kid]['deep'];
        $pid = empty($fm['pid']) ? '0' : $fm['pid'];
        $db->table($tabid)->data(array('pid'=>$pid,'deep'=>$deep))->where("model='$mod' AND kid='$kid'")->update();
        if(!(intval($deep)==intval($dorg))){
            $dmov = $dorg - $deep;
            $a = comTypes::getSubs($cfg['i'],$kid);
            $kids = '';
            foreach($a as $k=>$v){
                $kids .= (empty($kids) ? '' : ',')."'$k'";
            }
            $res2 = (intval($dmov)>0 ? '-' : '+').abs($dmov);
            $kids && $db->query("UPDATE {$db->pre}$tabid{$db->ext} SET deep=deep$res2 WHERE model='$mod' AND kid IN($kids)"); 
        }
        $msg = lang('flow.msg_move'); 
    } 
    glbCUpd::upd_model($mod);
    basMsg::show($msg);    
    
}else{ 

    echo "<div class='h02'>&nbsp;</div>";
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
    glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />");
    glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='".$cfg['i'][$kid]['title']."' class='txt w150 disc' disabled='disabled' />");
    glbHtml::fmae_row(lang('flow.title_op'),basElm::setRadio('op',"reid=".lang('admin.sid_eid')."\nmove=".lang('admin.sid_move')));

    $vstr = "url='".PATH_BASE."?ajax-cajax&act=keyExists&mod=$mod&tab=$tabid&old_val=$kid' tip='".lang('admin.fad_tip21245')."'";
    glbHtml::fmae_row(lang('flow.title_newid'),"<input name='fm[kre]' type='text' value='$kid' class='txt w150' maxlength='12' reg='key:2-12' $vstr />");
    
    $ops = comTypes::getOpt(comTypes::getPars($cfg['i'],$cfg['deep']));
    $ops = str_replace(lang('admin.sid_sel'),lang('admin.sid_top'),$ops); 
    glbHtml::fmae_row(lang('flow.title_pid'),"<select name='fm[pid]'>$ops</select> (".lang('admin.sid_uinmv').")");
    glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
    glbHtml::fmt_end(array("mod|$mod","pid|$pid","kid|$kid"));
}
