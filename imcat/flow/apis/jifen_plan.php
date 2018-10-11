<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require dirname(__DIR__).'/binc/_pub_cfgs.php';

$tabid = 'bext_paras'; //$tabid = 'bext_jifen'; die('coming soon!');
$pid = empty($pid) ? 'jifen_grade' : $pid;

if(empty($dialog)){
    $gnarr = basLang::ucfg('cfgbase.jifen_ops'); 
    $gname = @$gnarr[$pid];
    $lnkupd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"".lang('flow.jf_updcfg')."\");'>&lt;&lt;".lang('flow.jf_upd')."</a>";
    $lnkupd = in_array($pid,array('jifen_grade')) ? "$lnkupd<span class='span ph5'>|</span>" : ''; 
    $lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[$gname]\");'>".lang('flow.jf_add')."&gt;&gt;</a>";
    $lnkadd = in_array($pid,array('jifen_grade')) ? "<span class='span ph5'>|</span>$lnkadd" : ''; 
    $linkp = admPFunc::fileNav($pid,'cron_jifen');
    $links = admPFunc::fileNav($file,'cron_plan');
    glbHtml::tab_bar("$linkp".(in_array($pid,array('jifen_logs')) ? '' : "<br>{$lnkupd}[$gname]$lnkadd"),"$links",50);    
}
if($view=='upd'){
    
    comJifen::update(); 
    echo "\n<hr>End! ";
    
}elseif($pid=='jifen_grade' && $view=='list'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){
                $msg = lang('flow.msg_set');
                if($fs_do=='del'){ 
                    $db->table($tabid)->where("kid='$id'")->delete();
                }elseif($fs_do=='show'){ 
                    $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();  
                }elseif($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update();
                }elseif($fs_do=='stop'){ 
                    $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update(); 
                }
            }
        }
        basMsg::show($msg,'Redir',"?$mkv&mod=$mod&flag=v1");
    }
    
    $list = $db->table($tabid)->where("pid='$pid'")->order('top')->select();
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.jf_jfcnt')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; 
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tc'>$r[title]</td>\n";
      echo "<td class='tc'>$r[numa]</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
      echo "<td class='tl'><input type='text' value='$r[note]' class='txt w300' /></td>\n";
      echo "</tr>"; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='7'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($pid=='jifen_model' && $view=='list'){
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>+".lang('flow.jf_jifen')."</th><th>-".lang('flow.jf_jifen')."</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    $gma = array('docs','coms','users'); $gmold = '';
    foreach($gma as $gm){ 
    foreach($_groups as $mod=>$gv){
      $kid = "$mod"; if($gv['pid']!==$gm) continue;
      $mcfg = read($mod); 
      $numa = empty($mcfg['cradd']) ? 0 : $mcfg['cradd'];
      $numb = empty($mcfg['crdel']) ? 0 : $mcfg['crdel'];
      if($gmold!=$gv['pid']){
          echo "<tr><td class='tc fB' colspan='3'>{$_groups[$gv['pid']]['title']}</td></tr>";
      }
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$kid</td>\n";
      echo "<td class='tc'>$gv[title]</td>\n";
      echo "<td class='tc'>$numa</td>\n";
      echo "<td class='tc'>$numb</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"?admin-groups&mod=$gm&view=gform&kid=$mod&recbk=ref","");
      echo "<td class='tl'><input type='text' value='".str_replace(array("\n","\r",";;"),array(";",";",";"),@$mcfg['cfgs'])."' class='txt w300' /></td>\n";
      echo "</tr>";
      $gmold = $gv['pid']; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": (null) &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($pid=='jifen_logs' && $view=='list'){
    
    $cfg = array(
        'sofields'=>array('act','uto','jfmod'),
        'soorders'=>basLang::ucfg('cfgbase.ord_jifen'),
        //'soarea'=>array('jifen','数量'),
    );
    $dop = new dopExtra('logs_jifen',$cfg); 
    
    // 删除操作
    if(!empty($bsend)){
        require dirname(__DIR__).'/binc/act_ops.php';
    } 
    
    $umsg = $msg ? "<span class='cF00'>$msg</span>" : '';
    $dop->sobar("[".lang('flow.jf_logs')."] $msg",50); //$dop->sobar("xxx",50,'-1',array('job'=>$job));

    // 清理操作
    if(!empty($bsend)&&$fs_do=='dnow'){
        $msg = $dop->opDelnow();
        basMsg::show($msg,'Redir',"?$mkv&pid=$pid&mod=$mod&flag=v1");
    }
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>act</th><th>uto</th><th>jifen</th><th>time</th><th>model</th><th>note</th></tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          $act = $r['act']=='del' ? "<span class='cF00'>$r[act]</span>" : ($r['act']=='add' ? "<span class='c00F'>$r[act]</span>" : $r['act']);
          if(empty($idfirst)) $idfirst = $kid;
          echo "<tr>\n".$cv->Select($kid);
          echo "<td class='tc'>$act</td>\n";
          echo "<td class='tc'>$r[uto]</td>\n";
          echo "<td class='tc'>$r[jifen]</td>\n";
          echo $cv->Time($r['atime'],$td=1);
          echo "<td class='tc'>$r[jfmod]</td>\n";
          echo "<td class='tl'><input type='text' value='".@$r['note']."' class='txt w240' /></td>\n";
          echo "</tr>";
        }
        $dop->pgbar($idfirst,$idend);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='form'){
    
    if(!empty($bsend)){
        if($kid=='is__add'){
            if($db->table($tabid)->where("kid='$fm[kid]'")->find()){
                $msg = lang('flow.msg_exists',$fm['kid']);
            }else{
                $msg = lang('flow.msg_add');  
                $db->table($tabid)->data(basReq::in($fm))->insert();
                $id = $fm['kid'];    
            }
        }else{
            $msg = lang('flow.msg_upd');
            unset($fm['kid']); 
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
        } 
        glbCUpd::upd_model($mod);
        basMsg::show($msg);    
    }else{

        if(!empty($kid)){
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $kid = '';
        }
        $def = array('kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','detail'=>'','cfgs'=>'','numa'=>'0','numb'=>'0');
        foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "tip='".lang('admin.fad_uid41258')."'"; //url='".PATH_BASE."?ajax-cajax&act=modExists' 
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
        } 
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");

        $cfgs = "".lang('flow.ord_icon')."<input name='fm[cfgs]' type='text' value='$fm[cfgs]' class='txt w90' maxlength='12' tip='".lang('flow.ord_icocss')."' />";
        glbHtml::fmae_row(lang('flow.jf_jfcnt2'),"<input name='fm[numa]' type='text' value='$fm[numa]' class='txt w90' maxlength='9' reg='n+i' tip='".lang('flow.jf_l19n')."' /> &nbsp; $cfgs");

        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","fm[pid]|$pid","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
    }
}

?>
