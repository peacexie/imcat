<?php
(!defined('RUN_INIT')) && die('No Init');
include DIR_VENDOR.'/phpQuery/phpQuery.php';

class extQuery extends phpQuery{
    
    //使用phpQuery查询
    static function pqo($obj,$mark='body',$conv=array('ISO-8859-1','utf-8')){
        if(is_object($obj)) return $obj;
        $murl = substr($obj,0,7);
        if(in_array($murl,array('http://','https:/'))){
            $obj = comHttp::doGet($obj); 
        }
        $doc = self::newDocument($obj); 
        self::selectDocument($doc);
        $res = pq($mark); 
        empty($conv) || $res = mb_convert_encoding($res->html(),$conv[0],$conv[1]);
        return $res;
    }

    //使用phpQuery查询
    static function pq($data,$mark='li',$tags=array(),$conv=array('ISO-8859-1','utf-8')){
        $list = self::pqo($data,$mark,array());
        $re = array(); $rn = 0; //[$mark]
        foreach($list as $k=>$itm){ 
            foreach($tags as $tag){ 
                $val = pq($itm)->find($tag);
                empty($conv) || $val = mb_convert_encoding($val->html(),$conv[0],$conv[1]);
                $re[$k][$tag] = $val;
            }
        }
        return $re;
    }
    
}

/*
extQuery::newDocumentFile('http://www.php.net/'); 
$li = pq("nav li")->html(); 
echo "<hr>";
$li = str_replace("<a","<br><a",$li);
dump($li);

$li = extQuery::pq('http://www.php.net/','body',array('.navbar-inner','#trick'));
dump($li);
*/
