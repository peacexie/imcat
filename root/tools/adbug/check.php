<?php 
require(dirname(__FILE__).'/_config.php'); 

$act = @$_GET['act']; $act || $act = 'check'; 
$inptype = @$_GET['inptype']; 
$inpval = @$_GET['inpval'];
$rem_url = empty($_GET['rem_url']) ? 'http://www.baidu.com/' : $_GET['rem_url'];
$rem_type = empty($_GET['rem_type']) ? '' : $_GET['rem_type'];

$cfg = array(
	'check'=>'基本环境',
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

glbHtml::page("环境检测-$title",1);
glbHtml::page('imp');

$iniPath = get_cfg_var('cfg_file_path');
$iniInfo = $iniPath ? "PHP configuration is using THIS file: [$iniPath]" : "WARNING: No configuration file (php.ini) used by PHP!";

?>
<link rel='stylesheet' type='text/css' href='./style.css'/>
</head><body>

<div>
  <table width="100%" border="1" class="tblist">
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
</div>

<?php 
if($act=='check'){ 
?>

<div>
<p class="tip">环境检测 --- 基本环境</p>

  <table width="100%" border="1" class="tblist">
    <tr>
      <th class="tc">名称</th>
      <th class="tc">结果</th>
      <th>状态</th>
      <th>备注</th>
    </tr>
	<?php 
    $a = array('upfile','reset'); 
    foreach($a as $k){ $key = "can_$k";
    ?>
    <tr>
      <td class="tc"><?php echo $key; ?></td>
      <td class="tc"><span style="color: #<?php echo $$key ? "ff0000" : "008000"; ?>; font-weight : bold;"><?php echo $$key ? "危险" : "安全"; ?></span></td>
      <td><?php echo $key; ?>=<?php echo $$key; ?> (code/cfgs/boot/cfg_adbug.php页参数)</td>
      <td>正式使用请设置为0</td>
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
      <td><?php echo $re['tip'].(empty($re['demo']) ? '' : " &nbsp; -=&gt;<a href='{$re['demo']}'>示例</a>"); ?></td>
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
      <td class="tc">超级测试</td>
      <td class="tc">-</td>
      <td colspan="2">
    <select name="inptype" onChange="setInpval(this)" style="width:200px;">
      <option value="">---选一个操作---</option>
      <option value="memory">内存(填数字,出错表示不支持)</option>
      <option value="funcs">函数(填字符,Support表示支持)</option>
    </select>
    <input name="inpval" type="text" id="inpval" value="<?php echo $inpval; ?>" style="width:100px;"/>
    <input type="submit" name="submit" id="submit" value="Submit" class="btn" />   
<script>
function setInpval(e){
	var type = e.value,res = '';
	if(type=='memory')  res = 32;
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

</div>

<div>
<p class="tip">环境检测 --- 系统目录</p>

  <table width="100%" border="1" class="tblist">
    <tr>
      <th class="tc">名称</th>
      <th class="tc">结果</th>
      <th width="30%">Dir</th>
      <th width="40%">Path</th>
    </tr>
	<?php 
	//if(empty($_isOut)){
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
      <td class="tip" colspan="4">请对照文件[/root/run/_paths.php],设置相关路径！</td>
    </tr> 
    <?php } //} ?>
  </table>
</div>

<div>
<p class="tip">环境检测 --- 数据库连接</p>

  <table width="100%" border="1" class="tblist">
    <tr>
      <th class="tc">名称</th>
      <th class="tc">结果</th>
      <th width="70%">(连接)状态/结果</th>
    </tr>
    
	<?php 
    $a3 = devRun::runMydb3(); $nocnt = 0;
    foreach($a3 as $k=>$re){ $nocnt += $re['res']==FLAGNO ? 1 : 0;
    ?>
    <tr>
      <td class="tc">[<?php echo $k; ?>]扩展</td>
      <td class="tc"><?php echo $re['res']; ?></td>
      <td><?php echo $re['info']; ?></td>
    </tr>
    <?php } if($nocnt){ ?>
    <tr>
      <td class="tip" colspan="4">请检查[/code/cfgs/boot/cfg_db.php]配置</td>
    </tr> 
    <?php } ?>
    
  </table>
</div>

<?php 
}if($act=='define'){
?>

<div style="height:500px; overflow-y:scroll;">
<p class="tip">环境检测 --- define</p>
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
</div>

<?php
}if($act=='upload'){ 
?>
<div>
  <form id="fmup" name="fmup" method="post" action="?act=upload" enctype="multipart/form-data">
    <p class="tc tip">文件上传</p>
    <ul>
	<?php
    if(!empty($_POST['upLoad'])){	// LINKOK,FAILED
        $uppath = $_POST['uppath'];
        foreach($_FILES as $f){
            if(empty($can_upfile)) die("Please SET [ \$can_upfile = '1' ]"); 
			if($f['name']){ 
                $fp = $uppath.$f['name']; 
                $r = move_uploaded_file($f['tmp_name'],$fp); 
                chmod($fp, 0755);//设定上传的文件的属性 
                echo "<li><i class='w2'>结果:</i>$fp 上传OK</li>"; 
        }	}
    }
    ?>
	<li><i class="w2">文件：</i><input type="file" name="fileup1" id="fileup1"></li>
    <li>
    <i class="w2">路径：</i><input name="uppath" type="text" id="uppath" value="./" size="18"/>
    <input type="submit" name="upLoad" id="upLoad" value="上传" />
    </li>
    </ul>
  </form>
</div>
<?php } ?>

<?php 
if($act=='remote'){ 
?>
<div>
  <form id="fmremote" name="fmremote" method="get" action="?" target="_blank">
    <p class="tc tip">Remote抓取</p>
    <ul>
    <li>
      <i class="w2">Url: </i>
      <input name="rem_url" type="text" value="<?php echo $rem_url; ?>" style="width:360px;"> 
      <br>
      https://api.weixin.qq.com/cgi-bin/token<br>
      http://www.baidu.com/<br>
      https://www.baidu.com/    </li>
    <li><i class="w2">扩展:</i>
        <select name="rem_type" style="width:360px;">
          <!--option value="">---选一个操作---</option-->
          <option value="curl_init">函数(curl:curl_init,curl_setopt)</option>
          <option value="file_get_contents">函数(file_get_contents)</option>
          <option value="fsockopen">函数(fsockopen)</option>
        </select>
    </li>
    <li><i class="w2">显示:</i>
        <select name="rem_show" style="width:150px;">
          <option value="">---显示方式---</option>
          <option value="script_style">去script,style(默认)</option>
          <option value="script_style_tags">文本(包含默认)</option>
          <option value="_null_">原文(原本html)</option>
        </select>
        <select name="rem_cset" style="width:150px;">
          <option value="">---(默认utf-8)---</option>
          <option value="gbk">gbk编码</option>
          <option value="gb2312">gb2312编码</option>
          <option value="big5">big5编码</option>
        </select>
        <input type="submit" name="submit" id="submit" value="提交" />
    </li>
    </ul>
    <input name="remote" type="hidden" value="1">
    <input name="act" type="hidden" value="remote">
  </form>
</div>
<?php } ?>

</body></html>
