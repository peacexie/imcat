<?php 
require('_config.php');
$user || basMsg::show('Not Login.','die'); //未登录

$rdir = DIR_DTMP;
$rpath = PATH_DTMP;
$ufix = comFiles::getTmpDir(0);
	
//$dmsg = ''; //处理删除
$_upPerm = '1';//usrPerm::check('pextra','edtup'); $_upPerm = !$_upPerm;
$isbat = basReq::val('isbat','');

glbHtml::page('附件（文件）上传（表单）',1);
glbHtml::page('imadm'); //adm
echo basJscss::imp("/plus/file/funcs.js",'js');
echo basJscss::imp("/plus/file/style.css");
glbHtml::page('body',' style="margin:'.($isbat ? "3px; 1px;" : "10px 5px;").'"');

?>
<table width="100%" border='0' align="center" cellpadding='1' cellspacing='3'>
  <?php if($_upPerm){ ?>
  <form name="fup1" id="fup1" action="updeel.php?<?php echo $allpars; ?>" enctype="multipart/form-data" method="post">
    <?php if($isbat){ ?>
    <tr>
      <td nowrap class="tc"><input name='file1' type='file' id="file1" class="w320" 
        onChange="PreviewImage(this,'preview','preview_wrapper');">
        <input name="recbk" type="hidden" value="isbat_<?php echo $isbat; ?>">
        <input name="upren" id="upren" type="hidden" value="auto">
      </td>
      <td nowrap class="tc"><div id="preview_wrapper">
          <div id="preview_fake"> <img id="preview" alt="预览(id:<?php echo $isbat; ?>)" onload="imgShow(this,120,20)"/> </div>
        </div></td>
    </tr>
    <?php }else{ ?>
    <tr>
      <td height="30" nowrap class="tl"><input name='file1' type='file' id="file1" class="w210"
        onChange="PreviewImage(this,'preview','preview_wrapper');">
        <input name="recbk" type="hidden" value="pfield"></td>
      <td rowspan="2" nowrap class="tl"><div id="preview_wrapper" style="height:60px;">
          <div id="preview_fake"> <img id="preview" alt="图片预览" onload="imgShow(this,120,60)"/> </div>
      </div></td>
    </tr>
    <tr>
      <td height="30" nowrap class="tl">
        <select name="upren" id="upren" class="w120">
          <option value="auto">自动命名</option>
          <option value="keep">原文件名</option>
        </select>
        <input name="btUpload" type=submit id="btUpload" value="上传">
      </td>
    </tr>
    <?php } ?>
  </form>
  <?php }else{ echo 'Error!'; } ?>
</table>
<script>

function xx(id)
{
	var idImg = jsElm.jeID(id);
	idImg.innerHTML = ''; 	
	idImg.className="idHidden";
}

</script>
</body>
</html>
