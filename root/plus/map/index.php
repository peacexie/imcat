<?php
require '_config.php'; 

glbHtml::page("{$api} ".lang('plus.map_title'),1);
eimp('initJs','jspop'.($act=='pick'?',jquery':''));
eimp($urls[$api]);
echo "<style type='text/css'>body {padding:0px; margin:0px; }
body, html,#map {width: 100%;height: 100%;overflow: hidden;margin:0;}</style>\n";
glbHtml::page('aumeta');
glbHtml::page('body');

?>

<?php if(@$act=='pick'){ ?>
<div id="bar" style="z-index:666;position:absolute; left:56px; top:5px; font-size:12px;">
  <?php lang('plus.map_piont',0); ?><input name="point" type="text" id="point" size="<?php echo $width; ?>" value="<?php echo $pshow;?>">
  <input type="submit" name="button" id="button" value="<?php lang('plus.map_setok',0); ?>" onClick="setPoint()">
</div>
<?php } ?>
<div id="map" style="z-index:333;"></div>

<?php

require "$api.htm"; 

/*
key : http://ditu.google.cn/maps/api/js?key=AIzaSyCz-pQkTS-XnB2l3kc9JeT-NICKxO8dc-g&sensor=false
'25.745,123.47' : 中国-钓鱼岛
'26.309,113.531' : 南湾-八亩
*/

?>
