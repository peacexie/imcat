<?php
(!defined('RUN_INIT')) && die('No Init');
usrPerm::run('pfile','admin/groups.php');

$mod = empty($mod) ? '' : $mod;
$type = req('type','mdata'); // mod, tabid
$kid = empty($kid) ? '' : $kid;
$title = req('title','');

$_cfg = read($mod); 
$cp = new exdCopy($mod, $type);

if(!empty($bsend)){
    
    $method = $type=='tabid' ? 'cplan' : 'cdata'; 
    $res = $cp->$method($kid, $fm['kid'], $fm['title']);
    basMsg::show(" [{$fm['kid']}] - ".lang('flow.dops_cpok'));
    
}else{ 

    echo "<div class='h02'>&nbsp;</div>";
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');

    $tabid = glbDBExt::getTable($mod);
    if($type=='mdata'){
        $kids = glbDBExt::dbAutID($tabid,'yyyy-md-','31');
        $kidnew = $kids[0];
    }else{
        $kidnew = "{$kid}_".basKeyid::kidRand('0',5);
    }
    $okrow = "<input type='text' value='$kid' class='txt w240 disc' disabled='disabled' />";
    $nkrow = "<input name='fm[kid]' type='text' value='$kidnew' class='txt w240' />";
    glbHtml::fmae_row('Key'.lang('flow.dops_kid'),"$okrow");
    glbHtml::fmae_row(lang('flow.dops_copy').'-=>',"$nkrow");

    $titlenew = "{$title}_copy";
    $okrow = "<input type='text' value='$title' class='txt w240 disc' disabled='disabled' />";
    $nkrow = "<input name='fm[title]' type='text' value='$titlenew' class='txt w240' />";
    glbHtml::fmae_row(lang('flow.dops_itemname'),"$okrow");
    glbHtml::fmae_row(lang('flow.dops_copy').'-=>',"$nkrow");

    glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
    glbHtml::fmt_end(array("mod|$mod","type|$type","kid|$kid","title|$title"));
}

