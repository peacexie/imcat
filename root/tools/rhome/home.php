<?php
(!defined('RUN_MODE')) && die('No Init');
$_cbase['tpl']['tpl_dir'] = 'chn';
$isMobile = basEnv::isMobile();
$vcfg = vopTpls::etr1('tpl'); 
$qrActs = $isMobile ? '' : "qrActs();"; 
?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<?php glbHtml::page('imvop'); ?>
<script src="<?php echo PATH_ROOT; ?>/tools/rhome/hfunc.js?v12"></script>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_ROOT; ?>/tools/rhome/hstyle.css?v12'/>
<title><?php echo $_cbase['sys_name']; ?> - 通用PHP建站系统 </title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="<?php echo $_cbase['sys_name']; ?>,轻量免费,开源共享,PHP CMS,微信接口" />
<meta name="description" content="<?php echo $_cbase['sys_name']; ?>是一款轻量、免费、共享的通用PHP网站应用系统；PC版-手机版-微信接口一站搞定！" />
<style type="text/css">
div.outer { 
	background-image:url(<?php echo PATH_STATIC; ?>/media/cover/green_02_1024x576.jpg); 
}
<?php if($isMobile){ ?>
nav { margin:10px auto 10px auto; text-align:center; }
nav a { margin:5px 5px 5px 5px; }
p.logo, h1.title { left:auto; top:auto; position:relative; display:block; clear:both; margin:10px auto 10px auto; }
<?php } ?>
</style>
</head>
<body>
<?php glbHtml::ieLow_html(); ?>

<p class="ptop" style="display:none;">
</p>
<div class="outer" id="outer">
	<nav id="nav">
      <?php foreach($vcfg as $k=>$v){ ?>
      <a href="<?php echo PATH_PROJ.$v[1]; ?>" class="qrcode_tip"><?php echo $v ? "($k)$v[0]" : ''; ?><i class="qrcode_pic" id="qrcode_pic<?php echo $k; ?>" style="display:none;"></i></a>
      <?php } ?>
    </nav>
    <p class="logo"><img src="<?php echo PATH_ROOT; ?>/skin/a_img/logo120x60.jpg" width="120" height="60" ></p>
    <h1 class="title"><b><?php echo $_cbase['sys_name']; ?></b></h1>
    
    <div class="vnote">
        ● 管理中心
         --- 您的网站您做主！<br>
        ● 中文版 
         --- 传统经典PC主要展示版本<br>
        ● 演示版 
         --- 为您指点迷津;二次开发DIY!<br>
        ● 手机版 
         --- 精简移动端展示模块<br>
        ● 会员中心
         --- 用户:订单/公文/问答
    </div>
    
    <p class="foot">
    [<a href="?start">Start</a>] # 
     <a href="tencent://message/?uin=80893510&Site=贴心猫&Menu=yes">QQ</a> # 
     <a href="mailto:xpigeon#163.com">E-mail</a>
    [<a class="qrcode_home" _url='<?php echo PATH_PROJ; ?>'>Scan<i class="qrcode_hpic" id="qrcode_pichome" style="display:none;"></i></a>]
    <br>
    Copyright © <?php echo $_cbase['sys_name']; ?> 
    </p>
</div>

<script type="text/javascript">
<?php echo "var _burl = '{$_cbase['run']['rsite']}'\n"; ?>
$(function(){ winReset(); <?php echo $qrActs; ?> });
$(window).resize(function(){ winReset(); });
</script>
<?php echo "<!--".basDebug::runInfo()."-->"; ?>

</body>
</html>
