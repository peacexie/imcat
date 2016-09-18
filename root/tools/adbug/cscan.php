<?php 
require(dirname(__FILE__).'/_config.php'); 

$act = basReq::val('act','');
$part = $ntpl = basReq::val('part','');

glbHtml::page("Check/Scan",1);
glbHtml::page('imp');

if($act=='scanInit'){
  $bcfg = array('code'=>DIR_CODE, 'root'=>DIR_ROOT, 'tpls'=>DIR_CODE.'/tpls', 'a3rd'=>DIR_ROOT.'/a3rd'); @$burl = $bcfg[$part]; 
  $dcfg = array('code'=>'',       'root'=>'',       'tpls'=>'/code',          'a3rd'=>'/root');          @$durl = $dcfg[$part];
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
}elseif($act=='curlDown'){  
    $ref = array('_ref'=>'http://down.chinaz.com/soft/37712.htm');
    $url = "http://down.chinaz.com/download.asp?id=37712&dp=1&fid=$part&f=yes";
    $res = comHttp::curlCrawl($url,$ref);
    echo basStr::filForm($res)." [$part] "; die();
}

?>

<link rel='stylesheet' type='text/css' href='./style.css'/>
</head><body>

<div>
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
</div>

<?php if($act=='bomcheck'){ ?>

<?php 
$bomroot = empty($_GET['bomroot']) ? '../../../' : $_GET['bomroot'];
defined('DIR_PROJ') || define('DIR_PROJ',dirname(__FILE__));
if(empty($can_upfile)){
	if(!strstr($bomreal,DIR_PROJ)) $bomroot = '../../../';
}
$bompath = empty($_GET['bompath']) ? '' : $_GET['bompath'];
$bomfile = @$_GET['bomfile']; $bommsg = '';
$bomreal = str_replace("\\","/",realpath($bomroot)); //echo $bomreal;
?>

<div>
  <?php
  if(!empty($bomfile)){
	  if(empty($can_upfile)) die("Please SET [ \$can_upfile = '1' ]"); 
	  if(devRun::bomRemove($bomfile)) $bommsg = "<li class='rmok'>BOM Remove OK! ------ $bomfile</li>\n";
  }
  ?>
  <form id="fmbom" name="fmbom" method="get" action="?">
    <p class="tc tip">BOM Check</p>
    <ul>
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
</div>

<?php }else{ ?>

<div>
<p class="tip">Check/Scan</p>

  <table width="100%" border="1" class="tblist">
    <tr>
      <td class="tc">lang,down</td>
      <td class="tc" colspan="3">
       # <a href='?act=openDowns&part='>openDowns</a>
       # <a href='?act=scanDblang&part='>scanDblang</a>
       # <a href='cbaidu.php'>scanBaidu</a>
       #
      </td>
    </tr> 
    <tr>
      <td class="tc">Open[hlist]Entry</td>
      <td class="tc" colspan="3">
       # <a href='?act=openLinks&part=umc'>open_umc</a>
       | <a href='?act=openLinks&part=mob'>open_mob</a>
       | <a href='?act=openLinks&part=chn'>open_chn</a>
       | <a href='?act=openLinks&part=dev'>open_dev</a>
       | <a href='?act=openLinks&part=doc'>open_doc</a>
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
       | <a href='?act=scanCnchr&part=code/cfgs' target="_blank">cfgs</a>
       | <a href='?act=scanCnchr&part=code/core' target="_blank">core</a>
       | <a href='?act=scanCnchr&part=code/flow' target="_blank">flow</a>
       # <a href='?act=scanCnchr&part=code/tpls/adm' target="_blank">adm</a>
       | <a href='?act=scanCnchr&part=code/tpls/umc' target="_blank">umc</a>
       | <a href='?act=scanCnchr&part=code/tpls/doc' target="_blank">doc</a>
       # <a href='?act=scanCnchr&part=root/a3rd' target="_blank">a3rd</a> 
       | <a href='?act=scanCnchr&part=root/plus' target="_blank">plus</a>
       | <a href='?act=scanCnchr&part=root/skin' target="_blank">skin</a>
       | <a href='?act=scanCnchr&part=root/tools' target="_blank">tools</a>
      </td>
    </tr> 
  </table>

</div>

<?php
if($act=='openLinks'){ 
	$_cbase['tpl']['tpl_dir'] = empty($ntpl) ? $_cbase['tpl']['def_static'] : $ntpl;
	$ncfg = vopTpls::entry($part,'ehlist','ehlist');
?>
<div>
  <p>[<?php echo "$ntpl"; ?>]openLinks</p>
  <table width="100%" border="1" class="tblist" id="idlinks">
    <?php foreach($ncfg as $mod=>$vals){ ?>
    <tr id="res">
      <td>
	  --- <b><?php echo $mod; ?></b><br>
      <?php foreach($vals as $key=>$val){ 
	  	if(is_array($val)) continue; 
		if(!strpos($val,'/')) continue; 
		$mkv = $key=='m' ? $mod : "$mod-$key";
		$url = vopUrl::fout($mod=='home' ? '' : "$mkv");
	  ?>
      <a href="<?php echo "$url"; ?>" target="_blank"><?php echo "$val"; ?></a><br>
      <?php } ?>
      </td>
    </tr> 
    <?php } ?>
    <tr>
      <td class="tip">……</td>
    </tr> 
  </table>
</div>
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
  $(ilink).html(html.replace(_cbase.run.rsite,''));
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
            <a href="http://down.chinaz.com/soft/37712.htm" target="_blank">chinaz/37712</a><br>
            --- <b>open</b><br>
            <?php 
            $dcfg=array(28,7,22,19,10,20,16); 
            $ref = array('_ref'=>'http://down.chinaz.com/soft/37712.htm');
            foreach (array(1,2,3,4,5) as $ia) {
            foreach ($dcfg as $dkey) {
              echo "<a href='?act=curlDown&part=$dkey' target='_blank'>part=$dkey</a><br>";
            }}?>
      </td>
    </tr> 
    <tr>
      <td class="tip">……</td>
    </tr> 
  </table>
</div>
<script>
var wmax = 6; // 6~12
function funcOpen(){
  for(var i=0;i<wmax+1;i++){ window.open('','_w'+i); }
  $('#idlinks').find('a').each(function(no, ilink) {
        var r = jsRnd(1700,4300); //jsLog(i)
        setTimeout("funcOset("+no+");",(no+1)*2500+r);
    });
}
function funcOset(no){
  var ilink = $('#idlinks').find('a')[no];
  var url = $(ilink).prop('href');
  //var html = $(ilink).html()+' --- ';
  window.open(url,'_w'+(no%wmax));
  //$(ilink).html(html.replace(_cbase.run.rsite,''));
}
funcOpen();
</script>
<?php } ?>

<?php } ?>

<?php
glbHtml::page('end');
?>