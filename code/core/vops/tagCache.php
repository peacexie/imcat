<?php
/*

*/
// 标签缓存类
class tagCache{
    
    // adblock:abfoot0,2cF0F
    static function showAdv($mkey){ 
        $mk = explode(':',$mkey);
        if(empty($mk[1])) return '';
        $a = explode(',',$mk[1]);
        $p = empty($a[1]) ? '4' : substr($a[1],0,1);
        $s = (!empty($a[1]) && strlen($a[1])>1) ? ' '.substr($a[1],1) : ' cCCC'; 
        $cfg = array(
            1=>"<i class='advFlag advTopL$s'>广告</i>",
            2=>"<i class='advFlag advTopR$s'>广告</i>",
            3=>"<i class='advFlag advBotL$s'>广告</i>",
            4=>"<i class='advFlag advBotR$s'>广告</i>",
        );
        $sp = empty($cfg[$p]) ? '' : $cfg[$p];
        $file = tagCache::caPath($mk[0],$a[0],1);
        $data = file_exists($file) ? comFiles::get($file).$sp : "$mkey";
        $re = basJscss::jsShow($data, 0);
        return $re;
    }
    
    static function jsTag($k,$mkv,$para){ 
        preg_match("/\[cache\,([a-z0-9]+)\]/", $para, $m);  
        if(!empty($m[0]) && !empty($m[1])){ // && intval($m[1])>0
            $pkey = str_replace(array("[List]","[Page]","[One]"),'',$para);
            $path = self::ctPath($pkey,basReq::val('tpldir')); 
            $fpath = self::chkUpd($path,$m[1]); 
            $data = $path ? comFiles::get($fpath) : ''; 
            $para = str_replace($m[0],'',$para);     
        }else{
            $path = $data = ''; //无缓存,无数据        
        }
        if(empty($data)){
            $data = self::jsData($k,$para); 
            if($path) self::setCache($path,$data);     
        }
        $re = basJscss::jsShow($data, 0);
        return $re;
    }
    
    static function jsData($k,$data){ 
        ob_start();
        $vop = new vopShow(0);
        $vop->rjs($data);
        $re = ob_get_contents();
        ob_end_clean(); 
        return $re;
    }
    
    static function comTag($type,$mkv,&$paras){ 
        $cac = 0; $cex = $path = $fmkv = ''; 
        foreach($paras as $k=>$v){ 
            if($v[0]=='cache' && !empty($v[1])){
                $cac = $v[1];
                unset($paras[$k]);
            }
            if($v[0]=='stype' && !isset($v[1])){
                $fmkv = "-$mkv";
            }
            $cex .= '['.implode(',',$v).']';    
        } 
        if($cac){ 
            global $_cbase;
            $nowtpl = $_cbase['run']['tplnow']; 
            $tpl_dir = $_cbase['tpl']['tpl_dir']; 
            $path = self::ctPath("[{$nowtpl}][$type]{$cex}",$tpl_dir);
            $cfile = self::chkUpd($path,$cac,'ctpl'); 
            $data = $cfile ? unserialize(comFiles::get($cfile)) : ''; 
        }else{
            $data = ''; //无数据
        } 
        return array($path,$data);
    }
    
    static function ctPath($para,$tpldir){ 
        $para = str_replace(array('[modid,','[limit,','[cache,','[show,'),array('[m','[n','[c','[s'),$para); 
        $cp = extCache::CPath($para);
        $path = "/_tagc/$tpldir/{$cp['file']}.cac_htm";
        return $path;
    }
    static function caPath($mod,$type,$full=0){ 
        $path = "/_advs/$mod/$type.cfg_htm"; 
        $full && $path = DIR_CTPL.$path;
        return $path;
    }
    
    static function setCache($file,$data,$isa=0){
        global $_cbase; 
        if($isa){
            $data['page_bar'] = $_cbase['page']['bar'];
            $data = serialize($data); //var_export
        }
        comFiles::chkDirs($file,'ctpl');
        comFiles::put(DIR_CTPL.$file,$data);
    }
    
    // -
    static function chkUpd($file,$ctime=30,$bdir='dtmp'){ 
        $ctime = extCache::CTime($ctime);
        $cfg = array(
            'dtmp'=>DIR_DTMP,
            'ures'=>DIR_URES,
            'html'=>DIR_HTML,
            'ctpl'=>DIR_CTPL,
        );
        $bdir = empty($bdir) ? '' : (isset($cfg[$bdir]) ? $cfg[$bdir] : $bdir); 
        if(file_exists($bdir.$file)){ 
            $last = filemtime($bdir.$file);
            if($last + $ctime > $_SERVER["REQUEST_TIME"]){ 
                return $bdir.$file;
            }
        }
        return '';
    }

}
