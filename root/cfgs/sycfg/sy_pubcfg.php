<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
global $_cbase;

//发布配置
$ocfgs = read('outdb','ex');

//main,vary,vimp
$_sy_pubcfg = array();

//1. dirs 

//2. copy <root>

//3. parts
$_sy_pubcfg['parts'] =  array(
    'main' => array(), //'code','root','skin','@read',
    'vary' => array('ctpl','dtmp'), //'html','ures',
    'vimp' => array('static','vendor','vendui'),
);

//4. ids
$_sy_pubcfg['ids'] =  array(
    array('dtmp','/dset/_score.cfg.php',array('close_chn'=>'1')), //安装标记
);

//5. cdemo
// - 是否有个-cdemo文件
// - 替换
$_sy_pubcfg['cdemo'] =  array(
    'root/cfgs/excfg/ex_a3rd.php' =>'',
    'root/cfgs/excfg/ex_aliyun.php' =>'',
    'root/cfgs/excfg/ex_mail.php' =>'',
    'root/cfgs/excfg/ex_haoft.php' =>'',
    'root/cfgs/excfg/ex_sms.php' =>'',
    'root/cfgs/excfg/ex_store.php' =>'',
    'root/cfgs/boot/_paths.php' => '',
    'root/cfgs/boot/cfg_adbug.php' => '',
    'root/cfgs/boot/cfg_db.php' => '',
    'root/cfgs/boot/const.php' => array( 
        array("'".$_cbase['ck']['pre']."';", "'".$_cbase['sys']['sn']."';",),
        array("'v".devRun::rstVerfix()."_';", "'".comConvert::sysSn()."';",), 
    ), //sn, ver, sign
    'root/cfgs/excfg/ex_outdb.php' => array( 
        array("'".$ocfgs['sign']['sapp']."';",   "'".$ocfgs['sign']['skey']."';",   ), 
        array("'".basKeyid::kidRand('k',24)."';", "'".basKeyid::kidRand('f',36)."';", ),
    ),
    'root/cfgs/excfg/ex_sphinx.php' => array(
        array("'index_imcat_demo_main,index_imcat_demo_delta',",),
        array("'', //index_imcat_demo_main,index_imcat_demo_delta",),
    ), 
);

//6. rndata
$_sy_pubcfg['rndata'] =  array(
    //'tab' => array("kid IN ('admin','peace')",'token,appid,appsecret'),
    'base_paras' => array("model='prsafe'",'val'),
    'wex_apps' => array("1=1",'token,appsecret'),
    'bext_paras:1' => array("kid='push_token'",'detail'),
    'bext_paras:2' => array("kid='push_site'",'detail',array('detail'=>'www.your_domain.com')),
);

//7. skip-dirs
$_sy_pubcfg['skip'] =  array(
    'main' => array(), // 'fitpl','yscode'
    'vendor' => array('aliOss'), // 'composer','Monolog','psrlog','silex',
    'dtmp' => array('@test','@udoc','debug','update','weixin','remote'),
    'vendui' => array('artEditor','swplayer','photoSwipe','proxy'), //
);
//8. skip-files
$_sy_pubcfg['skfiles'] =  array(
    'wetest.php', // a3rd/weixin_pay/
    '_setfix_path.txt', // dtmp:/store/
    '_setup_lock.txt',
    '_setup_step.txt',
    
    'hello.3gpp', // static:/media/sample
    'movecar.3gp',
    'sample.avi',
    'sample.flv',
    'ZoomImg.rar',
    'demo-book1.xls',
    'demo-word.doc',
    'demo-ppt.pptx',
    'xbbs_Dance.gif', // static:/media/collect/
    'xbbs_Pazz.gif',
    'xditu.jpg',
    'zuowen_shangxin.jpg',
    'temp_480x200.jpg',
    'dbdic-cn.cac_htm', // vary:/dtmp/store/
    'dbdic-en.cac_htm',
    '_auto.cfg_php',
    '_china.cfg_php',
    '_world.cfg_php',
    'CN-tab.php', // static:/media/iptabs/
    'CN-tv2.php', 
    'haibao.jpg', // static:/media/cover/
    '100-gushi.jpg', // static:/file_demo1/
    'cacert.pem', // vendor:/a3rd/alipay_class/
    'echarts.min.js',
    'bank_full.html',
    'combo.png',

    'simkai.ttf', // static:/media/fonts/
    'efjian.ttf', //斜
    'efm17.ttf',
    'efmE.ttf',
    'efmO.ttf',

    //ext-*
    'extMedoo.php',
    'Medoo.cls_php',
    'Pimple.cls_php',
    'exvFtree.php',

    // mini-imps
    'supper_muma.imp_php',
    'check_yahei.imp_php',
    'spword.imp_txt',
    //'adminer.imp_php',
    //'derun.imp_php',

    //'jq/bs
    'jquery-3.x.js',
    'bs4.min.css',
    'bs4.min.js',
    //'spword.imp_txt',
    
    'jquery-fileupload.js',

    'fontawesome-webfont.svg', // format('svg'); /* iOS 4.1- */
    'bootstrap.cerulean.css',
    'bootstrap.flatly.css',
    'bootstrap.superhero.css',
    
);

