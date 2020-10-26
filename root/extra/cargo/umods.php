<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');


$view = empty($view) ? 'list' : $view;
$fs_do = req('fs_do');
$fs = basReq::arr('fs'); 
#$fm = basReq::arr('fm');
$msg = ''; $cnt = 0; 
$tabid = strstr($view,'at') ? 'exd_uatt' : 'exd_umod'; 
$pid = req('pid','0'); 
$ap = req('ap',''); 
$pid = empty($pid) ? '0' : $pid;
$aucut = basReq::getUri(1, '', 'view|pid|ap'); //dump($aucut);
$title = empty($fm['title']) ? '' : trim(preg_replace("/\s+/i", ' ', $fm['title']));
$title = str_replace(['（','）'], ['(',')'], $title);
$fm['title'] = $title;        
$fm['cfgs'] = empty($fm['cfgs']) ? '' : $text = basElm::linestrim($fm['cfgs'], 0);

$types = fldCfgs::viewTypes();
$tptab = [
    'input'  => '输入框',
    'select' => '下拉选择',
    'cbox'   => '多选框',
    'radio'  => '单选按钮',
    'text'  => '文本框',
    //'file'  => '文件域',
];

/*
$tmp = basKeyid::kidAuto(12);
echo 'xx'.substr($tmp,2,10)."<br>\n";
echo $tmp."<br>\n";
*/

$exlinks = "<a href='$aucut&view=batch'>批量添加</a><br>&nbsp;"; 

