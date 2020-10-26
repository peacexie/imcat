<?php 
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
#safComm::urlFrom(); 

# cfgs
$groups = read('groups');
$mod = req('mod','news');
$pid = req('pid','0'); // 0,pid
$init = req('init');
$lays = $gtab = [];
//dump($groups);

# mods
if(isset($groups[$mod])){
    $temp = read("$mod.i"); //dump($temp);
    if($init){
        $lays = comTypes::getLays($temp, $init);
    }
    $data = [];
    foreach($temp as $kno => $vr){
        if(!isset($vr['pid']) || "$vr[pid]"=="$pid"){ 
            $data[] = $vr;
        }
    }
}elseif($mod=='rels'){ // 缓存???
    $rid = req('rid','relpb'); 
    $rida = glbConfig::relids($rid, $pid?$pid:'-', 1); // 关联XX-id
    $rids = empty($rida) ? '' : implode("','", $rida);
    $up = req('up'); // brand
    if(!empty($rida) && $up && in_array($up,['brand','china'])){
        $tab = db()->table("types_$up")->where("kid IN ('$rids')")->order('top')->select(); 
        foreach ($tab as $tv) {
            $pid = $tv['pid'];
            if(strstr($rids,$pid)){ continue; }
            $rids .= ','.$pid;
            $rida[] = $pid;
        }
    }
    $rids = empty($rida) ? '~' : ','.implode(",", $rida).',';
    $data = ['rids'=>$rids];
}elseif($mod=='umod'){ // 缓存???
    $data = db()->table('exd_umod')->where("pid='$pid'")->order('top')->select();
    if($init){
        $temp = db()->table('exd_umod')->order('top')->select();
        $lays = comTypes::getLarr($temp, $init);
    }
    foreach($data as $kno => &$vr){
        unset($vr['aip'],$vr['atime'],$vr['auser'],$vr['eip'],$vr['etime'],$vr['euser']);
    }
}elseif($mod=='uatt'){ // 缓存???
    $data = db()->table('exd_uatt')->where("pid='$pid'")->order('top')->select();
    foreach($data as $kno => &$vr){
        unset($vr['aip'],$vr['atime'],$vr['auser'],$vr['eip'],$vr['etime'],$vr['euser']);
    }
    $gtab = extCargo::umodGtab($pid);
}else{
    $data = ['ercode'=>'err-params','ermsg'=>'Error Params'];
} // dump($data);

# view
$re = req('re', 'json');
glbHtml::head($re);
$res = ['gtab'=>$gtab, 'arr'=>$data];
if(!empty($lays)){ $res['lays'] = $lays; }
$res = basOut::fmt($res, $re);
die($res);

/*

*/
