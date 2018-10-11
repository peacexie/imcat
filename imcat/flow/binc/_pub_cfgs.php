<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 

$view = empty($view) ? 'list' : $view;
$fs_do = req('fs_do');
$fs = basReq::arr('fs'); 
#$fm = basReq::arr('fm');

$msg = ''; $cnt = 0; 
