<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(dirname(__FILE__)).'/apis/_pub_cfgs.php');

$part = req('part','dtmp'); 
$purls = comStore::cfgDirPath(0,'arr'); 
unset($purls['tpl'],$purls['tpc']);

if(isset($purls[$part])){ 

    $basedir = $purls[$part][0]; 
    $navp = '<b>'; 
    foreach ($purls as $idir => $itime) {
        $cur = $part==$idir ? "class='cur'" : '';
        $navp .= (strpos($navp,'<a')?' - ':'')."<a href='?file=$file&part=$idir' $cur>".basename($itime[0])."</a>";
    }
    $navp .= '</b><br>'; 

    $dir = req('dir'); 
    $opfile = req('opfile'); 
    // 删除操作
    if(!empty($bsend)){
        $fpath = $basedir."/$dir/$opfile";
        if($bsend=='down'){ 
            comHttp::downLoad($fpath); 
            die();
        }elseif($bsend=='del'){ 
            unlink($fpath);
            $msg = 'Delete OK!';
        }
    } 

    $dlist = comFiles::listDir($basedir,'dir');
    $navs = ''; 
    foreach ($dlist as $idir => $itime) {
        $cur = $dir==$idir ? "class='cur'" : '';
        $navs .= (empty($navs)?'':' - ')."<a href='?file=$file&part=$part&dir=$idir' $cur>$idir</a>";
    }
    $flist = $dir ? comFiles::listScan($basedir."/$dir",'',array()) : array();

    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    $psub = $dir ? "&gt;$dir" : "";
    $ldiy = "<a href='?file=admin/ediy&part=exdiy'>DIYSet</a> # ";
    glbHtml::tab_bar("$ldiy Sys Files : $part$psub $umsg","$navp$navs",40);
        
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>No.</th><th>File</th><th>Size</th><th>Edit Time</th><th>Actions</th></tr>\n";
    if(!empty($flist)){ 
        $i=0;
        foreach($flist as $ifile=>$cfg){ 
            $i++; $burl = "?file=$file&part=$part&dir=$dir&opfile=$ifile";
            $down = "<a href='$burl&bsend=down' target='_blank'>Down</a>";
            $move = "<i class='c999'>Move</i>";
            if($part=='dtmp' && !in_array($dir,array('dset','modcm','modex','store'))){
                $del = "<a href='$burl&bsend=del'>Delete</a>";
            }else{
                $del = "<i class='c999'>Delete</i>";
            }
            echo "<tr>\n<td class='tc'>$i</td>\n";
            echo "<td class='tl'><a href='{$purls[$part][1]}/$dir/$ifile' target='_blank'>$ifile</a></td>\n";
            echo "<td class='tc'>".basStr::showNumber($cfg[1], 'Byte')."</td>\n";
            echo "<td class='tc'>".date('Y-m-d H:i:s',$cfg[0])."</td>\n";
            echo "<td class='tc'>$del - $down - $move</td>\n";
            echo "</tr>";
        }
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod","part|$part"));

}else{
    
}

?>
