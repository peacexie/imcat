
<?php
(!defined('RUN_INIT')) && die('No Init');
$this->pimp();
$this->pimp('/zoeDylan/zoeDylan-1.0.1.min.js','vendui');
$this->pimp('/zoeDylan/zoeDylan-1.0.1.min.css','vendui');
?>

<center>
  <span style="font-size:15px; font-weight:bold; text-align:center; line-height:25px; color:#000; width:100%">jquery焦点图插件zoeDylan.ImgChange-1.0.1<br />
  <a href="http://www.jq-school.com" target="_blank" style="color:#000">JquerySchool</a>网站出品（<a href="http://www.jq-school.com" style="color:#000" target="_blank">http://www.jq-school.com</a>） <br />
   <a href="http://api.jq-school.com/" target="_blank" style="color:#000">在线API帮助文档</a> <br />
  <a target="_blank" href="http://wp.qq.com/wpa/qunwpa?idkey=6fcb83942dc3630777ae7745bd5093a1a5917f915f4e95cfc498633379ebfbb4">官方网站学习交流QQ群<img border="0" src="http://pub.idqqimg.com/wpa/images/group.png" style="width:90px; height:22px;" alt="Jquery学堂QQ⑤群" width="90" height="22" title="Jquery学堂QQ⑤群"></a></span>
</center>

<div id="img" ></div>
<hr />
<div id="img2"></div>
<hr />
<div id="img3"></div>
    
<script>
var a_imgs = new Array(//插入图片地址
	'http://ww1.sinaimg.cn/mw690/adde8400gw1ebwz3igq33j20fk08pwfu.jpg',
	'http://ww1.sinaimg.cn/mw690/adde8400gw1ebvqqzln0uj20ex09774z.jpg',
	'http://ww2.sinaimg.cn/mw690/adde8400gw1ebn1vuhpzcj20k00agq4v.jpg',
	'http://ww4.sinaimg.cn/mw690/adde8400gw1ebn1vqhh67j20k00aggo0.jpg'
),
a_links = new Array(//点击图片跳转的网址
	'www.jq-school.com',
	'www.jq-school.com',
	'www.jq-school.com',
	'www.jq-school.com'
),
a_tips = new Array(//鼠标停靠的提示
	'百度',
	'腾讯',
	'谷歌',
	'中关村'
);
$(function () {
	$('#img').zoeDylan_ImgChange({
		background: '#f60', //前景色
		color: '#fff', //高
		height: '300px',//宽
		width: '500px',//图片地址数组
		imgLinks:a_imgs,//图片内容
		imgCont:a_tips,//图片提示
		imgTips: a_tips,//图片点击
		imgClick:a_links,//是否等比例缩放
		isConstrain: true,//是否开启自动切换
		isAutoChange: true,//是否显示控制器
		isControl: true,//是否显示图片信息
		isInfo: true,//是否开启图片点击事件
		isClick: true,//是否显示缩略图控件
		isThumbnail: true,//自动切换时间(毫秒)
		timer: 1500,//切换速度(毫秒)
		speed: 300,
		direction: 'l'
	});
	$('#img2').zoeDylan_ImgChange({
		background: 'none', //前景色
		color: '#fff', //高
		height: '300px',//宽
		width: '100%',//图片地址数组
		imgLinks: a_imgs,//图片内容
		imgCont: a_tips,//图片提示
		imgTips: new Array(),//图片点击
		imgClick: new Array(),//是否等比例缩放
		isConstrain: false,//是否开启自动切换
		isAutoChange: true,//是否显示控制器
		isControl: true,//是否显示图片信息
		isInfo: false,//是否开启图片点击事件
		isClick: false,//是否显示缩略图控件
		isThumbnail: false,//自动切换时间(毫秒)
		timer: 1200,//切换速度(毫秒)
		speed: 800,
		direction:''
	});
	$('#img3').zoeDylan_ImgChange({
		background: 'none', //前景色
		color: '#fff', //高
		height: '100px',//宽
		width: '300',//图片地址数组
		imgLinks: a_imgs,//图片内容
		imgCont: a_tips,//图片提示
		imgTips: new Array(),//图片点击
		imgClick: new Array(),//是否等比例缩放
		isConstrain: false,//是否开启自动切换
		isAutoChange: true,//是否显示控制器
		isControl: true,//是否显示图片信息
		isInfo: false,//是否开启图片点击事件
		isClick: false,//是否显示缩略图控件
		isThumbnail: false,//自动切换时间(毫秒)
		timer: 1200,//切换速度(毫秒)
		speed: 800,
		direction: ''
	});
});
</script>