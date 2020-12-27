<?php
namespace imcat;
// 
class cacheFile{

    public $path  = null; //缓存目录
    public $exp = 3600; //默认缓存失效时间(1小时)

    function __construct($cfg = array()){
        $this->path = DIR_DTMP.$cfg['path'];
        $this->exp = $cfg['exp'];
        $this->prefix = $cfg['prefix'];
    }

    /**
     * 读取缓存<br />
     * 返回: 缓存内容,字符串或数组；缓存为空或过期返回null
     */
    function get($sKey){
        if(empty($sKey)) return false;
        $sFile  = self::fullPath($sKey);
        if(!file_exists($sFile)) return null;
        $handle = fopen($sFile, 'rb');
        if(intval(fgets($handle)) > $_SERVER["REQUEST_TIME"]){ //未失效期，取出数据
            $sData = fread($handle, filesize($sFile)); 
            fclose($handle);
            return unserialize($sData);
        }else{ // 已经失效期
            fclose($handle);
            return null;
        }
    }

    //写入缓存
    function set($sKey, $mVal, $iExpire=null){
        if(empty($sKey)) return false;
        $sFile = self::fullPath($sKey,1); 
        if(!$sFile) return false;
        $aBuf = array();
        $aBuf[] = $_SERVER["REQUEST_TIME"] + ((empty($iExpire)) ? $this->$exp : intval($iExpire));
        $aBuf[] = serialize($mVal);
        $handle = fopen($sFile,'wb');/*写入文件操作*/
        fwrite($handle, implode("\n", $aBuf));
        fclose($handle);
        return true;
    }

    //删除指定的缓存键值
    function del($sKey){
        if(empty($sKey)) return false;
        @unlink(self::fullPath($sKey));
        return true;
    }

    /**
     * 获取缓存文件全路径<br />
     * 返回: 缓存文件全路径<br />
     * $sKey的值会被转换成md5(),并分解为3级目录进行访问
     */
    function fullPath($sKey,$mkdir=0){
        if(empty($sKey)) return false;
        $path = extCache::CPath($sKey,$mkdir,$this->path);
        //dump(':::'.$this->path.$file);
        return $this->path.$path['dir'].'/'.$this->prefix.$path['file'];
    }

}

