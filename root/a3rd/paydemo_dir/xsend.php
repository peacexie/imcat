<?php
require dirname(__FILE__)."/config.php";
?>
<!DOCTYPE html><html><head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<title><?php lang('a3rd.xsend_title',0); ?></title>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php?act=autoJQ"></script>
<script src="<?php echo PATH_ROOT; ?>/plus/ajax/comjs.php"></script>
<link rel='stylesheet' type='text/css' href='<?php echo PATH_SKIN; ?>/_pub/a_jscss/stpub.css'/>
<style type="text/css">

p.nav{ margin:auto auto 10px auto; text-align:center; }
div.out{ width:480px; margin:auto; }

</style>
</head>
<body>
<div id="topMargin" style="display:none; border:0px solid #999;"></div>
<div class="out">

<?php

$ptabs = exvOpay::getCfgs();
$stabs = " # \n"; $i=0;
foreach($ptabs as $k=>$v){
    if($i && !($i%3)) $stabs .= " <br># \n";
    $stabs .= "<a href='?paymode=$k'>$v[method]</a> # \n";
    $i++;
}

$_cbase['tpl']['tpl_dir'] = 'chn';
$kar = glbDBExt::dbAutID('coms_corder','yyyy-md-','32');
$order['cid'] = $order['title'] = $kar[0]; 
$order['cno'] = $kar[1];
$order['ordstat'] = 'new';
$order['atime'] = $_cbase['run']['stamp'];
$order['auser'] = 'uname';
$order['aip'] = $_cbase['run']['userip'];
$order['eip'] = comConvert::sysEncode($order['atime'].@$unqid);
$order['ordpay'] = req('paymode','paydemo');
$order['feetotle'] = 0.01;

$opay = exvOpay::getParas($order,'Webchn');
$opay['showurl'] = surl(0,'',1)."?"; 
$oadm = @$opay['a']; unset($opay['a']);

echo "<p class='nav'>$stabs</p>";
?>
<form id='fmopay' name='fmopay' method="post" action="<?php echo PATH_ROOT; ?>/a3rd/<?php echo $oadm['apidir']; ?>/uapi.php" target="_blank">
<p class="nav">
<?php foreach(array('title','feetotle') as $k){ ?>
<?php echo "[".$k."] = ".$order[$k]; ?><br>
<?php } ?>
</p>
<p class="nav">
<input type="submit" value="<?php lang('a3rd.xsend_send',0); ?>">
</p>
<?php foreach($opay as $k=>$v){ ?>
<input name="<?php echo $k; ?>" type="hidden" value="<?php echo $v; ?>">
<?php } ?>
</form>

</div>
</body>
</html>

<script>winAutoMargin('topMargin');</script>
