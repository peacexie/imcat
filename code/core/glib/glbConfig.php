<?php

// Cache - 读写
class glbConfig{    

    public static $_CACHES_YS = array();//将读取过的缓存暂存可重用

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
            $base = DIR_CODE;
        }elseif(in_array($dir,array('dset'))){
            $key = "_$file";
            $file = "/dset/$key.cfg.php";
            $base = DIR_DTMP;
        }elseif(in_array($dir,array('sy','ex'))){
            $key = "_{$dir}_$file";
            $file = "/cfgs".($dir=='sy' ? "/sycfg" : "/excfg")."/".substr($key,1).".php";
            $base = DIR_CODE;
        }elseif(in_array($dir,array('va','vc','ve'))){
            $tpldir = cfg('tpl.tpl_dir');
            $key = "{$tpldir}_$file"; $kk = "_{$dir}_$file"; 
            $file = vopTpls::pinc("_config/{$dir}_$file",'',0); 
            $base = DIR_SKIN;
        }
        $file = "$base$file"; 
        $ck = "{$dir}_$key";
        if(!isset(self::$_CACHES_YS[$ck])){
            if(file_exists($file)){ // inc大文件，其实很占时间
                require($file); 
            }else{ 
                return array();
            }
            $tmp = self::$_CACHES_YS[$ck] = isset($$kk) ? $$kk : $$key; 
            if(is_array($tmp) && (!empty($tmp['i'])) && is_string($tmp['i'])){
                 self::$_CACHES_YS[$ck]['i'] = self::tmpItems($modid);
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
    
    //$_vc = vcfg('home'); //'news'
    static function vcfg($mod){ 
        $renull['c']['vmode'] = 'close';
        $tpldir = cfg('tpl.tpl_dir');
        if(empty($tpldir)) return $renull;
        $key = "{$tpldir}_$mod"; //检查缓存
        if(isset(self::$_CACHES_YS[$key])) return self::$_CACHES_YS[$key];
        $_groups = self::read('groups'); 
        if(!file_exists(vopTpls::pinc('_config/va_home'))) return array();
        $hcfgs = self::read('home','va'); 
        if($mod=='home'){ //首页
            $re = $hcfgs;
        }elseif(in_array($mod,$hcfgs['close'])){ //关闭模块
            $re = $renull; 
        }elseif(isset($hcfgs['imcfg'][$mod])){ 
            $re = self::read($hcfgs['imcfg'][$mod],'vc'); //导入模块
        }elseif(in_array($mod,$hcfgs['extra'])){ //扩展模块
            $re = self::read($mod,'ve');             
        }elseif(file_exists(vopTpls::pinc("_config/vc_$mod"))){ //常规模块
            $re = self::read($mod,'vc'); 
        }elseif(isset($_groups[$mod]) && $_groups[$mod]['pid']=='docs'){ //默认文档处理,(按va_docs)
            $re = self::read('docs','va');  
        }else{ //没有找到规则-当做关闭
            $re = $renull;    
        } 
        $re['c']['etr'] = vopTpls::etr1(0,$tpldir);
        if(isset($re['c']['vmode']) && $re['c']['vmode']=='static' && empty($re['c']['stext'])){ 
            $re['c']['stext'] = $hcfgs['c']['stext']; //模块未设置后缀,则继承home的后缀
        }
        self::$_CACHES_YS[$key] = $re;
        return $re;
    }

}
