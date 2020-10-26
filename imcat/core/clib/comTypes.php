<?php
namespace imcat;

// Types类
class comTypes{    
    
    // $arr 从db取得,ordby:deep,top
    // $re json-字串, arr-数组, N>个数-json字串, N<个数-数组
    static function arrLays($arr,$re='json'){ 
        $res = ''; $cnt = count($arr);
        foreach($arr as $k=>$row){
            $kid = $row['kid'];
            $pid = $row['pid']; 
            $itm = "\"$kid\":".comParse::jsonEncode($row).",\n(i_{$kid})\n";
            if(empty($pid)){
                $res .= $itm;
            }else{
                $res = str_replace("\n(i_{$pid})\n","\n$itm(i_{$pid})\n",$res);
            }
            unset($arr[$k]);
        }
        $res = preg_replace("/\(i_[\w\-]{2,36}\)[\n]{1}/",'',$res);
        $res = "{\n".substr($res,0,strlen($res)-2)."\n}";
        if(is_int($re)) $re = $cnt>=$re ? 'json' : 'arr'; 
        $res = $re=='arr' ? comParse::jsonDecode($res) : $res;
        return $res;
    }
    // $arr 从db取得,ordby:deep,top
    static function arrSubs($arr){ 
        $res = array(); 
        foreach($arr as $k=>$v){
            $kid = $v['kid'];
            $pid = $v['pid'];
            $v['subids'] = ',';
            $v['subnum'] = 0;
            $v['subarr'] = array();
            if(empty($pid)){
                $res[$kid] = $v;
            }else{
                $res[$pid]['subarr'][$kid] = $v;
                while(!empty($pid)){
                    $res[$pid]['subids'] .= "$kid,"; //小递归到所有pid
                    $res[$pid]['subnum']++; //小递归到所有pid
                    $pid = $res[$pid]['pid'];
                }
            }
            unset($arr[$k]);
        }
        return $res;
    }

    // xxx
    static function getChars($arr,$deep='12345'){ 
        $a = array(); 
        foreach($arr as $k=>$v){
            if(!strstr($deep,$v['deep'])) continue;
            $v['kid'] = $k;
            $a[$v['char']][] = $v;
        }
        ksort($a);
        return $a;
    }
    
    // getSubs,所有pid以下的子分类
    static function getSubs($arr, $pid='0', $deep='12345', $ra=1){ 
        $start = '0'; $fdeep = '-1'; $a = array(); 
        if(empty($arr)) return empty($ra) ? 0 : $a;
        foreach($arr as $k=>$v){
            if(!isset($v['deep'])) $v['deep'] = '1';
            if(!isset($v['pid'])) $v['pid'] = '0';
            if($start && $fdeep>$v['deep']) break;
            if($v['pid']===$pid){ 
                $start = '1'; 
                $fdeep = $v['deep'];
            }
            if($start && strstr($deep,"$v[deep]")){
                $a[$k] = $v;
            }
        } 
        $re = empty($ra) ? count($a) : $a;
        return $re;
    }
    
    // getPars(所有最大级别pmax外的父分类)
    static function getPars($arr,$pmax='3'){ 
        $a = array();
        foreach($arr as $k=>$v){
            if($pmax==$v['deep']) continue;
            $a[$k] = $v;
        } 
        return $a;
    }
    
    // getLays(id对应的树形分类:键值下标)
    static function getLays($arr, $id, $a=array()){
        $a[$id] = empty($arr[$id]['title']) ? '' : $arr[$id]['title'];
        $pid = empty($arr[$id]['pid']) ? '' : $arr[$id]['pid']; 
        if($pid){
            return self::getLays($arr, $pid, $a);
        }else{
            if(count($a)>1) $a = array_reverse($a, true);
            return $a;
        }
    }
    // getLarr(id对应的树形分类:数组下标)
    static function getLarr($arr, $id, $key='kid', $a=array()){
        foreach($arr as $ik=>$iv) {
            if($iv[$key]==$id){
                $a[$id] = $iv['title'];
                if(!empty($iv['pid'])){
                    return self::getLarr($arr, $iv['pid'], $key, $a);
                }
            }
        }
        if(count($a)>1) $a = array_reverse($a, true);
        return $a;
    }

    // getLnks(arr对应的连接)
    static function getLnks($arr,$tpl="<a href='?key=[k]'>[v]</a>",$gap='»'){ 
        $str = '';
        foreach($arr as $k=>$v){
            $lnk = str_replace(array('[k]','[v]'),array($k,$v),$tpl);
            $str .= (empty($str) ? '' : $gap).$lnk;
        }
        return $str;
    }
    
    // getOptions(Select)
    static function getOpt($arr,$def='',$msg='',$frame=1){ 
        $_groups = glbConfig::read('groups'); 
        if(is_string($arr) && isset($_groups[$arr])){
            $imod = glbConfig::read($arr);
            $arr = $imod['i'];  
        }
        $a = basArray::opaItems($arr,'',$frame);
        return basElm::setOption($a,$def,empty($msg) ? '-(def)-' : $msg);
    }
    

}
