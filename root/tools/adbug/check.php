<?php 
require dirname(__FILE__).'/_config.php'; 

$act = @$_GET['act']; $act || $act = 'check'; 
$inptype = @$_GET['inptype']; 
$inpval = @$_GET['inpval'];
$rem_url = empty($_GET['rem_url']) ? 'http://www.baidu.com/' : $_GET['rem_url'];
$rem_type = empty($_GET['rem_type']) ? '' : $_GET['rem_type'];

$cfg = array(
  'check'=>lang('tools.chk_envbaisc'),
  'memory'=>'Memory',
  'upload'=>'Upload',
  'bomcheck'=>'BOMCheck',
  'remote'=>'Remote',
);
$title = isset($cfg[$act]) ? $cfg[$act] : '？'.$act;

if($act=='image'){
  devRun::runGdlib();
  die(); // jpeg
}elseif($rem_type && $rem_url){
  $cfg = array(
    'curl_init'=>'1',
    'fsockopen'=>'2',
    'file_get_contents'=>'3',
  );
  comHttp::setWay($cfg[$rem_type]);
  $text = comHttp::doGet($rem_url);
  echo dfmtRemote($text,"$rem_type : $rem_url");
  die(); 
}

glbHtml::page(lang('tools.chk_envcheck')."-$title");
eimp('/_pub/a_jscss/cinfo.css');
eimp('/_pub/jslib/jsbase.js');

$iniPath = get_cfg_var('cfg_file_path');
$iniInfo = $iniPath ? "PHP configuration is using THIS file: [$iniPath]" : "WARNING: No configuration file (php.ini) used by PHP!";

?>
<style type="text/css">
td { white-space:nowrap; }
input.r { width:96%; }
</style>
</head><body class="divOuter">

<?php basLang::shead(lang('tools.chk_envcheck')); ?>

  <div class="pa5"></div>
  <table border="1" class="tblist">
  <?php tadbugNave(); ?>
  <tr class="tc">
    <td colspan="5">
    <a href="?">Basic</a> | <a 
  href='?act=define'>Define</a> | <a 
  href='?act=image' target="_blank">Image</a> | <a 
  href='?act=upload'>Upload</a> | <a 
  href='?act=remote'>Remote</a>
    </td>
  </tr>
  </table>

