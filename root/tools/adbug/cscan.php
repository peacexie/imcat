<?php 
require dirname(__FILE__).'/_config.php'; 
set_time_limit(300); 

$act = req('act','');
$part = $ntpl = req('part','');

glbHtml::page("Check/Scan");
eimp('/_pub/a_jscss/cinfo.css');
eimp('/_pub/jslib/jsbase.js');

$dcfgs = array(
 'curlDown' => 37712,
 'curlDasp' => 38283,
 'curlJava' => 38304,
 'curlCode' => 38305,
);

if($act=='scanInit'){
  $bcfg = array('code'=>DIR_CODE, 'root'=>DIR_ROOT, 'tpls'=>DIR_SKIN,   'a3rd'=>DIR_ROOT.'/a3rd'); @$burl = $bcfg[$part]; 
  $dcfg = array('code'=>'',     'root'=>'',     'tpls'=>'/code',  'a3rd'=>'/root');      @$durl = $dcfg[$part];
  if(!empty($burl)){
  $re = devBase::scanInit($burl,$_cbase['run']['rmain']."$durl/$part");
  echo "<base target='_blank'/>[$durl/$part]<pre>"; print_r($re); 
  echo "\n</pre>".basDebug::runInfo(); die();
  } 
}elseif($act=='scanMkvs'){
  $re = devBase::scanMkvs($part);
  echo "<base target='_blank'/>[$part]<pre>"; print_r($re); 
  echo "\n</pre>".basDebug::runInfo(); die();
}elseif($act=='scanCnchr'){ 
  $skip = array('ex_sfdata','safData','exvOcar','wmpError','sy_fdemo','sy_fsystem','sy_nava','derun'); 
  $re = devBase::scanCnchr(DIR_PROJ."/$part",array('yscode','utest'),$skip);
  echo "\n</pre>".basDebug::runInfo(); die();  
}elseif($act=='scanDblang'){ 
  $re = devBase::scanDblang();
  echo "\n</pre>".basDebug::runInfo(); die();  
}elseif(isset($dcfgs[$act])){  
  /*
  $did = $dcfgs[$act];
  $ref = array('_ref'=>"http://down.chinaz.com/soft/$did.htm");
  $url = "http://down.chinaz.com/download.asp?id=$did&dp=1&fid=$part&f=yes";
  $res = comHttp::curlCrawl($url,$ref);
  echo basStr::filForm($res)." [$part] "; die();
  */
}

