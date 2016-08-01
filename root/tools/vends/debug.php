<?php
require(dirname(__FILE__).'/_config.php'); 
error_reporting(E_ALL); 

use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

//
Debug::enable();
ErrorHandler::register();
ExceptionHandler::register();
//*//

$a = 2/0;
//$a = bb::cc();
dump($a);

// basDebug
echo basDebug::runInfo();

?>
