<?php
namespace imcat;

// basJscss类
class basJscss{

    // js-基础配置
    static function jsbcfg($lang=''){
        global $_cbase;
        echo "\n// js Config @ "; // base /* --- [load file] /views/base/assets/jslib/jcore-cn.js --- */
        echo "\nvar _cbase={}; _cbase.run={}; _cbase.sys={}; _cbase.path={}; _cbase.ck={};";
        echo "\n_cbase.safe={}; _cbase.safil={}; _cbase.jsrun={};"; //_cbase.safe={}; 
        echo "\nif(typeof(_pbase)=='undefined'){_pbase={}} ";
        echo "\n";
        echo "\n_cbase.run.timer = '".$_cbase['run']['timer']."';";
        echo "\n_cbase.run.stamp = '".$_cbase['run']['stamp']."';";
        echo "\n_cbase.run.userag = '".$_cbase['run']['userag']."';";
        echo "\n_cbase.run.jsimp = ',';";
        echo "\n"; // sys
        echo "\n_cbase.sys.cset = '".$_cbase['sys']['cset']."';";
        echo "\n_cbase.sys.tzone = '".$_cbase['sys']['tmzone']."';"; // 时区+-12
        if($lang) echo "\n_cbase.sys.lang = '$lang';";
        echo "\n"; // root
        echo "\n_cbase.run.ref = '".basEnv::serval('ref')."';"; // 
        echo "\n_cbase.run.rsite = '".$_cbase['run']['rsite']."';";
        echo "\n_cbase.run.rmain = '".$_cbase['run']['rmain']."';";
        echo "\n_cbase.run.roots = '".$_cbase['run']['roots']."';";
        echo "\n_cbase.run.rskin = '".$_cbase['run']['rsite'].PATH_VIEWS."';";
        echo "\n_cbase.run.fbase = '".$_cbase['run']['fbase']."';";
        echo "\n_cbase.run.dmtop = '".$_cbase['run']['dmtop']."';";
        echo "\n"; // Path
        echo "\n_cbase.path.cache   = '".PATH_DTMP."';"; 
        echo "\n_cbase.path.vendor  = '".PATH_VENDOR."';"; 
        echo "\n_cbase.path.vendui  = '".PATH_VENDUI."';"; 
        echo "\n_cbase.path.static  = '".PATH_STATIC."';"; 
        echo "\n_cbase.path.skin    = '".PATH_VIEWS."';"; 
        echo "\n"; // Cookie
        echo "\n_cbase.ck.ckpre = '".$_cbase['ck']['pre']."';";
        echo "\n_cbase.ck.ckdomain = '".$_cbase['ck']['domain']."';";
        echo "\n_cbase.ck.ckpath = '".$_cbase['ck']['path']."';";
        echo "\n"; // Safil
        echo "\n_cbase.safe.safil = '".$_cbase['safe']['safil']."';";
        echo "\n_cbase.safe.safix = '".$_cbase['safe']['safix']."';";
        #echo "\n_cbase.safe.rnum = '".$_cbase['safe']['rnum']."';";
        #echo "\n_cbase.safe.rspe = '".$_cbase['safe']['rspe']."';";
        echo "\n_cbase.safil.url = '".safComm::urlStamp('init')."';";
    }

