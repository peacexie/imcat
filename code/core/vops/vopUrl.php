<?php
/*
mod-type-view
mod.kid.view
mkv --=> tpl
*/
// Url 类
class vopUrl{    
    
    static $params = array('mkv','mod','key','view','type','hcfg','vcfg');
    
    // get/url初始数据
    static function iget($q=''){
        global $_cbase;
        $re = array(); 
        $q = strlen($q)==0 ? (empty($_SERVER['QUERY_STRING'])?'':$_SERVER['QUERY_STRING']) : $q; //可能为0
        if(empty($q) || $q=='home'){
            $ua = array();
            $mkv = 'home';
        }elseif(strpos($q,'=')){ 
            parse_str($q,$ua); 
            $mkv = empty($ua['mkv']) ? 'home' : $ua['mkv'];
            //about-profile&ext=2015-9d-d4k1 ??? 
            //$mkv = empty($ua['mkv']) ? key($ua) : $mkv = $ua['mkv'];
        }else{ //无=且不为空 
            $ua = array('mkv'=>$q);
            $mkv = $q;    
        }
        if(isset($_cbase['route'][$mkv])){
            $mkv = $_cbase['route'][$mkv];
        }
        $re['q'] = $q;
        $re['mkv'] = $mkv; 
        $re['ua'] = $ua;
        return $re;
    }
    
    // mkv/mod初始分析
    static function imkv($re,$remod=0){
        $hcfg = glbConfig::vcfg('home'); 
        $mkv = $re['mkv']; $type = '';
        if(isset($hcfg[$mkv])){
            $mkv = $re['mkv'] = "home-$mkv";
        }
        if(strpos($mkv,'.')){ //mod.id1-xxx-id2.view
            $a = explode('.',"$mkv.");
            $type = 'detail';
        }elseif(strpos($mkv,'-')){ //mod-type-view, mod--list, about-awhua-v2
            $a = explode('-',"$mkv"); 
            $type = empty($a[1]) ? 'mext' : 'mtype';
            if(isset($a[2]) && empty($a[2])){ // 结尾 -0, - 
                vopShow::msg($re['mkv'].lang('core.vop_parerr'));
            }
        }else{ //mod
            $a = array($mkv,'','');    
            $type = 'mhome';
        } 
        //$mod分析
        $mod = $a[0]; $key = $a[1]; $view = empty($a[2]) ? '' : $a[2];
        if($remod) return $remod=='a' ? $a : $mod;
        $vcfg = self::mcheck($hcfg,$mod); //mod-close, home-static, 
        if($type=='mhome' && $vcfg['m']=='first') self::ifirst($mod); //first跳转
        foreach(self::$params as $k) $re[$k] = $$k;
        return $re;
    }
    
    // tpl/key分析
    static function itpl($re){
        foreach(self::$params as $k) $$k = $re[$k]; 
        $tpl = '';
        if($type=='mtype'){ 
            if(isset($vcfg[$key])){
                $cfg = $vcfg[$key];
            }else{ 
                $mcfg = read($mod);
                if(isset($mcfg['i'][$key])){
                    $cfg = $vcfg['t'];
                }else{
                    vopShow::msg("[$key][type]".lang('core.vop_parerr'));
                }
            }
        }elseif($type=='detail'){
            $cfg = $vcfg['d'];
        }elseif($type=='mext'){ 
            $cfg = $vcfg['m'];
        }else{ //mhome
            $cfg = $vcfg['m']; 
        } 
        if($view && isset($cfg[$view])){ 
            $tpl = $cfg[$view]; 
        }else{ //if(!empty($cfg))
            $tpl = is_array($cfg) ? (isset($cfg[0]) ? $cfg[0] : '') : $cfg; 
            //about.2015-9d-d501.list2
            if($type=='detail' && $view && !isset($cfg[$view])){
                vopShow::msg($re['mkv']."[$view]".lang('core.vop_parerr'));
            }
            //indoc-get-my 接收列表
            if($type=='mtype' && $view){
                if(empty($vcfg['v']) || !strstr($vcfg['v'],$view)){
                    vopShow::msg($re['mkv']."[$view]".lang('core.vop_parerr'));
                }
            }    
        }
        if(empty($tpl)){
            vopShow::msg($re['mkv']."[tpl]".lang('core.vop_parerr'));
        }elseif($tpl=='close'){
            vopShow::msg($re['mkv']."[close]".lang('core.vop_closecat'));
        } // first
        // 处理{mod}, 
        $re['tplname'] = str_replace('{mod}',$mod,$tpl);
        return $re;
    }

