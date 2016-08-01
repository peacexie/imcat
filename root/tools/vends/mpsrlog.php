<?php
require(dirname(__FILE__).'/_config.php'); 

use Psr\Log\NullLogger;
use Monolog\Logger as ml;
use Monolog\Handler\StreamHandler;

// Psr\Log Test

$plog = new NullLogger();
$plog->log(1,'Error Message... ',array('a'));
echo "<br>... Psr\Log Test End<hr>";


// Monolog Test

$log = new ml('name');
$log->pushHandler(new StreamHandler(DIR_DTMP.'/debug/monolog.log', ml::WARNING));

$log->addWarning('Foo');
$log->addError('Bar');

echo 'Monolog\Logger ...';
@$a = 222/0;
$log->addWarning('AFoo');
$log->addError('BBar');
echo "<br>... Monolog Test End<hr>";


// basDebug
echo basDebug::runInfo();

?>
