<?php
if(empty($_cbase['skip']['_paths'])){
    include(dirname(dirname(dirname(__FILE__))).'/run/_init.php');
}

// idea From : Symfony : config.php
if(!isset($_SERVER['HTTP_HOST'])){
    exit('This script cannot be run from the CLI. <br>Please run it from a browser.');
}
if(!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))){
    header('HTTP/1.0 403 Forbidden');
    exit('This script is only accessible from: <br>localhost (127.0.0.1, ::1).');
}

