<?php
// 

$_sy_updvnow['selfcfgs'] = array(

    'cfgs/boot/cfg_db.php',
    'cfgs/boot/const.cfg.php',

    'cfgs/excfg/ex_paras.php',
    'cfgs/sycfg/sy_vopcfg.php',
    
    'cfgs/scfile/sc_fadm.php',
    'cfgs/boot/_paths.php',
    
);

$_sy_updvnow['compcfgs'] = array(

    'cfgs/boot/cfg_adbug.php',    
    'cfgs/boot/cfg_load.php',
    
    'cfgs/excfg/ex_a3rd.php',
    'cfgs/excfg/ex_ipstop.php',
    'cfgs/excfg/ex_mail.php',
    'cfgs/excfg/ex_outdb.php',
    'cfgs/excfg/ex_sfdata.php',
    'cfgs/excfg/ex_sms.php',

    'cfgs/sycfg/sy_keepid.php',
    'cfgs/sycfg/sy_urdirs.php',
    'cfgs/excfg/sy_wmark.php',

);

$_sy_updvnow['dellist'] = array(
    
    # - delete list ~ 2016-05-18
    'root/:.htaccess,robots.txt',
    'root/run/:chn.php,dev.php,mob.php',
    'root/tools/setup/update.php',
    'readme.htm',
);

$_sy_updvnow['compcfgs'] = array_merge($_sy_updvnow['selfcfgs'],$_sy_updvnow['compcfgs']);

