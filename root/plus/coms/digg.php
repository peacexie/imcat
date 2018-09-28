<?php
namespace imcat;
require dirname(__FILE__).'/_cfgall.php'; 

# mod,kid
$opfid = req('opfid'); // 操作字段
if(empty($mod)||!isset($_groups[$mod])) die("Error-1:{$mod}");
$tmp = glbDBExt::getTable($mod,'arr');
$tbid = $tmp[0]; $kfid = $tmp[1];
$tabfull = $db->table($tbid,2);

// 基本判断
if(empty($mod) || empty($opfid)){
    die("Error-a: Model or Filed.");
}
$cfgd = read('coms.digg','sy');

// 模型判断
if(empty($cfgd[$mod])){
    die("Error-b:{$mod}");
}
$cfg = $cfgd[$mod];
// 字段判断
if(empty($cfg[$opfid])){
    die("Error-c:$mod:{$opfid}");
}
// 字段规则
$rules = $cfg[$opfid];

// 规则检查
foreach ($rules as $rule) {
    $itm = explode('=',$rule);
    if(in_array($itm[0],array('login','iprep')) && !empty($itm[1])){
        $method = 'dchk'.ucfirst($itm[0]);
        dopCheck::$method($itm[1],$mod,$kid,$opfid);
    }
}
/*
    'login=1', // 登录发布
    'login=cvip,ccom', // 会员cvip,ccom等级:登录发布
    'iprep=6', // 同一ip间隔6可发布,cookie记录
*/

// 执行db操作
$db->query("UPDATE $tabfull SET $opfid=$opfid+1 WHERE $kfid='$kid' ");
die("success");

