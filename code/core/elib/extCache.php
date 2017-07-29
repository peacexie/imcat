<?php
//缓存类
class extCache{

    protected $cache = NULL;
    
    function __construct($ucfg = array()) {
        $cfg = glbConfig::read('cache','ex');
        if(!empty($ucfg)){
            $cfg = array_merge($cfg,$ucfg);
        }
        $driver = $cfg['type'];
        require_once DIR_CODE . '/adpt/cache/' . $driver . '.php';
        $this->cache = new $driver($cfg);
    }

    //读取缓存
    function get($key) {
        return $this->cache->get($key);   
    }
    
    //设置缓存
    function set($key, $value, $expire = 1800) {
        return $this->cache->set($key, $value, $expire);
    }
    
    //自增1
    function inc($key, $value = 1) {
        return $this->cache->inc($key, $value);    
    }
    
    //自减1
    function des($key, $value = 1) {
        return $this->cache->des($key, $value);    
    }
    
    //删除
    function del($key) {
        return $this->cache->del($key);
    }
    
    //清空缓存
    function clear() {
        return $this->cache->clear();    
    }

    // static-functions

    // cache-time : 30s,60m,12h,7d,4w,12m; 默认单位m
    static function CTime($ctime=30){ 
        if(is_numeric($ctime) || strpos($ctime,'m')){
            $ctime = intval($ctime)*60; 
        }elseif(strpos($ctime,'h')){
            $ctime = intval($ctime)*3600;
        }elseif(strpos($ctime,'d')){
            $ctime = intval($ctime)*86400;
        }elseif(strpos($ctime,'w')){
            $ctime = intval($ctime)*86400*7;
        }elseif(strpos($ctime,'m')){
            $ctime = intval($ctime)*86400*30;
        }else{ 
            $ctime = intval($ctime);
        }
        $ctime<60 && $ctime = 86400; //最小60s
        return $ctime;
    }

    // cache-path, dir: /12/34/ab 
    static function CPath($sKey,$mkdir=0,$base=''){
        $file = $sKey; $kmd5 = md5($sKey); $aDir = array();
        for($i=0;$i<5;$i=$i+2){ $aDir[] = substr($kmd5,$i,2); }
        if($mkdir){
            $base = $base ? $base : DIR_DTMP.'/cache';
            foreach(array(0,1,2) as $k){
                $tmp = $base.'/'.$aDir[$k];
                if(!is_dir($tmp)){
                    $flag = mkdir($tmp, 0666);
                    if(!$flag) return false;
                }
                $base = $tmp;
            }
        }
        $file = str_replace(array('/','+','*','|','?',':','%'),array('~','-','.','!','$',';',''),$file); 
        $file = basStr::filTitle($file); //del:&,#
        //$file = (strlen($file)>130 ? substr($file,0,130) : $file).'~'.md5($file);
        if(strlen($file)>150) $file = substr($file,0,120).'~'.md5($file);
        $dir = '/'.implode('/', $aDir);
        return array('dir'=>$dir,'file'=>$file);
    }

}
