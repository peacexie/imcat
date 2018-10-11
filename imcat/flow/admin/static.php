<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$ntpl = req('ntpl',$_cbase['tpl']['def_static']);
$_cbase['tpl']['vdir'] = $ntpl;
$cronurl = PATH_BASE."?ajax-cron";

$view = req('view','list');
$nmod = req('nmod','home'); 
$vcfgs = vopTpls::etr1('tpl');
unset($vcfgs['rest']); 
$stitle = lang('admin.st_admin').":($ntpl)".basLang::pick(0,$vcfgs[$ntpl][0]); 
$msg = ''; 

$lnks = "# "; $ncfg = array(); 
foreach($vcfgs as $itpl=>$suit){
    if($itpl==$ntpl){ // dynamic/static/both/all/
        $ncfg = vopTpls::entry($itpl,'ehlist','static');  
    }
    $cname = basLang::pick(0,$suit[0]); //is_array($suit[0]) ? $suit[0]['cn'] : $suit[0];
    $ititle = $itpl==$ntpl ? "<span class='cF0F'>$cname<span>" : $cname;
    $lnks .= "<a href='?$mkv&ntpl=$itpl'>{$ititle}</a> # ";
    
}
glbHtml::tab_bar("$stitle $msg",$lnks,40);

if($view=='list'){

    $mods = array_keys($ncfg);
    glbHtml::fmt_head('fmlist',"?",'tblist');
    echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.st_admin')." --- $ntpl:$nmod --- <a href='?$mkv&ntpl=$ntpl&nmod=all'>".lang('admin.st_allmod')."</a></th></tr>\n";
    echo "\n<tr><td class='tc'>".lang('admin.st_opmode').": </td>\n<td>"; $ti = 0;
    foreach($mods as $imod){
        $iname = $imod=='home' ? lang('admin.st_home') : (isset($_groups[$imod]) ? $_groups[$imod]['title'] : "($imod)");
        $ititle = $imod==$nmod ? "<span class='cF0F'>$iname<span>" : "$iname";
        if($ti==0) echo " ";
        else echo ($ti && $ti%6==0) ? "<br>" : " # ";
        echo "<a href='?$mkv&ntpl=$ntpl&nmod=$imod'>$ititle</a>";    
        $ti++;
    }
    echo "</td></tr>\n";
    if($nmod=='home'){
        $sfile = vopStatic::getPath('home','home',0);
        $exists = file_exists(DIR_HTML."/$sfile") ? date('Y-m-d H:i:s',filemtime(DIR_HTML."/$sfile")) : lang('admin.st_notfound');
        echo "\n<tr><td class='tc'>".lang('admin.st_hmstatic')."</td>\n<td>
            ".lang('admin.st_stfile').": {html}".$sfile." (".$exists.")
            <p class='tc f18'>
            <a href='$cronurl&static=home&tpldir=$ntpl&act=add' class='f18 fB' onclick='return winOpen(this);'>".lang('admin.st_crpage')."</a> #
            <a href='$cronurl&static=home&tpldir=$ntpl&act=del' class='f18 fB' onclick='return winOpen(this);'>".lang('admin.st_depage')."</a> # 
            <a href='".vopUrl::fout("$ntpl:0")."' target='_blank' class='f18 fB' target='_blank'>".lang('admin.st_vres')."</a>
            </p>
        </td></tr>\n";

    }elseif($nmod!=='all'){   
        $iname = isset($_groups[$nmod]) ? $_groups[$nmod]['title'] : lang('admin.st_udefine');
        $mcfgs = read($nmod);
        echo "\n<tr><td class='tc'>{$iname}<br>[$ntpl:$nmod]</td>\n<td>";
        echo "\n<p>
            &nbsp; ● ".lang('admin.st_cstatic').": ".lang('admin.st_allrecs',count($ncfg[$nmod]))."
            </p>
            <p class='tc f14'>
            <a href='$cronurl&static=mlist&mod=$nmod&tpldir=$ntpl&act=add' class='fB' onclick='return winOpen(this);'>".lang('admin.st_crpage')."</a> #
            <a href='$cronurl&static=mlist&mod=$nmod&tpldir=$ntpl&act=del' class='fB' onclick='return winOpen(this);'>".lang('admin.st_depage')."</a> 
            </p>";
        if(!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))){
            $recs = $db->table("{$mcfgs['pid']}_$nmod")->count();
            echo "\n<p class='right ph20'><a href='".vopUrl::fout("$ntpl:0")."?$nmod' target='_blank' class='f18 fB'>".lang('admin.st_dmhome')."</a></p>
            <p>
            &nbsp; ● ".lang('admin.st_dstatic').": ".lang('admin.st_allrecs',$recs)."
            <input name='limit' type='text' value='20' class='w40' maxlength='3'>".lang('admin.st_batnum')." &nbsp; 
            offset/dirfix: <input name='offset' type='text' value='' class='w40' maxlength='4'> &nbsp; 
            </p>
            <p class='tc f14'>
            <a href='$cronurl&static=mdetail&mod=$nmod&tpldir=$ntpl&act=add' class='fB' onclick='return stsetLink(this);'>".lang('admin.st_crpage')."</a> #
            <a href='$cronurl&static=mdetail&mod=$nmod&tpldir=$ntpl&act=del' class='fB' onclick='return stsetLink(this);'>".lang('admin.st_depage')."</a> 
            </p>";
        }
        echo "\n</td></tr>\n";
    }
    glbHtml::fmt_end(array("nmod|$nmod","ntpl|$ntpl"));
    
    if($nmod=='all'){
        echo "<table width='100%' border=1>";
        $i = 0;
        foreach($mods as $imod){
            $i++;
            if($imod=='home'){
                $url = "static=home&tpldir=$ntpl&act=add";
            }else{
                $url = "static=mlist&mod=$imod&tpldir=$ntpl&act=add";    
            }
            echo "\n<td><iframe src='$cronurl&$url' width='100%'></iframe></td>";
            if($i%3==0){ echo "</tr><tr>"; }
            $mcfgs = read($imod);
            if(!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))){
                $url = "static=mdetail&mod=$imod&tpldir=$ntpl&act=add";
                echo "\n<td><iframe src='$cronurl&$url' width='100%'></iframe></td>";
                $i++;
            }
            if($i%3==0){ echo "</tr><tr>"; } 
        }
        echo "</table>";
    }

    
}

/*    

*/

?>
