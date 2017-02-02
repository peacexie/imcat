<?php
require_once(dirname(__FILE__)."/config.php");
$cfg = array('ordid','feeamount','apino','status','msg');
foreach($cfg as $key){ 
    empty($res[$key]) && $res[$key] = '';  
}
defined('PATH_ROOT') || define('PATH_ROOT','../../');
?>
<!DOCTYPE html><html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<title><?php lang('a3rd.xresult_title',0); ?></title>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php"></script>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=autoJQ"></script>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_SKIN; ?>/_pub/a_jscss/stpub.css'/>
<style type="text/css">
.pay_info{ width:640px; border:1px solid #CCC; padding:10px; margin:10px auto; font-size:14px; }

.pay_info .main{ padding:10px 1px 10px 1px; border-bottom:1px dashed #CCCCCC; margin:0px auto 5px auto; }
.pay_info .main i{ display:inline-block; width:120px; }
.pay_info p{ padding:2px 5px; margin:2px 5px; }
.pay_info i{ font-style:normal; }

.pay_info .deget{ width:305px; padding:5px 1px; float:left; }
.pay_info .depost{ width:305px; padding:5px 1px; float:right; }

p.title{ border-bottom:1px solid #CCC; padding:2px 5px 10px 20px; }
p.detail{ width:100%; height:180px; overflow-y:scroll; }

</style>
</head>
<body>
<div id="topMargin" style="display:none; border:0px solid #999;"></div>

<div class="pay_info">

<p class="title">
    <span style="float:right"><?php lang('a3rd.xresult_ordno',0); ?> <?php echo $res['ordid']; ?></span>
    <b><?php lang('a3rd.xresult_title',0); ?></b>
</p>

<div class="main">
  <p><span class="right">@<?php echo @$res['api']; ?></span> <i><?php lang('a3rd.xresult_sysno',0); ?> </i>
  <?php echo $res['ordid']; ?>
  </p>
  <p><span class="right"><?php echo @$res['stamp']; ?></span> <i><?php lang('a3rd.xresult_amount',0); ?> </i>
  <?php echo $res['feeamount']; ?>
  </p>
  <p> <i><?php lang('a3rd.xresult_tradeno',0); ?> </i>
  <?php echo $res['apino']; ?>
  </p>
  <p> <i><?php lang('a3rd.xresult_state',0); ?> </i>
  <?php echo $res['status'].'('.$res['msg'].')'; ?>
  </p>
</div>
<div class="clear"></div>

<div class="depost">
  <p class="fB"> POST DATA：</p>
  <p class="detail"> 
  <?php
  foreach($_POST as $key=>$val){
     echo "• $key = $val<br>";
  }?>
  </p>
</div>
<div class="deget">
  <p class="fB"> GET DATA：</p>
  <p class="detail"> 
  <?php
  foreach($_GET as $key=>$val){
     echo "• $key = $val<br>";
  }?>
  </p>
</div>
<div class="clear"></div>

</div>

</body>
</html>

<script>winAutoMargin('topMargin');</script>
