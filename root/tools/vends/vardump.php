<?php
require(dirname(__FILE__).'/_config.php'); 
use Symfony\Component\VarDumper\VarDumper;

VarDumper::dump($_cbase);
dump($_cbase);

// basDebug
echo basDebug::runInfo();

?>
