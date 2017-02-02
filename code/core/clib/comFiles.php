<?php

// Files类
class comFiles{    

    //重定义file_get_contents来兼容不支持此函数的PHP
    static function get($fname){
        //return file_get_contents($fname);
        if(!file_exists($fname) || is_dir($fname)){
            return '';
        }else{
            $fp = fopen($fname, 'r');
            $size = filesize($fname);
            if($size==0) return '';
            $ct = fread($fp, $size);
            fclose($fp);
            return $ct;
        }
    }

    //重定义file_put_contents来兼容不支持此函数的PHP
    static function put($fname, $data){
        //return file_put_contents($fname, $data);
        $fp = @fopen($fname, "w");
        if(!$fp){
            $re = FALSE;
        }elseif(flock($fp,LOCK_EX)){ // 排它性的锁定
            fwrite($fp, $data);
            flock($fp,LOCK_UN); // release lock
            fclose($fp);
            $re = TRUE;
        }else{
            $re = FALSE;
        }
        return $re;
    }

    // 移除BOM
    static function killBOM($str){ 
        $len = strlen($str); 
        if($len==0) return $str;
        $start = 0;
        for($i=0; $i<$len; $i++) {
            $chr = ord($str[$i]);
            if(in_array($chr,array(239,187,191))){
                $start++;
            }else{
                return $start ? substr($str,$start) : $str;
                break;
            }
        }
        return $str;
    }

    static function getTIcon($file){ 
        $cfg = read('filetype','sy');
        $type = $icon = 'unknow';
        $ext = strtolower(strrchr($file,"."));
        $ext = substr($ext,1);
        foreach($cfg as $k=>$v){
            if(in_array($ext,$v)){
                $icon = $v[0];
                $type = $k;
                break;
            }
        }
        return array('type'=>$type,'icon'=>$icon); //'unknow';
    }

    static function listScan($dir,$sub='',$skips=array()){
        $re = array(); $mCount = 1200;
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if($file=='.'||$file=='..') continue;
            $key = "{$sub}$file";
            $fp = "$dir/$file"; 
            if(count($re)<$mCount && is_dir($fp)){ //不用:file_exists
                if(empty($sub) && !empty($skips) && in_array($file,$skips)) continue; 
                $re = array_merge($re,self::listScan($fp,"$sub$file/"));
            }else{ 
                $mtime = filemtime($fp);
                if(count($re)<$mCount){
                    $re[$key] = array($mtime,filesize($fp));
                }
            }
        }
        closedir($handle);
        return $re;
    }

    static function listDir($dir,$key=''){
        if(!is_dir($dir)) return array(); 
        $re = array('dir'=>array(),'file'=>array());
        // --- scandir();
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if($file=='.'||$file=='..') continue;
            $fp = "$dir/$file";
            $mtime = filemtime($fp);
            if(is_file($fp)){
                $re['file'][$file] = array($mtime,filesize($fp));
            }else{
                $re['dir'][$file] = $mtime; 
            }
        }
        closedir($handle);
        if($key){ return $re[$key]; }
        return $re;
    }
    
    // 目录状态统计(大小,文件数,目录数)
    static function statDir($path){
        $msize = 0;
        $fcount = 0;
        $dcount = 0;
        if ($handle = opendir ($path)){
            while (false !== ($file = readdir($handle))){
                $nextpath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link ($nextpath)){
                    if (is_dir ($nextpath)){
                        $dcount++;
                        $result = self::statDir($nextpath);
                        $msize += $result['nsize'];
                        $fcount += $result['cfile'];
                        $dcount += $result['cdir'];
                    }elseif (is_file ($nextpath)){
                        $msize += filesize ($nextpath);
                        $fcount++;
                    }
                }
            }
        }
        closedir($handle);
        $total['nsize'] = $msize;
        $total['cfile'] = $fcount;
        $total['cdir'] = $dcount;
        return $total;
    }

    static function chkDirs($subs,$flag='',$isfile=1){ 
        if(empty($subs)) return;
        if(strstr($subs,'../')) return;
        if($isfile){
            return self::chkDirs(dirname($subs),$flag,0);
        }
        $check = basStr::filKey($subs,'!()-@_~/'); //.+,;^`
        if($check!=$subs) return;
        $path = comStore::cfgDirPath($flag,'dir');
        $path || $path = DIR_DTMP;
        $a = explode('/',$subs); 
        $i = 0; $tmp = '';
        if(count($a)>0){
            foreach($a as $d){ 
                //if(empty($d)) return;
                if(!is_dir($path."$tmp/$d")){ 
                    mkdir($path."$tmp/$d", 0777, true);    
                    foreach(array('htm','html') as $var) @touch($path."$tmp/$d".'/index.'.$var);    
                }
                $tmp .= "/$d"; $i++;
                if($i==12) break;
            }
        }
    }

    //遍历删除目录和目录下所有文件
    static function delDir($dir,$delself=0,$keep='',$first=1){
        if(strlen($dir)<6) return false; //  /a/b  c:/a/b
        if(!defined('indo_Verset')){
            if(strstr($dir,DIR_CODE) || strstr($dir,DIR_ROOT) || strstr($dir,DIR_STATIC) || strstr($dir,DIR_VENDUI) || strstr($dir,DIR_VENDOR)) return false;
        }
        if($first){
            if(is_file($dir)){ return @unlink($dir); };
            if(in_array(substr($dir,-1,1),array('/',"\\"))){ $dir = substr($dir,strlen($dir)-1); }
            if(!is_dir($dir)){ return false; };
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false){
            if ($file != "." && $file != ".."){
                if(in_array($file,array('index.htm','index.html')) && empty($delself)) continue;
                if($keep && strstr($keep,$file)) continue;
                is_dir("$dir/$file") ? self::delDir("$dir/$file",1,'',0):@unlink("$dir/$file");
            }
        }
        if(!empty($delself)){
            if (readdir($handle) == false){
                closedir($handle);
                @rmdir($dir);
            }
        }
    }
    
    // 是否可写 //if(is_writable($pfile)){}
    static function canWrite($dir){
        if(is_dir($dir)){
            $file = '/__'.basKeyid::kidTemp().'__.test';
            if($fp = @fopen($dir.$file, 'w')) {
                @fclose($fp);
                @unlink($dir.$file);
                $fwrite = 1;
            }else $fwrite = 0;
        }elseif(is_file($dir)){
            $fwrite = is_writable($dir);
        }else{
            $fwrite = 0;    
        }
        return $fwrite;
    }
    
    // 复制目录 : $src -> $dst
    // $skip : 忽略目录
    static function copyDir($src,$dst,$skip=array(),$skfile=array()) {  // 原目录，复制到的目录
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if(is_dir($src.'/'.$file)){
                    if(!empty($skip) && in_array($file,$skip)) continue;
                    self::copyDir($src.'/'.$file, $dst.'/'.$file,$skip,$skfile);
                }else{
                    if(!empty($skfile) && in_array($file,$skfile)) continue;
                    @copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }
    
}
