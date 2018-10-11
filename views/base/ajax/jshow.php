<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
define('RUN_JSHOW', 1);
$_cbase['skip']['.none.'] = true;

vopTpls::set(req('tpldir'));
glbHtml::head();

$q = $_SERVER['QUERY_STRING'];
parse_str($q,$a);
$_cbase['run']['mkv'] = req('rf');

$sfie = array(); $scnt = array();
$stag = '';  $sadv = ''; 
foreach($a as $k=>$v){
    //$k = basStr::filKey($k,"-._@:");
    if(strstr($k,'jsid_tags_')){
        $re = tagCache::jsTag($k,$_cbase['run']['mkv'],$v);
        $stag .= "jtagRep('$k','$re');\n";
    }elseif(strstr($k,'jsid_advs_')){
        $adres = tagCache::showAdv($v);
        $sadv .= "jqHtml('$k','$adres');\n";
        if(strpos($adres,"class=\\'advFlag")){
            $sadv .= "jqPcpr('$k');\n";
        }
    }elseif(strstr($k,'jsid_field_')){
        $sfie[substr($k,11)] = $v;
    }elseif(strstr($k,'jsid_count_')){
        $scnt[substr($k,11)] = $v;
    }else{ 
        //;
    }
}

$rfie = vopCell::jsFields($sfie);
$rcnt = vopCell::jsCounts($scnt);

echo $rfie;
echo $rcnt;
echo $stag;
echo $sadv;

$jsrun = ''; //静态控制

/*
注意格式:<i id="jsid_field_news:2013-ff-dcyq:etime">0</i>中
要用[:]不能用[.] 测试代码：
$q = 'aa.bb=1&aa:bb=2'; //&path/file=3
parse_str($q,$b); print_r($b);
Array(
    [aa_bb] => 1
    [aa:bb] => 2
) */

?>