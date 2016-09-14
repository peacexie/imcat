
<?php
(!defined('RUN_MODE')) && die('No Init');

$this->pimp();
$this->pimp('/jquery/jquery.passwordStrength.js',PATH_VENDUI);
$this->pimp('/skin/a_jscss/jstyle.css','');
?>

<h3>
<a href="http://www.helloweba.com/view-blog-50.html">密码强度检测：passwordStrength</a>
</h3>
<p>
  <label>请输入密码：</label>
  <input type="password" id="pass" />
  <span id="passwordStrengthDiv" class="psc_is00"></span> 
  <br />
  <label>确认密码：</label>
  <input type="password" id="repass" />
</p>
<p>
注意：id#passwordStrengthDiv的DIV是用来加载强度图片的，你也可以自定义ID，<br />
但调用时就要给参数赋值：targetDiv : '#ID' //自定义加载图片的ID<br />
</p>
<script>
$(function(){
	$('#pass').passwordStrength();
});
</script>
<div>
  <p>Powered by <a href="http://www.helloweba.com">www.helloweba.com</a></p>
</div>
