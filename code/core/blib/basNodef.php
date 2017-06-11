<?php

// basNodef
class basNodef{    
    
    static $_CACHES_LG = array();//将读取过的缓存暂存可重用
    
    // php5.5弃用了mysql扩展后,此函数也不可用了
    static function quoteSql($str,$noq=0){
        $db = glbDBObj::dbObj();
        $str = $db->quoteSql($str);
        if(empty($noq)){ 
            return $db->class=='pdox' ? $str : "'$str'";
        }else{ /* 不要括号 */ 
            return $db->class=='pdox' ? substr($str,1,strlen($str)-2) : $str;
        }
    }

    // 低版本用mime_content_type, 较高版本用finfo扩展, 都不能用则用table对照...
    static function mimeType($filename) {
        if(function_exists('finfo_open')){
            $finfo = finfo_open(FILEINFO_MIME);
            $mtype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mtype;            
        }elseif(function_exists('mime_content_type')) {
            return mime_content_type($filename);
        }else{
            return 'application/octet-stream';
        }
    }

}

