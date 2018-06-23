<?php 

/**
 * 一组别名函数（使用Symfony的dump后添加的,有的叫助手函数）
 * 如果与其它程序一起使用，发现有如下函数冲突，请设置[run.outer]参数即可
 * 核心类库:core中，尽量不要使用别名函数
 */

// dump(格式化输出：变量，数组，Object)
if(!function_exists('dump')){ 
function dump($var,$min=1){
    if($min=='min'){
        echo "<pre>"; print_r($var); echo "</pre>";
    }else{
        basDebug::varShow($var,'',$min);
    }
} }

// cfg(读取cbase配置) cfg('sys.cset');
if(!function_exists('cfg')){ 
function cfg($key,$def=''){
    global $_cbase; 
    $org = $_cbase; $re = $def;
    $ak = explode('.',$key);
    foreach ($ak as $k) {
        $org = $re = isset($org[$k]) ? $org[$k] : $def;
    }
    return $re;
} }

// lang-tag(显示语言标识)
if(!function_exists('lang')){ 
function lang($mk, $val=''){
    if($val===0){ 
        echo basLang::show($mk, $val===0?'':$val);
    }else{
        return basLang::show($mk, $val);
    } 
} }

// read(读取缓存)
if(!function_exists('read')){ 
function read($file,$dir='modcm'){
    // $file:支持格式:news.i
    if(strpos($file,'.')){
        $t = explode('.',$file);
        $re = glbConfig::read($t[0],$dir);
        return isset($re[$t[1]]) ? $re[$t[1]] : $re;
    }else{
        return glbConfig::read($file,$dir);
    }
} }

// req(获得get/post参数)
if(!function_exists('req')){ 
function req($key,$def='',$type='Title',$len=255){
    return basReq::val($key,$def,$type,$len);
} }

// 输入 : addslashes 反斜杠
if(!function_exists('in')){ 
function in($data,$type=''){
    return basReq::in($data,$type);
} }

// 输出 : 格式: str,json,jsonp,xml
if(!function_exists('out')){ 
function out($data,$type='json'){
    if($type=='str'){ // 删除(addslashes添加的)反斜杠
        $data = basReq::out($data,$type);
    }else{ // fmt: json,jsonp,xml
        $data = basOut::fmt($data,$type);
    }
    return $data;
} }

// db(获得db对象)
if(!function_exists('db')){ 
function db($config=array(),$catch=0){
    return glbDBObj::dbObj($config,$catch);
} }

// user(获得user对象)
if(!function_exists('user')){ 
function user($uclass=''){
    return usrBase::userObj($uclass);
} }

// show-url:格式化url输出
if(!function_exists('surl')){ 
function surl($mkv='',$type='',$host=0){
    return vopUrl::fout($mkv,$type,$host);
} }

// sys-mod:系统(有效)模块,关闭或不存在返回`false`
if(!function_exists('smod')){ 
function smod($key=''){
    $_groups = glbConfig::read('groups'); 
    return isset($_groups[$key]);
} }

// close-mod:模块关闭(兼容v4.1-)
if(!function_exists('cmod')){ 
function cmod($key=''){
    return !smod($key);
} }

// echo-import:css,js
if(!function_exists('eimp')){ 
function eimp($type,$base='',$user=0){
    echo basJscss::imp($type,$base,$user);
} }

// user-function

if(!function_exists('subRep')){ 
function subRep($str){
    $sup = usrPerm::issup();
    $user = usrBase::userObj('Member');
    $mem = $user->userFlag=='Login';
    return ($sup||$mem) ? $str : basStr::subReplace($str); 
} }

# $sPUser = MemPW.MemID
if(!function_exists('MD5_Mem')){ 
function MD5_Mem($sPUser){
    $sk = '0BA8C000-1111-2222-3333-PEACE0ASP444';
    $enc = md5($sPUser.strtolower($sk));
    return $enc;
} }
