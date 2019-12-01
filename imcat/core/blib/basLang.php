<?php
namespace imcat;

// basLang多语言类
class basLang{    
    
    static $_CACHES_LG = array();//将读取过的缓存暂存可重用

    // ('fsystem'), ('cfglibs.upload');
    static function ucfg($file, $key=0){
        if(strpos($file,'.')){
            $mk = explode(".",$file);
            $file = $mk[0];
            $key = $mk[1];
        } 
        $re = self::getCfgs($key, $file, 'ucfgs');
        return $re;
    }

    // {basLang::show(core.view_times,$click)}
    // {basLang::show(core.sys_name)}
    static function show($mk, $val='', $dir='kvphp'){
        $mk = str_replace("'",'',$mk);
        if(!strpos($mk,'.')) $mk = "core.$mk";
        $arr = explode('.',$mk); 
        $re = self::getCfgs($arr[1], $arr[0], $dir);
        if(strlen($val)>0){
            $re = str_replace('{val}',$val,$re);
        }
        if(strpos($re,'{val}')){
            $re = str_replace('{val}','',$re);
        }
        return $re;
    }
    
    // 多语言...
    static function getCfgs($key, $mod='core', $dir='kvphp'){
        global $_cbase;
        $lang = $_cbase['sys']['lang'];
        if(isset(self::$_CACHES_LG[$mod])){
            $cfgs = self::$_CACHES_LG[$mod];
        }elseif(file_exists($_fex=DIR_ROOT."/extra/$dir/$mod-$lang.php")){
            $cfgs = self::$_CACHES_LG[$mod] = include $_fex;
        }elseif(file_exists($_fex=DIR_IMCAT."/lang/$dir/$mod-$lang.php")){
            $cfgs = self::$_CACHES_LG[$mod] = include $_fex;
        }else{
            $cfgs = self::$_CACHES_LG[$mod] = array();
        }
        if(empty($key)) return $cfgs;
        return isset($cfgs[$key]) ? $cfgs[$key] : '{'."$mod.$key".'}';
    }

    // {linc(file.part)} <大段文本>
    static function inc($file, $part='', $uarr=array()){
        global $_cbase;
        $lang = $_cbase['sys']['lang'];
        $flang = DIR_IMCAT."/lang/ptinc/$file-$lang.php";
        include $flang; 
        if(isset($reinc[$part])){
            return $reinc[$part];
        }
    }    

    // 字段从数组中选个语言键值
    static function pick($key, $vals=array()){
        global $_cbase;
        $lang = $_cbase['sys']['lang'];
        if(!is_array($vals)){
            return $vals;
        }
        if($key && isset($vals[$key])){
            return $vals[$key];
        }elseif(isset($vals[$lang])){
            return $vals[$lang];
        }else{
            return reset($vals);
        }
    }    
    
    // 前置处理: ucfg.lang, ucfg.skin ============================================= 

    // $_cbase['ucfg']['lang'] = '(auto)'; // (get)
    static function auto(){
        global $_cbase; 
        //if(empty($_cbase['ucfg']['lang'])) return;
        if(!empty($_cbase['ucfg']['lang']) && strpos($_cbase['ucfg']['lang'],')')){ // (auto), (get)
            $lang = $_cbase['ucfg']['lang']=='(get)' ? basReq::val('lang') : comCookie::oget('lang');
            $alang = basEnv::serval('HTTP_ACCEPT_LANGUAGE');
            if(!empty($lang)){
                $_cbase['sys']['lang'] = $lang;
            }elseif(empty($alang)){
                $_cbase['sys']['lang'] = 'cn';
            }else{
                $lang = substr($alang, 0, 2);
                $_cbase['sys']['lang'] = $lang=='zh' ? 'cn' : 'en';
            }
        }
        if(!empty($_cbase['ucfg']['skin']) && $_cbase['ucfg']['skin']=='(auto)'){
            $skin = comCookie::oget('skin');
            if(!empty($skin)){
                $_cbase['sys']['skin'] = $skin;
                return;
            }
        } 
    }

    // links
    static function links($dir='',$cfgs=array()){
        $vopcfg = glbConfig::read('vopcfg','sy');
        $langs = empty($cfgs) ? $vopcfg['langs'] : array();
        $url = PATH_ROOT."/plus/api/redir.php?lang:{key}";
        if(empty($dir)){
            $tpl = "<a href='$url' title='{title}'>{mini}</a>";
        }elseif(strpos($dir,'</')){
            $tpl = str_replace('{url}',$url,$dir);
        }else{ // lang:cn:&recbl=redir
            $url .= ":&recbk=$dir"; 
            $tpl = "<a href='$url' title='{title}'>{mini}</a>";
        }
        $res = '';
        foreach ($langs as $key => $val) {
            $res .= "\n".str_replace(array('{key}','{title}','{mini}'),array($key,$val[0],$val[1]),$tpl);
        }
        return $res;
    }    
    // sopts
    static function sopts($def='',$img=1){
        global $_cbase; 
        $lang = $_cbase['sys']['lang'];
        if(!empty($_cbase['ucfg']['lang']) && $_cbase['ucfg']['lang']=='(auto)'){
            $ops = "<option value=''> En<>中 </option>";
            $ops .= basLang::links("<option value='{url}'>{title}</option>",array());
        }else{ // <!-- <>◇/↔/⇔/&#x21d4; <i>«»<i> -->
            $cfgs = glbConfig::read('vopcfg','sy'); 
            $lname = empty($cfgs['langs'][$lang][0]) ? '' : $cfgs['langs'][$lang][0];
            $ops = "<option value=''> {$lang} : $lname </option>";
        }
        $img = $img ? '<img src="'.PATH_VIEWS.'/base/assets/logo/imcat-40x.png" width="40" height="40">' : '';
        echo "<p>$img<select id='locSetS' onchange='location.href=this.value;'>$ops</select></p>";
    }
    // shead
    static function shead($title){
        echo '<div class="header">';
        basLang::sopts();
        echo "<h2 class='title'>$title</h2>";
        echo '</div>';
    }

    // jimp
    static function jimp($path,$base='root',$lang='(auto)',$injs=0){
        global $_cbase;
        if($lang=='(auto)') $lang = $_cbase['sys']['lang']; 
        if($injs){
            $pcfg = comStore::cfgDirPath($base,'dir');
            $p1 = $path;
            $p2 = str_replace('.js',"-$lang.js",$path);
            $d1 = comFiles::get($pcfg.$p1);
            $d2 = comFiles::get($pcfg.$p2);
            echo "$d1\n\n//($lang)\n$d2";
        }else{
            $url = PATH_BASE."?ajax-comjs&act=1&exjs=$path&lang=$lang";
            echo basJscss::imp($url);
        }
    }

}

