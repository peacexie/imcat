<?php 
require(dirname(__FILE__).'/_config.php'); 

$act = basReq::val('act','');
$part = $ntpl = basReq::val('part','');

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
}

glbHtml::page("Check/Scan",1);
glbHtml::page('imp');
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
    <p class="tc tip">BOM检测</p>
    <ul>
	<?php echo $bommsg; ?>
    <li>
      <i class="w2">根目录: </i><input name="bomroot" type="text" value="<?php echo $bomroot; ?>" size="36"> 
      <input type="submit" value="设置">
      <span title="点[BOM]连接,将移除BOM">点击[子目录], [Ctrl+F]搜索[BOM]</span> &nbsp; 
     </li>
    <li>
    <i class="w2">子目录：</i>
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
      <td class="tc">Open[hlist]入口</td>
      <td class="tc" colspan="3">
       # <a href='?act=openLinks&part=umc'>open_umc</a>
       | <a href='?act=openLinks&part=mob'>open_mob</a>
       | <a href='?act=openLinks&part=chn'>open_chn</a>
       | <a href='?act=openLinks&part=dev'>open_dev</a>
       #
      </td>
    </tr> 
    <tr>
      <td class="tc">检查[file]入口</td>
      <td class="tc" colspan="3">
       # <a href='?act=scanInit&part=code' target="_blank">init_code</a> 
       | <a href='?act=scanInit&part=root' target="_blank">init_root</a>
       | <a href='?act=scanInit&part=tpls' target="_blank">init_tpls</a>
       | <a href='?act=scanInit&part=a3rd' target="_blank">init_a3rd</a>
       #
      </td>
    </tr> 
    <tr>
      <td class="tc">检查[mkv]入口</td>
      <td class="tc" colspan="3">
       # <a href='?act=scanMkvs&part=adm' target="_blank">mkv_adm</a> 
       | <a href='?act=scanMkvs&part=umc' target="_blank">mkv_umc</a>
       | <a href='?act=scanMkvs&part=mob' target="_blank">mkv_mob</a>
       | <a href='?act=scanMkvs&part=chn' target="_blank">mkv_chn</a>
       | <a href='?act=scanMkvs&part=dev' target="_blank">mkv_dev</a>
       #
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
<!--
var new_window = window.open("hello.html","html_name","width=200,height=200");
// blur the new window
new_window.blur();
<H1>A new window has been opened and moved to the background.</H1> 
<A onmouseover=new_window.focus(); href="#">Bring it forward</A> 
<A onmouseover=new_window.blur(); href="#">Put it backward</A> 
-->
<?php } ?>

<?php } ?>

<?php
glbHtml::page('end');
?>