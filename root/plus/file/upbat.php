<?php
require('_config.php'); 
usrPerm::run('pextra','edtup'); //上传权限 
pfileHead('upbat','附件（文件）批量上传（表单）');
?>

<table border='1' class='tbdata'>
  <tr>
    <th colspan="2">附件批量上传</th>
  </tr>
  <form name="fup1" id="fup1" action="updeel.php?<?php echo $allpars; ?>" enctype="multipart/form-data" method="post">
    <tr>
      <td nowrap class="tr">文件:</td>
      <td nowrap class="tl" id="tdfiles"></td>
    </tr>
    <tr>
      <td nowrap class="tr">增加:</td>
      <td nowrap class="tl">
        <input name="btn1" id="btn1" type="button" value="+1" onClick="batAdd1(1)">
        个 &nbsp;
        <input name="btn2" id="btn2" type="button" value="+2" onClick="batAdd1(2)">
        个 &nbsp;
        <input name="btn3" id="btn3" type="button" value="+4" onClick="batAdd1(4)">
        个 &nbsp;
        <input name="btn4" id="btn4" type="button" value="+8" onClick="batAdd1(8)">
        个 &nbsp; </td>
    </tr>

    <tr>
      <td nowrap class="tr">设置:</td>
      <td nowrap class="tl"><select name="upren" id="upren">
          <option value="auto">自动命名</option>
          <option value="keep">原文件名</option>
        </select>
        &nbsp;
        <input name="btUpload" type=submit id="btUpload" value="上传" onClick="batSends(1)"> <span id="res_msg"></span></td>
    </tr>
  </form>
    <tr>
      <td colspan="2" class="tl pa10 f12">说明：<br>
        ***1. 本程序受启发于<a href="http://www.babytree.com/">宝宝树</a>照片批量上传而制作，最先为asp版，后面php两次大改版而成；<br>
        ***2. 请先设置类别，再浏览图片；可用下方的(+n)按纽增加n个图片项目；一次最多可设置96个图片批量上传；<br>
        ***3. 本程序为增值程序，免费使用；请不要苛求它的功能；如不能满足您的需要，请用普通方式添加资料。<br>
      ***4. 建议把要上传的文件，放在同一文件夹中，用标题作为图片名(默认情况下,本系统把文件名作为信息的标题)；其文件名（除后缀外），不能用空格引号点等特殊字符； 建议全用英文半角的字母，数字或下划线；除图片名可用中文外，目录建议也不要用中文。</td>
    </tr>
</table>
<div id="ftemp" style="display:none">
  <div class="bat_fdiv" id="bidiv_0001">
    <input class="bat_delbtn" type="button" value="删除" onClick="batDel1('0001')" >
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
