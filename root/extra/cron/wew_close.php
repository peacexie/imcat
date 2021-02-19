<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$rdo = 'fail';
// 工单关闭

$day10 = time()-20*86400; // 10天内更新
$day3 = time()-3*86400; // 3天之前 ... + done=售后完成 & paied=付款
$where = "mflag IN('done','paied') AND `show`='all' AND etime>='$day10' AND etime<='$day3'";
$list = data('cstask', $where); //die($where);
if(!empty($list)){ 
    $pids = ',';
    $kar = glbDBExt::dbAutID('coms_cslogs'); // 2020-97-b602
    $cid1 = substr($kar[0],0,10); $cidno = 10; 
    foreach ($list as $row) {
        $did = $row['did'];
        $tmp = _ex_doLogs($did); 
        $re['doLogs'] = $tmp['doLogs'];
        $re['uFlags'] = $tmp['uFlags']; //dump($tmp['uFlags']);
        if(!empty($re['uFlags']['hasScore'])){ continue; } // 有评分
        $nofee = empty($re['uFlags']['hasFee']); // 无费用
        if($nofee){
            $title = '3天未评价'; // . ($nofee ? "不收费" : "已付款");
            $loga = [
                'cid'=>$cid1.($cidno++), 'cno'=>$cidno, 'pid'=>$did, 
                'mflag'=>'close', 'title'=>"$title,系统自动关闭", 'mname'=>'(System)', 'auser'=>'(system)'
            ]; 
            db()->table('coms_cslogs')->data($loga)->insert();
            // update
            $doc = ['mflag'=>'close']; // , 'douid'=>$fm['douid']
            db()->table('docs_cstask')->data($doc)->where("did='$did'")->update(); 
        }
    }
}

//die('cxc');
$rdo = 'pass';

// doLogs
function _ex_doLogs($did){
    $data = data('cslogs',"pid='$did' AND `show`='all'",99,'atime-0');
    $doLogs = []; 
    $uFlags = ['hasDone'=>0, 'hasFee'=>0, 'hasScore'=>0, 'hasPaied'=>0, 'hasClose'=>0, 'lastMflag'=>0, 'datetime'=>'', 'userNow'=>[]];
    if(!empty($data)){ 
        foreach ($data as $key => $row) {
            // 时间要求，打卡位置，费用，评分（备用）
            if(in_array($row['mflag'],['apnew','aptime']) && !empty($row['exmsg'])){ // servchk
                //
            }elseif($row['mflag']=='attapply'){ // 配件申请
                //
            }elseif($row['mflag']=='done' && !empty($row['exmsg'])){ // 费用
                $uFlags['hasFee'] = 1;
            }elseif($row['mflag']=='score' && !empty($row['exmsg'])){ // 评分
                //
            }elseif($row['mflag']=='served' && !empty($row['exmsg'])){ // map
                //
            }
            $doLogs[$key] = $row;
            /* if(!$uFlags['lastMflag']){ // 第一个 对应 时间的最近一个 }*/
            //if($row['mflag']=='apnew') { $uFlags['datetime'] = $row['exmsg']; } 
            if($row['mflag']=='done') { $uFlags['hasDone'] = 1; } // 已完成
            if($row['mflag']=='score'){ $uFlags['hasScore'] = 1; } // 已评分
            if($row['mflag']=='paied'){ $uFlags['hasPaied'] = 1; } // 已付款
            if($row['mflag']=='close'){ $uFlags['hasClose'] = 1; } // 已关闭
            $uFlags['lastMflag'] = $row['mflag']; // 最后一个 对应 时间的最近一个
        } 
        $skip = ($row['auser']=='(scaner)') || ($row['mflag']=='close');
        if($row['auser']!==$row['mname'] && !$skip){
            $uFlags['userNow'] = extWework::getUser($row['mname'], 'AppCS');
        }
    }
    return ['doLogs'=>$doLogs, 'uFlags'=>$uFlags];
}


/*
score=评分
close=关闭
*/

