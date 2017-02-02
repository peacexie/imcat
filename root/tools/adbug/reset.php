<?php 
require(dirname(__FILE__).'/_config.php'); 

$act = req('act','');
$part = req('part','');
$inptype = @$_GET['inptype']; 
$inpval = @$_GET['inpval'];

$exmod = basReq::arr('exmod'); 
$exmenu = basReq::arr('exmenu');
$exmod = empty($exmod) ? '' : implode(',',$exmod); 
$exmenu = empty($exmenu) ? '' : implode(',',$exmenu);

$orguser = 'adm_'.basKeyid::kidRand(0,3);
$orgpass = 'pass_'.basKeyid::kidRand(0,3);

glbHtml::page(lang('tools.rst_title'),1);
glbHtml::page('imp',array('css'=>'/tools/adbug/style.css'));
?>
</head><body>

<div>
  <table width="100%" border="1" class="tblist">
  <?php tadbugNave(); ?>
  </table>
</div>

<div>
<p class="tip"><?php lang('tools.rst_title',0); ?> / <?php lang('tools.rst_dbops',0); ?></p>

  <table width="100%" border="1" class="tblist">
  <tr>
    <td class="tc" colspan="4">(@code/cfgs/boot/cfg_adbug.php)
    &nbsp; [can_reset=<?php echo $can_reset; ?>] &nbsp; 
    <span style="color: #<?php echo $can_reset ? "ff0000" : "008000"; ?>; font-weight : bold;"><?php echo $can_reset ? lang('tools.chk_risk') : lang('tools.chk_safe'); ?></span>
    <?php lang('tools.chk_set0',0); ?>
    </td>
  </tr>
  <tr class="tc">
    <td width="25%"><?php lang('tools.rst_clear',0); ?></td>
    <td width="25%"><a href="?act=clrTmps"><?php lang('tools.rst_clrtmpfiles',0); ?></a></td>
    <td width="25%"><a href="?act=clrLogs"><?php lang('tools.rst_clrdblogs',0); ?></a></td>
    <td width="25%"><a href="?act=clrCTpl"><?php lang('tools.rst_clrtplcache',0); ?></a></td>
  </tr>   
  <tr class="tc">
    <td><?php lang('tools.rst_dbcheck',0); ?></td>
    <td><a href="?act=cdbPKey"><?php lang('tools.rst_dbnopk',0); ?></a></td>
    <td><a href="?act=cdbV255"><?php lang('tools.rst_dbvar255',0); ?></a></td>
    <td><a href="?act=cdbMKey"><?php lang('tools.rst_dbcindex',0); ?></a></td>
  </tr>
  <tr class="tc">
    <td><?php lang('tools.rst_export',0); ?></td>
    <td><a href="?act=expStru"><?php lang('tools.rst_expframe',0); ?></a></td>
    <td><a href="?act=expData"><?php lang('tools.rst_expall',0); ?></a></td>
    <td><a href="?act=expBack"><?php lang('tools.rst_exback',0); ?></a></td>
    </td>
  </tr> 

  <tr class="tc">
    <td><?php lang('tools.rst_reset',0); ?></td>
    <td><a href="?act=rstRndata"><?php lang('tools.rst_rstdata',0); ?></a> , 
    <a href="?act=rstTabcode"><?php lang('tools.rst_rstcomp',0); ?></a> , 
    <a href="?act=rstTabmini"><?php lang('tools.rst_rstmini',0); ?></a></td>
    <td><a href="?act=rstPub&part=main"><?php lang('tools.rst_rstpub',0); ?></a>:(<a  
    href="?act=rstPub&part=vary">vary</a>,<a  
    href="?act=rstPub&part=vimp">vimp</a>)</td>
    <td><a href="?act=rstCache"><?php lang('tools.rst_rstcache',0); ?></a></td>
  </tr>

  <form name="reidpw" action="?" method="get">
  <tr class="tc">
    <td><?php lang('tools.rst_rstidpw',0); ?></td>
    <td><input name="uname" value="<?php echo $orguser; ?>" type="text" onBlur="chkIdpass(this,0,3)" maxlength="12" class='w150'></td>
    <td><input name="upass" value="<?php echo $orgpass; ?>" type="text" onBlur="chkIdpass(this,1,6)" maxlength="18" class='w150'></td>
    <td><input name="" value="<?php lang('tools.rst_reset',0); ?>" class="btn" type="submit"><input name="act" type="hidden" value="rstIDPW"></td>
  </tr> 
  </form>

  <?php
    $arr = admPFunc::modList(array('docs','users','coms','type'),0); //,'advs'
    $muadm = read('muadm'); $muadm = $muadm['i'];
    $arm = array(); 
    foreach($muadm as $k=>$v){
      if($v['deep']>2) continue;
      if($v['deep']==1){
      $nkey = basArray::nextKey($muadm,$k);
      if($muadm[$nkey]['pid']==$k){
        $arm["^group^[$k]"] = "[$k]-$v[title]"; 
      }
      }else{
        $arm[$k] = "  &nbsp; [$k]$v[title]";
      }
    }
  ?>
  <form name="reins" action="?" method="get">
  <tr class="tc">
    <td>Export</td>
    <td><select id='exmod' name='exmod[]' class='msel' size="8" multiple="multiple"><?php echo basElm::setOption($arr,$exmod); ?></select></td>
    <td><select id='exmenu' name='exmenu[]' class='msel' size="8" multiple="multiple"><?php echo basElm::setOption($arm,$exmenu); ?></select></td>
    <td><input name="" value="Export" class="btn" type="submit"><input name="act" type="hidden" value="expMod"></td>
  </tr> 
  </form>

  </table>

