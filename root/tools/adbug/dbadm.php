<?php
$_cbase['run']['outer'] = 1;
#require dirname(dirname(__DIR__)).'/run/_init.php';
require __DIR__.'/_config.php';  

$_ufile = empty($_GET['_ufile']) ? '' : $_GET['_ufile'];
if($_ufile=='jsCheck'){
    //
}

/*
$_uname = empty($_GET['username']) ? '' : $_GET['username']; 
$_db = empty($_GET['db']) ? '' : $_GET['db']; // username=root&db=cms_imcat
$_file = empty($_GET['file']) ? '' : $_GET['file'];
*/

function adminer_object() {
    class AdminerMysql extends Adminer {
        function name() {
            // custom name in title and heading
            return '<a href="https://www.adminer.org/" title="Adminer for Imcat" target="0">Imcat</a>';
        }
        function head() { // headers,csp,head() 
            #echo "<script src='dbadm.php?_ufile=jsCheck'></script>";
        }
        function xlogin($login, $password) {
            //return 'xxx';
        }
    }
    return new AdminerMysql;
}


// line:71:if(!ini_bool("session.use_cookies")&&!session_id())
$_locfp = '/ximp/files/adminer.imp_php';
require DIR_STATIC.$_locfp;

