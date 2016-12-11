
<?php
(!defined('RUN_INIT')) && die('No Init');
$this->pimp();
$this->pimp('/jquery/jq_imgChange.js','vendui');
?>

<style type="text/css">
p { padding:0px; margin:0px; }
div { margin:10px; border:1px solid #CCF; }
</style>


<div id="test21" style="width:150px; height:80px; overflow:hidden">
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2008.jpg' width="120" height="60" />logo-2008.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2009.jpg' width="120" height="60" />logo-2009.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2010.jpg' width="120" height="60" />logo-2010.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2011.jpg' width="120" height="60" />logo-2011.jpg</p>
</div>	

<div id="test22" style="width:150px; height:80px; overflow:hidden">
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2008.jpg' width="120" height="60" alt="08" />logo-2008.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2009.jpg' width="120" height="60" alt="09" />logo-2009.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2010.jpg' width="120" height="60" alt="10" />logo-2010.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2011.jpg' width="120" height="60" alt="11" />logo-2011.jpg</p>
</div>

<div style="width:130px; height:120px; overflow:hidden">
<div id="test23">
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2008.jpg' width="120" height="60" />logo-2008.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2009.jpg' width="120" height="60" />logo-2009.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2010.jpg' width="120" height="60" />logo-2010.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2011.jpg' width="120" height="60" />logo-2011.jpg</p>
</div>
</div>

<div style="width:130px; height:120px; overflow:hidden">
<div id="test24">
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2008.jpg' width="120" height="60" alt="08" />logo-2008.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2009.jpg' width="120" height="60" alt="08" />logo-2009.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2010.jpg' width="120" height="60" alt="10" />logo-2010.jpg</p>
<p><img src='<?php echo PATH_SKIN; ?>/_pub/logo/_pub/logo-2011.jpg' width="120" height="60" alt="11" />logo-2011.jpg</p>
</div>
</div>

<script>

$('#test21 p').imgChange({});
// fade,turnDown,zoom,flipper,
$('#test22 p').imgChange({effect:'turnDown',changeTime:2000,speed:400,vertical:0,visible:0});
$('#test23 p').imgChange({effect:'wfScroll',steps:2,changeTime:50,vertical:0,wrapSize:180});
$('#test24 p').imgChange({effect:'wfScroll',steps:2,changeTime:50,vertical:1});
//,afterEnd:function(){jsLog('');}
function aftFunc(e){
	jsLog(e);
}
/*
$('#test22 li').imgChange({botPrev:'#prev1',botNext:'#next1',effect:'scroll',vertical:0});
$('#tab-con2 ul').imgChange({thumbObj:'#tab-tit2 li',autoChange:0,speed:0})
$('#tab-con3 ul').imgChange({thumbObj:'#tab-tit3 li',autoChange:0,speed:0})
$('#tab-con4 ul').imgChange({thumbObj:'#tab-tit4 li',autoChange:0,speed:0})
$('#tab-con .tj').imgChange({thumbObj:'#tab-tit li',speed:0,autoChange:0})

 * $('#bigimg li').imgChange({
 * thumbObj: '.tlist li',//缩略图对象;
 * botPrev: '.prev',//上一个对象;
 * botNext: '.next',//下一个对象;
 * effect: 'fade',//切换效果,效果有fade,scroll(滚动),wfScroll(无缝滚动steps:2,changeTime:50)，wb(微博),stream,turnDown,zoom,flyFade,tab,flipper,slide,cutIn,alternately
 * curClass: 'act',//当前缩略图对象的样式名
 * thumbOverEvent: 1,//鼠标悬停是否切换
 * speed: 400,//切换速度
 * autoChange: 1,//是否自动切换
 * changeTime: 5000,//自动切换时间
 * delayTime: 0,//鼠标悬停的延迟时间
 * showTxt: 0,//是否显示标题,标题调用img里的alt值
 * visible:1,//显示对象的个数
 * steps:1, //每次滚动的数量，effect==wfscroll时，每次滚动的距离
 * circular: 0,//是否循环滚动
 * vertical:1,//方向
 * easing: 'swing'//动画效果,需要easing插件支持
 * wrapSize:0,无缝滚动的外层宽度
 * beforeStart:function(){$('.txt').hide},效果执行前的函数
 * afterEnd:null,效果完成后的函数
 *})
	
*/

</script>
