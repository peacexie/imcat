<?php    
$_cbase['run']['outer'] = 1;

if(file_exists(dirname(__FILE__).'/start-360.php')){
  include dirname(dirname(dirname(__FILE__))).'/run/_init.php';
}else{
  require dirname(__FILE__).'/_config.php';  
}


$_locfp = '/ximp/files/adminer.imp_php';
require DIR_STATIC.$_locfp;
