<?php
if(file_exists(dirname(__FILE__).'/start-360.php')){
  include(dirname(__FILE__).'/start-360.php');
  die();
}
require dirname(dirname(__FILE__)).'/adbug/_config.php';

$qstr = @$_SERVER['QUERY_STRING'];
$proot = devRun::prootGet(); 
$fmsg = array();
if($qstr=='FixProot' && $proot!=PATH_PROJ){
  $fixres = devRun::prootFix($proot);
  $qstr = $fixres ? 'FixPrOkey' : 'FixPrError';
  $fmsg = devRun::prootMsg($proot, $qstr);
}else{
  //$qstr = 'start';
}
if(!in_array($qstr,array('FixPrError','FixPrOkey')) && $proot!=PATH_PROJ){ 
  header("Location:start.php?FixProot"); 
}

$umsg = devRun::startCheck(); 
if(!empty($fmsg)){
  $pmsg['fpath'] = $fmsg;
  $umsg = $pmsg + $umsg; 
}

$vcfg = vopTpls::etr1('tpl');
unset($vcfg['_pub']);

glbHtml::page($_cbase['sys_name'].' - '.lang('tools.start_title'),1);
eimp('/_pub/a_jscss/cinfo.css');
eimp('/_pub/jslib/jsbase.js');
?>
<base target="_blank"/>
</head><body class="divOuter">

<?php 

if(!empty($umsg)){ 
  echo "<table style='max-width:460px;margin:10px auto; '>\n";
  foreach($umsg as $k=>$v){ 
?>
  <tr><td class="tip"><?php echo $v['msg']; ?></td>
  <td class="tip"><?php echo $v['tip']; ?></td></tr>
<?php 
  }
  echo "</table>\n";
  die();
}
echo glbHtml::ieLow_html();
basLang::shead($_cbase['sys_name'].' - '.lang('tools.start_title').' - v'.$_cbase['sys']['ver']);
?>

