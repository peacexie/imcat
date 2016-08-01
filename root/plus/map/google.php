<?php require_once('_config.php'); ?>
<!--
http://sunflowers.iteye.com/blog/743086
【zk开发】zk5.0.3中使用google 地图 v3
-->
<script>
var mpoint = new google.maps.LatLng(<?php echo "$pa[1],$pa[0]";?>);
var map = new google.maps.Map(jsElm.jeID("map"),{
	zoom:13, center:mpoint, scaleControl:true,
	navigationControlOptions:{style: google.maps.NavigationControlStyle.ZOOM_PAN},
	mapTypeId:google.maps.MapTypeId.ROADMAP
});
marker = new google.maps.Marker({
	map :  map,
	<?php if($act=='pick'){ ?>
	draggable : true,
	<?php }else{ ?>
	title : "我是文字标注哦",
	<?php } ?>
	position : mpoint
});
<?php if($act=='pick'){ ?>
google.maps.event.addListener(marker, "dragend", function(){
	var pstr = this.getPosition().toString();
	pstr = pstr.replace(' ','').replace('(','').replace(')','');
	var parr = pstr.split(',');
	var num1 = new Number(parr[1]);
	var num2 = new Number(parr[0]);
	jsElm.jeID('point').value = num1.toFixed(6)+','+num2.toFixed(6)+','+map.zoom; 
});
function setPoint(){
    parent.document.getElementById('<?php echo $title;?>').value =jsElm.jeID('point').value; 
    popClose(); //window.close();
}
window.onresize = function(){window.location.reload();} //jqInit('');
<?php }else{ ?>

<?php } ?>
</script>
</body>
</html>

