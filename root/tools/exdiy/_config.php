<?php
namespace imcat;
//if(empty($_cbase['skip']['_paths'])){
    include dirname(dirname(dirname(__FILE__))).'/run/_init.php';
//}

// idea From : Symfony : config.php
if(!isset($_SERVER['HTTP_HOST'])){
    exit('This script cannot be run from the CLI. <br>Please run it from a browser.');
}
if(!basEnv::isLocal())
{
    header('HTTP/1.0 403 Forbidden');
    $msg = "This script is only accessible from: <br>\n";
    $msg .= "localhost (10.*, 127.*, 192.*, ::1, FE80:*, FEC0:*).";
    exit($msg);
}
