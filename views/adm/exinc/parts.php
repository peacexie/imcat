<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');


$act = req('act', 'list');
$pid = req('pid');
$kid = req('kid');
$res = [];


# 导航测试
if($act=='nav'){
    echo "<ul><li>nav</li>\n";
    echo "</ul>\n";
    die();
} 

if($act=='list'){
    $data = db()->table('exd_upart')->where("pid='$pid'")->order('top')->select(); //dump($data);
    if($data){
        foreach($data as $kno => &$vr){
            //if($vr['gkey'] && !in_array($vr['gkey'],$gtab)){ $gtab[] = $vr['gkey']; }
            unset($vr['aip'],$vr['atime'],$vr['auser'],$vr['eip'],$vr['etime'],$vr['euser']);
        }
        $cnt = count($data);
    }else{
        $cnt = 0;
    }
    $res = ['cnt'=>$cnt, 'arr'=>$data];
}elseif($act=='save'){
    $tab = ['kid','did','title','guige','price','top','cnt','attcom'];
    $row = [];
    foreach($_POST as $key=>$val) {
        if(!is_string($val)){ continue; }
        if(in_array($key,['price','top','cnt']) && !is_numeric($val)){
            $val = 0;
        }else{
            $val = basStr::filSafe4($val);
        }
        if(in_array($key,$tab)){
            $row[$key] = $val;
        }
    }
    if(empty($row['kid'])){
        $kar = glbDBExt::dbAutID('exd_upart'); 
        $row['kid'] = $kar[0];
    }
    $row['pid'] = req('pid');
    $row['title'] = trim($row['title']);
    $where = "pid='{$row['pid']}' AND title='{$row['title']}' AND kid!='$kid'";
    $cnt = db()->table('exd_upart')->where($where)->count();
    $cnt = intval($cnt);
    if($cnt>=1){
        $res = '配件重复'; //.$cnt.$where;
    }else{
        if(empty($kar[0])){
            $res = db()->table('exd_upart')->data($row)->where("kid='$kid'")->update();
        }else{
            $res = db()->table('exd_upart')->data($row)->insert();
        }
    }
    $res = ['cnt'=>$cnt, 'res'=>$res];
}elseif($act=='find'){
    $row = db()->table('exd_upart')->where("kid='$kid'")->find();
    $res = ['cnt'=>(empty($row)?0:1), 'row'=>$row];
}elseif($act=='del'){
    $red = db()->table('exd_upart')->where("kid='$kid'")->delete();
    $res = ['cnt'=>(empty($red)?0:1), 'res'=>$red];
}


# view
$re = req('re', 'json');
glbHtml::head($re);
$json = basOut::fmt($res, $re);
die($json);


/*

*/
