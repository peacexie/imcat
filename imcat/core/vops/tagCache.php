<?php
namespace imcat;

/*

*/
// 标签缓存类
class tagCache{

    // adblock:abfoot0,2cF0F
    static function showAdv($mkey, $reorg=0, $adtag=4){ 
        $mk = explode(':',$mkey);
        if(empty($mk[1])) return '';
        $a = explode(',',$mk[1]);
        $p = empty($a[1]) ? $adtag : substr($a[1],0,1);
        $s = (!empty($a[1]) && strlen($a[1])>1) ? ' '.substr($a[1],1) : ' cCCC'; 
        $cfg = array(
            1=>"TopL$s", 2=>"TopR$s",
            3=>"BotL$s", 4=>"BotR$s",
        );
        $sp = empty($cfg[$p]) ? '' : "<i class='advFlag adv{$cfg[$p]}'>广告</i>";
        $file = tagCache::caPath($mk[0],$a[0],1);
        $data = file_exists($file) ? comFiles::get($file).$sp : "$mkey";
        if($reorg) return $data;
        $re = basJscss::jsShow($data, 0);
        return $re;
    }
    
    static function jsTag($k, $mkv, $para){ 
        preg_match("/\[cache\,([a-z0-9]+)\]/", $para, $m);  
        if(!empty($m[0]) && !empty($m[1])){ // && intval($m[1])>0
            $pkey = str_replace(array("[List]", "[Page]","[One]") ,'', $para);
            $path = self::ctPath($pkey,basReq::val('tpldir'));
            $data = extCache::cfGet($path, $m[1], 'ctpl', 'str');
            $para = str_replace($m[0],'',$para);
        }else{
            $path = $data = ''; //无缓存,无数据
        }
        if(empty($data)){
            $data = self::jsData($k, $para); 
            if($path) self::setCache($path, $data);
        }
        $re = basJscss::jsShow($data, 0);
        return $re;
    }
    
    static function jsData($k,$data){ 
        ob_start();
        $vop = new vopShow(0);
        $vop->mkv = 'jstag';
        $vop->rjs($data);
        $re = ob_get_contents();
        ob_end_clean(); 
        return $re;
    }
    
    static function comTag($type, $mkv, &$paras){
        global $_cbase;
        if(!empty($_cbase['mkv']['q']) && strpos($_cbase['mkv']['q'],'=')>0){
            return ['','']; // ?mkv&page=2, /mkv?keywd=e
        }
        $cac = 0; $cex = $path = '';
        foreach($paras as $k=>$v){ 
            if($v[0]=='cache' && !empty($v[1])){
                $cac = $v[1];
                unset($paras[$k]);
            }
            if($v[0]=='stype'){
                $v[1] = empty($v[1]) ? $mkv : $v[1];
            }
            $cex .= '['.implode(',',$v).']';
        }
        if($cac){
            $nowtpl = $_cbase['run']['tplnow']; 
            $vdir = $_cbase['tpl']['vdir']; 
            $path = self::ctPath("[{$nowtpl}][$type]{$cex}", $vdir);
            $data = extCache::cfGet($path, $cac, 'ctpl', 'arr');
        }else{
            $data = ''; //无数据
        } 
        return array($path,$data);
    }
    
    static function ctPath($para, $tpldir){ 
        $para = str_replace(array('[modid,','[limit,','[cache,','[show,'), array('[m','[n','[c','[s'), $para); 
        $cp = extCache::CPath($para);
        $path = "/_tagc/$tpldir/{$cp['file']}.cac_htm";
        return $path;
    }
    static function caPath($mod, $type, $full=0){ 
        $path = "/_advs/$mod/$type.cfg_htm"; 
        $full && $path = DIR_CTPL.$path;
        return $path;
    }
    
    static function setCache($file, $data, $isa=0, $isp=0){
        global $_cbase; 
        if($isa){
            $data['page_bar'] = $isp ? $_cbase['page']['bar'] : [];
        }
        extCache::cfSet($file,$data,'ctpl');
    }
    
}
