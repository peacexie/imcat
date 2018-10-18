<?php
namespace imcat;

// Store存储类
class comStore{    

    public static $objs = array();
    public static $cfgs = array();

    static function storeCfgs(){
        if(empty(self::$cfgs)){
            $scfg = glbConfig::read('store', 'ex');
            self::$cfgs = $scfg;
        }
    }

    // 移动:从临时文件到正式附件地址 $cls::method(); 在php5.2下报错
    static function moveUres($org, $obj, $fmove=1){
        self::storeCfgs();
        $clsName = self::rsType($obj);
        $cfg = self::$cfgs['types'][$clsName];
        if($fmove){
            $rsObj = self::rsCobj($clsName);
            $re0 = $rsObj->moveUres($org, $obj, $fmove); 
        }
        return $cfg['spre'].$obj.$cfg['sfix'];
    }
    // 删除:删除id下的附件资源
    static function delFiles($mod, $kid){
        $dir = comStore::getResDir($mod, $kid, 0);
        $re = comFiles::delDir(DIR_URES.'/'.$dir, 1);
        $tabs = self::rsType($dir);
        foreach($tabs as $cls=>$cfg) {
            $rsObj = self::rsCobj($cls);
            $re = $rsObj->delFiles($dir);
        }
        return $re;
    } 
    static function rsType($fpdir){
        // news/2018/9k-dj1t/2018-9k-dj41.jpg 
        // news/2018/9k-dj1t/2018-9k-dj41
        self::storeCfgs();
        $clsName = 'rsLocal'; // default
        $tmp = explode('/', $fpdir);
        $dfix = $tmp[0].'/';
        if(strpos($fpdir,'.')>0){
            $ticon = comFiles::getTIcon($fpdir); // type,icon
            $ftype = $ticon['type'];
            foreach(self::$cfgs['types'] as $cls=>$cfg) {
                $indir = empty($cfg['mdirs']) || in_array($dfix,$cfg['mdirs']);
                $intype = empty($cfg['ftypes']) || in_array($ftype,$cfg['ftypes']);
                if($indir && $intype){
                    return $cls;
                }
            }
            return $clsName;
        }else{
            $res = array();
            foreach(self::$cfgs['types'] as $cls=>$cfg) {
                if(empty($cfg['mdirs']) || in_array($dfix,$cfg['mdirs'])){
                    $res[$cls] = $cfg;
                }
            }
            return empty($res) ? $clsName : $res;
        }
    }
    static function rsCobj($fname){
        if(empty(self::$objs[$fname])){
            require_once DIR_IMCAT."/adpt/store/$fname.php";
            $class = "\\imcat\\$fname";
            self::$objs[$fname] = new $class();
        }
        return self::$objs[$fname];
    }

    /**
     * 上传到的临时目录，后续再移动到正式目录
     * @return string
     */
    static function getTmpDir($isfull=1){
        $user = usrBase::userObj();
        $sid = empty($user->sinit['sid']) ? usrPerm::getUniqueid('Cook','sip') : $user->sinit['sid'];
        $path = "@udoc/$sid"; //$modFix-
        comFiles::chkDirs($path, 'dtmp', 0);
        return ($isfull ? DIR_DTMP.'/' : '')."$path"; //PATH_ROOT
    }
    
    static function fixTmpDir($path){
        $pos = strpos($path,"/@udoc/");
        $path = PATH_DTMP.substr($path, $pos);
        return $path;
    }
    
