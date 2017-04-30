<?php
/**
 * php文件缓存类 FileCache<br/>
 * @author Jerryli(hzjerry@gmail.com)
 * @version V0.20130513
 * @package
 * @example
 * <pre>
 * $oFC = new cacheFile('./tmp/'); //创建文件缓存类
 * $sKey = 'ab_123'; //缓存键值
 * $data = $oFC->get($sKey); //取得缓存
 * if(is_null($data))
 * &nbsp;&nbsp;$oFC->set($sKey, array('name'=>'ttt', 'datetime'=>date('Y-m-d H:i:s')), 10); //缓存不存在创建缓存
 * print_r($data);
 * </pre>
 */

//define('DIR_VARS', '/@vary');
 
final class cacheFile{
    
    private static $cacPath  = null; //缓存目录
    const cacExp = 3600; //默认缓存失效时间(1小时)

    function __construct($setPath='/cache/'){
        if(is_null(self::$cacPath)) self::$cacPath = DIR_DTMP.$setPath;    
    }

    /**
     * 读取缓存<br />
     * 返回: 缓存内容,字符串或数组；缓存为空或过期返回null
     */
    function get($sKey){
        if(empty($sKey)) return false;
        $sFile  = self::getFileName($sKey);
        if(!file_exists($sFile)) return null;
        $handle = fopen($sFile,'rb');
        if(intval(fgets($handle)) > $_SERVER["REQUEST_TIME"]){ //未失效期，取出数据
            $sData = fread($handle, filesize($sFile)); 
            fclose($handle);
            return unserialize($sData);
        }else{    //已经失效期
            fclose($handle);
            return null;
        }
    }

    //写入缓存
    function set($sKey, $mVal, $iExpire=null){
        if(empty($sKey)) return false;
        $sFile = self::getFileName($sKey,1); 
        if(!$sFile) return false;
        $aBuf = array();
        $aBuf[] = $_SERVER["REQUEST_TIME"] + ((empty($iExpire)) ? self::cacExp : intval($iExpire));
        $aBuf[] = serialize($mVal);
        $handle = fopen($sFile,'wb');/*写入文件操作*/
        fwrite($handle, implode("\n", $aBuf));
        fclose($handle);
        return true;
    }

    //删除指定的缓存键值
    function del($sKey){
        if(empty($sKey)) return false;
        @unlink(self::getFileName($sKey));
        return true;
    }

    /**
     * 获取缓存文件全路径<br />
     * 返回: 缓存文件全路径<br />
     * $sKey的值会被转换成md5(),并分解为3级目录进行访问
     */
    static function getFileName($sKey,$mkdir=0){
        if(empty($sKey)) return false;
        $key_md5 = md5($sKey);
        $aFileName = array();
        $aFileName[] = rtrim(self::$cacPath,'/');
        $aFileName[] = $key_md5{0} . $key_md5{1};
        $aFileName[] = $key_md5{2} . $key_md5{3};
        $aFileName[] = $key_md5{4} . $key_md5{5};
        $aFileName[] = strlen($sKey)>32 ? $key_md5 : basStr::filKey($sKey); 
        if($mkdir){
            $base = $aFileName[0];
            foreach(array(1,2,3) as $k){
                $tmp = $base.'/'.$aFileName[$k];
                if(!is_dir($tmp)){
                    $flag = mkdir($tmp, 0666);
                    if(!$flag) return false;
                }
                $base = $tmp;
            }
        }
        return implode('/', $aFileName);
    }

}

/*
    $fext = str_replace(array('/','+','*','|','?',':'),array('~','-','.','!','$',';'),$para); 
    $fext = str_replace(array('[modid,','[limit,','[cache,','[show,'),array('[m','[n','[c','[s'),$fext); 
    $fext = basStr::filTitle($fext); //del:&,#
    if(strlen($fext)>150) $fext = substr($fext,0,20).'~'.md5($fext);
    $path = "/_tagc/$tpldir$fext.cac_htm"; //".(substr($fmd5,0,1))."/
    return $path;
*/
