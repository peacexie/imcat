<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require dirname(dirname(__FILE__)).'/binc/_pub_cfgs.php';

$cfgs = read('haoft','ex'); 
$city = req('city', $cfgs['def-city']);
$type = req('type','area');

$hft = new extHaoft($city); // dump($hft);
$types = $hft->types;
$types['area'] = 'area';

$navsa = array();
foreach ($types as $itype) {
    $navsa["$mkv&city=$city&type=$itype"] = $itype;
}
$navsc = array();
foreach ($cfgs as $icity=>$icitm) {
    if(strpos($icity,'-')) continue;
    $navsc["$mkv&city=$icity"] = $icity;
}
$citys = admPFunc::fileNav($city, $navsc);


if(in_array($view,array('list','set'))){
    $lnkadd = 'set'; 
    $links = admPFunc::fileNav($type, $navsa);
    glbHtml::tab_bar("[extHaoft]<span class='span ph5'>#</span>$lnkadd","$links",50);
}
echo "<table border='1' class='table tblist'><tr><th>$citys</th></tr></table>";

if($view=='loctest'){ // 本地数据测试
    
    $tabs = '苏仙区/北湖区/资兴市/永兴县/桂阳县/宜章县/嘉禾县/临武县/汝城县/桂东县/安仁县'; // 不限/
    $arr = explode('/', $tabs);
    $db = db('imhaoft');
    $areas = $db->table('area')->where('pid=0')->select();
    foreach ($areas as $k=>$row) {
        echo "$k, {$arr[$k]}<br>";
        $data = array('title'=>$arr[$k+4]);
        $db->table('area')->data($data)->where("kid={$row['kid']}")->update(0);
    }
    $tabs = array('area','comp','dept','lease','sale','user');
    foreach ($tabs as $tab) {
        $db->table($tab)->data(['city'=>'chenzhou'])->where("1=1")->update(0);
    }
    //dump($areas);

}elseif($view=='list'){

    if(in_array($type,$hft->types)){
        $res = $hft->syncData($type);
        if(!empty($res['nextUrl'])){
            $res['nextUrl'] = str_replace('?', "?mkv=$mkv&", $res['nextUrl']);
        }
    }else{
        $res = $hft->syncArea();
    }
    include DIR_SKIN."/adm/stpl/hft_sync.htm";

}elseif(in_array($view,array('json','tpl'))){
    
    //
    
}else{
    
    echo 'xx-else';
      
}

?>
