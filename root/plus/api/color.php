<?php
require dirname(__FILE__).'/_config.php'; 
glbHtml::page('Color Pick',1);
eimp('initJs','jquery;/_pub/jslib/jscolor'); 
eimp('initCss',';comm');
glbHtml::page('aumeta');
?>
<style TYPE="text/css">
body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;color:#333;background-color:#fff}
table {border:1px #CCC solid; margin:auto;}
td {width:8px;height:8px;border:1px #FFF solid;}
td.tab td{ font-size:8px;line-height:8px;cursor:pointer; }
td.brd {border-right:1px solid #333;border-bottom:1px solid #333;border-top:1px solid #CCC;border-left:1px solid #CCC;}
#resDemo { width:40%; height:15px; border:1px #CCC solid; }
table.brd0{ border:0px; }
input { width:60px; margin:2px; }
button {width:5em; margin:2px 1px; }
body {margin:5px 5px;padding:0px;}
</style>
<script>var _tab='3';</script><!--3,2,1-->
<?php glbHtml::page('body'); ?>
<table border="0" align="center" cellpadding="0" cellspacing="1">  
  <tr>
    <td class="tab"><!--tab-->
      <table width="230" border="0" cellpadding="0" cellspacing="0" id=tabx onMouseOut="cOut()" align="center">
        <script>
        if(_tab=='1'){cTab1(6,36)}else if(_tab=='2'){cTab2()}else{cTab3()}
        </script>
      </table>
    </td>
  </tr>
  <tr>
    <td class="tab"><!--tab-->
      <table width="230" border="0" cellpadding="0" cellspacing="0" id=tab6 onMouseOut="cOut()" align="center">
        <script>cTab6()</script>
      </table>
    </td>
  </tr>
  <tr>
    <td><!--tab-->
      <table width="230" border="0" align="center" cellpadding="0" cellspacing="1" class="brd0" >
        <tr> 
          <td nowrap style="width:30%;line-height:120%;">
            <?php lang('plus.color_org',0); ?><INPUT id=resOrg value="" />
            <br />
            <?php lang('plus.color_code',0); ?><INPUT id=resCode value="" />
          </td>
          <td align="center" id=resDemo>
          <span style="background-color:#CCC;line-height:100%; display:inline-block;"><?php lang('plus.color_now',0); ?></span>
          </td>
          <td align="right">
          <button TYPE=SUBMIT onClick="btnSEnd('OK')"><?php lang('plus.color_setok',0); ?></button><br>
          <button onClick="btnSEnd('Cancel')"><?php lang('plus.color_cancel',0); ?></button>
          </td>
        </tr>
    </table></td>
  </tr>
</table>

<script> 
var resRGB = '#00FF00';
var resCode = jsElm.jeID('resCode');
var resDemo = jsElm.jeID('resDemo');
var pcolor = urlPara('color');
var ptitle = urlPara('title');
var parDoc = parent.document;
var resOrg = jsElm.jeID('resOrg');
resOrg.value = '#'+jsElm.pdID(pcolor).value; 
try{resOrg.style.color = resOrg.value;}catch(ex){}
</script>

</body>
</html>
