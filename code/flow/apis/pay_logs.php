<?php
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$cfg = array(
    'sofields'=>array('ordid','apino','api','expar'),
    'soorders'=>basLang::ucfg('cfgbase.ord_pay'),
    'soarea'=>array('amount',lang('flow.pay_amount')),
);
$dop = new dopExtra('plus_paylog',$cfg); 

// 删除操作
if(!empty($bsend)){
    require dirname(dirname(__FILE__)).'/binc/act_ops.php';
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($view=='vcfgs' ? 'vcfgs' : 'vlist','pay');
$dop->sobar("$links$umsg",40,array());

if($view=='vcfgs'){

    $cfgs = exvOpay::getCfgs();
    $para = glbDBExt::getExtp('paymode_%');
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.pay_api')."</th><th>".lang('flow.pay_method')."</th><th>".lang('flow.pay_dir')."</th><th>".lang('flow.pay_note')."</th><th>".lang('flow.pay_cfg')."</th></tr>\n";
    foreach($cfgs as $key=>$r){ 
      $title = isset($para[$key]['title']) ? $para[$key]['title'] : '---';
      $detail = isset($para[$key]['detail']) ? $para[$key]['detail'] : '---';
      echo "<td class='tc'>$key</td>\n";
      echo "<td class='tc'>$r[method]</td>\n";
      echo "<td class='tc'>{root}/a3rd/$r[dir]</td>\n";
      echo "<td class='tc'>$title</td>\n";
      echo "<td class='tc'>$detail</td>\n";
      echo "</tr>";
    }
    echo "\n<tr><td colspan='5'>
    1. <a href='?mkv=apis-exp_order&pid=paymode_cn&frame=1' target='_blank'>".lang('flow.pay_pcfg')."</a> <br>
    2. ".lang('flow.pay_2ndoc')."
    
    </td></tr>\n";
    glbHtml::fmt_end(array("mod|$mod"));
    
}else{
    // 清理操作
    if(!empty($bsend)&&$fs_do=='dnow'){
        $msg = $dop->opDelnow();
        basMsg::show($msg,'Redir',"?mkv=$mkv&mod=$mod&flag=v1");
    }
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>ordid</th><th>apino</th><th>amount</th><th>api</th><th>stat</th><th>".lang('flow.cfg_optime')."</th></tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          if(empty($idfirst)) $idfirst = $kid;
          echo "<tr>\n".$cv->Select($kid);
          echo "<td class='tc'>$r[ordid]</td>\n";
          echo "<td class='tc'>$r[apino]</td>\n";
          echo "<td class='tc'>$r[amount]</td>\n";
          echo "<td class='tc'>$r[api]</td>\n";
          echo "<td class='tc'>$r[stat]</td>\n";
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