<?php 
if($act=='check'){ 
?>

<p class="title"><?php lang('tools.chk_envcheck',0); ?> --- <?php lang('tools.chk_envbaisc',0); ?></p>

  <table width="100%" border="1" class="tblist">
  <tr>
    <th class="tc"><?php lang('tools.chk_envname',0); ?></th>
    <th class="tc"><?php lang('tools.chk_envres',0); ?></th>
    <th><?php lang('tools.chk_envstat',0); ?></th>
    <th><?php lang('tools.chk_envrem',0); ?></th>
  </tr>
 <?php 
  $a = array('upfile','reset'); 
  foreach($a as $k){ $key = "can_$k";
  ?>
  <tr>
    <td class="tc"><?php echo $key; ?></td>
    <td class="tc"><span style="color: #<?php echo $$key ? "ff0000" : "008000"; ?>; font-weight : bold;"><?php echo $$key ? lang('tools.chk_risk') : lang('tools.chk_safe'); ?></span></td>
    <td><?php echo $key; ?>=<?php echo $$key; ?> (@root/cfgs/boot/cfg_adbug.php)</td>
    <td><?php lang('tools.chk_set0',0); ?></td>
  </tr>
  <?php }?>
  
  <?php 
  $a = array('verPHP','verGdlib','runRemote'); 
  foreach($a as $k){ $re = devRun::$k();
  ?>
  <tr>
    <td class="tc"><?php echo $re['title']; ?></td>
    <td class="tc"><?php echo $re['res']; ?></td>
    <td><?php echo $re['info']; ?></td>
    <td><?php echo $re['tip'].(empty($re['demo']) ? '' : " &nbsp; -=&gt;<a href='{$re['demo']}'>".lang('tools.chk_demo')."</a>"); ?></td>
  </tr>
  <?php }?>

 <?php 
  $__a = array(array('Mail','mail'),array('FTP','ftp_connect')); //array('ob_gzhandler'),
  foreach($__a as $__a1){ $__f1 = isset($__a1[1]) ? $__a1[1] : $__a1[0];
  ?>
  <tr>
    <td class="tc"><?php echo $__a1[0]?></td>
    <td class="tc"><?php echo function_exists($__f1) ? FLAGYES : FLAGNO?></td>
    <td>-</td>
    <td>-</td>
  </tr>
  <?php }?>
  <form id="fmb" name="fmb2" method="get" action="?">
  <tr>
    <td class="tc"><?php lang('tools.chk_usptest',0); ?></td>
    <td class="tc">-</td>
    <td colspan="2">
  <select name="inptype" onChange="setInpval(this)" style="width:200px;">
    <option value="">---<?php lang('tools.chk_pick1',0); ?>---</option>
    <option value="memory"><?php lang('tools.chk_cmemory',0); ?></option>
    <option value="timeout">run: 60s --- Long-Task</option>
    <option value="funcs"><?php lang('tools.chk_cfunc',0); ?></option>
  </select>
  <input name="inpval" type="text" id="inpval" value="<?php echo $inpval; ?>" style="width:100px;"/>
  <input type="submit" name="submit" id="submit" value="Submit" class="btn" />   
<script>
function setInpval(e){
  var type = e.value,res = '';
  if(type=='memory')  res = 32;
  if(type=='timeout')  res = 60;
  if(type=='funcs')  res = 'phpinfo';
  document.getElementById('inpval').value = res;
}
</script>
    </td>
  </tr>
  <?php if($inptype && $inpval){ ?>
  <?php if($inptype=='memory'){ ?>
  <tr>
    <td class="tc">Memory Test</td>
    <td class="tc" colspan="3"><?php echo devRun::runMemory($inpval); ?></td>
  </tr>
  <?php } if($inptype=='timeout'){ ?>
  <tr>
    <td class="tc">Long-Task</td>
    <td class="tc" colspan="3"><?php echo devRun::runMTask($inpval); ?></td>
  </tr>
  <?php } if($inptype=='funcs'){ ?>
  <tr>
    <td class="tc"><?php echo $inpval; ?></td>
    <td class="tc" colspan="3"><?php echo fchkFuncs($inpval); ?></td>
  </tr>
  <?php } } ?>
  </form>
  <tr>
    <td class="tip" colspan="4"><?php echo $iniInfo; ?></td>
  </tr>
  </table>

<p class="title"><?php lang('tools.chk_envcheck',0); ?> --- <?php lang('tools.chk_sysdirs',0); ?></p>

  <table width="100%" border="1" class="tblist">
  <tr>
    <th class="tc"><?php lang('tools.chk_envname',0); ?></th>
    <th class="tc"><?php lang('tools.chk_envres',0); ?></th>
    <th width="30%">Dir</th>
    <th width="40%">Path</th>
  </tr>
  <?php 
  $cfg = devRun::runPath($k); $rea = '';
  foreach($cfg as $k=>$re){ $rea .= $re['res'];
  ?>
  <tr>
    <td class="tc">*_<?php echo $re['ukey']; ?></td>
    <td class="tc"><?php echo $re['res']; ?></td>
    <td><input class="r" value="<?php echo $re['dir']; ?>"></td>
    <td><input class="r" value="<?php echo $re['path']; ?>"></td>
  </tr> 
  <?php
  } if(strstr($rea,FLAGNO)){
  ?>
  <tr>
    <td class="tip" colspan="4"><?php lang('tools.chk_dirset',0); ?></td>
  </tr> 
  <?php } //} ?>
  </table>

<p class="title"><?php lang('tools.chk_envcheck',0); ?> --- <?php lang('tools.chk_dblink',0); ?></p>

  <table width="100%" border="1" class="tblist">
  <tr>
    <th class="tc"><?php lang('tools.chk_envname',0); ?></th>
    <th class="tc"><?php lang('tools.chk_envres',0); ?></th>
    <th width="70%"><?php lang('tools.chk_dbstatus',0); ?></th>
  </tr>
  
 <?php 
  $a3 = devRun::runMydb3(); $nocnt = 0;
  foreach($a3 as $k=>$re){ $nocnt += $re['res']==FLAGNO ? 1 : 0;
  ?>
  <tr>
    <td class="tc">[<?php echo $k; ?>] <?php lang('tools.chk_dbextra',0); ?></td>
    <td class="tc"><?php echo $re['res']; ?></td>
    <td><?php echo $re['info']; ?></td>
  </tr>
  <?php } if($nocnt){ ?>
  <tr>
    <td class="tip" colspan="4"><?php lang('tools.chk_dbset',0); ?></td>
  </tr> 
  <?php } ?>
  
  </table>

<?php 
}if($act=='define'){
?>

<div style="height:500px; overflow-y:scroll;">
<p class="title"><?php lang('tools.chk_envcheck',0); ?> --- define</p>
  <table width="100%" border="1" class="tblist">
  <?php 
  $df = get_defined_constants(true);
  foreach(array('user','Core','mhash','internal','pcre') as $gk){ 
  if(!isset($df[$gk])) continue;
  $gv = $df[$gk];
  ?>
  <tr>
    <th><?php echo $gk; ?></th>
    <th>Key</th>
    <th>Value</th>
  </tr>
  <?php 
  foreach($gv as $k=>$v){ 
  ?>
  <tr>
    <td class="tr" colspan="2"><?php echo $k; ?></td>
    <td class="tl"><?php echo $v; ?></td>
  </tr>
  <?php } } ?>
  </table>

<?php
}if($act=='upload'){ 
?>
  <form id="fmup" name="fmup" method="post" action="?act=upload" enctype="multipart/form-data">
  <p class="tc title"><?php lang('tools.cf_fupload',0); ?></p>
  <ul class="pa10">
 <?php
  if(!empty($_POST['upLoad'])){ // LINKOK,FAILED
    $uppath = $_POST['uppath'];
    foreach($_FILES as $f){
      if(empty($can_upfile)) die("Please SET [ \$can_upfile = '1' ]"); 
      if($f['name']){ 
          $fp = $uppath.$f['name']; 
          $r = move_uploaded_file($f['tmp_name'],$fp); 
          chmod($fp, 0755);//设定上传的文件的属性 
          echo "<li><i class='w2'>res:</i>$fp ".lang('tools.cf_upok')."</li>"; 
    } }
  }
  ?>
 <li><i class="w2"><?php lang('tools.cf_file',0); ?></i><input type="file" name="fileup1" id="fileup1"></li>
  <li>
  <i class="w2"><?php lang('tools.cf_path',0); ?></i><input name="uppath" type="text" id="uppath" value="./" size="18"/>
  <input type="submit" name="upLoad" id="upLoad" value="<?php lang('tools.cf_upbtn',0); ?>" />
  </li>
  </ul>
  </form>
<?php } ?>

