<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)'); 

$view = empty($view) ? 'list' : $view;
$fs_do = basReq::val('fs_do');
$fs = basReq::arr('fs'); 
#$fm = basReq::arr('fm');

$msg = ''; $cnt = 0; 
