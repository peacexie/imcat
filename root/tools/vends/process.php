<?php
require(dirname(__FILE__).'/_config.php'); 

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// linux: 'ls -lsa'
// windows: 'dir'
$process = new Process('dir');
$process->run();

// executes after the command finishes
if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

echo $process->getOutput();

// basDebug
echo basDebug::runInfo();

?>
