<?php
namespace imcat;

// Environment基本环境处理类
class basEnv{    

    // 处理_pbase
    static function runPbase($_pbase){
        global $_cbase;
        // 加载runskip
        if(isset($_cbase['skip'])){
            include DIR_ROOT.'/cfgs/boot/bootskip.php'; 
        }
        // 全局系统配置
        if(!empty($_pbase)){ 
            $_cbase = basArray::Merge($_cbase, $_pbase);
        }
    }

    // 系统信息,魔术变量,时区
    static function runVersion(){
        global $_cbase; 
        if(version_compare(PHP_VERSION,'5.4.0','<')) {
            ini_set('magic_quotes_runtime',0);
            ini_set('magic_quotes_gpc',0);
        }
        date_default_timezone_set($_cbase['sys']['tzcode']);        
    }
    // const,
    static function runConst(){
        define('IS_CGI',     substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
        define('IS_WIN',     strstr(PHP_OS, 'WIN') ? 1 : 0 );
        define('IS_CLI',     PHP_SAPI=='cli'? 1   :   0);
        define('KEY_NUM10',  '0123456789');
        define('KEY_CHR26',  'abcdefghijklmnopqrstuvwxyz');
        define('KEY_CHR22',  'abcdefghjkmnpqrstuvwxy'); // -iloz
        define('KEY_NUM16',  KEY_NUM10.'abcdef');
        define('KEY_TAB36',  KEY_NUM10.KEY_CHR26); // 极端情况下用
        define('KEY_TAB32',  KEY_NUM10.KEY_CHR22); // (字形可能与数字012混淆)
        define('KEY_TAB30',  '123456789abcdfghjkmnpqrstuvwxy'); // - 0e + iloz (0字形,e读音易混淆)
        define('KEY_TAB24',  '3456789abcdfghjkpqstuvwxy'); // - 012eilmnorz(25) (去除字形读音易混淆者)
        define('NSP_INIT',   "namespace imcat;\n(!defined('RUN_INIT')) && die('No Init');");
    }

    // 前置处理,运行时常用变量
    static function runCbase(){
        global $_cbase;
        $run = &$_cbase['run'];
        // 运行时常用变量,
        $run['domain'] = $_SERVER['SERVER_NAME'];
        $run['dmtop'] = self::topDomain($run['domain']);
        $run['stamp'] = $_SERVER["REQUEST_TIME"]; 
        $run['userag'] = self::userAG();
        $run['userip'] = self::userIP();
        $run['query'] = 0; //查询次数
        $run['qtime'] = 0; //查询时间
        $run['jsimp'] = ','; //imp-js:files
        $run['tplname'] = ''; //tpl:name
        $run['tplnow'] = ''; //tpl:now
        $run['tagnow'] = ''; //vopShow::tagParse()使用
        $run['tmpFile'] = array();
        $run['jtype_mods'] = ''; //fldView::lists()使用
        $run['jtype_init'] = ''; //fldView::lists()使用
        $run['sobarnav'] = ''; //dopBSo->Form()使用,搜索条上的导航
        $_cbase['tpl']['tplpend'] = ''; //默认'',除非人工改变
        $_cbase['tpl']['tplpext'] = ''; //默认'',除非人工改变
        //$_cbase['mkv'] = array();
        $run['headed'] = '';
        self::sysHome(); //,topDomain,IP过滤
    }
    
    // 处理skips
    static function runSkips(){
        global $_cbase;
        $skip = isset($_cbase['skip']) ? $_cbase['skip'] : array(); 
        // 错误处理类 
        if(!isset($skip['error'])){
            self::runError();
        }
        // *** robot
        if(isset($skip['robot'])){
            safBase::robotStop(); 
        }
        // 处理session
        if(!isset($skip['session'])){ 
            if(!session_id()) @session_start();
        }
    }

    // 加载错误处理类 
    static function runError(){
        global $_cbase;
        $debug = $_cbase['debug'];
        // 加载错误处理类 
        if(!isset($_cbase['skip']['error'])){ // && $debug['err_hand']
            if($debug['err_mode']){
                ini_set('display_errors', 'On');
                error_reporting(E_ALL); 
            }else{
                error_reporting(0); 
            }
            if($debug['err_hand']){
                $hkey = $debug['err_hkey'];
                $hkey = ($hkey=='(def)' || intval($hkey)<=0) ? E_ALL^E_WARNING^E_NOTICE : $hkey; 
                #set_exception_handler('except_handler_ys'); //注册异常处理函数
                #set_error_handler('error_handler_ys',$hkey); //注册错误处理函数
            }
        }
    }

    // 获取客户端软件信息
    static function userAG(){
        $ua = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        //basStr::filTitle($ua)
        $ua = str_replace(array("'","\\"),array("",""),$ua);
        return $ua;
    }
    
    // 获取客户端IP地址('::1','123.234.123.234, 127.0.0.1')(.:[ ])
    static function userIP($flag=0){
        $a = array('xf'=>'HTTP_X_FORWARDED_FOR','ra'=>'REMOTE_ADDR','cip'=>'HTTP_CLIENT_IP');
        $ip = ''; //'r'=>'HTTP_X_REAL_FORWARDED_FOR',
        foreach($a as $k=>$v){
            $v = str_replace(' ','',$v);
            if(!empty($_SERVER[$v]) && !strstr($ip,$_SERVER[$v])){
                $ip .= ';'.($flag ? "$k," : '').$_SERVER[$v];
            }
        }
        if(basArray::inStr(array("'","\\"),$ip)) safBase::Stop('userIPError');
        $ip = substr($ip,1);
        return $ip;
    }

    // ---- 用户信息 判断 --------------------------------------- 
    /*  Android.*MicroMessenger.*miniProgram//安卓端的小程序
        iPhone.*MicroMessenger//苹果端微信或小程序
    //*/
    
    // 是否搜索引擎来访
    static function isRobot($uastr=''){
        $rbt = glbConfig::read('uachk','sy');
        $kw_spiders = $rbt['spname'];
        $uastr || $uastr = self::userAG();
        if(preg_match("/($kw_spiders)/i",$uastr)) return true;
        return false;
    }
    // 是否miniProgram()
    static function isMpro($ver=0){
        $wxpos = strpos(self::userAG(), 'miniProgram');
        return $wxpos;
    } 
    // 是否Weixin()
    static function isWeixin($ver=0){
        $wxpos = strpos(self::userAG(), 'MicroMessenger');
        if($ver){
            preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/', $uagent, $matches);
            return $wxpos ? $matches[2] : '';
        }else{
            return $wxpos;
        }
    } 
    // 是否Qq()
    static function isQq($ver=0){
        $wxpos = strpos(self::userAG(), 'QQBrowser');
        return $wxpos;
    }
    // 是否Mobile
    static function isMobile($ckey=''){
        //return true;
        if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        if(isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'],"wap")){
            return true;
        }
        $mbstr = "Android|Windows Phone|webOS|iPhone|iPad|BlackBerry";
        if(preg_match("/$mbstr/i",self::userAG())){
            return true;
        }else{
            return false;
        }
    }

    # ======================================================

    // topDomain
    static function topDomain($host){
        if(strpos($host,':/')){
            $host = parse_url($host,PHP_URL_HOST);
            //IPv6，这里得到的host有问题
        }
        $arr = explode('.',$host);
        if(!strpos($host,'.')){ //主机名形式:localhost/pcname; IPv6形式:FE80::1, ::1, 2000::1:2345:6789:abcd
            return $host;
        }elseif(is_numeric($arr[count($arr)-1])){ //IPv4
            return $host;
        }else{ //域名
            $cnt = count($arr); 
            $part1 = $arr[$cnt-1]; $part2 = $arr[$cnt-2];
            $re = "$part2.$part1"; //默认
            if($cnt>=3){
                $tcfg = glbConfig::read('domain','sy'); 
                $tcfg = $tcfg['dmtop'];
                $t3p = '.com.net.org.edu.gov.int.mil.';
                $re3 = $arr[$cnt-3].".$re";
                if(!empty($tcfg[$re])){
                    $re = $tcfg[$re]==3 ? $re3 : $re;
                }elseif(strlen($part2)==2 && strlen($part1)==2){ //2.2 www.dg.gd.cn, www.88.cn
                    $re = preg_match('/[a-z]{2}/',$part2) ? $re3 : $re;
                }elseif(strlen($part2)==2 && strlen($part1)==3){ //2.3 www.fyh.cn.com, www.88.com
                    $re = strpos($t3p,$part1) ? $re3 : $re;
                }elseif(strlen($part2)==3 && strlen($part1)==2){ //3.2 www.txm.cn, www.net.cn
                    $re = strpos($t3p,$part2) ? $re3 : $re;
                }
            }
            return $re;
        } 
    }
    // sysHome // HTTP_HOST = SERVER_NAME : SERVER_PORT
    static function sysHome(){
        global $_cbase;
        $host = empty($_SERVER["HTTP_HOST"]) ? '' : $_SERVER["HTTP_HOST"]; 
        $res = glbConfig::read('domain','sy');
        $sdirs = $res['subDirs'];
        // dir-跳转:
        if(isset($sdirs[$host])){
            $http = self::isHttps() ? 'https' : 'http';
            $host = $sdirs[$host];
            $uri = $_SERVER['REQUEST_URI'];
            $dir = "$http://$host$uri"; 
            header("Location:$dir");
        }
        $_cbase['run']['rsite'] = "//$host"; 
        $_cbase['run']['rmain'] = "//$host".PATH_PROJ; 
        $_cbase['run']['roots'] = "//$host".PATH_ROOT; 
        $_cbase['run']['fbase'] = "//$host".PATH_BASE; 
    }
    // 判断是否 isHttps
    static function isHttps() {
        if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
            return true; // 1:Apache, on:IIS
        }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
            return true;
        } // HTTP_X_FORWARDED_PROTO='https'
        return false;
    }
    // 检查内网ip地址
    static function isLocal($ip=''){
        $ip || $ip = self::userIP();
        if(strpos($ip,'.')){ // IPv4:
            $pa = explode('.',$ip);
            $f1 = in_array($pa[0],array('10','127')); // 10.*, 127.*
            $f2 = $pa[0]=='192' && ($pa[1]=='168'); // 192.168.*
            $f3 = $pa[0]=='172' && ($pa[1]>='16' && $pa[1]<='31'); // 172.16.* ~ 172.31.*
            return $f1 || $f2 || $f3;
        }elseif(strpos($ip,':')){ // IPv6
            $arr = explode(':',$ip);
            return in_array($arr[0],array('FE80','FEC0'));
        }else{ // ::1=127.0.0.1=localhost
            return in_array($ip,array('::1'));
        }
    }

    // 缓冲区obSave(...)
    static function obSave($msg=''){
        $msg || $msg = "Contents... ";
        $file = __FUNCTION__;
        echo 'flag1';
        ob_start(); 
        echo $msg.date('Y-m-d H:i:s');
        $data = ob_get_contents();
        ob_end_clean(); 
        comFiles::put(DIR_DTMP."/@temp/test_$file.txt",$data);
        echo('flag2');        
    }
    
    // 缓冲区Start, 替代ob_start(...)
    static function obStart(){
        !ini_get('output_buffering') && ob_start();
    }
    // 缓冲区Clean, 替代ob_end_clean(),ob_clean()
    static function obClean($start=1){
        $obList = ob_list_handlers();
        $obLen = count($obList);
        while($obLen>0){
            ob_clean();
            $obLen--;
        };
        if(!empty($start)) self::obStart();
    }
    // 缓冲区调试
    static function obDebug($start=1){
        $obList = ob_list_handlers();
        $obLen = count($obList);
        $str = "\n<hr>";
        while($obLen>0){
            $c = ob_get_contents();
            $str .= "Debug($obLen):$c<br>\n";
            ob_flush();
            $obLen--;
        };
        echo "$str<hr>";
    }
    static function obShow($data='obData',$die=1){
        basEnv::obClean();
        echo "$data";
        $die && die();
    }
}

