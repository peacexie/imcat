<?php
//缓存类
class extCache{
	protected  $cache = NULL;
	
	function __construct( $config = array()) {
		$cacheDriver = 'cp' . $type;
		require_once(DIR_CODE . '/adpt/cache/' . $cacheDriver . '.class.php');
		$this->cache = new $cacheDriver( $config );
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
}