    static function ifirst($mod,$re=''){
        $minfo = read($mod);
        $key = empty($minfo['i']) ? '' : key($minfo['i']); 
        if($re=='key'){
            return $key;
        }elseif(defined('RUN_STATIC')){
            return "[$mod]-[$mod-$key]".lang('core.vop_st301dir');
        }else{
            header("Location:?$mod-$key");
        }
    }
    // url分析
    static function init($q='',$ext=array()){
        $re = self::iget($q); 
        $re = self::imkv($re); 
        if($re['mkv']=='home'){
            $re['tplname'] = $re['hcfg']['m'];
        }else{ 
            $re = self::itpl($re); 
            if(empty($re)) return array();
        }
        empty($re['hcfg']['u']) || $re['u'] = $re['hcfg']['u']; //自定义参数
        $re['vcfg'] = isset($re['vcfg']['c']) ? $re['vcfg']['c'] : '';
        $re['hcfg'] = $re['hcfg']['c']; 
        return $re; 
    }
    
    static function mcheck($hcfg,$mod){
        global $_cbase;
        $tpldir = $_cbase['tpl']['tpl_dir'];
        $_groups = read('groups');
        $ukeyh = array_merge($hcfg['extra'],array('home'));
        if(!in_array($mod,$ukeyh) && !isset($_groups[$mod])){
            vopShow::msg("[{$mod}][mod]".lang('core.vop_parerr'));
        }elseif(!empty($_cbase["close_$tpldir"])){ //close
            vopShow::inc("_pub:stpl/close_info",0,1);
            die('');   
        }
        if($mod=='home'){ // home-static
            if($hcfg['c']['vmode']=='close'){
                vopShow::msg("[{$mod}][close]".lang('core.vop_closemod'));    
            }
            if(!defined('RUN_STATIC') && $hcfg['c']['vmode']=='static'){
                $file = vopStatic::getPath('home','home',1);
                if($path=tagCache::chkUpd($file,$hcfg['c']['stexp'],0)){ 
                    include($path); 
                    die("\n<!--".(date('Y-m-d H:i:s'))."-->");
                }
            }
            $vcfg = $hcfg;
        }else{ // mod-close
            $vcfg = glbConfig::vcfg($mod);
            if(!$vcfg){
                vopShow::msg("[{$mod}][vcfg]".lang('core.vop_parerr'));
            }elseif($vcfg['c']['vmode']=='close'){
                vopShow::msg("[{$mod}][close]".lang('core.vop_closemod'));    
            }
        }
        return $vcfg;
    }

    // url格式化输出, 处理静态,伪静态,url优化(只在前台或生成静态,后台用跳转...)
    // paras: array, string 
    static function fout($mkv='',$type='',$host=0){ //,$ext=array()
        if(strpos($mkv,':')) return self::ftpl($mkv,$type,$host);
        $burl = self::burl($host); 
        if(strstr($mkv,'?')){ // {surl(umc:?login)}
            return $burl .= "$mkv";
        }
        //mkv分析
        if(strlen($mkv)<3) return self::bind($burl); //首页
        $type || $type = strpos($mkv,'.') ? '.' : '-';
        $a = explode($type,"$mkv$type$type");    
        $mod = $a[0]; $key = $a[1]; $view = $a[2];
        $key = $key=='first' ? self::ifirst($mod,'key') : $key;
        $mcfg = glbConfig::vcfg($mod);
        $vmode = @$mcfg['c']['vmode']; $url = '';
        //close,static
        if($vmode=='close') return '#close#'.$mkv;
        if($vmode=='static'){ // && empty($view)
            $vext = empty($view) ? '' : ".$view";
            $ust = '/'.vopStatic::getPath($mod,$key.$vext,0);
            $url = file_exists(DIR_HTML.$ust) ? PATH_HTML.$ust : '';
        }
        //动态
        if(empty($url)){
            $key = empty($key) ? '' : "$type$key";
            $view = empty($view) ? '' : "$type$view";
            $mkv = "$mod$key$view";
            $url = $burl."?$mkv";
        }
        $url = self::bind($url);
        return $url;
    }
    
