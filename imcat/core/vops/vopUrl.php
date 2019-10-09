<?php
namespace imcat;

// Url 类
class vopUrl{    
    
    static $params = array('mkv','mod','key','view','type','hcfg','vcfg');
    static $keepmk = array('c','d','m','t','u','mhome','mtype','detail'); // mext

    // get/url初始数据
    static function iget(){
        $q = self::route(); 
        parse_str((strstr($q,'mkv=')?'':'mkv=').$q, $ua);
        $mkv = empty($ua['mkv']) ? 'home' : $ua['mkv'];
        $re1 = preg_match("/^[A-Za-z0-9]{2}\w*(\-\-(so|list))?$/",$mkv); // modid, (--list)
        $re2 = preg_match("/^[A-Za-z0-9]{2}\w*\-[A-Za-z0-9]{1}\w*(\-\w+)?$/",$mkv); // modid-type, dop-a, (-v)
        $re3 = preg_match("/^[A-Za-z0-9]{2}\w*\.[A-Za-z0-9]{1}[\w-]*(\.\w+)?$/",$mkv); // mod.y-md-88, (-v)
        if(!($re1 || $re2 || $re3)){
            vopShow::msg("b:[$mkv]:".basLang::show('vop_parerr'));
        }
        $re = array('q'=>$q, 'mkv'=>$mkv, 'ua'=>$ua);
        return $re;
    }
    
    // mkv/mod初始分析
    static function imkv($re, $remod=0){
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
        if(in_array($a[1],self::$keepmk)){
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
        $dsub = $type; $cfg = "";
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
        $tpl = ($cfg && is_string($cfg)) ? $cfg : "$mod/$dsub";
        if($view){
            if(isset($cfg[$view])){
                $tpl = $cfg[$view];
            }elseif(!in_array($view,array('so','list'))){
                $tpl = "$mod/$dsub-$view";
            }
        }else{
            if(is_array($cfg) && isset($cfg[0])){ $tpl = $cfg[0]; }
        }
        // 处理{mod}
        $re['tplname'] = str_replace('{mod}',$mod,$tpl);
        return $re;
    }

    // url分析
    static function init(){
        $re = self::iget();
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
    
    static function ifirst($mod, $re=''){
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
    static function fout($mkv='', $type='', $host=0){ //,$ext=array()
        if(strpos($mkv,':')) return self::gtpl($mkv, $type, $host);
        global $_cbase;
        $tcfg = $_cbase['run']['tplcfg']; //dump($tcfg);
        $burl = self::burl($host); 
        //mkv分析
        if(!$mkv) return self::bind($burl,$tcfg); //首页
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
            if(!empty($tcfg[2])){
                if($tcfg[2]=='?'){
                    $url = str_replace('?', '/', $url);
                }else{
                    $rp = empty($tcfg[4]) ? '.php?' : $tcfg[1].'?';
                    $url = str_replace($rp, $tcfg[2], $url);
                } //echo "($rp, $tcfg[2], $url)";
            }
            if(!empty($tcfg[3])){ $url .= $tcfg[3]; }
        }
        $url = self::bind($url,$tcfg);
        return $url;
    }
    
    //base-url
    static function burl($host=0){ 
        $type = $host ? 1 : 0;
        return $burl = vopTpls::etr1($type);
    }
    
    //还原root路径
    static function root($val){
        $re = comStore::revSaveDir($val);
        $re = str_replace('{PATH_PROJ}',PATH_PROJ,$re);
        return $re;
    }
    //指定分组(tpl)下的url
    static function gtpl($str, $type='', $host=0){
        global $_cbase;
        $tplold = $_cbase['tpl']['vdir'];
        $a = explode(':',$str);
        $a[0] && vopTpls::set($a[0]);
        $path = self::fout($a[1], $type, $host);
        $a[0] && vopTpls::set($tplold);
        return $path;
    }
    //format指定mod下的第一个类别的url
    static function f1st($mod, $re='(key)'){
        $key = self::ifirst($mod, 'key');
        $url = self::fout("$mod-$key");
        if($re) $url = str_replace(array("/$key.","-$key"),array("/$re.","-$re"),$url);
        return $url;
    }

    // 绑定域名
    static function bind($url, $tcfg=array()){
        global $_cbase;
        if(!empty($tcfg[5])){
            $rfp = $tcfg[5]['0'];
            if(substr($url,-1*strlen($rfp))==$rfp){
                $url = str_replace($rfp,$tcfg[5]['1'],$url);
            }
        }
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
    static function umkv($key, $ukey=''){
        global $_cbase; 
        $ukey || $ukey = $key;
        $val = basReq::val($ukey,'','Key',24);
        if(empty($val) && !empty($_cbase['mkv'][$key])){
            $val = $_cbase['mkv'][$key]; 
        }
        return $val;
    }

    // 路由
    static function route($def=''){
        $q = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        if(!empty($_SERVER['PATH_INFO'])){
            $q = substr($_SERVER['PATH_INFO'],1) . ($q ? "&$q" : '');
        }
        $q || $q = $def;
        // 去掉开头的:mkv= (肯定是动态)
        if(substr($q,0,4)=='mkv='){
            header('Location:?'.substr($q,4));
            die();
        }
        // 修正微信分享url:?2018-12-31xx=&from=timeline
        if(preg_match("/^([\w\-\.]{3,24})\=(\&from\=(\w+))?$/i",$q)){
            preg_match("/^([\w\-\.]{3,24})\=(\&from\=(\w+))?$/i", $q, $p);
            header('Location:?'.$p[1]); 
            die();
        }
        global $_cbase; // 去掉.htm尾巴
        $tcfg = empty($_cbase['run']['tplcfg']) ? [] : $_cbase['run']['tplcfg'];
        if(!empty($tcfg[3])){ $q = preg_replace("/{$tcfg[3]}/", '', $q, 1); }
        return $q;
    }

    // jumpr, uf.php?mkv -=> uf.php/mkv
    static function jumpr(){
        $uri = $_SERVER["REQUEST_URI"]; 
        if(preg_match("/\.php\?([\w\-\.]{3,36})$/i",$uri)){ 
            preg_match("/\.php\?([\w\-\.]{3,36})$/i", $uri, $p);
            $uf = $_SERVER["SCRIPT_NAME"];
            header("Location:$uf/{$p[1]}"); 
            die();
        }
    }
}

/*
    $org['self'] = $_SERVER['PHP_SELF']; // path/file.php/mod/act
    $org['script'] = $_SERVER['SCRIPT_NAME']; // /path/file.php
*/
