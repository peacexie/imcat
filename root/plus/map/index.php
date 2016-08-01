<?php
require('_config.php'); 

glbHtml::page("{$api}地图",1);
glbHtml::page('imadm'); //adm
echo basJscss::imp($urls[$api],'js');
echo "<style type='text/css'>body {padding:0px; margin:0px; }\nbody, html,#map {width: 100%;height: 100%;overflow: hidden;margin:0;}</style>";
glbHtml::page('body');
?>

<?php if(@$act=='pick'){ ?>
<div id="bar" style="z-index:666;position:absolute; left:56px; top:5px; font-size:12px;">
  坐标:<input name="point" type="text" id="point" size="<?php echo $width; ?>" value="<?php echo "$point,$zoom";?>">
  <input type="submit" name="button" id="button" value="确定" onClick="setPoint()">
</div>
<?php } ?>
<div id="map" style="z-index:333;"></div>

<?php
require("$api.php"); 
?>