    //base-url
    static function burl($host=0){ 
        $dir = cfg('tpl.tpl_dir');
        $burl = vopTpls::etr1($host,$dir);
        return $burl;
    }
    
    //还原root路径
    static function root($val){ 
        $re = comStore::revSaveDir($val);
        $re = str_replace('{PATH_PROJ}',PATH_PROJ,$re);
        return $re;
    }
    //format指定tpl下的url
    static function ftpl($str,$type='',$host=0){
        $tplold = cfg('tpl.tpl_dir'); 
        $a = explode(':',$str);
        $ck = vopTpls::check($a[0],0);
        if(empty($ck['ok'])) return "#close#{$a[1]}";
        $a[0] && vopTpls::set($a[0]);
        $path = self::fout($a[1],$type,$host);
        $a[0] && vopTpls::set($tplold);
        return $path;
    }
    //format指定mod下的第一个类别的url
    static function f1st($mod,$re='(key)'){
        $key = self::ifirst($mod,'key');
        $url = self::fout("$mod-$key");
        if($re) $url = str_replace(array("/$key.","-$key"),array("/$re.","-$re"),$url);
        return $url;
    }

    // 绑定域名
    static function bind($url){
        $binds = cfg('ucfg.dbind'); 
        if(empty($binds)) return $url;
        $na = read('dmbind','ex');
        if(empty($na)) return $url;
        foreach($na as $v){
            $vbak = $v[0];
            $v[0] = str_replace('{html}',PATH_HTML,$v[0]);
            if(empty($v[2])){
                $url = str_replace($v[0],$v[1],$url);
            }else{ 
                if($v[2]==1){
                    $v[0] = "/^".preg_quote($v[0],"/")."/i";
                    $v[0] = str_replace(array("`d","`w","#`"),array("(\\d","(\\w","+)"),$v[0]);
                }else{ //自由写正则
                    $v[0] = str_replace('{html}',preg_quote(PATH_HTML,"/"),"/^$vbak/i");
                }
                $nurl = @preg_replace($v[0],$v[1],$url);
                $url = $nurl ? $nurl : $url;    
            }
        }
        return $url;
    }

    // 路由
    static function route($str=''){
        $org['self'] = $_SERVER['PHP_SELF']; // path/file.php/routdir/routpart
        $org['script'] = $_SERVER['SCRIPT_NAME']; // /path/file.php
        // PATH_INFO = /routdir/routpart, 可能不支持提示(cgi.fix_pathinfo=0)：No input file specified.
        $org['route'] = empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO']; 
        $org['query'] = $_SERVER['QUERY_STRING']; // act=test&key1=myval2
        parse_str($org['query'],$par); //parse_str() 函数把查询字符串解析到变量中。
        /*if(!safComm::urlQstr7()){
            vopShow::msg("[QUERY]参数错误!");
        }*/
        return array('org'=>$org,'par'=>$par);
    }
    
    // umkv：获取mkv: $_GET > $_cbase > 
    static function umkv($key,$ukey=''){
        $ukey || $ukey = $key;
        $val = req($ukey,'','Key',24);
        $cmk = cfg("mkv.$key");
        if(empty($val) && !empty($cmk)){
            $val = $cmk; 
        }
        return $val;
    }

}

