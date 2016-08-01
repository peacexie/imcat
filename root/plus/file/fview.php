<?php 
require('_config.php');

$user || basMsg::show('Not Login.','die'); //未登录
$dfile = basReq::val('dfile',''); 
$cfg_dirs = glbConfig::read('urdirs','sy');
//print_r($user); die();

if($parts=='temp'){
	
	$rdir = DIR_DTMP;
	$rpath = PATH_DTMP;
	$ufix = comFiles::getTmpDir(0);
	
}elseif($parts=='now'){
	
	$rdir = DIR_URES;
	$rpath = PATH_URES; 
	if(!isset($groups[$mod]) || strlen($kid)<10){
		glbError::show('Error mod Or kid.');	
	}
	$ufix = comFiles::getResDir($mod,$kid,0);
	
}elseif(isset($cfg_dirs[$dir])){ //
	$cfg = $cfg_dirs[$dir]; 
	$rcfgs = array(
		'wrskin' => array(DIR_ROOT,PATH_ROOT),
		'static' => array(DIR_STATIC,PATH_STATIC),
	);
	$_r = $rcfgs[$cfg[1]];
	$rdir = $_r[0];
	$rpath =$_r[1]; 
	$ufix = $cfg[2];
}else{
	glbError::show('Error Path.');
}

$dmsg = ''; //处理删除
$_admPerm = usrPerm::check('pextra','edtadm'); $_admPerm = !$_admPerm;
$_upPerm = usrPerm::check('pextra','edtup'); $_upPerm = !$_upPerm;
$_delPath = in_array($parts,array('temp','now',));
$_upPath = in_array($parts,array('temp',));
if($dfile && $_admPerm && $_delPath){
	if(strstr($dfile,'./')) glbError::show('Error Path.');
	@$dre = unlink($rdir.$dfile); //var_dump($dre);
	$dmsg = $dre ? ' 删除成功！' : ' 删除失败!';
}
pfileHead($parts,'媒体（视频，音频，Flash，iFrame，地图）插入');
?>

<table border='1' class='tbdata'>
  <tr>
    <td colspan="4">&nbsp;
	<?php
	$str = "";
	foreach($cfg_dirs as $k=>$v){
		if($parts==$v[0]){
			$str .= (empty($str) ? '' : ' # ')."\n<a href='fview.php?".basReq::getURep($allpars,'dir',$k)."'>$k</a>";
		}
	}
	echo $str ? $str : '(无子目录)';
	?>
    </th>
    <th colspan="2" title="击文件即选择并返回">附件选择管理</th>
  </tr>
  <tr>
    <td colspan="6">&nbsp;<?php echo "$rpath/$ufix/"; ?><span id="fsName" style="color:#00F;"><?php echo $dmsg; ?></span></td>
    <!--td colspan='2' align="center"> buttons </td-->
  </tr>
<?php 
$nFile = 0;
$nDir = 0;
$sFile = 0;
$i = 0;
?>
  <tr>
    <th nowrap>文件选择/目录列表</th>
    <th width="15%" align='center' nowrap>预览</th>
    <th width="8%" align='center' nowrap>选择</th>
    <th width="10%" align='center' nowrap>大小[B]</th>
    <th width="18%" align="center" nowrap>创建时间</th>
    <th width="10%" align="center" nowrap>删除</th>
  </tr>
