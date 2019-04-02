<?php 
namespace imcat;

/**
 * 类的自动加载 //可以多个...
 * spl_autoload_register('func_name');
 * spl_autoload_register(array('\\imcat\\basLoader','load'));     
 */
class basLoader{
    
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
        $fx3 = substr($cname,0,3);
        if(strpos($cname,'\\')){ // /views/chn/_ctrls/* 控制器
            return self::doinc('/'.str_replace('\\','/_ctrls/',$cname).".php", DIR_VIEWS, 1);
        /*}elseif($fx3=='exu'){ // /extra/ulibs/*扩展类库
            return self::doinc("/extra/ulibs/$cname.php", DIR_ROOT, 1);*/
        }else{ // /imcat/core/xdir/fixName.php 核心类库
            foreach(self::$acdirs as $dir=>$fixs){
                if(strstr($fixs,$fx3)) return self::doinc("/$dir/$cname.php", DIR_IMCAT, 1);
            }    
        }
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

# ---------------------------------

// 类库控制类
class basClass{

    // obj: "\\imcat\\xxxClass"::$func()
    // obj('xxxClass')->func() -=> \imcat\xxxClass::func()
    static function obj($cfile){
        $class = "\\imcat\\$cfile";
        return new $class();
    }

    // tex(调用模板扩展方法) 
    // tex('texClass')->func() -=> \imcat\chn\texClass::func()
    static function tex($cfile, $tpl=''){
        global $_cbase; 
        $tpl || $tpl = $_cbase['tpl']['vdir'];
        $class = "\\imcat\\$tpl\\$cfile";
        return new $class();
    }

}

# ---------------------------------

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

# ---------------------------------

