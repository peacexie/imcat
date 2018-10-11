<?php
$_cbase['run']['outer'] = 1;

if(file_exists(__DIR__.'/start-360.php')){
  include dirname(dirname(__DIR__)).'/run/_init.php';
}else{
  require __DIR__.'/_config.php';  
}

//session_destroy();
$_locfp = '/ximp/files/adminer.imp_php';
require DIR_STATIC.$_locfp;

