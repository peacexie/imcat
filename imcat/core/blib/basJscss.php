<?php
namespace imcat;

// basJscss类
class basJscss{

    // jspop,jq_base,bootstrap,layer
    static function loadExtjs($exjs){
        if(strstr($exjs,'jspop')){
            require DIR_VIEWS.'/base/assets/jslib/jspop.js';
        }
        if(strstr($exjs,'jq_base')){
            require DIR_VIEWS.'/base/assets/jslib/jq_base.js';
        }
        if(strstr($exjs,'bootstrap')){
            $jsimp = basJscss::jscode(0,PATH_VENDUI.'/bootstrap/js/bootstrap.min.js');
            echo "document.write(\"$jsimp\");\n";
        }
        if(strstr($exjs,'layer')){
            $jsimp = basJscss::jscode(0,PATH_VENDUI.'/layer/layer.js');
            echo "document.write(\"$jsimp\");\n";
        }
    }
    // bootstrap,stpub,jstyle
    static function loadBasecss($excss,$skin=''){
        if(strstr($excss,'bootstrap')){
            $jsimp = PATH_VENDUI."/bootstrap/css/bootstrap.".($skin ? $skin : 'min').".css";
            echo "/* ------ bootstrap ------ */\n";
            echo "@import url($jsimp);\n";
            $jsimp = PATH_VENDUI."/bootstrap/css/font-awesome.min.css";
            echo "/* ------ font-awesome ------ */\n";
            echo "@import url($jsimp);\n";
        }
        if(strstr($excss,'stpub')){
            echo "/* ------ stpub ------ */\n";
            include DIR_VIEWS."/base/assets/cssjs/stpub.css"; 
        }
        if(strstr($excss,'jstyle')){
            echo "/* ------ jstyle ------ */\n";
            include DIR_VIEWS."/base/assets/cssjs/jstyle.css";
        }
    }
    // xxx;comm;home
    static function loadTabs($tabs,$tpldir,$lang,$ext='.js'){
        if(strstr($tabs,';')){
            $arr = explode(';',$tabs);
            $len = count($arr);
            for($i=1;$i<$len;$i++){
                $imp = $arr[$i];
                if(!strstr($imp,'/')){
                    $imp = "/$tpldir/assets/{$arr[$i]}";
                }
                if(!strstr($imp,$ext)){
                    $imp = "$imp$ext";
                }
                if(strpos($imp,'(-mob)')){
                    $imp = str_replace('(-mob)',(basEnv::isMobile()?'-mob':''),$imp);
                }
                if(strpos($imp,'(-lang)')){
                    $imp = str_replace('(-lang)',"-$lang",$imp);
                }
                $imp = (in_array(substr($imp,0,6),array('/plus/','/tools'))?DIR_ROOT:DIR_VIEWS).$imp;
                if(file_exists($imp)) require $imp; 
            }
        }
    }
    // zepto,jquery,bootstrap,layer
    static function loadJqbs($exjs,$dw=1,$skin=''){
        if(empty($exjs)) return;
        //global $_cbase;
        if(strstr($exjs,'zepto')){ // 需要自行添加如下zepto文件
            $ims[] = basJscss::jscode(0,PATH_VENDUI.'/jquery/zepto-1x.js');
        }elseif(strstr($exjs,'jquery')){
            $ims[] = basJscss::jscode(0,PATH_VENDUI.'/jquery/jquery-2.x.js');
            // preg_match("/MSIE [6|7|8].0/",$_cbase['run']['userag'])
        }
        if(strstr($exjs,'bootcss')){
            $ims[] = basJscss::csscode(0,PATH_VENDUI."/bootstrap/css/bootstrap.".($skin ? $skin : 'min').".css");
            $ims[] = basJscss::csscode(0,PATH_VENDUI.'/bootstrap/css/font-awesome.min.css');
        }
        if(strstr($exjs,'bootstrap')){
            $ims[] = basJscss::jscode(0,PATH_VENDUI.'/bootstrap/js/bootstrap.min.js');
        }
        if(strstr($exjs,'layer')){
            $ims[] = basJscss::jscode(0,PATH_VENDUI.'/layer/layer.js');
        }
        if(!empty($ims)){
            foreach ($ims as $row) {
                $dw && $row = "document.write(\"$row\");";
                echo "$row\n";
            }
        }
    }
    // exjs=jspop
    static function loadCfgjs($exjs,$tpldir,$lang,$mkv){
        global $_cbase;
        // ***** js配置区 *****
        echo "\n// js Config";
        echo "\nvar _cbase={}; _cbase.run={}; _cbase.sys={}; _cbase.path={}; _cbase.ck={};";
        echo "\n_cbase.safe={}; _cbase.safil={}; _cbase.jsrun={};"; //_cbase.safe={}; 
        echo "\nif(typeof(_pbase)=='undefined'){_pbase={}} ";
        echo "\n";
        echo "\n_cbase.run.timer = '".$_cbase['run']['timer']."';";
        echo "\n_cbase.run.stamp = '".$_cbase['run']['stamp']."';";
        echo "\n_cbase.run.userag = '".$_cbase['run']['userag']."';";
        echo "\n_cbase.run.jsimp = ',';";
        //sys
        echo "\n_cbase.sys.cset = '".$_cbase['sys']['cset']."';";
        echo "\n_cbase.sys.tzone = '".$_cbase['sys']['tmzone']."';"; // 时区+-12
        echo "\n_cbase.sys.lang = '$lang';";
        echo "\n_cbase.run.ref = '".@$_SERVER['HTTP_REFERER']."';"; // 
        echo "\n_cbase.run.rsite = '".$_cbase['run']['rsite']."';";
        echo "\n_cbase.run.rmain = '".$_cbase['run']['rmain']."';";
        echo "\n_cbase.run.roots = '".$_cbase['run']['roots']."';";
        echo "\n_cbase.run.rskin = '".$_cbase['run']['rsite'].PATH_VIEWS."';";
        echo "\n_cbase.run.fbase = '".$_cbase['run']['fbase']."';";
        echo "\n_cbase.run.dmtop = '".$_cbase['run']['dmtop']."';";
        //tpl
        if($tpldir){
            vopTpls::set($tpldir);
            if(!empty($_cbase["close_$tpldir"])){ //close-for-static-files
                #basEnv::obClean();
                #die("location.href='".PATH_PROJ."?close&tpldir=$tpldir';");
            }
            echo "\n_cbase.run.mkv = '$mkv';";
            echo "\n_cbase.run.csname = '".vopUrl::burl()."';";
            echo "\n_cbase.run.tpldir = '$tpldir';";
        }
        // auto-dir:mob-page var _pbase={}; _pbase.rdmob=1; .jscode='xxx';
        echo "\n_cbase.run.isRobot = ".(basEnv::isRobot()?1:0).";";
        echo "\n_cbase.run.isMoble = ".(basEnv::isMobile()?1:0).";";
        echo "\n_cbase.run.isWeixin = ".(basEnv::isWeixin()?1:0).";";
        if($mkv && $tpldir!='mob'){
            #echo "\n_cbase.run.mobDir = '".vopUrl::fout("mob:$mkv")."';";
            #echo "\nif(typeof(_pbase.rdmob)!='undefined' && _cbase.run.isMoble){location.href=_cbase.run.mobDir;}";
        }
        echo "\nif(typeof(_pbase.jscode)!='undefined'){eval(_pbase.jscode);}";
        // Path  
        echo "\n_cbase.path.cache   = '".PATH_DTMP."';"; 
        echo "\n_cbase.path.vendor  = '".PATH_VENDOR."';"; 
        echo "\n_cbase.path.vendui  = '".PATH_VENDUI."';"; 
        echo "\n_cbase.path.static  = '".PATH_STATIC."';"; 
        echo "\n_cbase.path.skin    = '".PATH_VIEWS."';"; 
        echo "\n_cbase.path.editor  = _cbase.path.vendui + '/edt_".@$_cbase['sys_editor']."/';"; 
        // Cookie
        echo "\n_cbase.ck.ckpre = '".$_cbase['ck']['pre']."';";
        echo "\n_cbase.ck.ckdomain = '".$_cbase['ck']['domain']."';";
        echo "\n_cbase.ck.ckpath = '".$_cbase['ck']['path']."';";
        
        // Safil
        echo "\n";
        echo "\n_cbase.safe.safil = '".$_cbase['safe']['safil']."';";
        echo "\n_cbase.safe.safix = '".$_cbase['safe']['safix']."';";
        #echo "\n_cbase.safe.rnum = '".$_cbase['safe']['rnum']."';";
        #echo "\n_cbase.safe.rspe = '".$_cbase['safe']['rspe']."';";
        echo "\n_cbase.safil.url = '".safComm::urlStamp('init')."';";
        
        // Para
        echo "\n"; //_cbase.para={};\n
        echo "\n_cbase.sys_editor = '".@$_cbase['sys_editor']."';";
        echo "\n_cbase.sys_open = ".(empty($_cbase['sys_open']) ? 1 : $_cbase['sys_open']).";";
        echo "\n_cbase.sys_pop = ".(empty($_cbase['sys_pop']) ? 1 : $_cbase['sys_pop']).";";
        echo "\n_cbase.msg_timea = ".(empty($_cbase['sys_timea']) ? 1500 : $_cbase['sys_timea']).";";
        echo "\n_cbase.sys_map = '".@$_cbase['sys_map']."';";
        echo "\n";
        
		// ***** 加载Base.js *****
		require DIR_VIEWS.'/base/assets/jslib/jsbase.js';
		require DIR_VIEWS.'/base/assets/jslib/jsbext.js';
        if(strstr($exjs,'jspop')){
            require DIR_VIEWS.'/base/assets/jslib/jspop.js';
        }
        $flang = DIR_VIEWS."/base/assets/jslib/jcore-$lang.js";
        if(file_exists($flang)) require $flang; 
    }