if($view=='batch'){

    $exlinks = "<a href='$aucut&view='>返回模型</a><br>&nbsp;"; 

    if(!empty($bsend)){
        $bat_cfgs = req('bat_cfgs', '', 'Title', 2400); $bat_cfgs = str_replace(['（','）'], ['(',')'], $bat_cfgs);
        $bat_arrs = basElm::line2arr($bat_cfgs);
        $bat_attr = req('bat_attr'); 
        $tab = $bat_attr ? 'exd_uatt' : 'exd_umod';
        $def = $bat_attr ? '10240' : '5120';
        $kinit = admPFunc::umodKid($tab, $def);
        $kbase = is_numeric($kinit) ? $kinit : substr($kinit, 0, 8);
        #dump("$tab / $def / $kbase "); dump($bat_arrs); die();
        $fmumod = empty($fm['umod']) ? '0' : $fm['umod'];
        if($bat_attr){
            if(!$fmumod){ die('请选择模型!'); }
            $pid = $fmumod;
            foreach($bat_arrs as $ak => $val) {
                $kid = is_numeric($kinit) ? ($kbase+$ak) : ($kbase.($ak<10?"0$ak":$ak));
                $title = trim(str_replace(['* ','- '], '', $val));
                $type = 'input'; $cfgs = '';
                if(strstr($val,'* ')){
                    $type = 'select';
                    foreach($bat_arrs as $bk => $itm) { 
                        if($bk<=$ak){ continue; }
                        if(strstr($itm,'* ')){ break; }
                        $itm = trim(str_replace(['- '], '', $itm));
                        $cfgs .= "\n$itm";
                    }
                }elseif(strstr($val,'- ')){
                    continue;
                }else{
                    $pid = $fmumod;
                }
                $row = ['kid'=>$kid, 'title'=>$title, 'pid'=>$pid, 'type'=>$type, 'top'=>10+$ak, 'cfgs'=>$cfgs]; //dump($row);
                $whr = "pid='$pid' AND title='$title'"; //echo $whr;
                if($db->table($tab)->where($whr)->find()){ 
                    echo "~SKIP";
                }else{
                    echo "DBINS";
                    $db->table($tab)->data($row)->insert();
                }
                echo " : $kid-$title-$pid<br>\n";
            }
        }else{ // mod
            $pidbk = '0';
            foreach($bat_arrs as $ak => $val) {
                $kid = is_numeric($kinit) ? ($kbase+$ak) : ($kbase.($ak<10?"0$ak":$ak));
                $title = trim(str_replace(['* ','- '], '', $val));
                if(strstr($val,'* ')){
                    $pid = '0';
                }elseif(strstr($val,'- ')){
                    $pid = $pidbk;
                }else{
                    $pid = $fmumod;
                }
                // db
                $row = ['kid'=>$kid, 'title'=>$title, 'pid'=>$pid, 'top'=>10+$ak]; //dump($row);
                $whr = "pid='$pid' AND title='$title'"; //echo $whr;
                if($db->table($tab)->where($whr)->find()){ 
                    echo "~SKIP";
                }else{
                    echo "DBINS";
                    $db->table($tab)->data($row)->insert();
                }
                echo " : $kid-$title-$pid<br>\n";
                if(strstr($val,'* ')){
                    $pidbk = $kid;
                }
            }
        }
        basMsg::show($msg);    
    }else{

        echo basJscss::imp("/~base/jslib/jstypes.js");
        echo basJscss::imp("/~tpl/umods.js");
        glbHtml::tab_bar("[模型/属性]设置","$exlinks",30);
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');

        $umodstr = fldEdit::layTypes('umod', 'umod', ''); // 5122,a1047
        glbHtml::fmae_row('模型',"$umodstr");
        $isattr = "<label><input name='bat_attr' type='checkbox' class='rdcb' value='1' />添加为属性</label>";
        glbHtml::fmae_row('属性',"$isattr 不选则批量添加模型<div id='now_attrs'></div>");

        $tip1 = "一行一个模型或属性；\n模型为md格式，则添加父子关系的模型；<br>\n属性为md格式，则为单选及选项关系；\n1800字符以内,Demo格式如下";
        glbHtml::fmae_row('Cfgs',"<textarea name='bat_cfgs' rows='18' cols='50' wrap='wrap' onBlur='layBcheck(this)'></textarea><br>$tip1");
        $tip1 = comFiles::get(__DIR__.'/tip-batch.md');
        glbHtml::fmae_row('Demo格式',"<textarea rows='18' cols='50' wrap='wrap'>$tip1</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("fm[pid]|$pid","pid|$pid","ap|$ap","cid|$cid"));
        echo basJscss::jscode("$(function(){layInit('umod','umod')});");

    }

}elseif($view=='atts'){

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
        basMsg::show($msg,'Redir',"?$mkv&view=$view&pid=$pid&ap=$ap&flag=v1");
    }

    $lnklay = "<br>\n &lt;&lt; <a href='$aucut&view=list&pid=$pid'>返回</a> | for ";
    if($ap){
        $dbr = db()->table('exd_umod')->where("kid='$ap'")->find();
        $lnklay .= " - $dbr[title]";
    }else{
        $lnklay .= " - [---]";
    }
    $lnkadd = "<a href='$aurl[1]&view=atfm' onclick='return winOpen(this,\"增加属性[$dbr[title]]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    glbHtml::tab_bar("[属性]设置<span class='span ph5'>|</span>$lnkadd$lnklay","$exlinks",30);
    
    $list = $db->table($tabid)->where("pid='$ap'")->order('top')->select();
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>类型</th><th>".lang('flow.title_top')."</th>
        <th>".lang('flow.title_enable')."</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; 
      $tpstr = isset($types[$r['type']]) ? $types[$r['type']] : '(input)';
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tc'>$r[title]</td>\n";
      echo "<td class='tc'>$tpstr</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"$aucut&view=atfm&pid=$pid&ap=$ap&kid=$r[kid]&recbk=ref","");
      echo "<td class='tl'><input type='text' value='$r[cfgs]' class='txt w120' /></td>\n";
      echo "</tr>"; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='7'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("pid|$pid","ap|$ap"));
    
}elseif($view=='atfm'){
    
    if(!empty($bsend)){
        if($kid=='is__add'){
            // 批量
            if($db->table($tabid)->where("kid='$fm[kid]'")->find()){
                $msg = lang('flow.msg_exists',$fm['kid']);
            }else{
                $msg = lang('flow.msg_add');
                $fm['pid'] = $ap;
                $db->table($tabid)->data(basReq::in($fm))->insert();
                $id = $fm['kid'];    
            }
        }else{
            $msg = lang('flow.msg_upd');
            unset($fm['kid'],$fm['pid']); 
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
        } 
        #glbCUpd::upd_model($mod);
        basMsg::show($msg);    
    }else{

        if(!empty($kid)){
            $is_add = '';
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $is_add = 'is__add'; // 10240
            $kid = admPFunc::umodKid($tabid, '10240');
        }
        $def = array('kid'=>'','title'=>'','type'=>'input','top'=>'666','enable'=>'1','def'=>'','so'=>'','gkey'=>'1','note'=>'','cfgs'=>'');
        foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $iso = " &nbsp; <input name='fm[so]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[so]' />";
        $iso .= "搜索<input name='fm[so]' type='checkbox' class='rdcb' value='1' ".($fm['so']=='1' ? 'checked' : '')." />";        
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!$is_add){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable $iso");
        }else{
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='12' reg='str:3-12' tip='3-12个字母或数字' />$ienable $iso");
        } 
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='48' reg='tit:2-48' tip='2-12个中英文'  />$itop");
        $ops = basElm::setOption($tptab, $fm['type'], '');
        $def = "<input name='fm[def]' type='text' value='$fm[def]' class='txt w120' maxlength='24' />";
        glbHtml::fmae_row('类型',"<select name='fm[type]'>$ops</select> &nbsp; 默认值$def");
        $pmod = $db->table('exd_umod')->where("kid='$ap'")->find(); 
        $parr = basElm::line2arr($pmod['gtab'], 'kv'); //dump($parr); dump($pmod['gtab']);
        $parr = basElm::convOpts($parr);
        $pops = basElm::setOption($parr, $fm['gkey'], '-所属分组-');
        glbHtml::fmae_row('属性分组', $pops?"<select name='fm[gkey]'>$pops</select>":'(无分组)');

        $ph_tip = "placeholder='一行一个\n加价格用括号标明如：\n1G(-20)\n2G\n4G(+100)'";
        glbHtml::fmae_row('Cfgs',"<textarea name='fm[cfgs]' rows='6' cols='50' wrap='wrap' $ph_tip>$fm[cfgs]</textarea>");
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("fm[pid]|$pid","kid|$is_add","pid|$pid","ap|$ap","cid|$cid"));
    }

}elseif($view=='list'){

    $msg = '';    
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.msg_pkitem');
        else{
            foreach($fs as $id=>$v){ 
                $msg = lang('flow.msg_set');
                if($fs_do=='del'){ 
                    if($db->table($tabid)->where("pid='$id'")->find()){
                        $msg = lang('admin.cat_dsub',$id); // 有子类别禁止删除
                    }else{
                        $db->table($tabid)->where("kid='$id'")->delete();
                        $msg = lang('flow.msg_del');
                    }
                    $db->table('exd_uatt')->where("pid='$id'")->delete(); // 删除属性
                }elseif($fs_do=='show'){ 
                    $db->table($tabid)->data(array('enable'=>'1'))->where("kid='$id'")->update();
                }elseif($fs_do=='upd'){ 
                    $db->table($tabid)->data(basReq::in($fm[$id]))->where("kid='$id'")->update();
                }elseif($fs_do=='stop'){ 
                    $db->table($tabid)->data(array('enable'=>'0'))->where("kid='$id'")->update();
                }
            }
        }
        basMsg::show($msg,'Redir',"?$mkv&pid=$pid&flag=v1");
    }

    $lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.fl_addin')."[属性模型]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    $lnklay = "<br>\n<a href='?$mkv&view=list'>顶级</a>";
    if($pid){
        $dbr = db()->table($tabid)->where("kid='$pid'")->find();
        $lnklay .= " &gt; $dbr[title]";
    }else{
        $lnklay .= " &gt; [---]";
    }
    glbHtml::tab_bar("[属性模型]管理<span class='span ph5'>|</span>$lnkadd$lnklay","$exlinks",30);
    
    $list = $db->table($tabid)->where("pid='$pid'")->order('top')->select();
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.title_top')."</th>
        <th>".lang('flow.title_enable')."</th><th>属性</th><th>子类</th><th>".lang('flow.title_edit')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    if($list){
    foreach($list as $r){
      $kid = $r['kid']; 
      $cnt1 = db()->table('exd_uatt')->where("pid='$kid'")->count();
      $cnt2 = db()->table('exd_umod')->where("pid='$kid'")->count();
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tc'>$r[title]</td>\n";
      echo "<td class='tc'><input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' /></td>\n";
      echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
      echo "<td class='tc'><a href='$aucut&view=atts&pid=$pid&ap=$r[kid]'>设置($cnt1)</a></td>\n";
      echo "<td class='tc'>".($pid?'---':"<a href='$aucut&view=list&pid=$r[kid]'>管理($cnt2)</a>")."</td>\n";
      echo $cv->Url(lang('flow.dops_edit'),1,"$aucut&view=form&pid=$pid&kid=$r[kid]&recbk=ref","");
      echo "<td class='tl'><input type='text' value='$r[note]' class='txt w120' /></td>\n";
      echo "</tr>"; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='8'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": <select name='fs_do'>".basElm::setOption(lang('flow.op_op4'))."</select> <input name='bsend' class='btn' type='submit' value='".lang('flow.fl_deeltitle')."' /> &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("pid|$pid"));
    
}elseif($view=='form'){
    
    if(!empty($bsend)){
        if($kid=='is__add'){
            // 批量
            if($db->table($tabid)->where("kid='$fm[kid]'")->find()){
                $msg = lang('flow.msg_exists',$fm['kid']);
            }else{
                $msg = lang('flow.msg_add');  
                $db->table($tabid)->data(basReq::in($fm))->insert();
                $id = $fm['kid'];    
            }
        }else{
            $msg = lang('flow.msg_upd');
            unset($fm['kid'],$fm['pid']); 
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
        } 
        #glbCUpd::upd_model($mod);
        basMsg::show($msg);    
    }else{

        if(!empty($kid)){
            $is_add = '';
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $is_add = 'is__add'; // 5120
            $kid = admPFunc::umodKid($tabid, '5120');
        }
        $def = array('kid'=>'','title'=>'','top'=>'666','enable'=>'1','note'=>'','cfgs'=>'','gtab'=>'');
        foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }

        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!$is_add){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='12' reg='str:3-12' tip='3-12个字母或数字' />$ienable");
        } 
        glbHtml::fmae_row(lang('flow.dops_itemname'),"<input name='fm[title]' type='text' value='$fm[title]' class='txt w150' maxlength='48' reg='tit:2-48' tip='2-12个中英文'  />$itop");

        glbHtml::fmae_row('属性分组',"<textarea name='fm[gtab]' rows='6' cols='50' wrap='wrap' placeholder='一行一个；\n至少两行分组才有效'>$fm[gtab]</textarea>");
        glbHtml::fmae_row('Cfgs',"<textarea name='fm[cfgs]' rows='6' cols='50' wrap='wrap' placeholder='自定义配置'>$fm[cfgs]</textarea>");
        glbHtml::fmae_row(lang('flow.title_note'),"<textarea name='fm[note]' rows='6' cols='50' wrap='wrap'>$fm[note]</textarea>");
        
        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("fm[pid]|$pid","kid|$is_add","pid|$pid","cid|$cid"));
    }
}
