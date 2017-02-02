<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$cfg = array(
    'sofields'=>array('title','ufrom','uto','detail','stat'),
    'soorders'=>basLang::ucfg('cfgbase.ord_com2'),
    //'soarea'=>array('amount','金额'),
);
$dop = new dopExtra('plus_emsend',$cfg); 

// 删除操作
if(!empty($bsend)){
    require(dirname(dirname(__FILE__)).'/binc/act_ops.php');
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($view=='vcfgs' ? 'vcfgs' : 'vlist','mail');
$dop->sobar("$links$umsg",40,array());

if($view=='vcfgs'){
    
    $cfgs = read('mail','ex');
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    if(empty($cfgs)){
        echo "\n<tr><td class='tc w180'>".lang('flow.cfg_tips').": </td>\n<td>
            ".lang('flow.cfg_nocfg').": <br>".lang('flow.cfg_copy').": {code}/cfgs/excfg/ex_mail.php-demo ".lang('flow.cfg_to')." ex_mail.php; <br>".lang('flow.cfg_editip')."
        </td></tr>\n";
    }else{ 
        echo "\n<tr><td class='tc w150'>".lang('flow.cfg_nowcfg').":</td>\n<td>".lang('flow.cfg_nowfile').": {code}/cfgs/excfg/ex_mail.php，
        <a href='?file=admin/ediy&part=edit&dkey=cfgs&dsub=&efile=excfg/ex_mail.php' onclick=\"return winOpen(this,'".lang('flow.cfg_edit')."',780,560);\">".lang('flow.title_edit')."</a></td></tr>\n";
        foreach($cfgs as $key=>$v){
            echo "\n<tr><td class='tc'>{$key}: </td>\n<td>$v</td></tr>\n";
        }
    }
    glbHtml::fmt_end(array("mod|$mod"));
}else{
    // 清理操作
    if(!empty($bsend)&&$fs_do=='dnow'){
        $msg = $dop->opDelnow();
        basMsg::show($msg,'Redir',"?file=$file&mod=$mod&flag=v1");
    }  
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>title</th><th>ufrom</th><th>uto</th><th>api</th><th>stat</th><th>aip</th><th>".lang('flow.cfg_optime')."</th></tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          if(empty($idfirst)) $idfirst = $kid;
          echo "<tr>\n".$cv->Select($kid);
          echo "<td class='tc'>$r[title]</td>\n";
          echo "<td class='tc'>$r[ufrom]</td>\n";
          echo "<td class='tc'>$r[uto]</td>\n";
          echo "<td class='tc'>$r[api]</td>\n";
          echo "<td class='tc'>$r[stat]</td>\n";
          echo "<td class='tc'>$r[aip]</td>\n";
          echo "<td class='tc'>".date('Y-m-d H:i',$r['atime'])."</td>\n";
          echo "</tr>";
        }
        $dop->pgbar($idfirst,$idend);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));    
}

?>
