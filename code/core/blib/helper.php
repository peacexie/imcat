<?php 
/**
 * 一组别名函数（使用Symfony的dump后添加的,有的叫助手函数）
 * 如果与其它程序一起使用，发现有如下函数冲突，请设置[run.outer]参数即可
 * 核心类库:core中，尽量不要使用别名函数
 */

// dump(格式化输出：变量，数组，Object)
function dump($var,$min=1){
    if($min=='min'){
        echo "<pre>"; print_r($var); echo "</pre>";
    }else{
        basDebug::varShow($var,'',$min);
    }
}
// cfg(读取cbase配置) cfg('sys.cset');
function cfg($key,$def=''){
    global $_cbase; 
    $org = $_cbase; $re = $def;
    $ak = explode('.',$key);
    foreach ($ak as $k) {
        $org = $re = isset($org[$k]) ? $org[$k] : $def;
    }
    return $re;
}
// show(显示语言标识)
function lang($mk, $val=''){
    if($val===0){ 
        echo basLang::show($mk, $val===0?'':$val);
    }else{
        return basLang::show($mk, $val);
    } 
}
// read(读取缓存)
function read($file,$dir='modcm'){
    // $file:支持格式:domain.dmacc
    if(strpos($file,'.')){
        $tmp = explode('.',$file);
        $file = $tmp[0];
    }
    $res = glbConfig::read($file,$dir);
    return empty($tmp[1]) ? $res : $res[$tmp[1]];
}
// req(获得get/post参数)
function req($key,$def='',$type='Title',$len=255){
    return basReq::val($key,$def,$type,$len);
}
// 输入 : addslashes 反斜杠
function in($data,$type=''){
    return basReq::in($data,$type);
}
// 输出 : 格式: str,json,jsonp,xml
function out($data,$type='json'){
    if($type=='str'){ // 删除(addslashes添加的)反斜杠
        $data = basReq::out($data,$type);
    }else{ // fmt: json,jsonp,xml
        $data = basOut::fmt($data,$type);
    }
    return $data;
}
// db(获得db对象)
function db($config=array()){
    return glbDBObj::dbObj($config);
}
// user(获得user对象)
function user($uclass=''){
    return usrBase::userObj($uclass);
}
// 格式化url输出
function surl($mkv='',$type='',$host=0){
    return vopUrl::fout($mkv,$type,$host);
}
// cmod模块关闭
function cmod($key=''){
    $_groups = glbConfig::read('groups'); 
    return !isset($_groups[$key]);
}
