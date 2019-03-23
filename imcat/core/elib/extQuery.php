<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

include DIR_VENDOR.'/phpQuery/phpQuery.php';

class extQuery extends \phpQuery{

    static function pqa($data, $sel='li', $dtype=1){
        if(is_object($data)){
            $doc = $data;
        }else{
            if(is_array($data)){
                comHttp::$cache = $data[1];
                $data = comHttp::curlCrawl($data[0]);
            }
            if(!$dtype) $data = "<!DOCTYPE html>$data";
            $doc = \phpQuery::newDocument($data);   
        }
        $did = $doc->getDocumentID();
        if(!$sel) return $did;
        $list = pq($sel, $did);
        $res = [];
        foreach($list as $li) {
            $res[] = $li;
        }
        return $res;
    }

}

/*

// 1. $lists = extQuery::pqa([$url,30],'.item');
// 2. $lists = extQuery::pqa($html,'a',0); 
// 3. $doc = extQuery::newDocumentFile($url); 
//    $lists = extQuery::pqa($doc,'span'); // 

$rows = [];
$url2 = 'http://hezhou.loupan.com/xinfang/p2/';
$lists = extQuery::pqa([$url2,30],'.list-house li.item');
foreach($lists as $li) {
    $row['url'] = pq($li)->find('a:first')->attr('href');
    $img = pq($li)->find('img:first');
    $thumb = pq($img)->attr('data-src');
    $row['thumb'] = strpos($thumb,'images/nopic.') ? '' : $thumb;
    $row['title'] = pq($img)->attr('alt');
    $row['area'] = pq($li)->find('.address')->text();
    $row['price'] = pq($li)->find('.price')->text();
    $rows[] = $row;
}
dump($rows);

*/
