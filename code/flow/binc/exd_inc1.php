<?php
(!defined('RUN_INIT')) && die('No Init');

if(in_array($view,array('list','fields','urlset','urlist','loglist'))){ //'fdefs',
    $jname = $tabid=='exd_crawl' ? lang('flow.ei_cwraldata') : lang('flow.ei_impdata');
    $lnkadd = "<a href='$aurl[1]&view=form' onclick='return winOpen(this,\"".lang('flow.dops_add')."[$jname]\");'>".lang('flow.fl_addtitle')."&gt;&gt;</a>";
    $lnkre = "<a href='$aurl[1]&view=list'>&lt;&lt;".lang('flow.ei_back')."</a>";
    $links = admPFunc::fileNav($file,'exd_oimp');
    glbHtml::tab_bar("[$jname]".lang('flow.ei_paln')."<span class='span ph5'>|</span>".($view=='list' ? $lnkadd : $lnkre),"$links",50);
}

if($view=='set_a2'){
    
    glbHtml::fmt_head('fmlist',"?",'tblist');
    echo "\n<tr><th class='tc w150'></th>\n<th>".lang('flow.ei_sharecfg').": </th></tr>\n";
    if(empty($ocfgs)){
        echo "\n<tr><td class='tc w180'>".lang('flow.cfg_tips').": </td>\n<td>
            ".lang('flow.cfg_nocfg')."<br>".lang('flow.cfg_copy').": /root/cfgs/excfg/ex_outdb.php
        </td></tr>\n";
    }else{
        echo "\n<tr><td class='tc w150'>".lang('flow.ei_cfgfile').": </td>\n<td>/root/cfgs/excfg/ex_outdb.php，
        <a href='?file=admin/ediy&part=edit&dkey=cfgs&dsub=&efile=excfg/ex_outdb.php' onclick=\"return winOpen(this,'".lang('flow.ei_editcfg')."',780,560);\">".lang('flow.title_edit')."</a></td></tr>\n";
        echo "\n<tr><td class='tc'>['psyn']['server']</td>\n<td>{$ocfgs['psyn']['server']}/plus/ajax/exdb.php</td></tr>\n";
        echo "\n<tr><td class='tc'>['sign']['sapp']</td>\n<td>{$ocfgs['sign']['sapp']}</td></tr>\n";
        echo "\n<tr><td class='tc'>['sign']['skey']</td>\n<td>{$ocfgs['sign']['skey']}</td></tr>\n";
        echo "\n<tr><td class='tc'>".lang('flow.ei_doc').": </td>\n<td><a href='{$_cbase['server']['txmao']}/dev.php?dev2nd-exdata'>".lang('flow.ei_tip2nd')."</a></td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));

}if($view=='del_b3'){
    
    if(empty($fs_do)) $msg = lang('flow.dops_setop');
    if(empty($fs)) $msg = lang('flow.dops_setitem');
    $cnt = 0; 
    if(empty($msg)){
      foreach($fs as $id=>$v){ 
          if($fs_do=='dele'){ 
              $db->table($dop->tbid)->where("kid='$job' AND sysid='$id'")->delete(); 
              $cnt++;
          }elseif($fs_do=='xxx'){ 
              ;///
          }
      } 
    }
    $cnt && $msg = "$cnt ".lang('flow.dops_delok');
    
    /*/ 清理操作
    if(!empty($bsend)&&$fs_do=='dnow'){
        $msg = $dop->opDelnow();
        basMsg::show($msg,'Redir',"?file=$file&view=$view&job=$job&mod=$mod&flag=v1");
    }*/
    
}if($view=='fset'){

    if(!empty($bsend)){
        $msg = lang('flow.msg_upd');
        $fm['kid'] = $kid; 
        $fm['model'] = $job; //->where("kid='$job'")
        $fm['dealfmts'] = implode(',',$fm['dealfmts']); 
        if($tabid=='exd_crawl') exdBase::fldSave($fm,5);
        $db->table('exd_sfield')->data(basReq::in($fm))->replace();
        //basMsg::show("$msg","Redir","$aurl[1]");
        echo basJscss::Alert("$msg","Redir","$aurl[1]",1);     
    }

    echo "<div class='h02'>&nbsp;</div>";
    $fm = $db->table('exd_sfield')->where("model='$job' AND kid='$kid'")->find(); 
    $fa = array(
        "orgtg1","orgtg2","orgtg3","orgtg4","orgtg5",
        "dealtabs","dealfmts","dealconv","dealfunc","dealfunp","defval","defover",
    );
    foreach($fa as $k){ 
        $fm[$k] = @basStr::filForm($fm[$k]); 
    }
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
    echo "\n<tr><th class='tc w150'>$jcfg[title]</th>\n<th class='tl'>".lang('flow.ei_fldxcfg',$kid)."</th></tr>\n";
    glbHtml::fmae_row(lang('flow.ei_name'),"$jcfg[title] --- ".lang('flow.ei_fldxcfg',$kid));
    if($tabid=='exd_crawl'){
        $url = PATH_ROOT."/plus/ajax/exdb.php?act=crawl&mod=$jcfg[mod]&job=$job&debug=field&field=$kid&url=".urlencode($jcfg['odmp'])."&".exdBase::getJSign();
        glbHtml::fmae_row(lang('flow.ei_demodetail'),"<input name='fm_odmp' type='text' value='$jcfg[odmp]' class='txt w400' maxlength='240' readonly />");
        glbHtml::fmae_row(lang('flow.ei_debugrule'),"<a href='$jcfg[odmp]' target='_blank'>".lang('flow.ei_opendemo')."</a> # <a href='$url' target='_blank'>".lang('flow.ei_debfld',$kid)."</a> ");
        echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>".lang('flow.ei_getrule').": </th></tr>\n";
        exdBase::fldForm($fm,5);
    }else{
        $odbname = $ocfgs['list'][$jcfg['odb']]; 
        glbHtml::fmae_row(lang('flow.ei_fromdb'),"$jcfg[odb] --- $odbname");
        glbHtml::fmae_row(lang('flow.ei_fromfield'),"<input name='fm[orgtg1]' type='text' value='$fm[orgtg1]' class='txt w400' maxlength='48' reg='key:1-24' />");
    }
    
    echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>".lang('flow.ei_resdea').": </th></tr>\n";
    glbHtml::fmae_row(lang('flow.ei_reorg'),"<textarea name='fm[dealtabs]' rows='3' cols='50' wrap='wrap'>$fm[dealtabs]</textarea>");
    $cbarr = basLang::ucfg('cfgbase.crawl_fset'); 
    $cbstr = basElm::setCBox('dealfmts',$cbarr,$fm['dealfmts'],6);
    glbHtml::fmae_row(lang('flow.ei_cdeal'),"$cbstr");
    glbHtml::fmae_row(lang('flow.ei_retab'),"<textarea name='fm[dealconv]' rows='3' cols='50' wrap='wrap'>$fm[dealconv]</textarea>");
    $iparas = " &nbsp; ".lang('flow.ei_param')."<input name='fm[dealfunp]' type='text' value='$fm[dealfunp]' class='txt w150' maxlength='24' />";
    glbHtml::fmae_row(lang('flow.ei_resfunc'),"<input name='fm[dealfunc]' type='text' value='$fm[dealfunc]' class='txt w180' maxlength='24' />$iparas");
    glbHtml::fmae_row(lang('flow.ei_nuldef'),"<input name='fm[defval]' type='text' value='$fm[defval]' class='txt w400' maxlength='255' />"); 

    echo "\n<tr><th class='tc w150'></th>\n<th class='tl'>".lang('flow.exd_reurl')."</th></tr>\n";
    $detail = lang('flow.exd_rutip')."{$_cbase['server']['txmao']}/dev.php?advset-exdata#s_fields";
    glbHtml::fmae_row(lang('flow.title_note'),"<textarea rows='3' cols='50' wrap='wrap'>$detail</textarea>");
    glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
    glbHtml::fmt_end(array("mod|$mod","job|$job"));

}if($view=='fields'){

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.ei_extab')."</th>";
    echo "<th>".lang('flow.title_enable')."</th><th>".lang('flow.title_note')."</th>";
    echo "<th>".lang('flow.title_type')."</th><th>".lang('flow.title_cfg')."</th><th>".(empty($ispara) ? lang('flow.ei_db') : lang('flow.ei_nowval'))."</th><th title='".lang('flow.ei_maxlen')."'>".lang('flow.ei_nchr')."</th>";    
    echo "<th class='wp15'>".lang('flow.title_note')."</th>\n";
    echo "</tr>\n";
    $list = $db->table('base_fields')->where("model='$mod'")->order('enable DESC,top')->select();
    $fskip = basElm::line2arr($jcfg['fskip']); 
    if($list){
    foreach($list as $r){
      $kid = $r['kid'];
      $note = basReq::out($r['vreg']).' | '.basReq::out($r['vtip']);
      $note = $note==' | ' ? '' : $note;
      $types = fldCfgs::viewTypes();
      $plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$r['fmextra']]) ? ' ('.$plugs[$r['fmextra']].')' : '';
      $dbstr = "$r[dbtype] ".(empty($r['dblen'])?'':"($r[dblen])").(strlen($r['dbdef'])?' ['.$r['dbdef'].']':'');
      echo "<tr>\n";
      echo "<td class='tc'>$r[kid]</td>\n";
      echo "<td class='tl'>$r[title]</td>\n";
      if($r['dbtype']=='nodb' || in_array($kid,$fskip)){
          echo "<td class='tc c999' colspan=5>-- ".lang('flow.ei_skipfld')." --</td>\n";
      }else{
           echo "<td class='tc'>$r[top]</td>\n";
           echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
            echo "<td class='tc'>".($r['dbtype']=='nodb' ? '---' : glbHtml::null_cell($r['etab']))."</td>\n";
            echo "<td class='tl'>".$types[$r['type']]." $plugstr</td>\n";
          echo "<td class='tc'><a href='?file=$file&mod=$mod&view=fset&job=$job&kid=$r[kid]' onclick='return winOpen(this,\"".lang('flow.ei_fldcfg')."\")'>".lang('flow.title_cfg')."</a></td>\n";
      }
      echo "<td class='tc'>$dbstr</td>\n";
      echo "<td class='tr'>".glbHtml::null_cell($r['vmax'],'')." | ".glbHtml::null_cell($r['dblen'],'')."</td>\n";
      echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
      echo "</tr>"; 
    }}  

    if($_groups[$mod]['pid']=='docs'){
        $dop->fext['catid'] = array('title'=>lang('flow.title_cata'),'dbtype'=>"varchar (12)");
    }elseif($_groups[$mod]['pid']=='users'){
        $dop->fext['grade'] = array('title'=>lang('flow.title_grade'),'dbtype'=>"varchar (12)");
    } 
    echo "<tr style='border:1px solid #00CCFF'><td class='tc' colspan=10></td></tr>";
    foreach($dop->fext as $fk=>$fv){
      if(!in_array($fk,array('atime','catid','grade'))) continue;
      echo "<tr>\n";
      echo "<td class='tc'>$fk</td>\n";
      echo "<td class='tl'>$fv[title]</td>\n";
      echo "<td class='tc c999'>---</td>\n";
      echo "<td class='tc'>Y</td>\n";
      echo "<td class='tc c666' colspan=2>-- ".lang('flow.ei_addfld')." --</td>\n";
      echo "<td class='tc'><a href='?file=$file&mod=$mod&view=fset&job=$job&kid=$fk' onclick='return winOpen(this,\"".lang('flow.title_cfg')."\")'>".lang('flow.title_cfg')."</a></td>\n";
      echo "<td class='tc'>$fv[dbtype]</td>\n";
      echo "<td class='tr'>--</td>\n";
      echo "<td class='tl'><input type='text' value='$note' class='txt w150 disc' disabled='disabled' /></td>\n";
      echo "</tr>"; 
    }
    glbHtml::fmt_end(array("mod|$mod"));
    
}elseif($view=='fdefs'){
    echo 'fdefs - no use.';
}