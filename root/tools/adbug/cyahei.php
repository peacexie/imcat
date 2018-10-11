<?php    
$_cbase['run']['outer'] = 1;    
require __DIR__.'/_config.php';    

$_locfp = '/ximp/files/check_yahei.imp_php';
if(!is_file(DIR_STATIC.$_locfp)){
    $_dowurl = 'https://github.com/peacexie/imcat/raw/patches/excode/imp_files.rar';
    die("Please down the file `$_locfp` <br>\nfrom `$_dowurl`<br>\n");
}
require DIR_STATIC.$_locfp;
