<?php
(!defined('RUN_INIT')) && die('No Init');

// wework 配置

// 企业的id，在管理端->"我的企业" 可以看到
// "通讯录同步"应用的secret, 开启api接口同步后，可以在管理端->"通讯录同步"看到
$_ex_wework['CorpId']    = "ww******"; 
$_ex_wework['TxlSecret'] = '4uYHb_******'; 

// 打卡应用的 id 及secrete， 在管理端 -> 企业应用 -> 基础应用 -> 打卡，
// 点进去，有个"api"按钮，点开后，会看到
$_ex_wework['CHECKIN_APP_ID']   = '3010011';
$_ex_wework['CHECKIN_APP_SECRET']  = 'B8fQTjDbRoKrxNEB-******';

// 审批应用的 id 及secrete， 在管理端 -> 企业应用 -> 基础应用 -> 审批，
// 点进去，有个"api"按钮，点开后，会看到
$_ex_wework['APPROVAL_APP_ID']   = '3010040';
$_ex_wework['APPROVAL_APP_SECRET']   = 'CRbruunuz-******';

// 自定义应用
$_ex_wework['AppsConfig'] = [
    'AppCS' => [
        'name' => '工单管理',
        "AppDesc" => "应用1的描述",
        "AgentId" => 1000002,
        "Secret" => "******-qcwhyPt4",
        "Token" => "4nhq******",
        "EncodingAESKey" => "VKQYxMDGAX7WBxSU******"
    ],
    '1000002' => [
        'name' => 'T1迎宾测试',
        "AppDesc" => "应用2的描述",
        "AgentId" => 1000002,
        "Secret" => "应用2的密钥，在管理后台查看",
        "Token" => "应用2回调模式的Token，在应用的回调模式里面设置",
        "EncodingAESKey" => "应用2回调模式的加密串，在应用的回调模式里面设置"  
    ],
];

$_ex_wework['stab'] = [
    '1' => ['rgb'=>'fa5151', 'text'=>'非常差'],
    '2' => ['rgb'=>'fa9d3b', 'text'=>'很差'],
    '3' => ['rgb'=>'576b95', 'text'=>'一般'],
    '4' => ['rgb'=>'51ce8c', 'text'=>'很好'],
    '5' => ['rgb'=>'06ae56', 'text'=>'非常好'],
];
$_ex_wework['utab'] = [
    '(null)'   => '{"userid":"(unKnow)","name":"(未知)"}',
    '(admin)'  => '{"userid":"(admin)","name":"(管理员)"}',
    '(system)' => '{"userid":"(system)","name":"(系统)"}',
    '(scaner)' => '{"userid":"(scaner)","name":"(扫码者)"}',
];

$_ex_wework['sktab'] = [
    'ck' => "Jf7gnua1EaXWcfSVnvRuG", // Cookie-安全key
    'ak' => "v1HflNmPxKT6UpN8IF9x0", // Api-安全key
];
$_ex_wework['DefAppID'] = 'AppCS'; // 1000002, AppShouhou, AppCS

# ===================== 

$_ex_wework['AppCS'] = [
    // 自定义权限(后续考虑做成设置页,再移动端设置)
    'perms' => [
        'import' => 'TestMaster,PeaceXie', // 导入权限
        'publish' => 'TestMaster,PeaceXie', // 发布权限
        'alldata' => 'TestMaster,PeaceXie,PeaceXie,XieYongShun' // 所有历史单据
    ],
    'defs' => [ // 默认值：(人员id,部门id,自己)
        'apply_at' => 'PeaceXie,yorian,Adm1Test,PeaceXie', // 抄送人
        'apnew'    => 'yorian',
        'redo'     => 'yorian',
        'assign'   => '5', // 客服(5为测试部门)
        'servchk'  => '(me)',
        'aptime'   => '(me)',
        'ushift'   => '(me)',
        'served'   => '(me)',
        'done'     => '(me)',
        'paied'    => '(me)',
        'score'    => '',
        'close'    => '',
        'susing'   => 'TestMaster',
        'suspend'  => '(me)',
        'attapply' => '6', // 采购
        'attbuy'   => '(me)',
        'eqfix'    => '(me)',
        'eqfac'    => '(me)',
    ],
    'acts' => [ // 可选的下一步操作
        'apnew'    => ['assign'], // ,'done','close'
        'redo'     => ['assign','servchk','served'], // 
        'assign'   => ['servchk'], // 售后派工
        'servchk'  => ['served','aptime','ushift'], // 接收任务 ,'done','susing','attapply'
        'aptime'   => ['served','aptime','ushift'], // 约定时间
        'ushift'   => ['served','aptime','ushift'], // 转交任务
        'served'   => ['served','aptime','ushift','done','susing','attapply','eqfix','eqfac'], // 服务打卡
        'eqfix'    => ['served','aptime','ushift'], // 公司维修
        'eqfac'    => ['served','aptime','ushift'], // 返厂维修
        'done'     => ['paied','score'], // ,'close'
        'paied'    => ['score'], // ,'close'
        'score'    => ['paied'], // ,'close'
        'close'    => [''],
        'susing'   => ['suspend','redo'],
        'suspend'  => ['redo','close'],
        'attapply' => ['attbuy','redo'],
        'attbuy'   => ['redo']
    ],
];
/*
<option value="apnew">新售后单</option>
<option value="redo">重新派工</option>
<option value="assign">客服派工</option>
<option value="servchk">售后确认</option>
<option value="served">服务打卡</option>
<option value="done">完成</option>
<option value="paied">付款</option>
<option value="score">评分</option> ??? 
<option value="close">关闭</option> finish
<option value="susing">挂单申请</option>
<option value="suspend">已挂单</option>
<option value="attapply">配件申请</option>
<option value="attbuy">配件购买</option>
*/

$_ex_wework['1000003'] = [
    // 自定义权限(后续考虑做成设置页,再移动端设置)
    'perms' => [
        'xxxx' => 'xxx',
    ],
    // 默认值
    'defs' => [
        'xxx',
    ],
    'acts' => [
        'xxx'    => ['xxx'],
    ],
];

/*
# append-setting
$_fp = "/dtmp/wework/_set_app2.cac_tab";
$_data = \imcat\comFiles::get(DIR_VARS.$_fp);
$excfgs = \imcat\comParse::jsonDecode($_data);
if(isset($excfgs['ucfg']['debug'])){
    $_ex_wework['ucfg']['debug'] = $excfgs['ucfg']['debug'];
}
if(isset($excfgs['1000002']['perms']['alldata'])){
    $_ex_wework['1000002']['perms']['alldata'] = $excfgs['1000002']['perms']['alldata'];
}
if(isset($excfgs['1000002']['defs'])){
    foreach ($excfgs['1000002']['defs'] as $_key => $_val) {
        if(isset($excfgs['1000002']['defs'][$_key])){
            $_ex_wework['1000002']['defs'][$_key] = $_val;
        }
    }
}*/
//dump($excfgs);