<?php 
if($act=='remote'){ 
?>
  <form id="fmremote" name="fmremote" method="get" action="?" target="_blank">
  <p class="tc title"><?php lang('tools.cf_remote',0); ?></p>
  <ul class="pa10">
  <li>
    <i class="w2">Url: </i>
    <input name="rem_url" type="text" value="<?php echo $rem_url; ?>" style="width:360px;"> 
    <br>
    https://api.weixin.qq.com/cgi-bin/token<br>
    http://www.baidu.com/<br>
    https://www.baidu.com/  </li>
  <li><i class="w2"><?php lang('tools.cf_remext',0); ?></i>
    <select name="rem_type" style="width:360px;">
      <option value="curl_init">(curl:curl_init,curl_setopt)</option>
      <option value="file_get_contents">(file_get_contents)</option>
      <option value="fsockopen">(fsockopen)</option>
    </select>
  </li>
  <li><i class="w2"><?php lang('tools.cf_show',0); ?></i>
    <select name="rem_show" style="width:150px;">
      <option value="">---<?php lang('tools.cf_shmode',0); ?>---</option>
      <option value="script_style"><?php lang('tools.cf_tdef',0); ?></option>
      <option value="script_style_tags"><?php lang('tools.cf_text',0); ?></option>
      <option value="_null_"><?php lang('tools.cf_torg',0); ?></option>
    </select>
    <select name="rem_cset" style="width:150px;">
      <option value="">---(default:utf-8)---</option>
      <option value="gbk"><?php lang('tools.cf_gbk',0); ?></option>
      <option value="gb2312"><?php lang('tools.cf_gb2312',0); ?></option>
      <option value="big5"><?php lang('tools.cf_big5',0); ?></option>
    </select>
    <input type="submit" name="submit" id="submit" value="<?php lang('tools.cf_send',0); ?>" />
  </li>
  </ul>
  <input name="remote" type="hidden" value="1">
  <input name="act" type="hidden" value="remote">
  </form>
<?php } ?>

</body></html>
