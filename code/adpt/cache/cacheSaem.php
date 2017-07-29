<?php
// Memcache@新浪云平台
class cacheSaem {
    private $mmc = NULL;
    private $group = ''; 
    private $ver = 0;
    function __construct( $mcfgs = array() ) {
        $this->mmc = memcache_init();
        $this->group = $mcfgs['prefix'];
        $this->ver = intval( memcache_get($this->mmc, $this->group.'_ver') ); 
    }

    //读取缓存
    function get($key) {
        $expire = memcache_get($this->mmc, $this->group.'_'.$this->ver.'_time_'.$key);
        if(intval($expire) > $_SERVER["REQUEST_TIME"] ) {
             return memcache_get($this->mmc, $this->group.'_'.$this->ver.'_'.$key);
        } else {
            return false;
        }
    }
    
    //设置缓存
    function set($key, $value, $expire = 1800) {
        $expire = ($expire == -1)? $_SERVER["REQUEST_TIME"]+365*24*3600 : $_SERVER["REQUEST_TIME"] + $expire;
        memcache_set($this->mmc, $this->group.'_'.$this->ver.'_time_'.$key, $expire);//写入缓存时间
        return memcache_set($this->mmc, $this->group.'_'.$this->ver.'_'.$key, $value);
    }
    
    //自增1
    function inc($key, $value = 1) {
          return $this->set($key, intval($this->get($key)) + intval($value), -1);
    }
    
    //自减1
    function des($key, $value = 1) {
         return $this->set($key, intval($this->get($key)) - intval($value), -1);
    }
    
    //删除
    function del($key) {
        return $this->set($key, '', 0);
    }
    
    //全部清空
    function clear() {
        return  memcache_set($this->mmc, $this->group.'_ver', $this->ver+1); 
    }
    
}