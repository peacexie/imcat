<?php
require(dirname(__FILE__).'/_config.php'); 

use Symfony\Component\Stopwatch\Stopwatch;

$stopwatch = new Stopwatch();

// Start event named 'eventName'
$stopwatch->start('eventName');

// ... some code goes here
$a = 2000;
$b = $a*1234;
$s = '';
for($i=0;$i<102400;$i++){
	$s .= '123456789-123456789-123456789-12';
}

$event = $stopwatch->stop('eventName');
dump($event);

echo "<br>:getCategory:".$event->getCategory();   // Returns the category the event was started in
echo "<br>:getOrigin:".$event->getOrigin();     // Returns the event start time in milliseconds
echo "<br>:ensureStopped:".$event->ensureStopped(); // Stops all periods not already stopped
echo "<br>:getStartTime:".$event->getStartTime();  // Returns the start time of the very first period
echo "<br>:getEndTime:".$event->getEndTime();    // Returns the end time of the very last period
echo "<br>:getDuration:".$event->getDuration();   // Returns the event duration, including all periods
echo "<br>:getMemory:".$event->getMemory();     // Returns the max memory usage of all periods

$stopwatch->start('eventName', 'categoryName');
dump($event);

// basDebug
echo basDebug::runInfo();

?>