    // imp css/js
    static function imp($path,$base='',$mod=''){
        global $_cbase; 
        $fix6 = substr($path,0,6);
        $tpldir = empty($_cbase['tpl']['vdir']) ? '' : $_cbase['tpl']['vdir'];
        if(in_array($path,array('initCss','initJs','loadExtjs'))){
            $lang = empty($_cbase['sys']['lang']) ? '' : $_cbase['sys']['lang'];
            $skin = empty($_cbase['sys']['skin']) ? '' : $_cbase['sys']['skin'];
            $mkv = empty($_cbase['mkv']['mkv']) ? '' : $_cbase['mkv']['mkv'];
            if($path=='initCss'){
                $skin = empty($_cbase['sys']['skin']) ? 'min' : $_cbase['sys']['skin'];
                $exp = "&tpldir=$tpldir&lang=$lang&skin=$skin";
                $mod = 'css';
            }else{ // initJs/loadExtjs
                $exp = $path=='initJs' ? "&tpldir=$tpldir&lang=$lang&rf=$mkv" : '';
                $mod = 'js'; 
            }
            $path = "?ajax-comjs&act=$path$exp&ex$mod=$base";
            $base = PATH_BASE;
        }elseif(in_array($fix6,array('/views','/~base','/~tpl/','/~now/'))){
            $a1 = array('/views/', '/~base/',       '/~tpl/',         );
            $a2 = array('/',       "/base/assets/","/$tpldir/assets/");
            if(!empty($_cbase['mkv']['mod'])){
                $a1[] = '/~now/'; $a2[] = "/$tpldir/".$_cbase['mkv']['mod']."/";
            }
            $path = str_replace($a1,$a2,$path);
            $base = PATH_VIEWS;
        }elseif(in_array($fix6,array('?ajax-'))){
            $base = PATH_BASE;
        #}elseif(in_array($fix6,array('/plus/','/tools'))){
            #$base = PATH_ROOT;
        }else{
            $base = $base ? comStore::cfgDirPath($base,'path') : '';
        }
        if(strpos($_cbase['run']['jsimp'],$path)){ return ''; }
        $_cbase['run']['jsimp'] .= "$path,";
        $path .= (strstr($path,'?') ? '&' : '?').'_r='.$_cbase['sys']['ver'];
        $method = $mod ? "{$mod}code" : (strpos($path,'.css') ? 'css' : 'js').'code'; 
        return self::$method('',$base.$path)."\n"; 
    }

