<?php
    
// 路由
static function route($str=''){
    $org['self'] = $_SERVER['PHP_SELF']; // path/file.php/routdir/routpart
    $org['script'] = $_SERVER['SCRIPT_NAME']; // /path/file.php
    // PATH_INFO = /routdir/routpart, 可能不支持提示(cgi.fix_pathinfo=0)：No input file specified.
    $org['route'] = empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO']; 
    $org['query'] = $_SERVER['QUERY_STRING']; // act=test&key1=myval2
    parse_str($org['query'],$par); //parse_str() 函数把查询字符串解析到变量中。
    /*if(!safComm::urlQstr7()){
        vopShow::msg("[QUERY]参数错误!");
    }*/
    return array('org'=>$org,'par'=>$par);
}

