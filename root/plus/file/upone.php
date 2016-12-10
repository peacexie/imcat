<?php 
require('_config.php');
$user || basMsg::show('Not Login.','die'); //未登录

$rdir = DIR_DTMP;
$rpath = PATH_DTMP;
$ufix = comStore::getTmpDir(0);
	
//$dmsg = ''; //处理删除
$_upPerm = '1';//usrPerm::check('pextra','edtup'); $_upPerm = !$_upPerm;
$isbat = req('isbat','');

glbHtml::page(lang('plus.fop_uponetitle'),1);
glbHtml::page('imadm',array('js'=>'/plus/file/funcs.js','css'=>'/plus/file/style.css'));
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
          <div id="preview_fake"> <img id="preview" alt="(<?php lang('plus.fop_oview',0); ?>id:<?php echo $isbat; ?>)" onload="imgShow(this,120,20)"/> </div>
        </div></td>
    </tr>
    <?php }else{ ?>
    <tr>
      <td height="30" nowrap class="tl"><input name='file1' type='file' id="file1" class="w210"
        onChange="PreviewImage(this,'preview','preview_wrapper');">
        <input name="recbk" type="hidden" value="pfield"></td>
      <td rowspan="2" nowrap class="tl"><div id="preview_wrapper" style="height:60px;">
          <div id="preview_fake"> <img id="preview" alt="<?php lang('plus.fop_picview',0); ?>" onload="imgShow(this,120,60)"/> </div>
      </div></td>
    </tr>
    <tr>
      <td height="30" nowrap class="tl">
        <select name="upren" id="upren" class="w120">
          <option value="auto"><?php lang('plus.fv_atuoname',0); ?></option>
          <option value="keep"><?php lang('plus.fv_orgname',0); ?></option>
        </select>
        <input name="btUpload" type=submit id="btUpload" value="<?php lang('plus.fv_upload',0); ?>">
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
