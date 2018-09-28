<?php
(!defined('RUN_INIT')) && die('No Init');
// 附件模式: rsLocal, rsFtp, rsAlioss
#$_cfg['type'] = 'rsLocal'; // 自v4.4, 默认固定为:rsLocal

/*
* upload/save
* edit/save
* view
* del : oss
* thumb : oss

*/

// 默认(放最后):rsLocal(不用改动)
$_cfg['types']['rsLocal'] = array(
    // 类型/目录:配置
    'ftypes' => array(), // 见`sy_filetype.php`, 为空所有类型, array('none')不启用; 
    'mdirs' => array(),  // (目录)前缀:为`模型id+/`, 为空所有类型, array('none')不启用;
    // 前缀/后缀
    'spre' => '{uresroot}/', // save 前缀
    'sfix' => '',            // save 后缀, 一般为空
    'vpre' => PATH_URES.'/', // view 前缀
    'vfix' => '',            // view 后缀, 一般为空
    // 缩略图剪切地址,可自行设置重定向 
    'cut_ures' => '', // (size),(oimg,oext,fimg,fext),
    #'apicfgs' => array(),
    'dir_ures' => DIR_URES, // upload 根路径
);

// 使用ftp附件的:类型/目录前缀
$_cfg['types']['rsFtp'] = array(
    // 类型/目录:配置
    'ftypes' => array('image', 'flash', 'docus', 'file', 'ziper', 'video', 'audio'),
    'mdirs' => array('none'), //array('news/', 'cargo/'), 为空所有类型, array('none')不启用;
    // 前缀/后缀
    'spre' => '{ftproot}/', // save 前缀
    'sfix' => '',           // save 后缀
    'vpre' => 'http://ftp.txjia.com/imcat/', // view 前缀
    'vfix' => '', // view 后缀
    // 缩略图剪切地址,可自行设置重定向 
    'cut_ures' => 'http://ftp.txjia.com/thumb.php',
    // api-cfgs
    'apicfgs' => array(
        'ftp_ssl'     => false, // true,false
        'host'        => 'host-ip',
        'user'        => 'ftp-userid',
        'pass'        => 'ftp.passwd',
        'port'        => 18519,
        'passive'     => false,
        'debug'       => true,
    ),
    'dir_ures'  => '/imcat', // ftp根路径
);

// 使用oss附件的:类型/目录前缀
$_cfg['types']['rsAlioss'] = array(
    'ftypes' => array('image', 'flash', 'docus', 'file', 'ziper', 'video', 'audio'),
    'mdirs' => array('none'), // 为空所有类型, array('none')不启用; 'news/', 'cargo/', 'keres/'
    // 前缀/后缀
    'spre' => '{aliroot}/', // save 前缀 
    'sfix' => '', // save 后缀 ?{urlsign}
    'vpre' => 'http://vdo.imcat.com/', // view 前缀
    'vfix' => '', // view 后缀
    // 缩略图剪切地址,可自行设置重定向 
    'cut_ures' => '',
    #'apicfgs' => array(),
    'dir_ures' => 'test', // oss根路径
);

// 缩略图大小(此控制意图为:防止生成任意大小的缩略图,导致大量垃圾文件)
$_cfg['resize'] = ';240x180,160x120,120x90,120x60;88x31,40x40,120x120;'; 
$_cfg['resize'] .= '180x240,120x160,90x120,60x120;31x88,80x80,240x240;'; 


// return
$_ex_store = $_cfg; 