    /**
     * 上传资源目录
     * @return string
     */
    static function getResDir($mod, $kid, $isfull=1, $chkdir=0){
        $grs = glbConfig::read('groups'); 
        $mcfgs = empty($grs[$mod]) ? array() : $grs[$mod];
        if(empty($kid)){
            die(__FUNCTION__);
        }
        $kpath = $kid; 
        $fmts = glbConfig::read('frame.resfmt', 'sy'); // docs,users; types; advs,coms 
        $fmt = (!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))) ? 1 : 0;
        foreach($fmts as $k=>$v){ // 默认:fmt=1 : yyyy/md-noid
            if(in_array($mod,$v)){ $fmt=$k; break; }
        }
        if(!empty($fmt) && strpos($kid,'-')>0){ 
            $ka = explode('-',$kid);
            if($fmt==1) $kpath = $ka[0].'/'.$ka[1].(empty($ka[2])?'':'-'.$ka[2]);
            if($fmt==2) $kpath = $ka[0].'-'.$ka[1].(empty($ka[2])?'':'/'.$ka[2]);
            if($fmt==3) $kpath = $ka[0].'/'.$ka[1].(empty($ka[2])?'':'/'.$ka[2]);
            if($fmt==6){ // /html/news-16/ab-8899.html
                $repath = "$mod-".substr($kpath,2,2)."/".substr($kpath,5);
            }else{
                $repath = "$mod/$kpath"; 
            }
        }else{
            $repath = "$mod/$kpath"; //empty($kpath);
        }
        $chkdir && comFiles::chkDirs($repath, 'ures', 0);
        return ($isfull ? DIR_URES.'/' : '').$repath;
    }
    
    //移动临时文件夹中的文件
    static function moveTmpDir($str, $mod, $kid, $ishtml=0){
        self::storeCfgs();
        $ar2 = self::moveTmpFmt($str, $ishtml);
        if(empty($ar2)) return $str;
        foreach($ar2 as $v){
            if(self::moveTmpOne($str, $v, $mod, $kid)) continue;
            $cfg = array(
                array('ures', "/$mod"), 
                array('html', "/$mod"),
                array('static', "/"), 
                array('root', "/"),
            );
            foreach($cfg as $cv){
                $str = self::moveRepRoot($str, $v, $cv[0], $cv[1]);
            }
        }
        foreach(self::$cfgs['types'] as $tk=>$row){
            if(!empty($row['vpre'] && !empty($row['spre']) && strpos($str,$row['vpre'])>=0)){
                $str = str_replace($row['vpre'], $row['spre'], $str);
            } 
        }
        return $str;
    }
    // deel:@udoc
    static function moveTmpOne(&$str, $v, $mod, $kid){
        global $_cbase;
        $fix = PATH_DTMP."/@udoc/";
        $flag = 0;
        if($org=strstr($v,$fix)){
            $orgfile = DIR_DTMP.substr($org, strlen(PATH_DTMP));
            $obj = self::getResDir($mod,$kid,0,1)."/".basename($org);
            // 可能:mpic,content:有同一个图片,第一次移动后,第二次就不存在了,所以也要替换
            $fmove = is_file($orgfile);
            $rmove = self::moveUres($orgfile, $obj, $fmove);
            $str = str_replace($v, $rmove, $str);
            $flag = 1; 
            /*
            if(in_array($org,$_cbase['run']['tmpFile'])){
                $str = str_replace($v, '{uresroot}/'.$obj, $str);
                $flag = 1; 
            }elseif(is_file($orgfile)){ 
                if($re=self::moveUres($orgfile,$obj)){
                    $str = str_replace($v, '{uresroot}/'.$obj, $str);
                    $_cbase['run']['tmpFile'][] = $org;
                    $flag = 1; 
                }
            }*/
        }
        return $flag;
    }
    // str2arr
    static function moveTmpFmt($str, $ishtml=0){
        if($ishtml){ //a,img,embed,value?,
            preg_match_all("/\s+(src|href|value)=(\S+)[\s|>]+/i", $str, $arr); //3
            $ar2 = empty($arr[2]) ? array() : str_replace(array("\\",'"',"'"), array(), $arr[2]); 
        }else{
            if(strpos($str,';')){ //pics
                $ar2 = explode(';', $str);
                foreach($ar2 as $k=>$v){
                    $art = explode(',', $v);
                    if(empty($art[0])) unset($ar2[$k]);
                    else $ar2[$k] = str_replace(array("\r","\n",' '), array('','',''), $ar2[$k]);
                }
            }else{
                $ar2 = array($str);
            }
        } 
        $ar2 = array_unique(array_filter($ar2));
        return $ar2;
    }
    
    //替换root路径
    static function moveRepRoot($str, $v, $key, $fix=''){
        global $_cbase;
        $rmain = $_cbase['run']['rmain'];
        $cfg = self::cfgDirPath($key, 'arr');
        $res = $v;
        if(strpos($res,$cfg[1].$fix)===0 && !empty($cfg[1])){
            $res = '{'.$key.'root}'.substr($res, strlen($cfg[1]));
            $str = str_replace($v, $res, $str);
        }
        $reps = glbConfig::read('repath', 'sy');
        foreach (array('att','tpl') as $k0) {
            if(!empty($reps[$k0])){
                $str = str_replace(array_values($reps[$k0]), array_keys($reps[$k0]), $str);
            }
        }
        $str = self::revSaveDir($str);
        return $str;
    }
    
    //part:dir,arr,else
    static function cfgDirPath($key, $part='dir'){
        $cfg = array(
            'root'   => array(DIR_ROOT,    PATH_ROOT),
            'imcat'  => array(DIR_IMCAT,   PATH_IMCAT),
            'views'  => array(DIR_VIEWS,   PATH_VIEWS),
            'ctpl'   => array(DIR_CTPL,    ''),
            'dtmp'   => array(DIR_DTMP,    PATH_DTMP),
            'vars'   => array(DIR_VARS,    PATH_VARS),
            'ures'   => array(DIR_URES,    PATH_URES),
            'html'   => array(DIR_HTML,    PATH_HTML),
            'vendor' => array(DIR_VENDOR,  PATH_VENDOR),
            'vendui' => array(DIR_VENDUI,  PATH_VENDUI),
            'static' => array(DIR_STATIC,  PATH_STATIC),
            'tpl'    => array(vopTpls::path('tpl'), ''), //可能没有定义
            'tpc'    => array(vopTpls::path('tpc'), ''),
        );
        $re = isset($cfg[$key]) ? $cfg[$key] : $cfg;
        if($part=='arr') return $re;
        $id = $part=='dir' ? 0 : 1;
        return empty($re[$id]) ? $key : $re[$id];
    }
    
    //还原保存的路径
    static function revSaveDir($str, $part=''){
        self::storeCfgs();
        $paths = self::cfgDirPath(0, 'arr');
        foreach($paths as $ck=>$itm){
            if(in_array($ck,array('tpl','tpc','ctpl','code'))) continue;
            $path = $part=='dir' ? $itm[0] : $itm[1];
            $str = str_replace(array('{'.$ck.'root}','{$'.$ck.'root}'), $path, $str); 
        }
        $reps = glbConfig::read('repath', 'sy');
        foreach (array('att','tpl') as $k0) {
            if(!empty($reps[$k0])){
                $str = str_replace(array_keys($reps[$k0]), array_values($reps[$k0]), $str);
            }
        }
        foreach(self::$cfgs['types'] as $tk=>$row){ 
            if(!empty($row['vpre'] && !empty($row['spre']) && strpos($str,$row['spre'])>=0)){
                $str = str_replace($row['spre'], $row['vpre'], $str); 
            } 
        }
        return $str;
    }
    
}
