<?php
#namespace imcat;

use imcat\basDebug;
use imcat\basLang;
use imcat\basReq;
use imcat\glbConfig;
use imcat\glbError;
use imcat\usrBase;

/**
 * 一组别名函数（使用Symfony的dump后添加的,有的叫助手函数）
 * 如果与其它程序一起使用，发现有如下函数冲突，请设置[run.outer]参数即可
 * 核心类库:core中，尽量不要使用别名函数
 */

// data(模型数据函数)
if(!function_exists('data')){ 
function data($mod, $whr='', $lmt='10', $ord='', $ops=[]){
    return \imcat\glbData::get($mod, $whr, $lmt, $ord, $ops);
} }

// dump(格式化输出：变量，数组，Object)
if(!function_exists('dump')){ 
function dump($var, $min=1){
    if($min=='min'){
        echo "<pre>"; print_r($var); echo "</pre>";
    }else{
        basDebug::varShow($var, '', $min);
        if(!in_array($min,[0,1,2])){
            basDebug::varShow($min, '', 1);
        }
    }
} }

// tpath(模板路径)
if(!function_exists('tpath')){ 
function tpath($base=0, $assets=1, $dir=0){
    global $_cbase; 
    $root = $dir ? DIR_VIEWS : PATH_VIEWS;
    $vdir = $base ? 'base' : $_cbase['tpl']['vdir'];
    return "$root/$vdir".($assets ? '/assets' : '');
} }
// tex(调用模板扩展方法) 
// tex('texClass')->func() -=> \imcat\comm\texClass::func()
if(!function_exists('tex')){ 
function tex($cfile, $tpl=''){
    return \imcat\basClass::tex($cfile, $tpl);
} }
// tinc(模板包含) 
if(!function_exists('tinc')){ 
function tinc($fp, $inc=1, $refull=1){
    return \imcat\vopTpls::tinc($fp, $inc, $refull);
} }

// cfg(读取cbase配置) cfg('sys.cset');
if(!function_exists('cfg')){ 
function cfg($key, $def=''){
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
function read($file, $dir='modcm'){
    // $file:支持格式:news.i
    if(strpos($file,'.')){
        $t = explode('.', $file);
        $re = glbConfig::read($t[0], $dir);
        return isset($re[$t[1]]) ? $re[$t[1]] : $re;
    }else{
        return glbConfig::read($file, $dir);
    }
} }

// req(获得get/post参数)
if(!function_exists('req')){ 
function req($key, $def='', $type='Title', $len=255){
    return basReq::val($key, $def, $type, $len);
} }

// 输入 : addslashes 反斜杠
if(!function_exists('in')){ 
function in($data, $type=''){
    return basReq::in($data, $type);
} }

// 输出 : 格式: str,json,jsonp,xml
if(!function_exists('out')){ 
function out($data, $type='json'){
    if($type=='str'){ // 删除(addslashes添加的)反斜杠
        $data = basReq::out($data, $type);
    }else{ // fmt: json,jsonp,xml
        $data = \imcat\basOut::fmt($data, $type);
    }
    return $data;
} }

// db(获得db对象)
if(!function_exists('db')){ 
function db($config=array(), $catch=0){
    return \imcat\glbDBObj::dbObj($config, $catch);
} }

// user(获得user对象)
if(!function_exists('user')){ 
function user($uclass=''){
    return usrBase::userObj($uclass);
} }

// show-url:格式化url输出
if(!function_exists('surl')){ 
function surl($mkv='', $type='', $host=0){
    return \imcat\vopUrl::fout($mkv, $type, $host);
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
function eimp($type, $base='', $user=0){
    echo \imcat\basJscss::imp($type, $base, $user);
} }

// 一组handler函数 ---------------------------------

/*function uerr_handler($msg='') {  
    return $msg; 
}*/
// 默认异常处理函数
function except_handler_ys($e) {
    throw new glbError($e); 
}
// 默认错误处理函数
function error_handler_ys($Code, $Message, $File, $Line) {  
    throw new glbError(@$Code, $Message, $File, $Line); 
}
/*/ 当php脚本执行完成,或者代码中调用了exit ,die这样的代码之后：要执行的函数
register_shutdown_function(function(){
    glbError::show();
});*/

// user-function ---------------------------------

if(!function_exists('subRep')){ 
function subRep($str){
    $sup = \imcat\usrPerm::issup();
    $user = usrBase::userObj('Member');
    $mem = $user->userFlag=='Login';
    return ($sup||$mem) ? $str : \imcat\basStr::subReplace($str); 
} }

# $sPUser = MemPW.MemID
if(!function_exists('MD5_Mem')){ 
function MD5_Mem($sPUser){
    $sk = '0BA8C000-1111-2222-3333-PEACE0ASP444';
    $enc = md5($sPUser.strtolower($sk));
    return $enc;
} }
