<?php 
require dirname(__FILE__).'/_config.php'; 
#safComm::urlFrom(); 
extract(basReq::sysVars());
$act = basReq::val('act','sysInit'); 
$lang = basReq::val('lang'); 
$exjs = basReq::val('exjs'); 
$excss = basReq::val('excss'); 
$tpldir = basReq::val('tpldir');
$mkv = basReq::val('mkv');
glbHtml::head($excss ? 'css' : 'js');

// 初始化js
if(strstr($act,'sysInit')){
    $lang = $lang ? $lang : $_cbase['sys']['lang'];
    // ***** js配置区 *****
    $jscfg  = "\n// js Config";
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
    echo "\n_cbase.run.rskin = '".$_cbase['run']['rsite'].PATH_SKIN."';";
    echo "\n_cbase.run.dmtop = '".$_cbase['run']['dmtop']."';";
    //tpl
    if($tpldir){
        vopTpls::set($tpldir);
        if(!empty($_cbase["close_$tpldir"])){ //close-for-static-files
            basEnv::obClean();
            die("location.href='".PATH_PROJ."?close&tpldir=$tpldir';");
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
        if($mkv=='home') $mkv='0';
        echo "\n_cbase.run.mobDir = '".vopUrl::fout("mob:$mkv")."';";
        echo "\nif(typeof(_pbase.rdmob)!='undefined' && _cbase.run.isMoble){location.href=_cbase.run.mobDir;}";
    }
    echo "\nif(typeof(_pbase.jscode)!='undefined'){eval(_pbase.jscode);}";
    // Path  
    echo "\n_cbase.path.cache   = '".PATH_DTMP."';"; 
    echo "\n_cbase.path.vendor  = '".PATH_VENDOR."';"; 
    echo "\n_cbase.path.vendui  = '".PATH_VENDUI."';"; 
    echo "\n_cbase.path.static  = '".PATH_STATIC."';"; 
    echo "\n_cbase.path.skin    = '".PATH_SKIN."';"; 
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
    
    if(!empty($_GET['user'])){
        echo "\n// js Member/Admin"; 
        echo "\nvar _minfo={}, _mperm={}, _miadm={}, _mpadm={}; ";
        $user = usrBase::userObj('Member');
        if(!empty($user)){
            echo "\n_minfo.userType = '".$user->userType."';";
            echo "\n_minfo.userFlag = '".$user->userFlag."';";
            echo "\n_minfo.uname = '".$user->usess['uname']."';";
            echo "\n_mperm.title = '".@$user->uperm['title']."';";
        }
        $user = usrBase::userObj('Admin');
        if(!empty($user)){
            echo "\n_miadm.userType = '".$user->userType."';";
            echo "\n_miadm.userFlag = '".$user->userFlag."';";
            echo "\n_miadm.uname = '".$user->usess['uname']."';";
            echo "\n_mpadm.title = '".@$user->uperm['title']."';";
        }
    } 
    echo "$jscfg\n";
    
    // ***** 加载Base.js *****
    require DIR_SKIN.'/_pub/jslib/jsbase.js';
    require DIR_SKIN.'/_pub/jslib/jsbext.js'; 
    require DIR_SKIN.'/_pub/jslib/jspop.js'; 
    $flang = DIR_SKIN."/_pub/jslib/jcore-$lang.js";
    if(file_exists($flang)) require $flang; 

    // ***** 加载jsPlus *****
    require DIR_SKIN.'/_pub/jslib/jq_base.js'; 
    //require DIR_SKIN.'/_pub/jslib/jq_play.js'; 
    require DIR_SKIN.'/_pub/jslib/jq_win.js';
    //require DIR_VENDUI.'/jquery/jq-qrcode.js';
}

if(strstr($act,'autoJQ')){
    if(basReq::val('light')){ // 需要自行添加如下tepto文件
        require DIR_VENDUI.'/jquery/zepto-1.2.imp_js';
    }elseif(preg_match("/MSIE [6|7|8].0/",$_cbase['run']['userag'])){
        require DIR_VENDUI.'/jquery/jquery-1.x.imp_js'; 
        require DIR_VENDUI.'/jquery/html5.imp_js'; // html5shiv + respond
    }else{
        require DIR_VENDUI.'/jquery/jquery-2.x.imp_js';
    }
}

if(strstr($act,'cssInit')){
    echo "/*stpub*/";
    include DIR_SKIN."/_pub/a_jscss/stpub.css"; 
    echo "/*jstyle*/";
    include DIR_SKIN."/_pub/a_jscss/jstyle.css"; 
}

if(!empty($exjs)){ // /chn/b_jscss/comm.css;/b_jscss/home.css
    basJscss::imFiles($exjs,$lang);
}
if(!empty($excss)){ // /chn/b_jscss/comm.js;/jquery/jq_imgChange.js:vendui
    basJscss::imFiles($excss,0);
}

// test
if(strstr($act,'testInfo')){
    $pprev = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    echo "var testInfo = '$pprev';";
    echo "document.write(\"$pprev\")";
}
// test
if(strstr($act,'testSleep')){    
    usleep(1200*1000); //usleep(200000);#暂停200毫秒
    echo "var testSleep$r = 'ts1_$r'; ";    
}

// 一次一个if()关闭,是因为可能出现类似[?act=jsTypes:cargo,brand;jsRelat:relpb;jsFields:cargo ]参数，同时执行几段代码

// 拼音tab    
if(strstr($act,'pycfgTab')){
    $pyTab = str_replace(array("\r","\n",","),"",comConvert::pycfgTab());
    echo "function pycfgTab(){return '$pyTab';}";
}
// 简繁tab    
if(strstr($act,'jfcfgTab')){
    $tab1 = comConvert::jfcfgTab('Jian');
    $tab2 = comConvert::jfcfgTab('Fan');
    echo "function jfcfgJian(){return '$tab1';}\n";
    echo "function jfcfgFan(){return '$tab2';}\n";
}
// Types    
if(strstr($act,'jsTypes')){
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
// Relat    
if(strstr($act,'jsRelat')){
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
// Type2    
if(strstr($act,'jsType2')){
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
if(strstr($act,'jsFields')){
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
    echo "\nvar _{$mods}_fields = $ccfg;\n"; //"\",\"cfgs\":\""
}
