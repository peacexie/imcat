<?php
$url = 'review.php?';
foreach($_GET as $key => $value)
{
	$url .= $key . '=' . $value . '&';
}
?>
<html>
  <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>PayPal Demo Portal</title>
      <!--Including Bootstrap style files-->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
  </head>
  <body>
      <div class="container-fluid">
      <div class="row-fluid">
      <div class="span4">
      </div>
      <div class="span5">
<div class="row text-center"><h3>Loading...</h3></div>
<script type="text/javascript">
if (window!=top){top.location.href='<?php echo $url ?>';} //lightbox return
else
window.location.href='<?php echo $url ?>';  //return from full page paypal flow
</script>
</body>
</html>