    // css基本导入
    static function weysCss(){
        $fp = DIR_VIEWS.'/base/assets/weys.css';
        $data = comFiles::get($fp);
        $skin = basReq::val('skin'); // TODO: skin ???
        if(!empty($skin)){
            $skstr = comFiles::get(DIR_VIEWS.'/base/assets/weskin.css');
            $to = basElm::getPos($skstr, [":$skin{","}"]);
            if(!empty($to)){
                $from = basElm::getPos($data, ["/*skin-start*/","/*skin-end*/"]); 
                $data = str_replace($from, "\n:root{".$to."}\n", $data);
            } 
        }
        // comm, comm-mob?
        $tpldir = basReq::val('tpldir');
        if($tpldir){
            $ver = req('ver', 'comm');
            $fp = "/$tpldir/assets/$ver.css";
            $dstr = comFiles::get(DIR_VIEWS.$fp);
            $dstr = self::fixPath($dstr, PATH_VIEWS.$fp);
            $data .= "\n\n/* --- [load file] $fp --- */\n$dstr";
        }
        // fix:dir
        $ua = basEnv::serval('ua'); 
        #if(strpos($ua,'rv:11')>0 || strpos($ua,'MSIE')>0){ // IE11, MSIE 10, MSIE 9
            $data = self::fixVar($data);
        #}
        return $data;
    }
    // js基本配置
    static function weysJs(){
        global $_cbase;
        self::jsbcfg();
        // js-files
        $lang = basReq::val('lang', $_cbase['sys']['lang']); 
        $fp = "/base/assets/jslib/jcore-$lang.js"; // 核心语言包
        basJscss::inc($fp); 
        $fp = "/base/assets/weys.js"; // weys框架js
        basJscss::inc($fp); 
        $tpldir = basReq::val('tpldir');
        if($tpldir){
            $ver = req('ver', 'comm');
            $fp = "/$tpldir/assets/$ver-$lang.js"; // 当前模板语言包
            basJscss::inc($fp);
            $fp = "/$tpldir/assets/$ver.js"; // 当前模板js
            basJscss::inc($fp);
        }
    }
    // weysTab
    static function weysTab($tab, $fix=''){
        $tpldir = basReq::val('tpldir');
        $cfg = [
            'ui' => [DIR_VENDUI, PATH_VENDUI],
            'now' => [DIR_VIEWS."/$tpldir/assets", PATH_VIEWS."/$tpldir/assets"],
        ];
        preg_match_all("/(\w+)\:([\w\/\.\-]+)/i", $tab, $itms); // dump($itms);
        foreach($itms[1] as $ino=>$ik) {
            if(isset($cfg[$ik])){
                $dir = $cfg[$ik][0];
                $path = $cfg[$ik][1];
            }else{
                $dir = DIR_VIEWS."/$ik/assets";
                $path = PATH_VIEWS."/$ik/assets";
            }
            $fp = "/{$itms[2][$ino]}".(strpos($itms[2][$ino],".$fix")?'':".$fix");
            if($fix=='js'){
                basJscss::inc($fp, $dir);
            }else{
                if(file_exists($dir.$fp)){
                    $data = comFiles::get($dir.$fp);
                    $data = self::fixPath($data, $path.$fp);
                    echo "\n\n/* --- [load file] $fp --- */\n$data";
                }
            }
        }
    }
    // tab = 'jq,zepto,jsbase,jsbext,fa,jstyle,mulnews,mulpic';
    static function weysInit($tab='jq', $excss='', $exjs='', $skin=''){
        global $_cbase;
        if(strstr($tab,'jq')){ echo self::jscode('', PATH_VENDUI."/jquery/jquery-2.x.js")."\n"; }
        if(strstr($tab,'zepto')){ echo self::jscode('', PATH_VENDUI."/jquery/zepto.js")."\n"; }
        $lang = empty($_cbase['sys']['lang']) ? '' : $_cbase['sys']['lang'];
        $mkv = empty($_cbase['mkv']['mkv']) ? '' : $_cbase['mkv']['mkv'];
        $tpldir = $_cbase['tpl']['vdir'];
        $ver = '&_r='.$_cbase['sys']['ver'];
        if(strstr($tab,'jsbase')){ $exjs .= ';base:jslib/jsbase'; }
        if(strstr($tab,'jsbext')){ $exjs .= ';base:jslib/jsbext'; }
        if(strstr($tab,'fa')){ $excss .= ';ui:bootstrap/css/font-awesome.min'; }
        if(strstr($tab,'jstyle')){ $excss .= ';base:cssjs/jstyle'; }
        if(strstr($tab,'mulnews')){ $excss .= ';base:cssjs/mulnews'; }
        if(strstr($tab,'mulpic')){ $excss .= ';base:cssjs/mulpic'; }
        $css = "?ajax-weys&act=css&tpldir=$tpldir&skin=$skin&tab=initCss;$excss$ver";
        $js = "?ajax-weys&act=js&tpldir=$tpldir&rf=$mkv&tab=initJs;$exjs$ver";
        echo self::csscode('', PATH_BASE.$css)."\n";
        echo self::jscode('', PATH_BASE.$js)."\n";
    }
    // 编译var(变量) - IE11下不兼容var
    static function fixVar($data){
        preg_match_all("/\-\-(\w+)\:([^\;]+)\;/i", $data, $itms);
        foreach($itms[1] as $ino=>$ik) { // --key:{value}; -=> var(--key);
            $data = str_replace("var(--$ik)", $itms[2][$ino], $data);
        }
        return $data;
    }
    // fix路径 - 写法多样,鸡肋？
    static function fixPath($data, $rfp=''){
        // background:url(./lunbo.png), src:url('../fonts/
        preg_match_all("/\:url\(([\'\"]?)([\.\/]+)([\w\/\.\-]+)/i", $data, $itms); 
        foreach($itms[2] as $ino=>$ik) { 
            $bracket = $itms[1][$ino];
            if(strstr($itms[3][$ino],'views/')){ // 兼容旧版...
                //$data = str_replace("url($bracket{$ik}views/", "url($bracket".PATH_VIEWS."/", $data);
            }else{
                $pre = extCrawl::urlJoin($ik, $rfp);
                $data = str_replace("url($bracket{$ik}", "url($bracket{$pre}", $data);
            }
        }
        return $data;
    }

    static function inc($fp, $base=''){ 
        $base = $base ? $base : DIR_VIEWS;
        if(file_exists($base.$fp)){
            echo "\n\n/* --- [load file] /".basename($base)."$fp --- */\n";
            require $base.$fp;
        }
    }

