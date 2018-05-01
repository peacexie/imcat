<?php

// Cache - 读写
class glbConfig{    

    public static $_CACHES_YS = array(); // 将读取过的缓存暂存可重用
    public static $_CACHES_VC = array(); // skin/_confg/_* 缓存

    // 获取自由参数: 
    // part.item.key ('parnav.group_a.title');
    static function parex($keys=''){
        $arr = explode('.',$keys); 
        if(empty($arr[0])) return '';
        $res = self::read("parex_{$arr[0]}",'dset');
        if(!empty($arr[2])){
            return isset($res[$arr[1]][$arr[2]]) ? $res[$arr[1]][$arr[2]] : '';
        }elseif(!empty($arr[1])){
            return isset($res[$arr[1]]) ? $res[$arr[1]] : '';
        }else{
            return $res;
        }
    }

    // read config
    // $_demo = read('demo');
    // $_sy_keepid = read('keepid','sy');
    // $dbcfg = read('db','cfg'); 
    static function read($file,$dir='modcm'){ 
        global $_cbase;
        $modid = $file;
        if(in_array($dir,array('modcm','modex'))){ 
            $key = "_$file";
            $file = "/$dir/".$key.($dir=='modcm' ? ".cfg.php" : "cfg_php");
            $base = DIR_DTMP;
        }elseif(in_array($dir,array('_c'))){ //栏目配置
            $key = "_c_$file"; 
            $file = "/modex/$key.cfg.php";
            $base = DIR_DTMP;
            if(!file_exists(DIR_DTMP.$file)) return array();
        }elseif(in_array($dir,array('cfg'))){
            $key = "_cfg_$file"; $kk = "_cfgs"; 
            $file = "/cfgs/boot/cfg_$file.php";
            $base = DIR_ROOT;
        }elseif(in_array($dir,array('dset'))){
            $key = "_$file";
            $file = "/dset/$key.cfg.php";
            $base = DIR_DTMP;
        }elseif(in_array($dir,array('sy','ex'))){
            $key = "_{$dir}_$file";
            $file = "/cfgs".($dir=='sy' ? "/sycfg" : "/excfg")."/".substr($key,1).".php";
            $base = DIR_ROOT;
        }
        $file = "$base$file"; 
        $ck = "{$dir}_$key";
        if(!isset(self::$_CACHES_YS[$ck])){
            if(file_exists($file)){ // inc大文件，其实很占时间
                require $file; 
                $tmp = self::$_CACHES_YS[$ck] = isset($kk) ? $$kk : $$key;
                if(is_array($tmp) && (!empty($tmp['i'])) && is_string($tmp['i'])){
                     self::$_CACHES_YS[$ck]['i'] = self::tmpItems($modid);
                }
            }else{ 
                self::$_CACHES_YS[$ck] = array();
            }
        }
        return self::$_CACHES_YS[$ck];
    }

    // save config
    static function save($data,$file,$dir='modcm',$type='php'){
        $key = "_$file";
        $file = "$dir/_$file.cfg";
        comFiles::chkDirs($file,'dtmp'); 
        $file = "/$file";
        if($type=='php'){
            if(is_array($data)){
                $data = var_export($data,1);
                $data = "\$$key = $data;";
            }
            $data = "<?php\n$data\n?>"; 
            $file .= ".php";
        }else{
            $file .= $type;
        } 
        comFiles::put(DIR_DTMP."$file",$data);
    }
    
    // ~tmp items
    static function tmpItems($mod,$itms=array()){
        $file = "modex/_$mod.cfg_php";
        comFiles::chkDirs($file,'dtmp'); 
        $file = DIR_DTMP."/$file";
        if(!empty($itms)){ //save
            $data = is_array($itms) ? comParse::jsonEncode($itms) : $itms; 
            comFiles::put($file,$data); 
        }else{ //get
            $data = comFiles::get($file); 
             $itms = comParse::jsonDecode($data); 
            return $itms;
        }
    }
    
    //返回模型中cfg的数组
    static function mcfg($mod,$re='array'){ 
        $mcfg = self::read($mod);
        if($re=='text') return @$mcfg['cfgs'];
        $cfgs = basElm::text2arr(@$mcfg['cfgs']);
        if($re!='array'){
            return @$cfgs[$re];
        }else{
            return $cfgs;    
        }
    }
    
    static function vinc($dir, $mkey='', $mex=''){
        if($mkey=='home' && !file_exists(DIR_SKIN."/$dir/_config/va_home.php")){
            $cfgs = self::read('home','sy'); 
        }else{
            $file = DIR_SKIN."/$dir/_config/{$mex}_$mkey.php";
            require $file; 
            $kk = "_{$mex}_$mkey"; 
            $cfgs = $$kk;
        }
        if($mkey=='home'){
            $cfgs['null'] = array('c'=>array('vmode'=>'dynamic'),'m'=>'');
        }
        $cfgs['c']['etr'] = vopTpls::etr1(0, $dir);
        return $cfgs;
    }

    //$_vc = vcfg('home'); //'news'
    static function vcfg($mod){
        global $_cbase;
        $dir = $_cbase['tpl']['tpl_dir'];
        $key = "{$dir}/$mod"; //检查缓存
        if(isset(self::$_CACHES_VC[$key])) return self::$_CACHES_VC[$key];
        $_groups = self::read('groups'); 
        $hcfgs = self::vinc($dir, 'home', 'va'); 
        $hc = $hcfgs['c']; $re = $hcfgs['null']; // 默认动态
        if($mod=='home'){ //首页
            self::$_CACHES_VC[$key] = $hcfgs;
            return $hcfgs;
        }elseif(isset($hc['imcfg'][$mod])){ 
            $re = self::vinc($dir, $hc['imcfg'][$mod],'vc'); //导入模块
        }elseif(in_array($mod,$hc['extra'])){ //扩展模块
            $re = self::vinc($dir, $mod,'ve');             
        }elseif(file_exists(vopTpls::pinc("_config/vc_$mod"))){ //常规模块
            $re = self::vinc($dir, $mod,'vc');   
        }
        $a = array('vmode','stext','stexp');
        foreach ($a as $k) { // 模块未设置,则继承home的设置
            if(!isset($re['c'][$k]) && isset($hc[$k])){
                $re['c'][$k] = $hc[$k];
            }
        }
        self::$_CACHES_VC[$key] = $re;
        return $re;
    }

}
