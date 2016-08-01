<?php
require_once(dirname(__FILE__)."/config.php");
safComm::urlFrom();
$act = basReq::val('act');
$rndname = 'user_'.basKeyid::kidRand('24',8).'@domain.com';
$rndpass = 'pass_'.basKeyid::kidRand('fs3',18); 
$out_trade_no = basReq::val('out_trade_no');
$total_fee = basReq::val('total_fee');
if($act=='dologin'){
	$fm = $_POST['fm'];
	$re2 = safComm::formCAll('fmlpay');
	if(empty($re2[0])){ //OK
		$msg = '登录OK！';
		dmdoSend();
	}else{
		$msg = '错误！请复制重新登录';	
	}
}else{
	$msg = '请复制到左边框模拟登录';	
}
?>
<!DOCTYPE html><html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<title>支付流程演示</title>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php"></script>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=autoJQ"></script>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_ROOT; ?>/skin/a_jscss/stpub.css'/>
<style type="text/css">
.pgu_login{ width:640px; border:1px solid #CCC; padding:2px; margin:10px auto; font-size:14px; }
.pgu_login p{ padding:5px; line-height:150%; }
.pgu_login p.button{ padding:0 50px; }
.pgu_login .apply{ width:280px; padding:10px 1px; float:right; }
.pgu_login .apply p{ padding:5px; }
.pgu_login .apply a{ cursor:pointer; }
.pgu_login p.title{ border-bottom:1px solid #CCC; padding-left:20px; }
.pgu_login .login{ width:350px; padding:10px 1px; float:left; }
.login i { font-style:normal; display:inline-block; width:65px; padding-left:10px; }
</style>
<link rel="shortcut icon" href="<?php echo PATH_ROOT; ?>/skin/a_img/favicon.ico" />
</head>
<body>
<div id="topMargin" style="display:none; border:0px solid #999;"></div>

<form action="?" method="post" name="fmlpay" id="fmlpay">
<div id="idx_login" class="pgu_login">
<p class="title">
	<span style="float:right">单号：<?php echo $out_trade_no; ?> &nbsp; 金额：<?php echo $total_fee; ?></span>
    <b>支付流程演示</b>
</p>
<div class="apply">
  <p> 模拟账号： <br>
    <a onClick="osetInfo(this,'uname')"><?php echo $rndname; ?></a> <br>
    模拟密码： <br>
    <a onClick="osetInfo(this,'upass')"><?php echo $rndpass; ?></a></p>
  <p> <?php echo $msg; ?><br>包含前面的user_,pass_，不含空格。 </p>
</div>
<div class="login">
  <p> <i>用户名: </i>
    <input id="fm[uname]" name="fm[uname]" tabindex="1" type="text" value="" class="txt w250" reg="key:2-48" autocomplete="off" tip="字母开头,允许3-16字节<br>允许字母数字下划线" />
  </p>
  <p> <i>密　码: </i>
    <input id="fm[upass]" name="fm[upass]" tabindex="2" type="password" value="" class="txt w250" reg="str:6-48" autocomplete="off" tip="允许6,15字节" />
  </p>
  <p> <i>认证码: </i>
    <script>fsInit('fmlpay');</script>
  </p>
  <p class="button"> <i class="right pt2 f14"><a href="index.php">刷新</a></i> 
    <input name="submit" value="提交" tabindex="19830" type="submit" class="btn" />
    <input name="act" type="hidden" value="dologin" />
  </p>
</div>
<div class="clear"></div>
</div>
<?php 
foreach($_POST as $k=>$v){ 
	if(is_array($v)) continue;
	$v = basReq::fmt($v,'','Safe4');
	echo "<input name='_post_{$k}' type='hidden' value='{$v}'>\n"; 
	if(in_array($k,array('out_trade_no','total_fee'))){
		echo "<input name='{$k}' type='hidden' value='{$v}'>\n";
	}
} 
?>
</form>
<script>
function ologinreset(){
	$("[name='fm[uname]']").val('');
	$("[name='fm[upass]']").val('0');
	$("[name='fm[upass]']").val('');
}
function osetInfo(e,key){
	$("[name='fm["+key+"]']").val($(e).html());
}
setTimeout('ologinreset()',300);
</script>

</body>
</html>

<script>winAutoMargin('topMargin');</script>