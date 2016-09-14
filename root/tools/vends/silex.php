<?php
require(dirname(__FILE__).'/_config.php'); 


echo basDebug::runInfo();


$app = new Silex\Application();
$app['debug'] = true;

$app->get('/', function() {
	return 'Silex Start! - <a href="silex.php/hello/Silex?aaa=bbb">hello</a>';
});

$app->get('/hello/{name}', function($name) use($app) {
	//$aaa = $app->get('aaa'); echo $aaa;
	#$a = 333/0;
    return 'Hello '.$app->escape($name).'! - <a href="../../silex.php">[home]</a>';
});
/*
$app->get('/hello/{name}', function($name='Silex') use($app) {
	$name = $app->escape($name);
	return 'Hello '.$name.'! - <a href="../silex.php">[home]</a>';
});*/

$app->run();

dump($app);

// basDebug
echo basDebug::runInfo();
