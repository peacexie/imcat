<?php
namespace imcat;

// Url 类
class vopUrl{    
    
    static $params = array('mkv','mod','key','view','type','hcfg','vcfg');
    static $keepmk = array('c','d','m','t','u','mhome','mtype','detail'); // mext
    
    // get/url初始数据
    static function iget($q=''){
        global $_cbase;
        $q = empty($q) ? (empty($_SERVER['QUERY_STRING'])?'home':$_SERVER['QUERY_STRING']) : $q;
        if(preg_match("/^[\w\-\.]{3,24}\=\&\w+/i",$q)){
            $q = str_replace('=&','&',$q);
        } // 修正微信分享url:?2018-12-31xx=&from=timeline
        $uri = $_SERVER['REQUEST_URI']; // /dev/mkv.htm?api=Local
        if(strpos($uri,'.htm?') || strpos($uri,'.html?')){
            $tmp = parse_url($uri);
            $q = "mkv=$q&".$tmp['query'];
            parse_str($q, $_GET);
            $ua = $_GET;
        }else{
            parse_str((strstr($q,'mkv=')?'':'mkv=').$q, $ua);
        }
        if(empty($ua['mkv'])){ // || in_array($q,array('mkv=home'))
            vopShow::msg("a:[$q]:".basLang::show('vop_parerr'));
        }
        $mkv = empty($ua['mkv']) ? 'home' : $ua['mkv'];
        $re1 = preg_match("/^\w+(\-\-(so|list))?$/",$mkv); // modid, (--list)
        $re2 = preg_match("/^\w+\-[A-Za-z0-9]{1}\w*(\-\w+)?$/",$mkv); // modid-type, dop-a, (-v)
        $re3 = preg_match("/^\w+\.[A-Za-z0-9]{1}[\w-]*(\.\w+)?$/",$mkv); // mod.y-md-88, (-v)
        $re4 = preg_match("/(^home\-)|((\-|\_)$)/",$mkv);
        if(!($re1 || $re2 || $re3) || $re4){
            vopShow::msg("b:[$mkv]:".basLang::show('vop_parerr'));
        }
        $re = array('q'=>$q, 'mkv'=>$mkv, 'ua'=>$ua);
        return $re;
    }
    
    // mkv/mod初始分析
    static function imkv($re,$remod=0){
        $hcfg = glbConfig::vcfg('home'); 
        $mkv = $re['mkv']; $type = ''; $vcfg = array();
        // ?login -=>别名 ?uio-login // 都可访问
        if(isset($hcfg[$mkv])){
            $mkv = $re['mkv'] = "home-$mkv";
        }
        if(strpos($mkv,'.')){ //mod.y-md-88.view
            $a = explode('.',$mkv);
            $type = 'detail';
        }elseif(strpos($mkv,'-')){ //mod-type-view, --list, -awhua-v2
            $a = explode('-',"$mkv"); 
            $type = empty($a[1]) ? 'mext' : 'mtype';
        }else{ //mod
            $a = array($mkv,'');    
            $type = 'mhome';
        }
        $re1 = preg_match("/^[a-z0-9]{1}\_/",$a[0]); // 预留前缀:x_
        $re2 = in_array($a[1],self::$keepmk);
        if($re1 || $re2){
            vopShow::msg("c:[$mkv]:".basLang::show('vop_parerr'));
        }
        //$mod分析
        $mod = $a[0]; $key = $a[1]; $view = empty($a[2]) ? '' : $a[2];
        if($remod) return $remod=='a' ? $a : $mod;
        foreach(self::$params as $k) $re[$k] = $$k;
        return $re;
    }
    
    // tpl/key分析
    static function itpl($re){
        foreach(self::$params as $k) $$k = $re[$k]; 
        $tpl = ''; $dsub = $type;
        $cfg = array();
        if($type=='mtype'){
            $dsub = $key;
            if(isset($vcfg[$key])){
                $cfg = $vcfg[$key];
            }else{ 
                $mcfg = glbConfig::read($mod);
                if(isset($mcfg['i'][$key])){
                    $cfg = empty($vcfg['t']) ? '' : $vcfg['t'];
                    $dsub = $type;
                }
            }
        }elseif($type=='detail'){
            $cfg = empty($vcfg['d']) ? '' : $vcfg['d'];
            if(!empty($view) && isset($vcfg['d']['v'])){
                $cfg[$view] = $vcfg['d']['v'];
            }
        }elseif(isset($vcfg['m'])){ // mext,mhome
            $cfg = $vcfg['m']; 
        }
        if($view){
            if(isset($cfg[$view])){
                $tpl = $cfg[$view];
            }elseif(empty($cfg[1]) && in_array($view,array('so','list'))){
                $tpl = '';
            }else{
                vopShow::msg("d:[-$view]:".basLang::show('vop_parerr'));
            }
        }else{
            $tpl = is_array($cfg) ? (isset($cfg[0]) ? $cfg[0] : '') : $cfg; 
        }
        if(empty($tpl)){
            $tpl = "$mod/$dsub"; // $re['hcfg']['c']['extra']
        }
        // 处理{mod}
        $re['tplname'] = str_replace('{mod}',$mod,$tpl); 
        return $re;
    }