    // jspop,jq_base,bootstrap,layer
    static function loadExtjs($exjs){
        if(strstr($exjs,'jspop')){
            self::inc('/base/assets/jslib/jspop.js');
        }
        if(strstr($exjs,'jq_base')){
            self::inc('/base/assets/jslib/jq_base.js');
        }
        if(strstr($exjs,'bootstrap')){
            $jsimp = self::jscode(0,PATH_VENDUI.'/bootstrap/js/bootstrap.min.js');
            echo "document.write(\"$jsimp\");\n";
        }
        if(strstr($exjs,'layer')){
            $jsimp = self::jscode(0,PATH_VENDUI.'/layer/layer.js');
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
            self::inc("/base/assets/cssjs/stpub.css");
        }
        if(strstr($excss,'jstyle')){
            self::inc("/base/assets/cssjs/jstyle.css");
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
                $base = in_array(substr($imp,0,6),array('/plus/','/tools')) ? DIR_ROOT : DIR_VIEWS; 
                if(file_exists($base.$imp)) self::inc($imp, $base); 
            }
        }
    }
    // zepto,jquery,bootstrap,layer
    static function loadJqbs($exjs,$dw=1,$skin=''){
        if(empty($exjs)) return;
        //global $_cbase;
        if(strstr($exjs,'zepto')){ // 需要自行添加如下zepto文件
            $ims[] = self::jscode(0,PATH_VENDUI.'/jquery/zepto-1x.js');
        }elseif(strstr($exjs,'jquery')){
            $ims[] = self::jscode(0,PATH_VENDUI.'/jquery/jquery-2.x.js');
            // preg_match("/MSIE [6|7|8].0/",$_cbase['run']['userag'])
        }
        if(strstr($exjs,'bootcss')){
            $ims[] = self::csscode(0,PATH_VENDUI."/bootstrap/css/bootstrap.".($skin ? $skin : 'min').".css");
            $ims[] = self::csscode(0,PATH_VENDUI.'/bootstrap/css/font-awesome.min.css');
        }
        if(strstr($exjs,'bootstrap')){
            $ims[] = self::jscode(0,PATH_VENDUI.'/bootstrap/js/bootstrap.min.js');
        }
        if(strstr($exjs,'layer')){
            $ims[] = self::jscode(0,PATH_VENDUI.'/layer/layer.js');
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
        self::jsbcfg($lang);
        //tpl
        if($tpldir){
            vopTpls::set($tpldir);
            echo "\n_cbase.run.mkv = '$mkv';";
            echo "\n_cbase.run.csname = '".vopUrl::burl()."';";
            echo "\n_cbase.run.tpldir = '$tpldir';";
        }
        // auto-dir:mob-page var _pbase={}; _pbase.rdmob=1; .jscode='xxx';
        echo "\n_cbase.run.isRobot = ".(basEnv::isRobot()?1:0).";";
        echo "\n_cbase.run.isMoble = ".(basEnv::isMobile()?1:0).";";
        echo "\n_cbase.run.isWeixin = ".(basEnv::isWeixin()?1:0).";";
        echo "\nif(typeof(_pbase.jscode)!='undefined'){eval(_pbase.jscode);}";
        // Para
        echo "\n"; //_cbase.para={};\n
        echo "\n_cbase.sys_open = ".(empty($_cbase['sys_open']) ? 1 : $_cbase['sys_open']).";";
        echo "\n_cbase.sys_pop = ".(empty($_cbase['sys_pop']) ? 1 : $_cbase['sys_pop']).";";
        echo "\n_cbase.msg_timea = ".(empty($_cbase['sys_timea']) ? 1500 : $_cbase['sys_timea']).";";
        echo "\n_cbase.sys_map = '".(empty($_cbase['sys_map']) ? 1 : $_cbase['sys_map'])."';";
        if(isset($_cbase['sys_editor'])){
            echo "\n_cbase.sys_editor = '".$_cbase['sys_editor']."';";
            echo "\n_cbase.path.editor  = _cbase.path.vendui + '/edt_".$_cbase['sys_editor']."/';"; 
        }
        echo "\n";
		// ***** 加载Base.js *****
        self::inc('/base/assets/jslib/jsbase.js');
		self::inc('/base/assets/jslib/jsbext.js');
        if(strstr($exjs,'jspop')){
            self::inc('/base/assets/jslib/jspop.js');
        }
        $flang = "/base/assets/jslib/jcore-$lang.js";
        self::inc($flang); 
    }

    // imp css/js
    static function imp($path,$base='',$mod=''){
        global $_cbase; 
        $fix6 = substr($path,0,6);
        $tpldir = $_cbase['tpl']['vdir'];
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
            $fnm = DIR_DTMP."/modex/_$mod.cfg_php";
            if(is_file($fnm)){
                $data = file_get_contents($fnm);
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
