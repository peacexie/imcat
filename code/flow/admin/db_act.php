<?php
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$tabinfo = $db->tables(1); 
$tabid = req("tabid",'base_model'); 

$fixs = lang('admin.dba_sel').':'; $fixa = array('-1'); 
$opts = ""; 
foreach($tabinfo as $r){
    $itab = $r['Name'];
    $fix = substr($itab,0,strpos($itab,'_'));
    $igap = $fix=='exd' ? '<br>' : ' ';
    if(!in_array($fix,$fixa)) $fixs .= "$igap<input type='checkbox' class='rdcb' value='cache' onClick=\"fmSelGroup(this,'$fix');\">$fix"; 
    $fixa[] = $fix;
    $opts .= "$itab=$itab($r[Rows])\n";
}
$sact = "window.location.href='?mkv=$mkv&view=$view&tabid='+this.options[selectedIndex].value";
$opts = "<select onchange=\"$sact\">".basElm::setOption($opts,$tabid,lang('admin.dba_stab'))."</select>";
$exp = " &nbsp; <a href='$aurl[1]&view=Export' target='_blank'>".lang('admin.dba_dict')."</a>";
$exp .= " &nbsp; <a href='$aurl[1]&view=Clear' target='_blank'>".lang('admin.dba_clear')."</a>";
$sbar = $view=='list' ? $fixs : "$opts $exp";

