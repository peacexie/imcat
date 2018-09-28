<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$tabid = 'bext_paras';
$pid = empty($pid) ? 'down_speci' : $pid;

if($view=='upd'){ 
    glbCUpd::upd_parex($pid); die('OK!');
}

$fview = '/plus/file/fview.php?parts=files&dir=office';

if(empty($bsend)){
    $lnkupd = "<a href='$aurl[1]&view=upd' onclick='return winOpen(this,\"".lang('flow.jf_updcfg')."\");'>&lt;&lt;".lang('flow.jf_upd')."</a>";
    $lnkupd .= "<span class='span ph5'>|</span>"; 
    $links = admPFunc::fileNav($pid,'downs'); $gname = admPFunc::fileNavTitle($pid,'downs');
    $lnkadd = "<a href='$aurl[1]&view=form'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    $lnklist = "<a href='?mkv={$this->ucfg['mkv']}&pid=$pid'>&lt;&lt;Go-Back</a>";
    $lnk12 = $view=='list' ? $lnkadd : $lnklist;
    glbHtml::tab_bar("{$lnkupd}[{$gname}]".lang('flow.exp_set')."<span class='span ph5'>|</span>$lnk12","$links",30);
 }   

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
                }
            }
        }
        basMsg::show($msg,'Redir',"?mkv=$mkv&mod=$mod&flag=v1");
    }

    $list = $db->table($tabid)->where("pid='$pid'")->order('top')->select();
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th><th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; 
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tc'>$r[title]</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$r[kid]&recbk=ref","");
      echo "<td class='tl'><input type='text' value='$r[detail]' class='txt w360' /></td>\n";
      echo "</tr>"; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='form'){
    
    if(!empty($bsend)){
        $files = $_POST['files']; $names = $_POST['names']; //dump($files);
        $cfgs = array();
        foreach ($files as $key => $val) {
            if(!empty($val)){
                $cfgs[] = array('file'=>$val,'name'=>$names[$key]);
            }
            $fm['cfgs'] = comParse::jsonEncode($cfgs);
        }
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
        $cfgs = json_decode($fm['cfgs'],1); 

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "tip='".lang('admin.fad_uid41258')."'"; 
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$did' class='txt w150' maxlength='12' reg='key:4-12' $vstr />$ienable");
        } // paymode_, numa,  附加金额
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='12' reg='tit:2-12' tip='".lang('admin.fad_tip21246')."'  />$itop");

        $numb = "numb <input name='fm[numb]' type='text' value='$fm[numb]' class='txt w60' maxlength='12'/>";
        glbHtml::fmae_row(lang('flow.ord_extfee'),"<input name='fm[numa]' type='text' value='$fm[numa]' class='txt w150' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  /> &nbsp; $numb");

        $tpl = "<input id='fm_no_1_' name='files[no_1]' type='text' value=''1 onChange=\"mitmCheck(this)\" class='w360' >
            <input type='button' value='".basLang::show('admin.fv_view')."' onclick=\"winOpen('".PATH_ROOT."$fview&fid=fm_no_1_','".basLang::show('admin.fv_vfiles')."',720,480)\">
            <br>标题:<input name='names[no_1]' type='text' value=''2 class='w360'>";
        $hid = "<script type='text/html' id='itmTpl'><div class='mv5'>$tpl</div></script>";
        $files = ''; $no = 1000;
        if(!empty($cfgs)){ foreach ($cfgs as $k => $row) {
            $ni = $no+$k;
            $fitm = str_replace(array('no_1',"=''1","=''2"),array($ni,"='$row[file]'","='$row[name]'"),$tpl);
            $files .= "<div class='mv5'>$fitm</div>";
        } }
        glbHtml::fmae_row("cfgs$hid","<div id='itmBox'>$files</div>");
        glbHtml::fmae_row(lang('flow.exp_detail'),"<textarea name='fm[detail]' rows='2' cols='50' wrap='off'>$fm[detail]</textarea>");
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='2' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("mod|$mod","fm[pid]|$pid","kid|".(empty($kid) ? 'is__add' : $kid),"cid|$cid"));
        echo basJscss::jscode(0,PATH_SKIN.'/adm/b_jscss/finps.js');
        echo basJscss::jscode("$(function(){mitmInit('itm')})");
        
    }
}

?>
