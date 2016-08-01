<?php
require(dirname(__FILE__).'/_config.php'); 
use Vdemo\dir1\Test4;

echo Test4::func2(' 4.Vdemo\dir1\Test4 ');
echo "<br>... Vdemo\dir1\Test4 Test End<hr>";

// die();
register_shutdown_function('shutdown_handler_ys');

// basDebug
echo basDebug::runInfo();

?>
