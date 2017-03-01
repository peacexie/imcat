<?php 
/**
 * 类的自动加载
 * spl_autoload_register('func_name'); //可以多个...
 * spl_autoload_register(array('autoLoad_ys','load'));     
 */
class autoLoad_ys{
    
    static $acdirs = array();
    static $cfgpr4 = array();
    static $cfgnsp = array();
    static $cfgmap = array();
    
    static $upath = array();
    
    // autoLoad_ys::ureg('/adpt/wechat'); //code目录下
    // autoLoad_ys::ureg('/a3rd/wechat',0); //root目录下
    static function ureg($upath,$pcode=1){
        $key = $pcode ? 'code' : 'root';
        if(empty(self::$upath[$key]) || !in_array($upath,self::$upath[$key])){ 
            self::$upath[$key][] = $upath;
        }
        spl_autoload_register(array(__CLASS__,'uload'));
    }
    static function uload($name){ 
        foreach(self::$upath as $key=>$kpath){ 
            foreach($kpath as $path){ 
                self::doinc($path."/$name.php",constant("DIR_".uppercase($key)));
            }
        }
    }
    
    static function init(){ 
        require DIR_CODE.'/cfgs/boot/cfg_load.php';
        self::$acdirs = $_cfgs['acdir'];
        self::$cfgpr4 = $_cfgs['acpr4'];
        self::$cfgnsp = $_cfgs['acnsp']; 
        self::$cfgmap = $_cfgs['acmap'];
        spl_autoload_register(array(__CLASS__,'cload')); 
        spl_autoload_register(array(__CLASS__,'vload')); 
    }
    // 核心类库
    static function cload($name){ 
        $path = ''; 
        if(isset(self::$cfgmap[$name])){ // 自定义-class-map
            if(substr(self::$cfgmap[$name],0,1)=='~'){
                $base = DIR_CODE;
                $file = substr(self::$cfgmap[$name],1);
            }else{
                $base = DIR_VENDOR;
                $file = self::$cfgmap[$name];
            } 
            return self::doinc($file,$base);
        }else{ // 按[前缀-目录]对照加载
            $fx3 = substr($name,0,3); //echo "<br>$name";
            foreach(self::$acdirs as $dir=>$fixs){ //
                if(strstr($fixs,$fx3)){ 
                    return self::doinc("/$dir/$name.php",DIR_CODE);
                }
            }
        }
    }
    // 第三方
    static function vload($name){ 
        // -pr4规范
        foreach(self::$cfgpr4 as $k=>$v){ 
            if(!strstr($name,$k)) continue; //strstr 比file_exists快多了吧？
            $file = str_replace('\\', '/', str_replace($k, $v[0].'/', $name)).'.php'; 
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
    static function doinc($file,$base=''){
        global $_cbase; 
        if(file_exists($base.$file)){ 
            $_cbase['run']['aclass'][] = $file;
            require($base.$file); 
        }
    }
}

/**
 * 权限判断函数
 * 用于未加载核心类库场合
 */
function bootPerm_ys($key='',$re='0',$exmsg=''){
    global $_cbase; // 不能用cfg()
    $tid = preg_replace("/[^\w]/", '', $_cbase['safe']['safil']); 
    $sid = 'pmSessid_'.$tid; //echo $sid;
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