    // url分析
    static function init($q='',$ext=array()){
        $re = self::iget($q); 
        $re = self::imkv($re);
        if($re['mkv']=='home'){
            $re['vcfg'] = $re['hcfg'];
            $re['tplname'] = $re['hcfg']['m'];
        }else{ 
            $re['vcfg'] = glbConfig::vcfg($re['mod']); 
            if($re['type']=='mhome' && isset($re['vcfg']['m']) && $re['vcfg']['m']=='first')
                self::ifirst($re['mod']); //first跳转
            $re = self::itpl($re);
        }
        empty($re['hcfg']['u']) || $re['u'] = $re['hcfg']['u']; //自定义参数
        $re['vcfg'] = isset($re['vcfg']['c']) ? $re['vcfg']['c'] : '';
        $re['hcfg'] = $re['hcfg']['c']; 
        return $re; 
    }
    
    static function ifirst($mod,$re=''){
        $minfo = glbConfig::read($mod); 
        $key = empty($minfo['i']) ? '' : key($minfo['i']); 
        if($re=='key'){
            return $key;
        }elseif(defined('RUN_STATIC')){
            return "[301]-[$mod-$key]".basLang::show('core.vop_st301dir');
        }else{
            if(!$key){
                $url = "Error:[$mod-$key]";
            }else{
                $url = (self::fout("$mod-$key"));
                header("Location:?$mod-$key");
            }
            die($url);
        }
    }

    // url格式化输出, 处理静态,伪静态,url优化(只在前台或生成静态,后台用跳转...)
    // paras: array, string 
    static function fout($mkv='',$type='',$host=0){ //,$ext=array()
        global $_cbase;
        if(strpos($mkv,':')) return self::gtpl($mkv,$type,$host);
        $burl = self::burl($host); 
        //mkv分析
        if(strlen($mkv)<3) return self::bind($burl); //首页
        $type || $type = strpos($mkv,'.') ? '.' : '-';
        $a = explode($type,"$mkv$type$type");
        $mod = $a[0]; $key = $a[1]; $view = $a[2];
        $mcfg = glbConfig::vcfg($mod);
        $vmode = @$mcfg['c']['vmode']; $url = '';
        if(empty($key) && empty($view) && isset($mcfg['m']) && $mcfg['m']=='first'){
            $key = self::ifirst($mod,'key');
            $mkv .= "-$key";
        }
        //close,static
        if($vmode=='close') return '#close#'.$mkv;
        if($vmode=='static'){
            $vext = empty($view) ? '' : ".$view";
            $ust = '/'.vopStatic::getPath($mod,$key.$vext,0);
            $url = file_exists(DIR_HTML.$ust) ? PATH_HTML.$ust : '';
        }
        //动态
        if(empty($url)){
            $url = $burl.'?'.$mkv;
            // 处理伪静态
            if(!empty($_cbase['run']['tplcfg'][2])){
                $url = str_replace('.php?', $_cbase['run']['tplcfg'][2], $url);
            }
            if(!empty($_cbase['run']['tplcfg'][3])){
                $url .= $_cbase['run']['tplcfg'][3];
            }
        }
        $url = self::bind($url);
        return $url;
    }
    
    //base-url
    static function burl($host=0){ 
        global $_cbase;
        $dir = $_cbase['tpl']['vdir']; 
        $vcfg = glbConfig::read('vopcfg','sy'); 
        $burl = PATH_PROJ.$vcfg['tpl'][$dir][1];
        if($host){ //$full
            $burl = $_cbase['run']['rsite'].$burl;
        }
        return $burl;
    }
    
    //还原root路径
    static function root($val){
        $re = comStore::revSaveDir($val);
        $re = str_replace('{PATH_PROJ}',PATH_PROJ,$re);
        return $re;
    }
    //指定分组(tpl)下的url
    static function gtpl($str,$type='',$host=0){
        global $_cbase;
        if(!empty($_cbase['run']['tplcfg'])){
            $cfgold = $_cbase['run']['tplcfg']; // ??? 切换当中怎么这个变了?
        }
        $tplold = $_cbase['tpl']['vdir'];
        $a = explode(':',$str);
        $ck = vopTpls::check($a[0],0);
        if(empty($ck['ok'])) return "#close#{$a[1]}";
        $a[0] && vopTpls::set($a[0]);
        $path = self::fout($a[1],$type,$host);
        $a[0] && vopTpls::set($tplold);
        if(!empty($cfgold)){
            $_cbase['run']['tplcfg'] = $cfgold; // ??? vopTpls::etr1(使用...)
        }
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
        global $_cbase;
        $binds = $_cbase['ucfg']['dbind']; 
        if(empty($binds)) return $url;
        $na = glbConfig::read('dmbind','sy');
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
    
    // umkv：获取mkv: $_GET > $_cbase > 
    static function umkv($key,$ukey=''){
        global $_cbase; 
        $ukey || $ukey = $key;
        $val = basReq::val($ukey,'','Key',24);
        if(empty($val) && !empty($_cbase['mkv'][$key])){
            $val = $_cbase['mkv'][$key]; 
        }
        return $val;
    }

}

