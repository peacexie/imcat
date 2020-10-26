<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');


function cfg_type($key, $name){
    if($key==1){
        $tab = [
            '便携式计算机' => ['5125' => '笔记本'],
            '台式计算机'   => ['5124' => '台式机'],
            '空调机'      => ['5130' => '空调机'],
        ];
        return $tab[$name];
    }else{
        /*$tab = [
            '256' => '操作系统配件',
            '257' => '显示器配件',
            '258' => '鼠标配件',
            '259' => '键盘配件',
            '260' => '电脑包配件',
            '261' => '铜管配件',
            '262' => '联想智能USB屏蔽功能配件',
        ];
        foreach ($tab as $tk => $tv) {
            $ctv = str_replace('配件', '', $tv);
            $cname = fix_rtab($name);
            if($ctv==$cname || strstr($cname,$ctv)){
                return [$tk=>$tv];
            }
        }*/
    }
    return [];
}
function opts_type($key='type', $name='类别'){
    //global $pt_ids;
    $tab = cfg_type($key=='type'?1:2, $name);
    $str = "$name: <select name='fm[$key]' id='fm_$key' onchange=\"setModel('$key')\">\n";
    $str .= "<option value='0'>-导入到-</option>\n";
    foreach ($tab as $tk => $tv) {
        //if($key!='type'){ $pt_ids[$tk] = $tv; }
        $str .= "<option value='$tk'>$tv</option>\n";
    }
    $burl = basReq::getUri(1, '', 'act'); //dump($aucut);
    $str .= "</select> ### <a id='chk_$key' url='$burl&act=check' onclick='setCheck(this)' target='chk_$key'>检查属性</a>\n";
    $str .= "          ### <a id='imp_$key' url='$burl&act=imp' onclick='setCheck(this)' target='imp_$key'>导入项目</a>\n";
    return $str;
}


function arrTotab($arr, $parts, $name){
    $str = "";
    $pkeys = array_flip($parts); //dump($pkeys);
    foreach($arr as $kt => $itms) {
        if(is_string($itms)){ continue; }
        $str .= "<table class='Ptable' id='".$pkeys[$kt]."'>\n";
        $str .= "<tbody><tr><th colspan='2'>$kt</th></tr>\n";
        if($pkeys[$kt]=='info'){ //if(count($itms)==1){
                $row = $itms[0];
                foreach($row as $rk => $rv) {
                    $str .= "<tr><td class='tdTitle'>$rk</td><td>$rv</td></tr>\n";
                }
        }else{
            foreach($itms as $ino => $row) {
                $str .= "<tr><td><table class='Ptable'><tbody>\n";
                foreach($row as $rk => $rv) {
                    $str .= "<tr><td class='tdTitle'>$rk</td><td>$rv</td></tr>\n";
                }
                $str .= "</tbody></table></td></tr>\n";
            }
        }
        $str .= "</tbody></table>\n";
    }
    #$data = str_replace("\n",'',$str);
    $fp = _getFpfmt_($arr['主体参数']['0']['商品型号'], $name);
    file_put_contents(DIR_VARS."/gbatts/$fp.htm", $str);
    return $str;
}

function xlsToarr($cells, $parts){
    //$list = [];
    $res = []; $rno = 0;
    $skips = []; $pk = ''; $pno = 0; $pns = 0;
    $act = req('act','-'); 
    foreach($cells as $kr => $row) {
        if(count($row)<3){ continue; }
        if(in_array($kr,$skips)){ continue; }
        if(isset($row[1])){
            $skips = []; $pk = ''; $pno = 0; $pns = 0;
            $rno++;
            $res[$rno]['name'] = $row[1];
            #echo $row[1]." *** <br>\n";
        }
        // pk
        $tmp = '';
        foreach($row as $k1 => $v1) {
            if(in_array($v1, $parts)){
                $pk = $v1;
                $pno = 0;
                $pns = $k1;
                $tmp = '1';
                break;
            }
        }
        if(!$tmp){ $pno++; }
        // 
        $r1 = $cells[$kr];
        $r2 = $cells[$kr+1];
        $skips[] = $kr+1;
        foreach($r1 as $k1 => $v1) {
            if($k1<=$pns){ continue; }
            if($pk){
                if(strpos(",check,imp,",$act)>0){
                    $v1 = trim(preg_replace("/\s+/i", ' ', $v1));
                    $v1 = str_replace(['（','）'], ['(',')'], $v1);
                }
                $res[$rno][$pk][$pno][$v1] = isset($r2[$k1]) ? $r2[$k1] : '';
            }
        }
    }
    return $res;
}

function _getFpfmt_($model, $name, $snote=0){
    $a1 = ['（', '）', ' ', '三','二','一'];
    $a2 = ['(',  ')',  '-', '3', '2', '1'];
    $model = str_replace($a1, $a2, $model); 
    $model = preg_replace("/[\-]{2,}/", '-', $model);
    //
    $name = str_replace($a1, $a2, $name);
    $name = str_replace($model, '', $name);
    $name = preg_replace("/[\x{4e00}-\x{9fa5}]+/u", '', $name);
    $name = preg_replace("/[\-]{2,}/", '-', $name);
    //
    $fp = $model.'---'.strtoupper($name); //md5($name);
    if($snote){ return $fp; }
    $fp = str_replace(['%28','%29','%'], ['(',')','_'], urlencode($fp));
    return $fp;
}


/*

*/
