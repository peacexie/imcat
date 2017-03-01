<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>AppServer说明</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name='robots' content='noindex, nofollow'>
<style type="text/css">
  #help_cont{ max-width:760px; line-height:150%; margin:10px auto 10px auto; }
  h3{ text-align: center; }
  #help_cont h4 { display: block; padding: 10px 10px 1px 10px; margin: 10px 10px 1px 40px; border-top: 1px solid #CCC; }
  h4:before { display: inline-block; content:"◎◎"; color:#036; }
</style>
</head><body>

<div id="help_cont">
  <?php 
  $text = comFiles::get(vopTpls::pinc("c_mod/info-read",'.txt')); 
  $text = extMkdown::pdext($text);
  echo $text; 
  ?>
</div>

</body></html>
