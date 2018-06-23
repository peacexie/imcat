<?php
(!defined('RUN_INIT')) && die('No Init');

// 1. 备份:
// 2. 清理

$rdo = 'fail';

// 备份:order
devData::data1ExpInsert('/debug/','coms_corder',array(),0,"atime>'".(time()-90*86400)."'");
devData::data1ExpInsert('/debug/','coms_cocar',array(),0,"atime>'".(time()-90*86400)."'");
devData::data1ExpInsert('/debug/','coms_coitem',array(),0,"atime>'".(time()-90*86400)."'");

// 备份:cargo
devData::data1ExpInsert('/debug/','docs_cargo',array(),0);
devData::data1ExpInsert('/debug/','dext_cargo',array(),0);

// 清理:记录文件
$stamp = time()-5*86400;
$lists = comFiles::listDir(DIR_DTMP.'/debug'); 
foreach ($lists['file'] as $file => $row) {
    if($row[0]<$stamp) unlink(DIR_DTMP.'/debug/'.$file);
}

#die();
//basDebug::bugLogs('demo_file','do-sth','detmp','db');

$rdo = 'pass';
