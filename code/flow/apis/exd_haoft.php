<?php
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

if($view=='set'){
    
    echo 'xx-set';

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

    /*
    $db = db('imhouse');
    dump($db->table('area')->select());
    */


}elseif(in_array($view,array('json','tpl'))){
    
    //
    
}else{
    
    echo 'xx-else';
      
}

?>
