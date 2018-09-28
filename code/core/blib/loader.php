<?php 
namespace imcat;

/**
 * 类的自动加载 //可以多个...
 * spl_autoload_register('func_name');
 * spl_autoload_register(array('autoLoad_ys','load'));     
 */
class autoLoad_ys{
    
    static $acdirs = array();
    static $cfgpr4 = array();
    static $cfgnsp = array();
    static $cfgmap = array();

    static function init(){ 
        require DIR_ROOT.'/cfgs/boot/cfg_load.php';
        self::$acdirs = $_cfgs['acdir'];
        self::$cfgpr4 = $_cfgs['acpr4'];
        self::$cfgnsp = $_cfgs['acnsp']; 
        self::$cfgmap = $_cfgs['acmap'];
        spl_autoload_register(array(__CLASS__,'cload')); 
        spl_autoload_register(array(__CLASS__,'vload')); 
    }
    // 核心类库
    static function cload($name){
        if(substr($name,0,6)!='imcat\\') return;
        $cname = substr($name,6);
        if(strpos($cname,'\\')){ // 模板扩展函数
            // imcat\chn\texName // -=> skin/chn/_ctrls/texName.php 
            return self::doinc('/'.str_replace('\\','/_ctrls/',$cname).".php", DIR_SKIN, 1);
        }else{ // 核心类库
            // imcat\fixName // -=> code/core/xdir/fixName.php
            $fx3 = substr($cname,0,3);
            foreach(self::$acdirs as $dir=>$fixs){
                if(strstr($fixs,$fx3)){ // 按[前缀-目录]对照加载 
                    return self::doinc("/$dir/$cname.php", DIR_CODE, 1);
                }
            }    
        } // (控制器) imcat\chn\topicCtrl // -=> skin/chn/_ctrls/topicCtrl.php 

    }
    // 第三方
    static function vload($name){
        // -自定义-class-map
        if(isset(self::$cfgmap[$name])){
            $file = self::$cfgmap[$name];
            return self::doinc($file,'',1);
        }
        // -pr4规范
        foreach(self::$cfgpr4 as $k=>$v){
            if(!strstr($name,$k)) continue; //strstr 比file_exists快多了吧？
            $file = str_replace('\\', '/', str_replace($k, $v[0].'/', $name)).'.php';
            $file = str_replace('//', '/', $file);
            return self::doinc($file,DIR_VENDOR);
        }
        // -namespace规范
        foreach(self::$cfgnsp as $k=>$v){ 
            if(!strstr($name,$k)) continue; //strstr 比file_exists快多了吧？
            $file = str_replace('\\', '/', $v[0].'/'.$name).'.php'; 
            return self::doinc($file,DIR_VENDOR);
        }
        return ''; 
    }
    // inc
    static function doinc($file,$base='',$exist=0){
        global $_cbase; 
        if($exist || file_exists($base.$file)){ 
            $_cbase['run']['aclass'][] = $file;
            require $base.$file; 
        }
    }
}

// 权限判断函数,用于未加载核心类库场合
function bootPerm_ys($key='',$re='0',$exmsg=''){
    global $_cbase; // 不能用cfg()
    $sid = usrPerm::getSessid(); 
    if($re=='sid') return $sid;
    $sval = @$_SESSION[$sid];
    if(empty($key)){
        $msg = empty($sval) ? '-' : '';
    }else{ 
        $msg = strstr($sval,$key) ? '' : "$key";
    } 
    if($msg){
        if(!empty($re)) return $msg;
        $exmsg && $exmsg = "<hr>$exmsg";
        die("NO Permission :<br>\n$msg$exmsg"); 
    }
}

// 一组handler函数
/*function uerr_handler($msg='') {  
    return $msg; 
}*/
// 默认异常处理函数
function except_handler_ys($e) {
    throw new glbError($e); 
}
// 默认错误处理函数
function error_handler_ys($Code,$Message,$File,$Line) {  
    throw new glbError(@$Code,$Message,$File,$Line); 
}
// 当php脚本执行完成,或者代码中调用了exit ,die这样的代码之后：要执行的函数
function shutdown_handler_ys() {  
    //echo "(shutdown)";
    basDebug::bugLogs('handler',"[msg]","shutdown-".date('Y-m-d').".debug",'file');
}

//(!function_exists('intl_is_failure'))
