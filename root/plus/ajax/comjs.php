<?php 
require(dirname(__FILE__).'/_config.php'); 
#safComm::urlFrom(); 
extract(basReq::sysVars());
$act = req('act','sysInit'); 
$lang = req('lang'); 
$exjs = req('exjs'); 
$excss = req('excss'); 
glbHtml::head($excss ? 'css' : 'js');
//echo "$act<br>\n$exjs\n$excss\n";

// 初始化js
if(strstr($act,'sysInit')){
    $lang = $lang ? $lang : $_cbase['sys']['lang'];
    // ***** js配置区 *****
    $jscfg  = "\n// js Config";
    $jscfg .= "\nvar _cbase={}; _cbase.run={}; _cbase.sys={}; _cbase.path={}; _cbase.ck={};";
    $jscfg .= "\n_cbase.safe={}; _cbase.safil={}; _cbase.jsrun={};"; //_cbase.safe={}; 
    $jscfg .= "\n";
    $jscfg .= "\n_cbase.run.timer = '".$_cbase['run']['timer']."';";
    $jscfg .= "\n_cbase.run.stamp = '".$_cbase['run']['stamp']."';";
    $jscfg .= "\n_cbase.run.userag = '".$_cbase['run']['userag']."';";
    $jscfg .= "\n_cbase.run.jsimp = ',';";
    //sys
    $jscfg .= "\n_cbase.sys.cset = '".$_cbase['sys']['cset']."';";
    $jscfg .= "\n_cbase.sys.tzone = '".$_cbase['sys']['tmzone']."';"; // 时区+-12
    $jscfg .= "\n_cbase.sys.lang = '$lang';";
    $jscfg .= "\n_cbase.run.ref = '".@$_SERVER['HTTP_REFERER']."';"; // 
    $jscfg .= "\n_cbase.run.rsite = '".$_cbase['run']['rsite']."';";
    $jscfg .= "\n_cbase.run.rmain = '".$_cbase['run']['rmain']."';";
    $jscfg .= "\n_cbase.run.roots = '".$_cbase['run']['roots']."';";
    $jscfg .= "\n_cbase.run.rskin = '".$_cbase['run']['rsite'].PATH_SKIN."';";
    $jscfg .= "\n_cbase.run.dmtop = '".$_cbase['run']['dmtop']."';";
    //tpl
    if($tpldir=req('tpldir')){
        $tpldir = vopTpls::set($tpldir);
        if(!empty($_cbase["close_$tpldir"])){ //close-for-static-files
            die("location.href='".PATH_PROJ."?close&tpldir=$tpldir';");
        }
        $jscfg .= "\n_cbase.run.mkv = '".req('mkv')."';";
        $jscfg .= "\n_cbase.run.csname = '".vopUrl::burl()."';";
        $jscfg .= "\n_cbase.run.tpldir = '$tpldir';";
    }
    // Path  
    $jscfg .= "\n_cbase.path.cache   = '".PATH_DTMP."';"; 
    $jscfg .= "\n_cbase.path.vendor  = '".PATH_VENDOR."';"; 
    $jscfg .= "\n_cbase.path.vendui  = '".PATH_VENDUI."';"; 
    $jscfg .= "\n_cbase.path.static  = '".PATH_STATIC."';"; 
    $jscfg .= "\n_cbase.path.skin    = '".PATH_SKIN."';"; 
    $jscfg .= "\n_cbase.path.editor  = _cbase.path.vendui + '/edt_".@$_cbase['sys_editor']."/';"; 
    // Cookie
    $jscfg .= "\n_cbase.ck.ckpre = '".$_cbase['ck']['pre']."';";
    $jscfg .= "\n_cbase.ck.ckdomain = '".$_cbase['ck']['domain']."';";
    $jscfg .= "\n_cbase.ck.ckpath = '".$_cbase['ck']['path']."';";
    
    // Safil
    $jscfg .= "\n";
    $jscfg .= "\n_cbase.safe.safil = '".$_cbase['safe']['safil']."';";
    $jscfg .= "\n_cbase.safe.safix = '".$_cbase['safe']['safix']."';";
    #$jscfg .= "\n_cbase.safe.rnum = '".$_cbase['safe']['rnum']."';";
    #$jscfg .= "\n_cbase.safe.rspe = '".$_cbase['safe']['rspe']."';";
    $jscfg .= "\n_cbase.safil.url = '".safComm::urlStamp('init')."';";
    
    // Para
    $jscfg .= "\n"; //_cbase.para={};\n
    $jscfg .= "\n_cbase.sys_editor = '".@$_cbase['sys_editor']."';";
    $jscfg .= "\n_cbase.sys_open = ".(empty($_cbase['sys_open']) ? 1 : $_cbase['sys_open']).";";
    $jscfg .= "\n_cbase.sys_pop = ".(empty($_cbase['sys_pop']) ? 1 : $_cbase['sys_pop']).";";
    $jscfg .= "\n_cbase.msg_timea = ".(empty($_cbase['sys_timea']) ? 1500 : $_cbase['sys_timea']).";";
    $jscfg .= "\n_cbase.sys_map = '".@$_cbase['sys_map']."';";
    $jscfg .= "\n";
    
    if(!empty($_GET['user'])){
        $jscfg .= "\n// js Member/Admin"; 
        $jscfg .= "\nvar _minfo={}, _mperm={}, _miadm={}, _mpadm={}; ";
        $user = user('Member');
        if(!empty($user)){
            $jscfg .= "\n_minfo.userType = '".$user->userType."';";
            $jscfg .= "\n_minfo.userFlag = '".$user->userFlag."';";
            $jscfg .= "\n_minfo.uname = '".$user->usess['uname']."';";
            $jscfg .= "\n_mperm.title = '".@$user->uperm['title']."';";
        }
        $user = user('Admin');
        if(!empty($user)){
            $jscfg .= "\n_miadm.userType = '".$user->userType."';";
            $jscfg .= "\n_miadm.userFlag = '".$user->userFlag."';";
            $jscfg .= "\n_miadm.uname = '".$user->usess['uname']."';";
            $jscfg .= "\n_mpadm.title = '".@$user->uperm['title']."';";
        }
    } //print_r($user->uperm);
    echo "$jscfg\n";
    
    // ***** 加载Base.js *****
    require(DIR_SKIN.'/_pub/jslib/jsbase.js');
    require(DIR_SKIN.'/_pub/jslib/jsbext.js'); 
    require(DIR_SKIN.'/_pub/jslib/jspop.js'); 
    $flang = DIR_SKIN."/_pub/jslib/jcore-$lang.js";
    if(file_exists($flang)) require($flang); 

    // ***** 加载jsPlus *****
    require(DIR_SKIN.'/_pub/jslib/jq_base.js'); 
    //require(DIR_SKIN.'/_pub/jslib/jq_play.js'); 
    require(DIR_SKIN.'/_pub/jslib/jq_win.js');
    //require(DIR_VENDUI.'/jquery/jq-qrcode.js');

    //print_r(basDebug::runInfo());
}

