<?php
$cmsg = '404 Not Found';
header('HTTP/1.1 '.$cmsg);
header('Status:'.$cmsg);
?>
<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>404 Not Found</title>
<meta name='robots' content='noindex, nofollow'>
</head>
<body style="margin:10px">
<h1>HTTP 404 Not Found</h1>
<p>The requested URL was not found on this server.</p>
<hr>
<p>More Infomation you can find:</p>
<ul>
  <li>Go to [<a href="/" target="_blank">Homepage</a>].</li>
  <li>Find@baidu.com [<a href="http://www.baidu.com/s?wd=HTTP 404 Error" target="_blank">HTTP 404 Error</a>].</li>
  <li>Find@google.com [<a href="http://www.google.com.hk/search?q=HTTP 404 Error" target="_blank">HTTP 404 Error</a>].</li>
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
