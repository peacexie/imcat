<?php
$_cbase['run']['outer'] = 1;
require __DIR__.'/_config.php';  


function adminer_object() {
    class AdminerMysql extends Adminer {
        function name() {
            // custom name in title and heading
            $url = 'https://www.adminer.org/';
            return "<a href='$url' title='Adminer for Imcat' target='0'>Imcat-DBA</a>";
        }
        function xhead() { // headers,csp,head() 
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