if(strstr($act,'autoJQ')){
    if(req('light')){ // 需要自行添加如下tepto文件
        require(DIR_VENDUI.'/jquery/zepto-1.2.imp_js');
    }elseif(preg_match("/MSIE [6|7|8].0/",$_cbase['run']['userag'])){
        require(DIR_VENDUI.'/jquery/jquery-1.x.imp_js'); 
        require(DIR_VENDUI.'/jquery/html5.imp_js'); 
    }else{
        require(DIR_VENDUI.'/jquery/jquery-2.x.imp_js');
    }
}

if(strstr($act,'cssInit')){
    echo "/*stpub*/";
    include(DIR_SKIN."/_pub/a_jscss/stpub.css"); 
    echo "/*jstyle*/";
    include(DIR_SKIN."/_pub/a_jscss/jstyle.css"); 
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
        $mcfg = read($mod);
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
        echo read($m1,'modcm','json')."\n";
        $done .= "$m1,";
    }
}
if(strstr($act,'jsFields')){
    //扩展字段
    $mods = exvFunc::actMods($act,'jsFields'); 
    $ccfg = read($mods,'_c');
    //常规字段
    $cmod = req('cmod');
    $amod = array();
    if($cmod){
        ${"_$mods"} = read($mods); 
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
    } //print_r($ccfg);
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
                } // 'enable','etab','dbtype','dblen','dbdef','vreg','vtip','vmax','fmsize','fmtitle'
            }
            $v3['cfgs'] = str_replace(array("\r\n","\r","\n",";;"),array(";",";",";",";"),$v3['cfgs']);
    }   }  //print_r($ccfg);
    $ccfg = comParse::jsonEncode($ccfg);
    $ccfg = str_replace(array("\\/","\"}},\"",),array("/","\"}}\n,\"",),$ccfg);
    echo "\nvar _{$mods}_fields = $ccfg;\n"; //"\",\"cfgs\":\""
}

//echo basDebug::runInfo();
