<?php require_once('_config.php'); ?>
<!--
http://developer.baidu.com/map/jsdemo.htm#h0_4
示例DEMO
-->
<script>
var mpoint = new BMap.Point(<?php echo "$pa[0],$pa[1]";?>);
var map = new BMap.Map("map");
map.centerAndZoom(mpoint, <?php echo $zoom;?>);
map.addControl(new BMap.NavigationControl({type:BMAP_NAVIGATION_CONTROL_ZOOM})); //缩放
map.addControl(new BMap.ScaleControl()); // 比例尺
map.addControl(new BMap.MapTypeControl());   //添加地图类型控件
map.enableScrollWheelZoom();
var marker = new BMap.Marker(mpoint);  
map.addOverlay(marker); 
<?php if($act=='pick'){ ?>
marker.enableDragging(); //可拖拽
marker.addEventListener("dragend", showPoint);
function showPoint(e){
  jsElm.jeID('point').value = e.point.lng + "," + e.point.lat+','+map.getZoom(); 
}
function setPoint(){
  parent.document.getElementById('<?php echo $title;?>').value =jsElm.jeID('point').value; 
  popClose(); //window.close();
}
<?php }else{ ?>
var label = new BMap.Label("<?php echo $title;?>",{offset:new BMap.Size(20,-10)});
marker.setLabel(label);
<?php } ?>
</script>
</body>
</html>

