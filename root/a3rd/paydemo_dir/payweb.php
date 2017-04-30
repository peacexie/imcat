<?php
require_once(dirname(__FILE__)."/config.php");
safComm::urlFrom();
$act = req('act');
$rndname = 'user_'.basKeyid::kidRand('24',8).'@domain.com';
$rndpass = 'pass_'.basKeyid::kidRand('fs3',18); 
$out_trade_no = req('out_trade_no');
$total_fee = req('total_fee');
if($act=='dologin'){
    $fm = $_POST['fm'];
    $re2 = safComm::formCAll('fmlpay');
    if(empty($re2[0])){ //OK
        $msg = lang('a3rd.payweb_loginok');
        dmdoSend();
    }else{
        $msg = lang('a3rd.payweb_erragain');    
    }
}else{
    $msg = lang('a3rd.payweb_copyto');    
}
?> 
<!DOCTYPE html><html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<title><?php lang('a3rd.demo_title',0); ?></title>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=autoJQ"></script>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php"></script>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_SKIN; ?>/_pub/a_jscss/stpub.css'/>
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
#evf_vtip{ display:none; }
</style>
<link rel="shortcut icon" href="<?php echo PATH_SKIN; ?>/_pub/logo/favicon.ico" />
</head>
<body>
<div id="topMargin" style="display:none; border:0px solid #999;"></div>

<form action="?" method="post" name="fmlpay" id="fmlpay">
<div id="idx_login" class="pgu_login">
<p class="title">
    <span style="float:right"><?php lang('a3rd.payweb_tradeno',0); ?> <?php echo $out_trade_no; ?> &nbsp; <?php lang('a3rd.payweb_amount',0); ?> <?php echo $total_fee; ?></span>
    <b><?php lang('a3rd.demo_title',0); ?></b>
</p>  
<div class="apply">
  <p> <?php lang('a3rd.payweb_demoid',0); ?> <br>
    <a onClick="osetInfo(this,'uname')"><?php echo $rndname; ?></a> <br>
    <?php lang('a3rd.payweb_demopw',0); ?> <br>
    <a onClick="osetInfo(this,'upass')"><?php echo $rndpass; ?></a></p>
  <p> <?php echo $msg; ?><br><?php lang('a3rd.payweb_idpwtip',0); ?></p>
</div>
<div class="login">
  <p> <i><?php lang('uname',0); ?>: </i>
    <input id="fm[uname]" name="fm[uname]" tabindex="1" type="text" value="" class="txt w250" reg="key:2-48" autocomplete="off" tip="<?php lang('a3rd.payweb_letters316',0); ?>" />
  </p>
  <p> <i><?php lang('upass',0); ?>: </i>
    <input id="fm[upass]" name="fm[upass]" tabindex="2" type="password" value="" class="txt w250" reg="str:6-48" autocomplete="off" tip="<?php lang('a3rd.payweb_letters615',0); ?>" />
  </p>
  <p> <i><?php lang('vcode',0); ?>: </i>
    <script>fsInit('fmlpay','5,-32','txt w80');</script>
  </p>
  <p class="button"> <i class="right pt2 f14"><a href="index.php"><?php lang('a3rd.payweb_refresh',0); ?></a></i> 
    <input name="submit" value="<?php lang('submit',0); ?>" tabindex="19830" type="submit" class="btn" />
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