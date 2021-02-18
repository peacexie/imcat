<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// 工单提醒
$rdo = 'fail';

$min = date("Y-m-d").' 06:00'; $max = date("Y-m-d").' 20:00';
$list = data('cslogs',"mflag='swevchk' AND `show`='all' AND exmsg>='$min' AND exmsg<='$max'"); 
//dump($list); dump("mflag='swevchk' AND exmsg>='$min' AND exmsg<='$max'");
if(!empty($list)){ 
    $pids = ',';
    foreach ($list as $log) {
        if(strpos($pids,$log['pid'])){ continue; } // 一个单多次提醒,只提醒一次。
        $pids .= "$log[pid],";
        $doc = data('cstask',"did='$log[pid]'",1);
        $row = ['did'=>$log['pid'], 'title'=>$doc['title'], 'mflag'=>$log['mflag'], 'remind_time'=>$log['exmsg']];
        $res = \imcat\umc\texBase::msgSend($row, '1000002', 'remind', [$log['mname']]);
        dump($res);
    }
}

//die('cxc');
$rdo = 'pass';
