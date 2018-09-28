<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$part = req('part','slogs'); //logs,charge

$cfg = array(
    'sofields'=>($part=='charge' ? array('uto','amount','note') : array('msg','tel','res','api')),
    'soorders'=>basLang::ucfg('cfgbase.ord_smslog'),
    'soarea'=>array('amount',lang('flow.sms_count')),
);
$dop = new dopExtra(($part=='charge' ? 'plus_smcharge' : 'plus_smsend'),$cfg); 

// 删除操作
if(!empty($bsend)){
    require dirname(dirname(__FILE__)).'/binc/act_ops.php';
} 

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav($part,'sms');
$dop->sobar("$links$umsg",40,array());

// 清理操作
if(!empty($bsend)&&$fs_do=='dnow'){
    $msg = $dop->opDelnow();
    basMsg::show($msg,'Redir',"?mkv=$mkv&mod=$mod&part=$part&flag=v1");
}
    
glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');

echo "<th>".lang('flow.title_select')."</th>";
if($part=='charge'){
    echo "<th>uto</th><th>amount</th><th>note</th><th>time</th><th>ip</th><th>op</th>";
}else{
    echo "<th>msg</th><th>api - keyid / tel</th><th>res@amount/time</th>";
}
echo "</tr>\n";
$idfirst = ''; $idend = '';
if($rs=$dop->getRecs()){ 
    foreach($rs as $r){ 
      $kid = $idend = $r['kid'];
      if(empty($idfirst)) $idfirst = $kid;
      echo "<tr>\n".$cv->Select($kid);
      if($part=='charge'){
          echo "<td class='tl'>$r[uto]</td>\n";
          echo "<td class='tc'>$r[amount]</td>\n";
          echo "<td class='tc'><input type='text' value='$r[note]' class='txt w240'/></td>\n";
          echo "<td class='tc'>".date('m-d H:i:s',$r['atime'])."</td>\n";
          echo "<td class='tc'><input type='text' value='$r[aip]' class='txt w120'/></td>\n";
          echo "<td class='tc'>$r[auser]</td>\n";
      }else{
          $msg = basSql::fmtShow($r['msg']);
          $ext = "$r[api] # $r[kid]";
          echo "<td class='tl'><textarea cols=60 rows=3>$msg</textarea></td>\n";
          echo "<td class='tc'>$ext<br><textarea cols=40 rows=2>$r[tel]</textarea></td>\n";
          echo "<td class='tc'>$r[res] @ $r[amount]<br>".date('m-d H:i:s',$r['atime'])."</td>\n";  
      }
      echo "</tr>";
    }
    $dop->pgbar($idfirst,$idend);
}else{
    echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
}    

glbHtml::fmt_end(array("mod|$mod","part|$part"));

?>
