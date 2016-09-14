<?php
(!defined('RUN_MODE')) && die('No Init');

//发布配置
$ocfgs = glbConfig::read('outdb','ex');

//main,vary,vimp
$_sy_pubcfg = array();

//1. dirs
$_sy_pubcfg['dirs'] = array(
	'code'=>DIR_CODE,
	'root'=>DIR_ROOT,
	'dtmp'=>DIR_DTMP,
	'html'=>DIR_HTML,
	'ures'=>DIR_URES,
	'static'=>DIR_STATIC,
	'vendor'=>DIR_VENDOR,
	'vendui'=>DIR_VENDUI,
);

//2. copy
$_sy_pubcfg['copy'] = array(
	'robots.txt',
	'chn.php',
	'dev.php',
	'doc.php',
	'mob.php',
	'index.php',
);

//3. ids
$_sy_pubcfg['ids'] =  array(
	array('dtmp','/dset/_score.cfg.php',array('close_chn'=>'1')), //安装标记
);

//4. parts
$_sy_pubcfg['parts'] =  array(
	'main' => array('code','root'),
	'vary' => array('dtmp','html','ures'),
	'vimp' => array('static','vendor','vendui'),
);

//5. cdemo
// - 是否有个-cdemo文件
// - 替换
$_sy_pubcfg['cdemo'] =  array(
	'code/cfgs/excfg/ex_mail.php' =>'',
	'code/cfgs/excfg/ex_sms.php' =>'',
	'code/cfgs/excfg/ex_a3rd.php' =>'',
	'root/run/_paths.php' => array(
		array("'/txmao'",),
		array("''",),
	), 
	'code/cfgs/boot/cfg_db.php' => array(
		array("'".glbDBObj::getCfg('db_name')."';",                       "'".glbDBObj::getCfg('dc_prefix')."';", ),
		array("'txmao_".str_replace('-','',basKeyid::kidTemp('hm'))."';", "'".basKeyid::kidRand('f',5)."';",),
	),
	'code/cfgs/boot/cfg_adbug.php' => array(
		array("'ut.<rnd8>';",                       "'up.<rnd12>';",),
		array("'ut.".basKeyid::kidRand('f',8)."';", "'up.".basKeyid::kidRand('f',12)."';",),
	),	
	'code/cfgs/boot/const.cfg.php' => array( 
		array("'".$_cbase['ck']['pre']."';", "'".$_cbase['sys']['sn']."';",),
		array("'".basKeyid::kidRand('f',5)."';", "'".comConvert::sysSn()."';",), 
	), //sn, ver, sign
	'code/cfgs/excfg/ex_outdb.php' => array( 
		array("'".$ocfgs['sign']['sapp']."';",   "'".$ocfgs['sign']['skey']."';",   "'".$ocfgs['psyn']['server']."';",), 
		array("'".basKeyid::kidRand('k',24)."';", "'".basKeyid::kidRand('f',36)."';", "'http://master.domain.com/root';",),
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
	'vendor' => array('ks-buzz','Monolog','psrlog','silex','Symfony'),
);
//8. skip-files
$_sy_pubcfg['skfiles'] =  array(
	'wetest.php', // a3rd/weixin_pay/
	'_setfix_path.txt', // dtmp:/store/
	'_setup_lock.txt',
	'_setup_step.txt',
	'simkai.ttf', // static:/media/fonts/
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
	'haibao.jpg', // static:/media/cover/
	'100-gushi.jpg', // static:/file_demo1/
	'cacert.pem', // vendor:/a3rd/alipay_class/
);

