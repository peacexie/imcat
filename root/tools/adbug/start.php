<?php
require(dirname(dirname(__FILE__)).'/adbug/_config.php');

$proot = devRun::prootGet(); 
$fmsg = array();
if($qstr=='FixProot' && $proot!=PATH_PROJ){
  $fixres = devRun::prootFix($proot);
  $qstr = $fixres ? 'FixPrOkey' : 'FixPrError';
  $fmsg = devRun::prootMsg($proot, $qstr);
}else{
  $qstr = 'start';
}
if(!in_array($qstr,array('FixPrError','FixPrOkey')) && $proot!=PATH_PROJ){ 
  header("Location:../adbug/start.php?FixProot"); 
}

$umsg = devRun::startCheck(); 
if(!empty($fmsg)){
  $pmsg['fpath'] = $fmsg;
  $umsg = $pmsg + $umsg; 
}

$vcfg = vopTpls::etr1('tpl');

?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title><?php echo $_cbase['sys_name'].' - '.lang('tools.start_title'); ?></title>
<meta name='robots' content='noindex, nofollow'>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel='stylesheet' type='text/css' href='<?php echo PATH_SKIN; ?>/_pub/a_jscss/stpub.css'/>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_SKIN; ?>/adm/b_jscss/comm.css'/>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_ROOT; ?>/tools/adbug/style.css'/>
<base target="_blank"/>
</head><body>
<!--[if lt IE 9]>
<h4 class='LowIE'>IE浏览器过低！建议你更换浏览器，如Chrome,Firefox,IE9+！</h4>
<![endif]-->
<dd class="langbar">
  <?php echo basLang::links("<a href='{url}' class='c666' target='_self'>{title}</a>"); ?>
</dd>
<div style="max-width:720px;margin:10px auto;">
  <table width="100%" border="1" class="tblist">
  <?php if(!empty($umsg)){ foreach($umsg as $k=>$v){ ?>
  <tr class="tc">
    <td class="tip"><?php echo $v['msg']; ?></td>
    <td class="tip"><h4><?php echo $v['tip']; ?></h4></td>
  </tr>
  <?php } } ?>
  <tr class="tc">
    <td>
    <h4><?php echo $_cbase['sys_name'].' - '.lang('tools.start_title'); ?> </h4>
    </td>
    <td width="25%" class="tc"><p class="txcode_logopub txcode_logostart"></p></td>
  </tr> 
  </table>
</div>

<?php
if(!empty($fmsg)){ die('</body></html>'); }
$mapurl = PATH_ROOT.'/plus/map/index.php?api=';
?>
<div>
  <p><?php lang('tools.bug_tools',0); ?></p>
  <table width="100%" border="1" class="tblist">
  <tr class="tc">
    <td><a href="binfo.php?phpinfo1" target="_self">phpinfo</a></td>
    <td colspan="2" class="tip">
   <a href="../setup/">Setup - <?php lang('tools.start_setup',0) ?></a>
    </td>
    <td><a href="search.php">search</a></td>
  </tr>
  <?php tadbugNave(1); ?>
  <tr class="tc">
    <td><a href="cstudy.php">study</a>-<a href='cyahei.php'>yahei</a></td>
    <td><a href="<?php echo PATH_ROOT; ?>/plus/api/color.php">Color Pick</a></td>
    <td><a href="<?php echo $mapurl; ?>baidu">baidu<?php lang('tools.start_map',0) ?></a>-<a href="<?php echo $mapurl; ?>baidu&act=pick&point=113.756963,23.02224,17">pick</a></td>
    <td><a href="<?php echo $mapurl; ?>google">google<?php lang('tools.start_map',0) ?></a>-<a href="<?php echo $mapurl; ?>google&act=pick&point=113.750633,23.016454,16">pick</a></a></td>
  </tr> 
  <tr class="tc">
    <td><a href="binfo.php?login"><?php lang('tools.start_login',0) ?></a></td>
    <td><a href="../exdiy/rplan.php">rplan</a>-<a href="../exdiy/build.php">build</a></td>
    <td><a href="../exdiy/index.php">tools</a>-<a href="../exdiy/derun.php">derun</a></td>
    <td><a href="dbadm.php"><?php lang('tools.start_dbadmin',0) ?></a></td>
  </tr> 
  </table>
</div>

<?php
$col = 4; $ti = 0; 
$tm = count($vcfg)%$col; 
if($tm>0){ 
  $tm = $col - $tm;
}else{ 
  $tm = $col;
}
for($i=1;$i<$tm;$i++){
  $vcfg[$i] = array('','-');
}
$vcfg['---'] = array('HOME','');
?>
<div>
  <p>CMS<?php lang('tools.start_cmsentry',0) ?></p>
  <table width="100%" border="1" class="tblist">
  <tr class="tc">
   <?php foreach($vcfg as $k=>$v){ $ti++; $url=($k=='---')?'../../':PATH_PROJ.@$v[1]; ?>
    <td width="25%"><a href="<?php echo $url; ?>"><?php echo !empty($v[0]) ? "($k)".basLang::pick(0,$v[0]) : ''; ?></a></td>
    <?php if(($ti)%$col==0 && $ti<count($vcfg)){ echo "</tr><tr class='tc'>\n"; }  } ?>
  </tr>
  <tr class="tc">
    <td><a href="<?php echo $_cbase['server']['txcode']; ?>/">yscode@txjia.com</a></td>
    <td><a href="http://txmao.txjia.com/">txmao@txjia.com</a></td>
    <td><a href="http://txjia.com/peace/txbox.htm">txbox@txjia.com</a></td>
    <td><a href="http://txjia.com/peace/txasp.htm">txasp@txjia.com</a></td>
  </tr> 
  <tr>
    <td colspan="4" class="tl"><?php 
   $rtime = 1000*(microtime(1)-$_cbase['run']['timer']); 
   $rinfo = basDebug::runInfo();
   echo "<pre>"; echo "$rtime : $rinfo\n"; print_r($_cbase); echo "</pre>"; 
   ?></td>
  </tr> 
  <tr>
    <td colspan="4" class="tip tc">Hi, I am <?php echo "<a href='{$_cbase['server']['txmao']}'>{$_cbase['sys_name']}</a>! I run @ <strong>{$_SERVER['HTTP_HOST']}</strong>";?></td>
  </tr> 
  </table>
</div>

</body></html>