?>
<style type="text/css">
li { border-bottom: 1px solid #CCC; padding: 5px; margin: 1px 0px; }
i { width: 150px; font-style: normal; display: inline-block; overflow: hidden; padding: 0px 3px; margin: 0px; }
</style>

</head><body class="divOuter">

<?php basLang::shead('Check/Scan'); ?>

  <div class="pa5"></div>
  <table width="100%" border="1" class="tblist">
  <?php tadbugNave(); ?>
  <tr class="tc">
    <td colspan="5">
    <a 
  href='?act=bomcheck'>BOMCheck</a> # <a 
  href='cstudy.php' target="_blank">study</a> | <a 
  href='cyahei.php' target="_blank">yahei</a> | <a 
  href='search.php' target="_blank">Search</a>
    </td>
  </tr>
  </table>


<?php if($act=='bomcheck'){ ?>

<?php 
$bomroot = empty($_GET['bomroot']) ? '../../../' : $_GET['bomroot'];
defined('DIR_PROJ') || define('DIR_PROJ',dirname(__FILE__));
if(empty($can_upfile)){
  if(!strstr($bomreal,DIR_PROJ)) $bomroot = '../../../';
}
$bompath = empty($_GET['bompath']) ? '' : $_GET['bompath'];
$bomfile = @$_GET['bomfile']; $bommsg = '';
$bomreal = str_replace("\\","/",realpath($bomroot)); 
?>

  <?php
  if(!empty($bomfile)){
    if(empty($can_upfile)) die("Please SET [ \$can_upfile = '1' ]"); 
    if(devRun::bomRemove($bomfile)) $bommsg = "<li class='rmok'>BOM Remove OK! ------ $bomfile</li>\n";
  }
  ?>
  <form id="fmbom" name="fmbom" method="get" action="?">
  <p class="tc title">BOM Check</p>
  <ul class='pa10'>
  <?php echo $bommsg; ?>
  <li>
    <i class="w2"><?php lang('tools.scan_root',0); ?></i><input name="bomroot" type="text" value="<?php echo $bomroot; ?>" size="36"> 
    <input type="submit" value="<?php lang('tools.scan_set',0); ?>">
    <br>
    <span title="<?php lang('tools.scan_tip1',0); ?>"><?php lang('tools.scan_tip2',0); ?></span> &nbsp; 
   </li>
  <li>
  <i class="w2"><?php lang('tools.scan_dirs',0); ?></i>
  <?php 
  $handle = opendir($bomroot);
  while($file=readdir($handle)){
    if(in_array($file,array('.','..','.svn',))) continue;
    if(is_dir("$bomroot/$file")){
      echo " : <a href='?act=bomcheck&bomroot=$bomroot&bompath=$file'>$file</a>\n";
    }
  }
  closedir($handle);
  ?>
  </li>
  </ul>
  <input name="act" type="hidden" value="bomcheck">
  </form>
  <?php
  if($bompath){
    devRun::bomScan($bomreal,$bompath);
  }
  ?>


<?php }else{ ?>

<p class="title">Check/Scan</p>

  <table width="100%" border="1" class="tblist">
  <tr>
    <td class="tc">lang,down</td>
    <td class="tc" colspan="3">
     <!--# <a href='?act=openDowns&part=1'>openDowns</a>-->
     # <a href='?act=scanDblang&part='>scanDblang</a>
     # <a href='cbaidu.php'>scanBaidu</a>
     #
    </td>
  </tr> 
  <tr>
    <td class="tc">Open[hlist]Entry</td>
    <td class="tc" colspan="3">
     # <a href='?act=openLinks&part=umc'>umc</a>
     | <a href='?act=openLinks&part=mob'>mob</a>
     | <a href='?act=openLinks&part=chn'>chn</a>
     | <a href='?act=openLinks&part=dev'>dev</a>
     | <a href='?act=openLinks&part=doc'>doc</a>
     #
    </td>
  </tr> 
  <tr>
    <td class="tc">Check[file]Entry</td>
    <td class="tc" colspan="3">
     # <a href='?act=scanInit&part=code' target="_blank">init_code</a> 
     | <a href='?act=scanInit&part=root' target="_blank">init_root</a>
     | <a href='?act=scanInit&part=tpls' target="_blank">init_tpls</a>
     | <a href='?act=scanInit&part=a3rd' target="_blank">init_a3rd</a>
     #
    </td>
  </tr> 
  <tr>
    <td class="tc">Check[mkv]Entry</td>
    <td class="tc" colspan="3">
     # <a href='?act=scanMkvs&part=adm' target="_blank">mkv_adm</a> 
     | <a href='?act=scanMkvs&part=umc' target="_blank">mkv_umc</a>
     | <a href='?act=scanMkvs&part=mob' target="_blank">mkv_mob</a>
     | <a href='?act=scanMkvs&part=chn' target="_blank">mkv_chn</a>
     | <a href='?act=scanMkvs&part=dev' target="_blank">mkv_dev</a>
     #
    </td>
  </tr> 
  <tr>
    <td class="tc">Check[Cnchr]Char</td>
    <td class="tc" colspan="3">
     <a href='?act=scanCnchr&part=code/adpt' target="_blank">adpt</a> 
     | <a href='?act=scanCnchr&part=root/cfgs' target="_blank">cfgs</a>
     | <a href='?act=scanCnchr&part=code/core' target="_blank">core</a>
     | <a href='?act=scanCnchr&part=code/flow' target="_blank">flow</a>
     # <a href='?act=scanCnchr&part=skin/adm' target="_blank">adm</a>
     | <a href='?act=scanCnchr&part=skin/umc' target="_blank">umc</a>
     | <a href='?act=scanCnchr&part=skin/doc' target="_blank">doc</a>
     # <a href='?act=scanCnchr&part=root/a3rd' target="_blank">a3rd</a> 
     | <a href='?act=scanCnchr&part=root/plus' target="_blank">plus</a>
     | <a href='?act=scanCnchr&part=root/tools' target="_blank">tools</a>
    </td>
  </tr> 
  </table>

<?php
if($act=='openLinks'){ 
  echo basJscss::imp('/plus/ajax/comjs.php?act=autoJQ'); 
  $_cbase['tpl']['tpl_dir'] = empty($ntpl) ? $_cbase['tpl']['def_static'] : $ntpl;
  $ncfg = vopTpls::entry($part,'ehlist','ehlist'); 
  if($part=='umc'){
    $ncfg = array();
    $list = db()->table('docs_faqs')->where("1=1")->order('did DESC')->select(); 
    foreach ($list as $key => $row) {
      $ncfg['faqs'][] = $row['did'];
    }
  }
?>

  <p>[<?php echo "$ntpl"; ?>]openLinks</p>
  <table width="100%" border="1" class="tblist" id="idlinks">
  <?php foreach($ncfg as $mod=>$vals){ ?>
  <tr id="res">
    <td>
   --- <b><?php echo $mod; ?></b><br>
    <?php foreach($vals as $key=>$val){ 
      if(is_array($val)) continue; 
      if(is_numeric($key)){
        $url = surl("$mod.$val");
      }else{
        if(!strpos($val,'/')) continue; 
        //if(!strpos($val,'/')) continue;
        $mkv = $key=='m' ? $mod : "$mod-$key";
        $url = surl($mod=='home' ? '' : "$mkv");
        if(strpos($url,'?ocar')) continue;
      }
   ?>
    <a href="<?php echo "$url"; ?>" target="_blank"><?php echo "$val"; ?></a><br>
    <?php } ?>
    --- <b>tiexin-tips</b><br>
    <a href="http://txjia.com/peace/txasp.htm" target="_blank">tiexin-asp</a><br>
    <a href="http://txjia.com/peace/txbox.htm" target="_blank">tiexin-box</a><br>
    </td>
  </tr> 
  <?php } ?>
  <tr>
    <td class="tip">……</td>
  </tr> 
  </table>

<script>
var wmax = 6; // 6~12
function funcOpen(){
  for(var i=0;i<wmax+1;i++){ window.open('','_w'+i); }
  $('#idlinks').find('a').each(function(no, ilink) {
    var r = jsRnd(100,400);
    setTimeout("funcOset("+no+");",(no+1)*1500+r);
    //jsLog(i);
  });
}
function funcOset(no){
  var ilink = $('#idlinks').find('a')[no];
  var url = $(ilink).prop('href');
  var html = $(ilink).html()+' --- ';
  if(url.indexOf('close#')<=0){
    window.open(url,'_w'+(no%wmax));
    //var wobj = wobj.blur();
    html += url;
  }else{
    html += 'Error!'
  }
  $(ilink).html(html.replace('<?php echo $_cbase['run']['rsite']; ?>',''));
}
funcOpen();
</script>
<?php } ?>

<?php
if($act=='openDowns'){ 
?>
<div>
  <p>[<?php echo "$ntpl"; ?>]openDowns</p>
  <table width="100%" border="1" class="tblist" id="idlinks">
  <tr id="res">
    <td>
      --- <b>demo</b><br>
      <?php foreach ($dcfgs as $dkey=>$did) {  ?>
      <a href="http://down.chinaz.com/soft/<?php echo $did; ?>.htm" target="_blank">chinaz/<?php echo "$dkey:$did"; ?></a><br>
      <?php } ?>
      --- <b>open</b><br>
      <?php 
      $xcfg=array(28,7,22,19,10,20,16); 
      for ($ia=0;$ia<$part;$ia++) {
      foreach ($xcfg as $pt) {
        foreach ($dcfgs as $dkey=>$did) {
         echo "<a href='?act=$dkey&part=$pt' target='_blank'>part=$pt : $dkey</a><br>";
        }
      }}?>
    </td>
  </tr> 
  <tr>
    <td class="tip">……</td>
  </tr> 
  </table>

<script>
var wmax = 6; // 6~12
function funcOpen(){
  for(var i=0;i<wmax+1;i++){ window.open('','_w'+i); }
  $('#idlinks').find('a').each(function(no, ilink) {
    var r = jsRnd(1700,4300); 
    setTimeout("funcOset("+no+");",(no+1)*2500+r);
  });
}
function funcOset(no){
  var xr = jsRnd(1000,2000);
  if(xr>1000&&xr<1200) return true;
  var ilink = $('#idlinks').find('a')[no];
  var url = $(ilink).prop('href');
  var html = $(ilink).html()+' --- opened ';
  window.open(url,'_w'+(no%wmax));
  $(ilink).html(html);
}
//funcOpen();
</script>
<?php } ?>

<?php } ?>

<?php
glbHtml::page('end');
?>