<?php 
require('_config.php');
/*
    error_reporting(E_ERROR|E_WARNING);
    $key =htmlspecialchars($_POST["searchKey"]);
    $type = htmlspecialchars($_POST["videoType"]);
    $html = file_get_contents('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw='.$key.'&pageNo=1&pageSize=20&channelId='.$type.'&inDays=7&media=v&sort=s');
    echo $html;
*/
pfileHead('media','附件（文件）选择管理');
?>

<form name="media" id="media" method="post" action="">
  <table width="80%" border="1" align="center" class="tbdata">
    <tr>
      <td width="20%" align="center">媒体类型：</td>
      <td><select name="type" id="type" onChange="mediaChange()">
          <?php echo basElm::setOption(vopMedia::cfgTypes(),''); ?>
        </select></td>
    </tr>
    <tr id="r_url" style="display:none">
      <td align="center">媒体地址：</td>
      <td><input type="text" name="url" id="url" class="w360"></td>
    </tr>
    <tr id="r_map" style="display:none">
      <td align="center">地图坐标：</td>
      <td>
      <input id='map' name='map' type='text' value='' class='txt w300'  maxlength='36' />
      <span class='fldicon fmap' onClick="mapPick('<?php echo $_cbase['sys_map']; ?>','map',620,460);">&nbsp;</span>
      </td>
    </tr>
    <tr id="r_ext">
      <td align="center"><span id="ext_flag">扩展属性</span>：</td>
      <td>
      <input id='ext' name='ext' type='text' value='' class='txt w360'  maxlength='36' />
      </td>
    </tr>
    <tr>
      <td align="center">显示大小：</td>
      <td><input name="pw" type="text" class="w50" id="pw">
        x
      <input type="text" class="w50" name="ph" id="ph">
       &nbsp; 默认显示大小(480x360)px</td>
    </tr>

    <tr>
      <td colspan="2" align="center"><input type="button" name="button" id="button" value="确定" onClick="mediaInsert()">
        &nbsp;
      <input type="reset" name="button2" id="button2" value="取消"></td>
    </tr>
    <tr>
      <td colspan="2">说明：
        &nbsp;
    </tr>
  </table>
</form>
<script>
var fidForPick = '<?php echo $fid; ?>';
var otype = jsElm.jeID('type');
</script>
</body>
</html>
