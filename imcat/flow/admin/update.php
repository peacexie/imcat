<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 

$parts = empty($parts) ? 'cache' : $parts;
$cfg = basLang::ucfg('cfgbase.admupd'); 

$gbar = ''; $ggap = "\n"; // class='cur,
foreach($cfg as $k=>$v){ 
    $imp = "<input type='checkbox' class='rdcb' value='cache' onClick=\"clr_group(this,'$k');\">";
    $gbar .= "$ggap<label id='navid_$k'>$imp$v</label>\n";    
    $ggap = ' | ';
}

$g0 = $db->table('base_model')->where("enable='1'")->order('pid,top,kid')->select();
$g1 = array();
foreach($g0 as $k=>$v){
    $g1[$v['kid']] = $v;
}

if(empty($bsend)){
    $str1 = ''; $ti = 0;
    foreach($g1 as $k=>$v){
        if($v['pid']=='types'){
            if($ti && $ti%6==0) $str1 .= "<br>";
            $str1 .= "<label><input type='checkbox' class='rdcb cbg_types' name='clr[]' id='clr_$k' value='type_$k'>$v[title]</label>";
            $ti++;
        }
    }
    $stra = "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_cache' value='cache'>{$cfg['cache']}</label>";
    $stra .= "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_relat' value='relat'>".lang('admin.ud_reltype')."</label>";    
    $stra .= "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_gperm' value='gperm'>".lang('admin.ud_gperm')."</label>";

    $strb = "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_data' value='data'>".lang('admin.ud_exdata')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_file' value='file'>".lang('admin.ud_exfile')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_ctpl' value='ctpl'>".lang('admin.ud_tplcache')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_ctag' value='ctag'>".lang('admin.ud_tagcache')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_cadv' value='cadv'>".lang('admin.ud_advcache')."</label>";    
    
    $strm = "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_menua' value='menua'>".lang('admin.ud_amenu')."</label>";
    $strm .= "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_menum' value='muadm'>".lang('admin.ud_umenu')."</label>";    
    $strm .= "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_madvs' value='madvs'>".lang('admin.ud_adlink')."</label>";    
    
    glbHtml::tab_bar($cfg['cache']." : ".lang('admin.ud_updclr'),lang('admin.ud_quick')." : $gbar",35);
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
    glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['cache'],$stra);
    glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['types'],$str1);
    glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['menux'],$strm);
    glbHtml::fmae_row(lang('flow.dops_clear').':'.$cfg['data'],$strb);
    glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
    // opcache
    $opbar = "<a href='?admin-update&bsend=opcache&act=view'>View-Cache</a>\n # ";
    $opbar .= "<a href='?admin-update&bsend=opcache&act=delete'>Clear-Cache</a>\n";
    glbHtml::fmae_row('Opcache',$opbar);
    glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid))); 
    echo basJscss::jscode("function clr_group(e,part){fmSelGroup(e,part);$('#navid_'+part).toggleClass('cur');}$('input:first').trigger('click');");

}elseif($bsend=='opcache'){
    if(!function_exists('opcache_get_status')){
        echo "<p class='f18 tc'>Opcache Disabled! Please set your php.ini</p>";
    }elseif($act=='view'){
        dump(opcache_get_status());
    }elseif($act=='delete'){
        opcache_reset();
        echo "<p>Clean Opcache OK!</p>";
        dump(opcache_get_status()); 
    }
    echo "<p class='tc'><br><br><a href='?admin-update'>Go-back!</a></p>\n";

}elseif(!empty($bsend)){

    $clr = basReq::arr('clr'); 
    if(empty($clr)) basMsg::show(lang('admin.ud_pick'),'Redir',"?admin-update&parts=$parts");
    
    if(in_array('cache',$clr)){
        glbCUpd::upd_groups();
        foreach($g1 as $k=>$v){
            if(in_array($k,array('score','sadm','smem','suser',))){ 
                glbCUpd::upd_paras($k);
            }
            if($v['pid']=='groups') continue;
            if($v['pid']=='types' && !empty($v['etab'])) continue; 
            if(in_array($v['pid'],array('score','sadm','smem','suser',))) continue;
            glbCUpd::upd_model($k); 
        }
    }
    if(in_array('relat',$clr)){
        $re = glbCUpd::upd_relat();
        dump($re);    
    }
    foreach($clr as $k){
        if(substr($k,0,5)=='type_'){
            $key = substr($k,5);
            if(isset($g1[$key])){ 
                echo "<br>$key : OK! ";
                glbCUpd::upd_model($key); 
            }        
        }
    }
    if(in_array('menua',$clr)){
        foreach(array('muadm') as $mod){
            echo glbCUpd::upd_menus($mod);
        }
    } // upd_grade放在upd_menus后面
    if(in_array('menum',$clr)){
         admAFunc::mkvInit();
    }
    if(in_array('gperm',$clr)){
        glbCUpd::upd_grade(); 
    }
    if(in_array('data',$clr)){
        devScan::clrLogs();
    }
    if(in_array('file',$clr)){
        devScan::clrTmps();
        $p0 = DIR_DTMP.'/modcm/';
        $a0 = comFiles::listDir($p0);
        $af = $a0['file'];
        foreach($af as $k=>$v){
            if(strstr($k,'.cfg.php')){
                $k2 = substr(str_replace(".cfg.php","",$k),1);
                if($k2=='groups') continue;
                if(!isset($g1[$k2])){
                    unlink("{$p0}_{$k2}.cfg.php");    
                }
            }
        }
    }
    
    // 
    $arr = array('ctpl'=>'','ctag'=>'tagc','cadv'=>'advs',);
    foreach($arr as $k=>$v){
        if(in_array($k,$clr)){
            devScan::clrCTpl($v);    
        }
    }

    if(in_array('madvs',$clr)){
        foreach($g1 as $k=>$v){
            if(in_array($v['pid'],array('advs',))){ 
                vopStatic::advMod($k,"(all)");
            }
        }
    }
    
    basMsg::show(lang('admin.ud_end'),'Redir',"?admin-update&parts=$parts");

}elseif($view=='set'){



}

?>
