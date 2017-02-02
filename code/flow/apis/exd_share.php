<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');
$ocfgs = read('outdb','ex');

if(in_array($view,array('list','set'))){
    $lnkadd = admPFunc::fileNav($view,'exd_share');
    $links = admPFunc::fileNav($file,'exd_psyn');
    glbHtml::tab_bar("[".lang('flow.sh_title')."]<span class='span ph5'>#</span>$lnkadd","$links",50);
}
//echo $mod;

if($view=='set'){
    
    $vbak = $view;
    $view = 'set_a2';
    require(dirname(dirname(__FILE__)).'/binc/exd_inc1.php');
    $view = $vbak;

}elseif($view=='list'){

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>Key</th><th>".lang('flow.title_name')."</th><th>".lang('flow.sh_json')."</th><th>".lang('flow.sh_tpl')."</th><th class='wp15'>".lang('flow.title_note')."</th>\n";
    $gma = array('docs','users','coms'); $gmold = '';
    foreach($gma as $gm){ 
    foreach($_groups as $mod=>$gv){
      $kid = "$mod"; if($gv['pid']!==$gm) continue;
      $mcfg = read($mod); 
      if($gmold!=$gv['pid']){
          echo "<tr><td class='tc fB' colspan='3'>{$_groups[$gv['pid']]['title']}</td></tr>";
      }
      echo "<tr>\n".$cv->Select($kid);
      echo "<td class='tc'>$kid</td>\n";
      echo "<td class='tc'>$gv[title]</td>\n";
      echo $cv->Url(lang('flow.sh_json'),1,"?file=$file&view=json&mod=$mod","blank");
      echo $cv->Url(lang('flow.sh_tpl'),1,"?file=$file&view=tpl&mod=$mod","blank");
      echo "<td class='tl'><input type='text' value='".str_replace(array("\n","\r",";;"),array(";",";",";"),@$mcfg['cfgs'])."' class='txt w300' /></td>\n";
      echo "</tr>";
      $gmold = $gv['pid']; 
    }}
    echo "<tr>\n";
    echo "<td class='tc'><input name='fs_act' type='checkbox' class='rdcb' onClick='fmSelAll(this)' /></td>\n";
    echo "<td class='tr' colspan='6'><span class='cF00 left'>$msg</span>".lang('flow.fl_opbatch').": (null) &nbsp; </td>\n";
    echo "</tr>";
    glbHtml::fmt_end(array("mod|$mod"));

}elseif(in_array($view,array('json','tpl'))){
    
    $msg1 = $view=='json' ? lang('flow.sh_tip1') : lang('flow.sh_tip2');
    if(in_array($_groups[$mod]['pid'],array('docs','users'))){
        $dop = new dopBase(read($mod)); 
        $ops = $dop->fmType('stype',150); $ops = str_replace(array("fm[stype]","reg='"),array("stype","'"),$ops); //
        $s_type = ($_groups[$mod]['pid']=='docs' ? lang('flow.title_cata') : lang('flow.title_grade')).": $ops";
    }else{
        $s_type = lang('flow.sh_cg').": ---";
    }
    $stype = req('stype');
    $limit = req('limit',10);
    $order = req('order',substr($_groups[$mod]['pid'],0,1)."id:".($view=='json' ? 'ASC' : 'DESC'));
    $offset = req('offset');
    $cut = req('cut','title,compony');
    $clen = req('clen',48);
    $ret = req('ret','html');
    $tpl = req('tpl','','');
    $tpldef = $tpl ? $tpl : "<li><a href='{rhome}/run/chn.php?$mod.{kid}'>{title}</a></li>";
    $dis = $view=='json' ? 'disabled' : '';

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist'); //
        echo "\n<tr><th class='wp40'>".lang('flow.sh_whr')."</th>\n<th class='wp60'>".lang('flow.sh_show')."</th></tr>\n";
        echo "\n<tr><td>".lang('flow.sh_mod').": $mod {$_groups[$mod]['title']}</td>\n
                    <td>".lang('flow.sh_mode').": {$view} $msg1</td></tr>\n";
        echo "\n<tr><td>$s_type</td>\n
                    <td>cut: <input name='cut' type='text' value='$cut' class='txt w120' $dis/> &nbsp; clen: <input name='clen' type='text' value='$clen' class='txt w40' $dis/></td></tr>\n";
        echo "\n<tr><td>limit: <input name='limit' type='text' value='$limit' class='txt w150' /></td>\n
                    <td>ret: <input name='ret' type='text' value='$ret' class='txt w120'  $dis/>eg: html,js</td></tr>\n";
        echo "\n<tr><td>order: <input name='order' type='text' value='$order' class='txt w150' /></td>\n
                    <td>tpl: <input name='tpl' type='text' value=\"$tpldef\" class='txt w320' $dis/></td></tr>\n";
        echo "\n<tr><td>offset: <input name='offset' type='text' value='$offset' class='txt w150' tip='eg: 2016-2e-1234' /></td>\n
                    <td class='tc'><input name='bsend' class='btn' type='submit' value=".lang('flow.dops_send')." /></td></tr>\n";
    if(!empty($bsend)){    
        // mod,stype,limit(1-100),order(did:DESC),offset,tpl,cut,clen,ret(html/js),
        $entpl = comParse::urlBase64($tpl); 
        $usign = exdBase::getJSign();
        $ptpl = $view=='json' ? '' : "&cut=$cut&clen=$clen&ret=$ret&tpl=".($tpl ? $entpl : '');
        $apiurl = $_cbase['run']['roots']."/plus/ajax/exdb.php?mod=$mod&act=".($view=='json' ? 'pull' : 'show')."";
        $apiurl .= "&stype=$stype&limit=$limit&order=$order&offset=$offset".$ptpl."&".$usign;
        echo "\n<tr><td colspan=2 style='border-top:5px solid #99F;'>".lang('flow.sh_url').": <input type='text' value=\"$apiurl\" class='txt w700' /></td></tr>\n";    
        if($view=='tpl') echo "\n<tr><td colspan=2>".lang('flow.sh_ctpl').": <input type='text' value=\"comParse::urlBase64(&quot;$tpl&quot;) = [$entpl]\" class='txt w700' /></td></tr>\n";    
        echo "\n<tr><td colspan=2>".lang('flow.sh_surl').": <input type='text' value=\"$usign\" class='txt w700' /></td></tr>\n";    
        echo "\n<tr><td colspan=2 style='border-top:5px solid #99F;'>".lang('flow.sh_res').": <iframe src='$apiurl' style='width:100%; height:640px; overflow-y:scroll; overflow-x:hidden;' frameBorder=0></iframe></td></tr>\n";    
    }

    glbHtml::fmt_end(); //array("xxx|xxx")
    
}else{
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    
    glbHtml::fmt_end(array("mod|$mod"));
        
}

?>