    // keyid, subject('"<>\n\r), js
    static function Alert($xMsg,$xAct='prClose',$xAddr='',$head=0){
      global $_cbase;
      if($head && empty($_cbase['run']['headed'])) glbHtml::page();
      if(empty($xAddr)) $xAddr = @$_SERVER["HTTP_REFERER"];
      $s = "alert('$xMsg');\n";
      switch ($xAct) { 
      case "Back" : 
        $s .= "history.go($xAddr);\n";
        break; 
      case "Close" : 
        $s .= "window.close();\n";
        break; 
      case "prClose" : 
        if(@$_cbase['sys_open']==='4'){ 
            $s .= "parent.location.reload();\n";
        }else if(@$_cbase['sys_open']==='1'){ 
            $s .= "window.opener.location.reload();\n";
            $s .= "window.close();\n";
        }else{ 
            $s .= "window.close();\n";
        }
        break; 
      case "Open" : 
        $s .= "window.open('$xAddr');\n";
        break; 
      case "Redir" : 
        $s .= "location.href='$xAddr';\n";
        break;
      default: 
        break; 
      }
      return self::jscode($s);
    }
    // $enchf=1,编码html标记：尖括号，
    static function jsShow($xStr, $enchf=1){
       $Tmp = $xStr;
       $enchf && $Tmp = str_replace(array('<','>'),array('&lt;','&gt;'),$Tmp);
       $Tmp = addcslashes($Tmp, "'\"\\\r\n");
       return $Tmp;
    }
    static function jsKey($xStr) {
       $xStr = str_replace(array('[',']',' '),array('_','_',''),$xStr);
       $xStr = str_replace(array('/','-','.'),array('_','_','_'),$xStr);
       return $xStr;    
    }
    // document.write
    static function write($xStr){ // ,array(),array()
        return "document.write('".self::jsShow($xStr, 0)."');";
    }
    static function jscode($code,$url=''){ 
        if($url){
            return "<script src='$url'></script>"; 
        }else{
            return "<script>$code</script>";
        }
    }
    static function csscode($code,$url=''){ 
        if($url){
            return "<link href='$url' type='text/css' rel='stylesheet'/>"; 
        }else{
            return "<style type='text/css'>$code</style>";
        }
    }