<?php 
$re = comFiles::listDir("$rdir/$ufix");
if(!empty($re['dir'])){
foreach($re['dir'] as $file=>$v){
  $i++; //if($i>=120) { break; }
  $iSize = 0; 
  $iTime = date("Y-m-d H:i:s",$v);
  $nDir++;
?>
  <tr>
    <td nowrap><img src="<?php echo PATH_STATIC; ?>/icons/file18/folder.gif" width="18" height="18" border="0" align="absmiddle"><?php echo $file ?>/</td>
    <td align='right' nowrap>&nbsp;</td>
    <td align='right' nowrap>&nbsp;</td>
    <td align='right' nowrap><?php echo $iSize ?></td>
    <td align="center" nowrap class="txtSC"><?php echo $iTime ?></td>
    <td align="center" nowrap class="txtSC">&nbsp;</td>
  </tr>
<?php 
} }
if(!empty($re['file'])){
foreach($re['file'] as $file=>$v){
  $i++; //if($i>=120) { break; }
  $iSize = basStr::showNumber($v[1]);
  $iTime = date("Y-m-d H:i:s",$v[0]);
  $nFile++;
  $sFile += $iSize;
  $fPName = "$rpath/$ufix/$file";
  $ticon = comFiles::getTIcon($file);
  $id = $tdAct = '';
  if(strstr(".db.php.xx.xx2.xx3",$ticon['icon'])) continue;
	if($ticon['icon']=='pic'){
	  $id = str_replace('.','___',str_replace('-','_',$file));
	  $jsAct = " onmouseover=\"fviShow('$id','$fPName',this)\" onmouseout=\"fviShow('$id')\" ";
	  $fSubj = "预览"; 
	  $tdAct = $jsAct;
	}else{
	  $fSubj = "打开查看";
	} 	
?>
  <tr>
    <td onClick="fviPick(<?php echo "'$fPName','{$ticon['type']}','$iSize'"; ?>);" title="选择文件" nowrap>
    <img src="<?php echo PATH_STATIC."/icons/file18/{$ticon['icon']}.gif"; ?>" width="18" height="18" border="0" align="absmiddle"> <?php echo $file ?></td>
    <td align='center' nowrap><a href="<?php echo $fPName ?>" target="_blank"><?php echo $fSubj ?></a></td>
    <td align="center" nowrap style="cursor:hand;color:#0000FF; " title="选择文件" <?php echo $tdAct ?> onClick="fviPick(<?php echo "'$fPName','{$ticon['type']}','$iSize'"; ?>);">
    选择<span class="idHidden" id='<?php echo $id ?>'></span></td>
    <td align='right' nowrap><?php echo $iSize ?></td>
    <td align="center" nowrap class="txtSC"><?php echo $iTime ?></td>
    <td align="center" nowrap class="txtSC">
      <?php if($_admPerm && $_delPath){ ?> 
      <a href="#" onClick="urlConfirm('?<?php echo "$allpars&dfile=/$ufix/$file" ?>','确认删除[<?php echo $file; ?>]? \n请小心操作哦！')">删除</a>
      <?php }else{ ?>
      <span style="color:#999">删除</span>
      <?php } ?></td>
  </tr>
<?php
}}
?>
  <?php if($i>0){ ?>
  <tr>
    <td colspan='7' nowrap>&nbsp;
    文件:<font color="#FF0000"><?php echo $nFile ?></font> &nbsp;
    目录:<font color="#FF0000"><?php echo $nDir ?> [<?php echo basStr::showNumber($sFile) ?>]</font> &nbsp;
  </td>
  </tr>
  <?php }else{ ?>
  <tr>
    <td colspan='7' nowrap> 暂时无 文件/目录列表...请上传!</td>
  </tr>
  <?php } ?>
</table>
<div style="line-height:8px;">&nbsp;</div>

<table width="99%" border='0' align="center" cellpadding='5' cellspacing='1'>
  <?php if(2==1){ ?>
  <form name="ffimg2" id="ffimg2" action="?" method="post">
    <tr>
      <td nowrap>目录:
        <input name="Dir" type="text" id="Dir" value="<?php echo $dDir ?>" size="24" maxlength="12" Xreadonly>
        <input name=Button type=submit id="Button2" value="建立目录" <?php echo $sDis ?>disabled>
        <input name="Act" type="hidden" id="Act" value="Dir">
        <input name="yPath" type="hidden" id="yPath" value="<?php echo $yPath ?>"> </td>
      <td align="left" nowrap>&nbsp;</td>
    </tr>
  </form>
  <?php } if($_upPerm && $_upPath){ ?>
  <form name="fup1" id="fup1" action="updeel.php?<?php echo $allpars; ?>" enctype="multipart/form-data" method="post">
    <tr>
      <td nowrap>本地上传:
        <input name='local' type='file' id="local" style="width:360px; "> 或</td>
      <td rowspan="2" valign="top" nowrap class="tl pv20"><select name="upren" id="upren">
        <option value="auto">自动命名</option>
        <option value="keep">原文件名</option>
      </select>
      <!--input name="recbk" type="hidden" value="refview"-->
      <input name="btUpload" type=submit id="btUpload" value="上传"></td>
    </tr>
    <tr>
      <td nowrap><select name="uptype" class="w80"><option value="remote">远程图片</option><option value="base64">Base64图片</option></select>
        <input name='udata' type='text' id="udata" style="width:340px; "></td>
    </tr>
  </form>
  <?php } ?>
  <tr>
    <td colspan='2' class="read">注意：<br>
        1. 大文件请用FTP上传，可参考[管理帮助] 或 [<a href="#readme.txt" target="_blank">文件目录规划</a>] 相关文件；<br>
        2. 可用[预览&gt;&gt;复制链接地址]得到相关附件地址；<br>
        3. [临时] 新上传文档,都放在这里,添加后会自动移动到相关文件夹； <br>
        4. [当前] 编辑资料时显示此项,此文件夹下的附件关联当前资料；<br>
        5. [上传] 可批量上传到文件；<br>
        6. [插入] 可插入：iframe(内框架),map(地图),swf(Flash媒体),audio(音频媒体),video(视频媒体)；</td>
  </tr>
</table>
<div style='line-height:10px;'>&nbsp;</div>
<script>
var fidForPick = '<?php echo $fid; ?>';
</script>
</body>
</html>
