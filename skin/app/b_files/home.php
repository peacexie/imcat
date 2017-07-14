<?php 
glbHtml::page('Hi, ApiServer/AppServer'); 
$text = comFiles::get(vopTpls::pinc("c_mod/info-read",'.txt')); 
$text = extMkdown::pdext($text);
$text = str_replace('{sign}',safComm::signApi('init'),$text);
include(vopTpls::pinc("c_mod/info-css",'.htm'));
?>
</head><body>

<div class="help_cont">
  <?php echo $text; ?>
</div>

<hr>
<p class="pc"><?php
echo basDebug::runInfo(); 
?></p>

</body></html>