    static function jsTypes($act){ 
        $mods = exvFunc::actMods($act,'jsTypes');
        $moda = explode(',',$mods); 
        $done = ",";
        foreach($moda as $mod){
            if(empty($mod)) continue;
            if(strstr($done,",$mod,")) continue;
            $mcfg = glbConfig::read($mod);
            $s0 = "\nvar _{$mod}_data = ["; $gap = '';
            if(!empty($mcfg['i'])){
            foreach($mcfg['i'] as $k=>$v){
                $frame = empty($v['frame']) ? 0 : $v['frame'];
                $char = empty($v['char']) ? '' : $v['char'];
                $s0 .= "\n{$gap}['$k','$v[pid]','$v[title]',$v[deep],$frame,'$char']";
                $gap = ',';
            }}
            $s0 .= "\n]; ";
            unset($mcfg['f'],$mcfg['i']); 
            echo "var _{$mod}_cfg = ".comParse::jsonEncode($mcfg).";$s0\n\n";
            $done .= "$mod,";
        }
    }
    static function jsType2($act){
        $mods = exvFunc::actMods($act,'jsType2');
        $moda = explode(',',$mods);
        $done = ",";
        foreach($moda as $m1){
            if(empty($m1)) continue;
            if(strstr($done,",$m1,")) continue;
            echo glbConfig::read($m1,'modcm','json')."\n";
            $done .= "$m1,";
        }
    }
    static function jsRelat($act){ 
        $mods = exvFunc::actMods($act,'jsRelat'); 
        $moda = explode(',',$mods); 
        $moda[] = 'relat'; 
        foreach($moda as $mod){ 
            if(empty($mod)) continue;
            //if(strstr($done,",$mod,")) continue;
            $fnm = DIR_DTMP."/modex/_$mod.cfg_php";
            if(is_file($fnm)){
                $data = file_get_contents($fnm);
                //$itms = comParse::jsonDecode($data); 
                echo "\nvar _{$mod}_data = $data;\n";
            }
        }
    }
    static function jsFields($act){ 
        //扩展字段
        $mods = exvFunc::actMods($act,'jsFields'); 
        $ccfg = glbConfig::read($mods,'_c');
        //常规字段
        $cmod = basReq::val('cmod');
        $amod = array();
        if($cmod){
            ${"_$mods"} = glbConfig::read($mods); 
            if($mfields = @${"_$mods"}['f']){
                foreach($mfields as $k1=>$v1){
                    if(($cmod=='(a)'&&in_array($v1['type'],array('select','cbox','radio'))) || strstr($cmod,$k1)){
                        $amod[$k1] = $v1;
                    }
                }
            }
            if(!empty($amod)){
                $ccfg['f'] = $amod;
            }
        } 
        foreach($ccfg as $k1=>$v1){
            foreach($v1 as $k2=>$v2){ 
                $v3 = &$ccfg[$k1][$k2]; 
                if(!in_array($v3['type'],array('select','cbox','radio'))){
                    unset($ccfg[$k1][$k2]);
                    continue;
                }
                foreach($v3 as $k4=>$v4){
                    if(!in_array($k4,array('title','type','fmline','cfgs'))){
                        unset($v3[$k4]);
                    } 
                }
                $v3['cfgs'] = str_replace(array("\r\n","\r","\n",";;"),array(";",";",";",";"),$v3['cfgs']);
        }   }  
        $ccfg = comParse::jsonEncode($ccfg);
        $ccfg = str_replace(array("\\/","\"}},\"",),array("/","\"}}\n,\"",),$ccfg);
        echo "\nvar _{$mods}_fields = $ccfg;\n"; 
    }
    
}