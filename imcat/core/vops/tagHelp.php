<?php
namespace imcat;

// 标签助手
class tagHelp{
    
    static $typtabs = array(
        'One'  => 'One:单条数据', 
        'List' => 'List:列表标签', 
        'Page' => 'Page:分页标签', 
        'Type' => 'Type:类别列表', 
        'Push' => 'Push:数据推送',
        'Free' => 'Free:自由调用',
    );

    // 自动标签名
    static function defTagname(){ 
        $ktab = KEY_TAB32; // mt_rand(1,20)
        $name = 'd_'.$ktab{intval(date("y")/4)}.$ktab{date("m")}.$ktab{date("d")};
        $name .= $ktab{date("H")}.$ktab{intval(date("i")/2)}.$ktab{intval(date("s")/2)};
        return $name;
    }
    
}