<?php
if(!empty($fmsg)){ die('</body></html>'); }
$mapurl = PATH_ROOT.'/plus/map/index.php?api=';
$tolurl = PATH_PROJ.'/dev.php';
?>

  <p class="title"><?php lang('tools.bug_tools',0); ?></p>
  <table width="100%" border="1" class="tblist">
  <tr class="tc">
    <td><a href="binfo.php?phpinfo1" target="_self">phpinfo</a></td>
    <td colspan="2" class="tip">
   <a href="../setup/">Setup - <?php lang('tools.start_setup',0) ?></a>
    </td>
    <td><a href="search.php">Search</a></td>
  </tr>
  <?php tadbugNave(1); ?>
  <tr class="tc">
    <td><a href="cstudy.php"><?php lang('tools.start_dtstudy',0) ?></a></td>
    <td><a href='cyahei.php'><?php lang('tools.start_dtyahei',0) ?></a></td>
    <td><a href="../exdiy/nomuma.php"><?php lang('tools.start_tmuma',0) ?></a></td>
    <td><a href="<?php echo PATH_ROOT; ?>/plus/api/color.php">Color</a></td>
  </tr> 
  <tr class="tc">
    <td><a href="binfo.php?login"><?php lang('tools.start_login',0) ?></a></td>
    <td><a href="../exdiy/rplan.php">rplan</a>-<a href="../exdiy/build.php">build</a></td>
    <td><a href="../exdiy/index.php">tools</a>-<a href="../exdiy/derun.php">derun</a></td>
    <td><a href="dbadm.php"><?php lang('tools.start_dbadmin',0) ?></a></td>
  </tr> 
  <tr class="tc">
    <td colspan='2'>
    <a href="<?php echo $mapurl; ?>baidu">baidu<?php lang('tools.start_map',0) ?></a> -
    <a href="<?php echo $mapurl; ?>baidu&act=pick&point=113.756963,23.02224,17">pick</a> -
    <a href="<?php echo $mapurl; ?>baidu&point=113.537,26.315,16">bamu</a> -
    <a href="<?php echo $mapurl; ?>baidu&point=123.480,25.750,16">diao</a>
    </td>
    <td colspan='2'>
    <a href="<?php echo $mapurl; ?>google">google<?php lang('tools.start_map',0) ?></a> -
    <a href="<?php echo $mapurl; ?>google&act=pick&point=113.750633,23.016454,16">pick</a> -
    <a href="<?php echo $mapurl; ?>google&point=113.531,26.309,16">bamu</a> -
    <a href="<?php echo $mapurl; ?>google&point=123.47,25.745,16">diao</a>
    </td>
  </tr>
  </table>

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
$scfg = array('min','cerulean','flatly','superhero'); // ,'(ull)'
?>

  <p class="title">CMS<?php lang('tools.start_cmsentry',0) ?></p>
  <table width="100%" border="1" class="tblist">
  <tr class="tc">
   <?php foreach($vcfg as $k=>$v){ $ti++; if($k=='umc'){$v[1].='?login';} $url=($k=='---')?'../../../?':PATH_PROJ.@$v[1]; ?>
    <td width="25%"><a href="<?php echo $url; ?>"><?php echo !empty($v[0]) ? basLang::pick(0,$v[0]) : ''; ?></a></td>
    <?php if(($ti)%$col==0 && $ti<count($vcfg)){ echo "</tr><tr class='tc'>\n"; }  } ?>
  </tr>
  <tr class="tc" style="border-top:3px solid #A6CAF0;">
    <td><a href="<?php echo $tolurl; ?>?tools-seal">PHP印章</a></td>
    <td><a href="<?php echo $tolurl; ?>?tools-qrcode">二维码</a></td>
    <td><a href="<?php echo $tolurl; ?>?tools-vimg">图片码</a></td>
    <td><a href="<?php echo $tolurl; ?>?tools-cnconv">拼音</a></td>
  </tr> 
  <tr class="tc">
    <td><a href="<?php echo $_cbase['server']['txcode']; ?>/">贴心口袋</a></td>
    <td><a href="http://txjia.com/peace/wenode.htm">Node微框架</a></td>
    <td><a href="http://txmao.txjia.com/chn/topic.2018-3j-g9b1.htm">微爬(Wepy)</a></td>
    <td><a href="http://txjia.com/peace/txbox.htm">Java盒子</a></td>
  </tr>
  <?php if($qstr=='skin'){ ?>
  <tr class="tc">
    <td>Skin(s)</td>
    <td>-</td>
    <td>-</td>
    <td><a href="#">--</a></td>
  </tr>
  <tr class="tc">
   <?php foreach($scfg as $k){ ?>
    <td width="25%"><a href="<?php echo PATH_ROOT; ?>/plus/ajax/redir.php?skin:<?php echo $k; ?>" target='_self'><?php echo $k; ?></a></td>
   <?php } ?>
  </tr>
  <?php } ?>
  </table>

  <table width="100%" border="1" class="tblist">
  <tr class="tc">
    <td class="tip tc">Hi, I am <?php echo "<a href='{$_cbase['server']['txmao']}'>{$_cbase['sys_name']}</a> @ <strong>{$_SERVER['HTTP_HOST']}</strong>";?></td>
  </tr> 
  <tr>
    <td class="tc"><?php 
   $rtime = 1000*(microtime(1)-$_cbase['run']['timer']); 
   $rinfo = basDebug::runInfo();
   echo "<textarea style='width:96%; height:360px; overflow:visible;'>"; 
   echo "$rtime\n$rinfo\n"; print_r($_cbase); 
   echo "</textarea>"; 
   ?></td>
  </tr> 
  </table>

<?php
$fres = var_export($proot!=PATH_PROJ,1);
$flag = $proot!=PATH_PROJ ? "?FixProot" : 'isOK';
echo "<!--\n($proot)\n(".PATH_PROJ.")\n($fres:$flag)\n-->";
?>

</body></html>