// dbdict操作
$syrem = glbDBExt::dbComment(); 
if(!empty($bsend)){
    if(empty($fs_do)) $msg = lang('flow.dops_setop');
    if(empty($fs)) $msg = lang('flow.dops_setitem');
    $cnt = 0; $cnu=0; $act = '';
    if(empty($msg)){
      foreach($fs as $id=>$v){ 
          if(in_array($fs_do,array('opt','rep'))){ 
             $tab = $db->table($id,2);
             $op = $fs_do=='opt' ? 'OPTIMIZE' : 'REPAIR';
             $db->query("$op TABLE $tab");
             $cnt++;
          }elseif($fs_do=='upd'){ 
             $uptab = basReq::arr('uptab');
             $uprem = basReq::arr('uprem'); 
             if(!empty($uprem[$id])){
                 if(isset($syrem['fdemo'][$id]) && $uprem[$id]==$syrem['fdemo'][$id]){ 
                     $db->table('bext_dbdict')->where("kid='$id' AND tabid='$tabid'")->delete(); //array('kid'=>$id,'tabid'=>$tabid)
                 }else{
                     $uprem[$id] = $irem = trim(basStr::filSafe4($uprem[$id]));
                     $db->table('bext_dbdict')->data(array('kid'=>$id,'tabid'=>$tabid,'title'=>$irem))->replace();
                 }
                 $cnu++;
             }
          }
      } 
    }
    $cnt && $msg = "$cnt ".lang('admin.dba_tabs').($fs_do=='opt' ? lang('admin.dba_opt') : lang('admin.dba_fix')).lang('admin.dba_ok');
    $cnu && $msg = "$cnu ".lang('admin.dba_dok');
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($view,'dba');

if($view=='runsql'){ $sbar = "Run-SQL : ---"; }
if(!in_array($view,array('Export','View'))) glbHtml::tab_bar("$links $umsg",$sbar,35,'tc');

if($view=='Clear'){
    
    devScan::clrLogs();

}elseif($view=='runsql'){

    $sqldata = req("sqldata",'','html'); 
    $part = req("part"); 
    if(!empty($sqldata)){ 
        $res = devData::run1Sql($sqldata);
        $msg = is_numeric($res) ? "($res) Run OK!" : $res;
    }else{
        $msg = 'Use `---&lt;split&gt;---` Split SQL Statement.';
    }
    $istr = "<textarea name='sqldata' cols='' wrap='off' rows='24' style='width:100%'></textarea>";
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "\n<tr><td class='tc w120'>Run-SQL</td>\n<td>$istr</td></tr>\n";
    $opbar = "<div class='w180 tc right'><input name='btnrun' class='btn' type='submit' value='SQL' /></div>";
    echo "\n<tr><td class='tc'>Run</td>";
    echo "<td colspan='15'>$opbar<div class='pg_bar'>msg: $msg</div></td>\n</tr>";
    glbHtml::fmt_end(array("view|$view","tabid|tabid","fs_do|upd"));

}elseif($view=='list'){
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    $hdex = "<th>Create/Update</th><th>Engine/Collation</th><th>Comment</th></tr>";
    echo "<th>".lang('flow.title_select')."</th><th>Name</th><th>Rows</th><th>Data</th><th>Index</th><th>Free</th><th>AI</th>$hdex\n";
    foreach($tabinfo as $r){ 
        $kid = $r['Name']; $fix = substr($kid,0,strpos($kid,'_'));
        //$data = basStr::showNumber($r['Data_length'],1)."/".basStr::showNumber($r['Index_length'],1)."/".basStr::showNumber($r['Data_free'],1);
        $date = "<span class='block h100'>".$r['Create_time'].'<br>'.$r['Update_time']."<span>";
        echo "<tr>\n".str_replace("'rdcb'","'rdcb cbg_$fix'",$cv->Select($kid)); //cbg_types
        echo "<td class='tc'>$kid</td>\n";
        echo "<td class='tc'>$r[Rows]</td>\n";
        echo "<td class='tc'>".basStr::showNumber($r['Data_length'],'Byte')."</td>\n";
        echo "<td class='tc'>".basStr::showNumber($r['Index_length'],'Byte')."</td>\n";
        echo "<td class='tc'>".basStr::showNumber($r['Data_free'],'Byte')."</td>\n";
        echo "<td class='tc'>$r[Auto_increment]</td>\n";
        echo "<td class='tc'>$date</td>\n";
        echo "<td class='tc'>$r[Engine]/$r[Collation]</td>\n";
        echo "<td class='tc'>$r[Comment]</td>\n";
        echo "</tr>";
    } 
    $op = "".basElm::setOption("opt|".lang('admin.dba_opt')."\nrep|".lang('admin.dba_fix')."",'',lang('flow.op0_bacth'));
    $opbar = "<div class='w180 tc right'><select name='fs_do'>$op</select>";
    $opbar .= "<input name='bsend' class='btn' type='submit' value='".lang('admin.dba_deel')."' /></div>";
    echo "\n<tr><td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>";
    echo "<td colspan='15'>$opbar<div class='pg_bar'>Actions</div></td>\n</tr>";
    glbHtml::fmt_end(array("view|$view"));
    
}elseif($view=='rem'){
    
    $fields = glbDBExt::dbComment($tabid); 
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Name</th><th>type</th><th>null</th><th>def</th><th>pri/AI</th><th>Comment</th>\n";
        $r = @$fields[0];
        $irem = empty($uprem[0]) ? @$r['_rem'] : $uprem[0];
        $css = @$r['_flag']=='sys' ? 'disc' : '';
        $name = @$r['_flag']=='sys' ? 'disabled' : "name='uprem[0]'";
        echo "<tr>\n".$cv->Select('0');
        echo "<td class='tc bold' colspan='2'>$tabid</td>\n";
        echo "<td class='tc' colspan='3'>(".lang('admin.dba_dbtab').")</td>\n";
        echo "<td class='tc'><input type='text' $name value='$irem' maxlength='24' class='txt w240 $css'/></td>\n";
        echo "</tr>"; unset($fields[0]);
    foreach($fields as $r){ 
        $kid = $r['name'];
        $irem = empty($uprem[$kid]) ? @$r['_rem'] : $uprem[$kid];
        $default = $r['default'];
        $data = $r['primary']."/".$r['autoinc'];
        $css = ($r['_flag']=='sys'||$r['_flag']=='mod') ? 'disc' : ($r['_flag']=='demo' ? 'c00F' : ''); //'d'.$r['_flag']
        $name = ($r['_flag']=='sys'||$r['_flag']=='mod') ? 'disabled' : "name='uprem[$kid]'";
        echo "<tr>\n".$cv->Select($kid);
        echo "<td class='tc'>$kid</td>\n";
        echo "<td class='tc'>$r[type]</td>\n";
        echo "<td class='tc'>".glbHtml::null_cell(!$r['notnull'])."</td>\n";
        echo "<td class='tc'>$default</td>\n";
        echo "<td class='tc'>$data</td>\n";
        echo "<td class='tc'><input type='text' $name value='$irem' maxlength='24' class='txt w240 $css'/></td>\n";
        echo "</tr>";
    } 
    $opbar = "<div class='w180 tc right'><input name='bsend' class='btn' type='submit' value='".lang('admin.dba_upd')."' /></div>";
    echo "\n<tr><td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>";
    echo "<td colspan='15'>$opbar<div class='pg_bar'>".lang('admin.dba_gray')."</div></td>\n</tr>";
    glbHtml::fmt_end(array("view|$view","tabid|$tabid","fs_do|upd"));

}elseif($view=='Export'){ 
    $data = devBase::dbDict($tabinfo);
    //basEnv::obShow($data,0);
    comFiles::put(DIR_DTMP."/store/dbdic-{$_cbase['sys']['lang']}.cac_htm",$data);
    echo "<p class='tc'><br><a href='$aurl[1]&view=View'>".lang('admin.dba_view')."</a><br><br><p>";
}elseif($view=='View'){ 
    basEnv::obClean();
    include DIR_DTMP."/store/dbdic-{$_cbase['sys']['lang']}.cac_htm";
    die();
}
