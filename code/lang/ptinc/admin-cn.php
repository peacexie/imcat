<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?>

中<?php echo $uarr['hname'] ?>

<?php break;case 'loginread': ?> 

<li>推荐浏览器: IE9+, Chrome, Firefox ；</li>
<li>第一次使用，请查看 《<a href='<?php echo surl('uio') ?>' target="_blank">管理帮助文件</a>》>>>；</li>
<li>帐号密码区分大小写;帐号>=2位,密码>=5位；</li>
<li>认证码不分大小写,如错误,请刷新登陆；</li>
<li>技术支持: Peace(txmao.txjia.com).</li>

<?php break;case 'adminread': ?> 

<li>推荐浏览器: IE9+, Chrome, Firefox ；</li>
<li>第一次使用，请查看 《<a href='?help' target="_blank">管理帮助文件</a>》>>>；</li>
<li>帐号密码区分大小写;帐号>=2位,密码>=5位；</li>
<li>认证码不分大小写,如错误,请刷新登陆；</li>
<li>技术支持: Peace(txmao.txjia.com).</li>

<?php break;case 'xxx': ?>  

<?php } ?>