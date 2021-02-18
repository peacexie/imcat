<?php
(!defined('RUN_INIT')) && die('No Init');

// weedu 配置
//$_ex_weedu['isOpen'] = '1';
// 并检查：/root/wework/目录及文件

// 企业的id，在管理端->"我的企业" 可以看到
// "通讯录同步"应用的secret, 开启api接口同步后，可以在管理端->"通讯录同步"看到
$_ex_weedu['CorpId']    = "***"; 
$_ex_weedu['TxlSecret'] = '"***";'; 

// 打卡应用的 id 及secrete， 在管理端 -> 企业应用 -> 基础应用 -> 打卡，
// 点进去，有个"api"按钮，点开后，会看到
$_ex_weedu['CHECKIN_APP_ID']   = '3010011';
$_ex_weedu['CHECKIN_APP_SECRET']  = '"***";';

// 审批应用的 id 及secrete， 在管理端 -> 企业应用 -> 基础应用 -> 审批，
// 点进去，有个"api"按钮，点开后，会看到
$_ex_weedu['APPROVAL_APP_ID']   = '3010040';
$_ex_weedu['APPROVAL_APP_SECRET']   = '"***";';

// 自定义应用
$_ex_weedu['AppsConfig'] = [
    '700443' => [ // 
        'name' => 'T小程序 ',
        "SuiteID" => 700443, // 这个id各企业下不一定相同
        "SuiteKey" => "***",
        "Token" => "***",
        "EncodingAESKey" => "***",
    ],
    '700440' => [ // 
        'name' => 'T独立访问 ',
        "SuiteID" => 700440, // 这个id各企业下不一定相同
        "SuiteKey" => "***",
        "Token" => "***",
        "EncodingAESKey" => "***",
    ],
    '700439' => [ // 
        'name' => 'T托管H5 ',
        "SuiteID" => 700439, // 这个id各企业下不一定相同
        "SuiteKey" => "***",
        "Token" => "***",
        "EncodingAESKey" => "***",
    ],
    '700434' => [
        'name' => 'T无移动端',
        "SuiteID" => 700434,
        "SuiteKey" => "***",
        "Token" => "***",
        "EncodingAESKey" => "***",
    ],
];
$_ex_weedu['DefAppID'] = '700439'; // 

/*

700550 iOS
700510 云备课平台
700481 家校共育
700477 智慧教务
700443 T小程序
700440 T独立访问
700439 T托管H5
700434 测试移动端

*/


$_ex_weedu['sktab'] = [
    'ck' => "***", // Cookie-安全key
    'ak' => "***", // Api-安全key
];
$_ex_weedu['ucfg'] = [
    'debug' => 'TestAdmin,PeaceXie,XieYongShun', // 调试页权限
];

# ===================== 

$_ex_weedu['AppCS'] = [
    // 自定义权限(后续考虑做成设置页,再移动端设置)
];


/*
# append-setting
$_fp = "/dtmp/wework/_set_app2.cac_tab";
$_data = \imcat\comFiles::get(DIR_VARS.$_fp);
$excfgs = \imcat\comParse::jsonDecode($_data);
if(isset($excfgs['ucfg']['debug'])){
    $_ex_weedu['ucfg']['debug'] = $excfgs['ucfg']['debug'];
}
if(isset($excfgs['AppCS']['perms']['alldata'])){
    $_ex_weedu['AppCS']['perms']['alldata'] = $excfgs['AppCS']['perms']['alldata'];
}
if(isset($excfgs['AppCS']['defs'])){
    foreach ($excfgs['AppCS']['defs'] as $_key => $_val) {
        if(isset($excfgs['AppCS']['defs'][$_key])){
            $_ex_weedu['AppCS']['defs'][$_key] = $_val;
        }
    }
}*/
//dump($excfgs);