</div>

<?php
$re = '-';

$names = array(

  'clrTmps'=>lang('tools.rst_clrtmpfiles'),
  'clrLogs'=>lang('tools.rst_clrdblogs'),
  'clrCTpl'=>lang('tools.rst_clrtplcache'),
  
  'rstCache'=>lang('tools.rst_rstcache'),
  'rstIDPW'=>lang('tools.rst_setidpw'),
  'rstPub'=>lang('tools.rst_rstpub'), 
  
  'cdbPKey'=>lang('tools.rst_dbnopk'),
  'cdbV255'=>lang('tools.rst_dbvar255'),
  'cdbMKey'=>lang('tools.rst_dbcindex'),
  
  'expStru'=>lang('tools.rst_expframe'),
  'expData'=>lang('tools.rst_export'),

  'expMod'=>'Export',

);

if(in_array($act,array('expData','expBack'))){
  $method = $act=='expBack' ?  "dataExpGroup" : 'dataExp';
  $dpre = $act=='expBack' ?  "gbak" : 'data';
  define('MINI_CUT_DETAIL',',dext_demo,dext_indoc,dext_keres,dext_news,dext_topic,');
  define('MINI_DEL_TABLES',',coms_cocar,coms_coitem,coms_corder,coms_inread,coms_nrem,coms_votep,');
  devData::$method("/dbexp/$dpre~",$part); 
}elseif(in_array($act,array('expStru'))){
  devData::struExp('/dbexp/');
}elseif($act=='rstIDPW'){
  if(empty($can_reset)){
    $exmsg = "<br>".lang('tools.rst_idpw_set',0)."<span class='cF03'>[ \$can_reset = '1' ]</span>";
    $_res = 'Error';
  }else{
    $uname = req('uname');
    $upass = req('upass');
    if($uname && $upass){ 
      devScan::rstIDPW($uname,$upass);
      $exmsg = "<br>".lang('tools.rst_idpw_id')."[<span class='cF03'>$uname</span>] ".lang('tools.rst_idpw_pw')."[<span class='cF03'>$upass</span>] ".lang('tools.rst_idpw_memory')."";
    }else{
      $exmsg = lang('tools.rst_idpw_error'); 
    }
  }
}elseif($act=='expMod'){
  $exmsg = "<br>Export file to: ".devSetup::expGroup($exmod,$exmenu).".(php|dbsql)";
//}elseif($act=='xxx'){
}elseif(in_array($act,array('cdbPKey','cdbV255','cdbMKey'))){
  $_res = devScan::cdbStrus($act);
}elseif(method_exists('devData',$act)){
  devData::$act(); 
}elseif(method_exists('devScan',$act)){
  devScan::$act(); 
}elseif($act){
  echo "Error:$act"; 
}
@$re = $act ? $names[$act].' ['."$act:".$part.'] - '.(empty($_res) ? lang('tools.rst_end') : $_res) : '';
$re .= '<br> @ '.date('Y-m-d H:i:s');

?>

<div>
  <p><?php lang('tools.rst_res',0); ?></p>
  <table width="100%" border="1" class="tblist">
  <tr id="res" class="tc">
    <td><?php echo $re.@$exmsg; ?></td>
  </tr> 
  <tr>
    <td class="tip"><?php lang('tools.rst_idpw_tip1',0); ?></td>
  </tr> 
  </table>
</div>

<script>
function chkIdpass(e,no,len){
  var orgcfgs = '<?php echo "$orguser,$orgpass"; ?>'.split(',');
  var simpass = ',<?php echo implode(',',read('simpass','sy')); ?>,';
  var tmp = $(e).val().replace(/\W/g, ""); //jsLog(tmp);
  if(simpass.indexOf(tmp)>0 || tmp.length<len){
    tmp = orgcfgs[no];
    alert('<?php lang('tools.rst_idpw_tip2',0); ?>'+simpass);
  }
  $(e).val(tmp);
}
</script>

<?php
glbHtml::page('end');
?>