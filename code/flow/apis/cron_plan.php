<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$tabid = 'bext_cron';
$units = basLang::ucfg('cfgbase.corn_unit');

if($view=='list'){

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
                }elseif(in_array($fs_do,array('runp','rinc'))){ 
                    $upd = $fs_do=='runp' ? 1 : 0;
                    $cron = comCron::run($id);
                }
            }
        }
        basMsg::show($msg,'Redir',"?mkv=$mkv&mod=$mod&flag=v1");
    }

    $lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.cr_addplan')."\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    $links = admPFunc::fileNav($file,'cron_plan');
    glbHtml::tab_bar("".lang('flow.cr_planord')."<span class='span ph5'>|</span>$lnkadd","$links",50);
    
    $list = $db->table($tabid)->where("hkflag='0'")->order('top')->select(); 
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>".lang('flow.cr_kfine')."</th><th>".lang('flow.title_name')."</th><th>".lang('flow.cr_cycle')."</th><th>".lang('flow.cr_runprev')."</th><th>".lang('flow.cr_runnext')."</th><th>".lang('flow.cr_runms')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th>\n";
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; 
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tc'>$r[title]</td>\n";
      echo "<td class='tc'>$r[excycle]".@$units[$r['excunit']]."</td>\n";
      echo $cv->Time($r['exlast'],$td=1);
      echo $cv->Time($r['exnext'],$td=1);
      echo "<td class='tc'>$r[exsecs]</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
      echo "</tr>"; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    $opstr = basElm::setOption(lang('flow.op_op4')."\nrinc|*".lang('flow.cr_dirun')."\nrunp|*".lang('flow.cr_odrun')."");
    echo "<td class='tr' colspan='9'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>$opstr</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}elseif($view=='form'){
    
    if(!empty($bsend)){
        $fm['exnext'] = strtotime($fm['exnext'].":00");
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
        basMsg::show($msg);    
    }else{

        if(!empty($kid)){
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $kid = '';
        }
        $def = array(
            'kid'=>'','title'=>'','top'=>'888','enable'=>'1','note'=>'','cfgs'=>'',
            'excycle'=>'1','excunit'=>'d','exlast'=>'0','exnext'=>'946526400','extime'=>'0','exskip'=>'','exsecs'=>date('i:s'),
        );
        foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "tip='".lang('flow.cr_tip1')."'"; //url='".PATH_ROOT."/plus/ajax/cajax.php?act=modExists' 
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='tit:4-12' $vstr />$ienable");
        } 
        glbHtml::fmae_row(lang('flow.cr_pname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");

        $excunit = basElm::setOption($units,$fm['excunit']);
        $excunit = " &nbsp; ".lang('flow.cr_unit')."<select name='fm[excunit]' class='w90'>$excunit</select>";
        glbHtml::fmae_row(lang('flow.cr_cycle'),"<input name='fm[excycle]' type='text' value='$fm[excycle]' class='txt w90' maxlength='3' reg='n+i' tip='".lang('flow.cr_tip2')."' />$excunit");

        echo basJscss::imp('/My97DatePicker/WdatePicker.js','vendui'); 
        $slast = empty($fm['exlast']) ? '-' : date('Y-m-d H:i',$fm['exlast']);
        $slast = " &nbsp; ".lang('flow.cr_last').": $slast";
        $iinp = "<input id='fm[exnext]' name='fm[exnext]' type='text' value='".date('Y-m-d H:i',$fm['exnext'])."' class='txt w130' />";
        $item = "$iinp<span class='fldicon fdate' onClick=\"WdatePicker({el:'fm[exnext]',dateFmt:'yyyy-MM-dd HH:mm'})\" /></span>";
        glbHtml::fmae_row(lang('flow.cr_runnext'),"$item $slast");
        
        glbHtml::fmae_row(lang('flow.cr_runms'),"<input name='fm[exsecs]' type='text' value='$fm[exsecs]' class='txt w150' maxlength='5' reg='str:5-5' tip='00:00~59:59, ".lang('flow.cr_tip3')."' />00:00~59:59");

        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","kid|".(empty($kid) ? 'is__add' : $kid)));
    }
}

?>
