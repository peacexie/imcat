<?php
(!defined('RUN_MODE')) && die('No Init'); 
$cmsg = 'Site Closed!';
$sname = urlencode($_cbase['sys_name']);
?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>Site Closed!</title>
<meta name='robots' content='noindex, nofollow'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
</head>
<body style="margin:10px">
<h1>Site Closed!</h1>
<p>Hi, <?php echo lang('core.cfg_close'); ?></p>
<hr>
<p>More Infomation you can find:</p>
<ul>
  <li>Find@ [<a href="http://www.baidu.com/s?wd=<?php echo $sname; ?>" target="_blank">baidu.com</a>].</li>
  <li>Find@ [<a href="http://www.google.com.hk/search?q=<?php echo $sname; ?>" target="_blank">google.com</a>].</li>
</ul>
<hr>
<footer>
<?php
echo "\n<li>".date('Y-m-d H:i:s').'</li>';
echo "\n<li>".$_SERVER['SERVER_SOFTWARE'].' PHP/'.PHP_VERSION.'</li>';
?>
</footer>
</body>
</html>
<?php
die(); //date('Y-m-d H:i:s')
?>
