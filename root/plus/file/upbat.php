<?php
namespace imcat;
require '_config.php'; 
usrPerm::run('pextra','edtup'); //上传权限 
pfileHead('upbat',lang('plus.fop_upbattitle'));
?>

<table border='1' class='tbdata'>
  <tr>
    <th colspan="2"><?php lang('plus.fop_batup',0); ?></th>
  </tr>
  <form name="fup1" id="fup1" action="updeel.php?<?php echo $allpars; ?>" enctype="multipart/form-data" method="post">
    <tr>
      <td nowrap class="tr"><?php lang('plus.fop_add',0); ?>:</td>
      <td nowrap class="tl" id="tdfiles"></td>
    </tr>
    <tr>
      <td nowrap class="tr"><?php lang('plus.fop_add',0); ?>:</td>
      <td nowrap class="tl">
        <input name="btn1" id="btn1" type="button" value="+1" onClick="batAdd1(1)">
        <?php lang('plus.fop_item',0); ?> &nbsp;
        <input name="btn2" id="btn2" type="button" value="+2" onClick="batAdd1(2)">
        <?php lang('plus.fop_item',0); ?> &nbsp;
        <input name="btn3" id="btn3" type="button" value="+4" onClick="batAdd1(4)">
        <?php lang('plus.fop_item',0); ?> &nbsp;
        <input name="btn4" id="btn4" type="button" value="+8" onClick="batAdd1(8)">
        <?php lang('plus.fop_item',0); ?> &nbsp; </td>
    </tr>

    <tr>
      <td nowrap class="tr"><?php lang('plus.fop_set',0); ?>:</td>
      <td nowrap class="tl"><select name="upren" id="upren">
          <option value="auto"><?php lang('plus.fv_atuoname',0); ?></option>
          <option value="keep"><?php lang('plus.fv_orgname',0); ?></option>
        </select>
        &nbsp;
        <input name="btUpload" type=submit id="btUpload" value="<?php lang('plus.fv_upload',0); ?>" onClick="batSends(1)"> <span id="res_msg"></span></td>
    </tr>
  </form>
    <tr>
      <td colspan="2" class="tl pa10 f12">
        <?php basLang::inc('uless','plus_upbat'); ?>
      </td>
    </tr>
</table>
<div id="ftemp" style="display:none">
  <div class="bat_fdiv" id="bidiv_0001">
    <input class="bat_delbtn" type="button" value="<?php lang('plus.fv_tdel',0); ?>" onClick="batDel1('0001')" >
    <iframe id='biifr_0001' name="biifr_0001" src='upone.php?isbat=0001' frameBorder=0 width='480' scrolling='no' height='32'></iframe>
  </div>
</div>
<div style='line-height:10px;'>&nbsp;</div>
<script>
var sendNO = 0;
var sendCnt = 0;
var deelNO = 0;
var sendOK = 0;
var addCnt = 0;
batAdd1(4);
</script>
</body>
</html>
