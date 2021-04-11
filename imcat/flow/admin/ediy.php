<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$_cfgfp = '/cfgs/boot/cfg_adbug.php';
include DIR_ROOT.$_cfgfp; 
if(empty($can_diycfg)){ 
    die("Prohibited! Use FTP Edit `$_cfgfp`, And set `\$can_diycfg = '1'; First!`"); 
}
$_sy_nava['exdiys'] = array(
    'skin' => '/views',
    'cfgs' => '/root/cfgs',
    //'dtmp' => '', // 与`admin/files`gonge功能重复
    'runs' => 'vopcfg',
); 

$part = req('part','binfo'); 
$dkey = req('dkey','cfgs'); 
$dsub = req('dsub',''); 

$lfile = "<a href='?admin-files'>SysFiles</a> # ";
$links = admPFunc::fileNav($part,'envdiy');
if(!in_array($part,array('edit','restore'))) glbHtml::tab_bar("$lfile ".lang('admin.ediy_extool')."[$part]","$links",50); 
$msg = ''; 

$view = req('view');
$efile = req('efile','');
if(substr($efile,0,1)=='.'){
    die("Error [$efile]");
}

if(in_array($part,array('edit','restore','down'))){

    $edir = $_sy_nava['exdiys'][$dkey]; 
    $edir = $edir=='vopcfg' ? '' : (empty($dsub) ? $edir : "$edir/$dsub");
    $nfile = str_replace('//','/',"/$edir/$efile");
    $fp = DIR_PROJ.$nfile;
    
    if($part=='restore'){
        unlink($fp); copy("$fp.maobak",$fp);
        basMsg::show(lang('admin.ediy_rebok'));
    }elseif($part=='down'){
        comHttp::downLoad(DIR_DTMP.$nfile, basename(DIR_DTMP.$nfile)); 
        die();
    }elseif(!empty($bsend)){
        $ndata = $_POST['ndata']; //req('ndata','','Html',102400);
        safScan::deel($ndata); 
        safScan::deel($ndata,1);
        @unlink("$fp.maobak"); copy($fp,"$fp.maobak");
        comFiles::put($fp,$ndata);
        basMsg::show(lang('admin.ediy_editok'));
    }else{
        $ndata = basStr::filForm(comFiles::get($fp)); //str_replace(array('<','>'),array('&lt;','&gt;')
        glbHtml::fmt_head('fmlist',"?$mkv&part=edit&dkey=$dkey&dsub=$dsub&efile=$efile",'tblist');
        echo "\n<tr>\n<th colspan=2>".lang('admin.ediy_doing').": $nfile</th></tr>\n"; 
        echo "\n<tr>\n<td style='width:50%'>".lang('admin.ediy_size').": ".basStr::showNumber(filesize($fp),'Byte')."</td>
                <td class='tr'>".lang('admin.ediy_etime').": ".date("Y-m-d H:i:s",filemtime($fp))."</td></tr>\n";
        echo "\n<tr>\n<td colspan=2><textarea name='ndata' rows='18' wrap='off' style='width:100%'>$ndata</textarea></td></tr>\n";
        echo "\n<tr>\n<td style='width:50%'>".lang('admin.ediy_sbak').".bak</td><th><input name='bsend' type='submit' value='".lang('admin.ediy_sedit')."' /></th></tr>\n";
        glbHtml::fmt_end(array("nmod|nmod","ntpl|ntpl"));        
    }

}elseif($part!='exdiy'){
    
    glbHtml::fmt_head('fmlist',"?$mkv&part=$part&dkey=$dkey",'tblist');
    echo "\n<tr><td><iframe src='".PATH_ROOT."/tools/adbug/$part.php' width='100%' height='560'></iframe></td></tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}else{
    
    $lnkdk = admPFunc::fileNav($dkey,'exdiys');
    
    if($dkey=='runs'){
        $lnkds = " -- ".lang('admin.ediy_nosdir')." -- ";
        $listu = comFiles::listDir(DIR_PROJ); $listu = $listu['file'];
        $lists = comFiles::listDir(DIR_PROJ."/root/run");  
        $edir = '';
        foreach($lists['file'] as $ifile=>$fv){
            $listu["root/run/$ifile"] = $fv; 
        }
    }elseif($dkey=='cfgs'){
        $lnkds = " -- ".lang('admin.ediy_nosdir')." -- ";
        $edir = $_sy_nava['exdiys'][$dkey];
        $listu = comFiles::listScan(DIR_PROJ.$edir);
    }elseif($dkey=='dtmp'){
        $lnkds = " -- ".lang('admin.ediy_nosdir')." -- ";
        $edir = $_sy_nava['exdiys'][$dkey];
        $listu = comFiles::listScan(DIR_DTMP.$edir);

    }else{ //views
        $edir = $_sy_nava['exdiys'][$dkey];
        $lists = comFiles::listDir(DIR_VIEWS);
        $lnkds = ""; $dsub || $dsub = $_cbase['tpl']['def_static']; 
        foreach($lists['dir'] as $sdir=>$etime){
            if(in_array($sdir,array('aimg','b_img','logo'))) continue;
            $ititle = $sdir==$dsub ? "<span class='cF0F'>$sdir<span>" : $sdir;
            $lnkds .= (empty($lnkds)?'':' # ')."<a href='?$mkv&part=$part&dkey=$dkey&dsub=$sdir'>$ititle</a>";
            $listu = comFiles::listScan(DIR_VIEWS."/$dsub");
        }
        $edir = $edir."/$dsub";
    } 
    glbHtml::tab_bar("$lnkdk",$lnkds,50); 

    glbHtml::fmt_head('fmlist',"?",'tblist');
    
    echo "<tr><th>".(empty($edir)?'[/]':$edir)."</th><th>".lang('admin.ediy_file')."</th><th>".lang('admin.ediy_size')."</th>"; 
    echo "<th>".lang('admin.ediy_etime')."</th><th>".lang('admin.ediy_atime')."</th><th colspan=2>".lang('admin.ediy_op')."</th></tr>\n"; //<th>创建</th>
    $idir = $odir = '|';
    foreach($listu as $ifile=>$fv){ 
      $ext = strtolower(strrchr($ifile,".")); 
      if(!in_array($ext,array('.php','.htm','.html','.css','.js','.txt'))) { continue; } 
      $tmp = explode('/',$ifile); $bkfile = $ifile;
      if(count($tmp)==1){
          $idir = '[/]';
          $ifile = $ifile;
      }elseif(count($tmp)>2){
          $idir = $tmp[0];
          $ifile = substr($ifile,strpos($ifile,'/'));
      }else{
          $idir = $tmp[0];
          $ifile = $tmp[1]; 
      }
      $ndir = $idir==$odir ? '' : $idir;
      $odir = $idir;
      $atime = $dkey=='dtmp' ? 0 : fileatime(DIR_PROJ.$edir."/$bkfile"); 
      $atstr = date("Y-m-d H:i",$atime);
      $atstr = $atime==$fv[0] ? "<span class='cCCC'>$atstr</span>" : "<span class='cF0F'>$atstr</span>";
      echo "<td class='tr'>$ndir</td>\n";
      echo "<td class='tl'>$ifile</td>\n"; 
      echo "<td class='tr'>".basStr::showNumber($fv[1],'Byte')."</td>\n";
      echo "<td class='tc'>".date("Y-m-d H:i",$fv[0])."</td>\n"; //$title,$td=1,$url,$twin='',$w=780,$h=560
      echo "<td class='tc'>$atstr</td>\n";
      if($dkey=='dtmp'){
          echo $cv->Url('Down','1',"?$mkv&part=down&dkey=$dkey&dsub=$dsub&efile=$bkfile");
      }elseif(file_exists(DIR_PROJ.$edir."/$bkfile.maobak")){
          echo $cv->Url(lang('admin.ediy_rebak'),'1',"?$mkv&part=restore&dkey=$dkey&dsub=$dsub&efile=$bkfile");
      }else{
          echo "<td class='tc cCCC'>".lang('admin.ediy_rebak')."</td>\n";
      }
      echo $cv->Url(lang('flow.dops_edit'),'1',"?$mkv&part=edit&dkey=$dkey&dsub=$dsub&efile=$bkfile",lang('admin.ediy_edit').":{$bkfile}");
      echo "</tr>"; 
    }
    
    glbHtml::fmt_end(array("nmod|nmod","ntpl|ntpl"));
    
} 

?>
