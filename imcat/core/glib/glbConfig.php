<?php
namespace imcat;

// Cache - 读写
class glbConfig{    

    public static $_CACHES_YS = array(); // 将读取过的缓存暂存可重用
    public static $_CACHES_VC = array(); // views/_confg/_* 缓存


    // 获取关联信息: 
    // part.key ('relpb','a0208');
    static function relids($part, $key='', $rea=1){
        $fp = DIR_DTMP."/modex/_$part.cfg_php";
        $res = [];
        if(is_file($fp)){
            $data = file_get_contents($fp);
            $res = json_decode($data, 1); 
        } 
        if($key){
            $res = isset($res[$key]) ? $res[$key] : '';
            if($rea){ $res = array_filter(explode(',', $res)); }
            return $res;
        }else{
            return $res;
        }
    }

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
    static function read($file,$dir='modcm'){ 
        global $_cbase;
        $modid = $file; $base = DIR_DTMP; 
        if(in_array($dir,array('modcm','dset'))){
            $key = "_$file";
            $file = "/$dir/".$key.".cfg.php";
        }elseif($dir=='modex'){
            return self::tmpItems($file); 
        }elseif(in_array($dir,array('_c'))){ //栏目配置
            $key = "_c_$file"; 
            $file = "/modex/$key.cfg.php";
            if(!file_exists(DIR_DTMP.$file)) return array();
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
                if(isset($_cbase['tpl']['bdpart'])){
                    $bdpart = $_cbase['tpl']['bdpart'];
                    if(isset($_cbase['lang'][$bdpart]["i_$modid"]) && isset($tmp['i'])){
                        self::$_CACHES_YS[$ck]['i'] = basArray::Merge($tmp['i'], $_cbase['lang'][$bdpart]["i_$modid"]);
                    }
                    if(isset($_cbase['lang'][$bdpart]["f_$modid"]) && isset($tmp['f'])){
                        self::$_CACHES_YS[$ck]['f'] = basArray::Merge($tmp['f'], $_cbase['lang'][$bdpart]["f_$modid"]);
                    }
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
        if($mkey=='home' && !file_exists(DIR_VIEWS."/$dir/_config/va_home.php")){
            $cfgs = self::read('home','sy'); 
        }else{
            $file = DIR_VIEWS."/$dir/_config/{$mex}_$mkey.php";
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
        $dir = $_cbase['tpl']['vdir'];
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
        }elseif(isset($hc['extra']) && in_array($mod,$hc['extra'])){ //扩展模块
            $re = self::vinc($dir, $mod,'ve');             
        }elseif(file_exists(vopTpls::tinc("vc_$mod",0))){ //常规模块
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
