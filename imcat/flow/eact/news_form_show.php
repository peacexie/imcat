<?php
namespace imcat;
(!defined('RUN_DOPA')) && die('No DopA'); 
 
if(!empty($did)){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find();
    $fme = $db->table($dop->tbext)->where("did='$did'")->find();
    $fme && $fmo = $fmo + $fme;
    $isadd = 0;
}else{
    $fmo = array();
    $isadd = 1;
}
if(empty($fmo['vtype'])){ $fmo['vtype']='ptxt'; } 
$dop->fmo = $fmo;
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
$dop->fmCatid();

    // extra-start
    $vals = $fmo; $skip = array('0');
    $mfields = read("$mod.f");
    $_cbase['run']['jtype_mods'] = '';
    $_cbase['run']['jtype_init'] = '';
    foreach($mfields as $k=>$v){ 
        if($v['type']=='parts'){ //(分段字段...)
            echo "<tr><th>$v[title]</th><th class='tr'>$v[vtip]</th></tr>\n";
            continue;
        }elseif(!in_array($k,$skip)){
            $item = fldView::fitem($k,$v,$vals);
            $item = fldView::fnext($mfields,$k,$vals,$item,$skip);
            glbHtml::fmae_row($v['title'],$item);
            if($k=='exfile'){
                $expics = comStore::picsTab(empty($vals['exfile'])?'':$vals['exfile']);
                $pic1 = empty($expics[0]) ? '' : $expics[0];
                $vals['uatt'] = $vals['uvdo'] = $pic1; 
                $_ftab = ['uatt'=>'附件','uvdo'=>'视频'];
                foreach ($_ftab as $_fk=>$_ftitle) {
                    $item = fldView::fitem($_fk,$mfields['mpic'],$vals); // str:
                    $item = str_replace(['fix:image','gif/jpg/jpeg/png'], ['str:0-255','media/dou'], $item);
                    glbHtml::fmae_row($_ftitle,$item); $skip[] = $_fk;
                }
            }
        }
    }
    if(!empty($_cbase['run']['jtype_mods'])){
        $jpath = PATH_BASE."?ajax-comjs&act=jsTypes&mod=".$_cbase['run']['jtype_mods']."";
        echo basJscss::jscode("jQuery.getScript('$jpath',function(){\n".$_cbase['run']['jtype_init']."})"); 
    }
    // extra-start
    //fldView::lists($mod,$fmo);


$dop->fmProp();
glbHtml::fmae_send('bsend',lang('flow.dops_send'));
// extra-start
$detail_bak = in_array($fmo['vtype'],['cmd','ctxt']) ? $fmo['detail'] : '';
echo "<textarea id='detail_valbk' name='detail_valbk' style='display:none;'>$detail_bak</textarea>";
// extra-end
glbHtml::fmt_end(array("mod|$mod","isadd|$isadd"));

// 扩展
echo basJscss::jscode("",tpath().'/news-form.js?ver=609');
//echo basJscss::csscode(".optsItem_X{ display:block; padding:0.2rem 0; }");

