<?php 
glbHtml::page('Hi, ApiServer/AppServer'); 
$text = comFiles::get(vopTpls::pinc("c_mod/info-read",'.txt')); 
$text = extMkdown::pdext($text);
include(vopTpls::pinc("c_mod/info-css",'.htm'));
?>
</head><body>

<div class="help_cont">
  <?php echo $text; ?>
  <h4>Sample</h4>
  <p align="center"><a href='?mod=info&act=sample&<?php echo safComm::signApi('init'); ?>'>Demo Code &gt;&gt;</a></p>
</div>

</body></html>
