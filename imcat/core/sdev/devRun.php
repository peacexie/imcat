<?php
namespace imcat;

define('FLAGYES', '<span style="color: #008000; font-weight : bold;">&#10004;<!--isYes--></span>');
define('FLAGNO', '<span style="color: #ff0000; font-weight : bold;">&#10008;<!--isNo--></span>');
define('MINPHPVER', '5.3');

// ...类
class devRun{    
    
    static $sfixpath = '/store/_setfix_path.txt'; 
    static $sfixidpw = '/store/_setrnd_idpw.txt'; 
    static $fp_paths = '/cfgs/boot/_paths.php';
    static $fp_dbcfg = '/cfgs/boot/cfg_db.php';

    static function rstVerfix(){
        global $_cbase;
        $fix = str_replace('.','',$_cbase['sys']['ver']);
        return $fix;
    }

    static function rstDbname(){
        $fsu = DIR_DTMP.devSetup::$flagfile;
        $fdb = DIR_ROOT.self::$fp_dbcfg;
        # 自动修正路径时运行,db配置一天内未修改,且是初始安装
        $mt = time()-filemtime($fdb);
        if(abs($mt)>86400 && !file_exists($fsu)){
            include(DIR_ROOT.self::$fp_dbcfg);
            $oldname = $_cfgs['db_name'];
            $dbname = self::autoDbname();
            $data = comFiles::get(DIR_ROOT.self::$fp_dbcfg);
            $data = str_replace("'$oldname';", "'$dbname';",$data);
            $fres = comFiles::put(DIR_ROOT.self::$fp_dbcfg,$data);
        }
    }
    // imcat_v42_a12
    static function autoDbname(){
        include(DIR_ROOT.self::$fp_dbcfg.'-cdemo');
        $tmp = explode('_', $_cfgs['db_name']);
        $dbname = $tmp[0].'_v'.self::rstVerfix().'_';
        $dbname .= dechex(date('m')).date('d');
        return $dbname;
    }
    // prootGet
    static function prootGet(){
        // 虚拟目录: /imcat/index.php
        $path = $_SERVER['SCRIPT_NAME']; // SCRIPT_NAME,PHP_SELF
        if(strpos($path,'.php/')>0){ // fix:engix下pathinfo(index.php/mkv)
            $path = substr($path, 0, strpos($path,'.php/')+4);
        }
        $files = array(
            "/root/tools/setup/index.php",
            "/root/tools/adbug/start.php",
            "/base.php",
            "/index.php",
        );
        $path = str_replace($files,'',$path);
        return $path;
    } 
    // prootFix
    static function prootFix($proot){
        $fpath = DIR_DTMP.self::$sfixpath;
        if(file_exists($fpath)) return false; //只修正一次
        $data = comFiles::get(DIR_ROOT.self::$fp_paths);
        $fix = "define('PATH_PROJ'"; 
        $data = str_replace("$fix,","$fix, '$proot'); #Old: ",$data);
        $fres = comFiles::put(DIR_ROOT.self::$fp_paths,$data);
        if($fres && !empty($data)){
            $re = 1;
            comFiles::put($fpath,date('Y-m-d H:i:s')); 
            //self::rstDbname();
        }else{
            $re = 0;
        }
        return $re;
    }
    // prootMsg
    static function prootMsg($proot, $fixres){
        $re = array();
        $fps = self::$fp_paths;
        $exmsg = empty($proot) ? basLang::show('devrun_tipr1') : basLang::show('devrun_tipr2');
        $exupd = "<a href='?' style='color:blue;float:right' target='_top'>".basLang::show('devrun_upd')."</a>";
        $exok = basLang::show('devrun_fixpararm')."(/imcat$fps): <br>define('PATH_PROJ', '$proot');";
        $exng = basLang::show('devrun_file')."(/imcat$fps), ".basLang::show('devrun_setpath')."<br>define('PATH_PROJ', '$proot');";
        if($fixres=='FixPrOkey'){
            $re['msg'] = "$exok $exupd <br>$exmsg : ".basLang::show('devrun_upding');
            $re['tip'] = FLAGYES;
        }else{ // FixPrError
            $re['msg'] = "$exng $exupd <br>$exmsg";
            $re['tip'] = FLAGNO;
        }
        return $re;
    } 

    static function startCheck(){ 
        $umsg = array(); 
        // php版本
        $pver = MINPHPVER;
        if(version_compare(PHP_VERSION,$pver,'<')){
            $umsg['vphp']['msg'] = basLang::show('devrun_needenv')." PHP V{$pver}+";
            $umsg['vphp']['tip'] = FLAGNO;
        }

        // db配置
        include DIR_ROOT.'/cfgs/boot/cfg_db.php';
        $dbcls = $_cfgs['db_driver'];
        if($dbcls=='pdo'){
            $dbflag = class_exists('PDO');
        }else{
            $dbflag = function_exists("{$dbcls}_connect");
        }
        // mysql扩展
        $exlib = array('mysqli'=>'mysqli','mysql'=>'mysql','pdo'=>'pdo_mysql');
        if(!$dbflag){
            $umsg['mysql']['msg'] = basLang::show('devrun_my3a',$exlib[$_cfgs['db_driver']]).basLang::show('devrun_my3b')."(/root/cfgs/boot/cfg_db.php)，<br>".basLang::show('devrun_my3c')."\$_cfgs['db_driver'] = 'mysqli,mysql,pdo'; //".basLang::show('devrun_my3d');
            $umsg['mysql']['tip'] = FLAGNO; 
        }
        // gd2扩展
        if(!function_exists('gd_info')){
            $umsg['gd2']['msg'] = basLang::show('devrun_gd2')."<br>extension=php_gd2.dll";
            $umsg['gd2']['tip'] = FLAGNO;
        }
        // 重置辅助调试工具账号密码
        if(!file_exists($fpath=DIR_DTMP.self::$sfixidpw)){
            $cfgs = glbConfig::read('pubcfg','sy');
            $key = 'cfgs/boot/cfg_adbug.php'; 
            $rep = $cfgs['cdemo']["root/$key"];
            $data = comFiles::get(DIR_ROOT."/$key-cdemo");
            $data = str_replace($rep[0],$rep[1],$data);
            comFiles::put(DIR_ROOT."/$key",$data);
            comFiles::put($fpath,date('Y-m-d H:i:s')); 
        }
        return $umsg;
    }

    static function startDbadd($dbname){ 
        include DIR_ROOT.'/cfgs/boot/cfg_db.php';
        foreach($_cfgs as $k=>$v){
            if(!empty($_POST[$k])) $_cfgs[] = $_POST[$k];
        }
        $type = $_cfgs['db_driver'];
        $sql = "CREATE DATABASE `$dbname` COLLATE 'utf8_general_ci';";
        if($type=='pdo'){
            $dsn = 'mysql:host='.$_cfgs['db_host'].';dbname='.$_cfgs['db_name'].'';
            $idb = new PDO( $dsn, $_cfgs['db_user'], $_cfgs['db_pass']); 
            $re = $idb->query($sql);
        }elseif($type=='mysqli'){
            $link = mysqli_connect($_cfgs['db_host'], $_cfgs['db_user'], $_cfgs['db_pass']);
            $re = mysqli_query($link,$sql);
        }elseif($type=='mysql'){ 
            $link = mysql_connect($_cfgs['db_host'], $_cfgs['db_user'], $_cfgs['db_pass']);
            $re = mysql_query($sql,$link);
        }else{
            $re = false;
        }    
        return $re;    
    }
    
    // runPHPVer测试, 
    static function verPHP(){ 
        $info = PHP_VERSION.' (SAPI:'.PHP_SAPI.')';
        $res = version_compare(PHP_VERSION,MINPHPVER,">") ? FLAGYES : FLAGNO;
        $tip = 'V5.4+, '.basLang::show('devrun_phpvbest').' V5.6+';
        $status = array('title'=>basLang::show('devrun_phpver'),'info'=>$info,'res'=>$res,'tip'=>$tip);
        return $status;
    }
    
    // verGdlib
    static function verGdlib() { 
        if(!function_exists('gd_info')) return array('title'=>'GD library','info'=>'-','res'=>FLAGNO,'tip'=>'V2.0+');
        $info = gd_info(); 
        $ver = preg_replace("/[^\d\.]/",'',$info['GD Version']); 
        $res = version_compare($ver,"2.0",">") ? FLAGYES : FLAGNO;
        $status = array('title'=>'GD library','info'=>$info['GD Version'],'res'=>$res,'tip'=>'V2.0+','demo'=>'?act=image');
        return $status;
    }
    
    // runRemote
    static function runRemote() { 
        $a = array('curl_init','fsockopen','file_get_contents');
        $f1 = $f2 = '';
        foreach($a as $k){ 
            $f1 .= function_exists($k) ? ($f1 ? ', ' : '')."$k" : "";
            $f2 .= function_exists($k) ? "" : ($f2 ? ', ' : '')."$k";
        }
        $res = $f1 ? FLAGYES : FLAGNO;
        $status = array('title'=>'Remote(3)','info'=>$f1?$f1:'-','res'=>$res,'tip'=>$f2?$f2:'','demo'=>'?act=remote');
        return $status;
    }
    
    // runPath测试, 
    static function runPath($key){ 
        $ca1 = array('static','vendor','vendui');
        $ca2 = array('ctpl','dtmp','ures','html');
        $cdirs = array_merge($ca1,$ca2);
        $dhid = dirname(DIR_PROJ);
        $re = array();
        foreach($cdirs as $key){ 
            $ukey = strtoupper($key);
            $file = "/@setup_flag.txt"; 
            $dconst = get_defined_constants(1);
            $dir = $dconst['user']["DIR_$ukey"];
            $path = $dconst['user']["PATH_$ukey"];  //is_dir
            @$datad = comFiles::get($dir.$file);
            $host = (basEnv::isHttps() ? 'https://' : 'http://').basEnv::serval('host');
            $datap = comHttp::curlCrawl($host.$path.$file);
            if($datad && $datap && $datad==$datap){
                $stat = FLAGYES;
            }else{
                $stat = FLAGNO;    
            }
            if(in_array($key,$ca2)){
                $fwrite = comFiles::canWrite($dir);
                if(!$fwrite){
                    $stat = str_replace('<!--isNo-->',basLang::show('devrun_notwrite'),FLAGNO);        
                } 
            }
            $re[] = array('ukey'=>$ukey,'dir'=>str_replace($dhid,"{...}",$dir),'path'=>$path,'res'=>$stat);
        } 
        return $re;
        
    }
    
    // Mysql测试, 
    static function runMydb3($dbcfgs=array()){ 
        $a = array('mysqli'=>array(),'mysql'=>array(),'pdo'=>array());
        if(empty($dbcfgs)){
            include DIR_ROOT.'/cfgs/boot/cfg_db.php';
        }else{
            $_cfgs = $dbcfgs; 
        }
        foreach($_cfgs as $k=>$v){
            if(!empty($_POST[$k])) $_cfgs[] = $_POST[$k];
        }
        $type = 'pdo'; 
        if(!class_exists($type)){
            $a[$type] = array('res'=>FLAGNO,'info'=>basLang::show('devrun_extendset',$type));
        }else{
            $a[$type] = array('res'=>FLAGYES,'info'=>''); //支持pdo扩展
            try{
                $dsn = 'mysql:host='.$_cfgs['db_host'].';dbname='.$_cfgs['db_name'].'';
                @$pdo = new \PDO( $dsn, $_cfgs['db_user'], $_cfgs['db_pass']); 
                // PDO::ATTR_DRIVER_NAME, PDO::ATTR_SERVER_VERSION, PDO::ATTR_SERVER_VERSION
                $info = " OK : ".$pdo->getAttribute(\PDO::ATTR_SERVER_VERSION)."; OK : {$_cfgs['db_name']}";
            }catch(\PDOException $e){
                $info = ' - '.$e->getMessage().' - ';
            }
            if(!strstr($info,'; OK :')){
                $a[$type]['res'] = $stat = FLAGNO;    
            }
            $a[$type]['info'] = $info;
        }
        //return $a;
        $myext = array('mysqli'); 
        if(strnatcmp(PHP_VERSION, '5.5.0')<0){ $myext[] = 'mysql'; }
        else { unset($a['mysql']); } 
        foreach($myext as $type){
            $fconn = "{$type}_connect";
            $finfo = "{$type}_get_server_info";
            $ferrno = "{$type}_errno"; 
            $ferror = "{$type}_error";
            if(!function_exists($fconn)){
                $a[$type] = array('res'=>FLAGNO,'info'=>basLang::show('devrun_extendset',$type));
            }else{
                $a[$type] = array('res'=>FLAGYES,'info'=>""); //支持type
                $link = @$fconn($_cfgs['db_host'], $_cfgs['db_user'], $_cfgs['db_pass']);
                @$info = $link ? " OK : ".$finfo($link) : " [".$ferrno($link)."] ".$ferror(); 
                @$_erno = $ferrno($link);
                if($link && empty($_erno)){
                    $dbflag = $type=='mysql' ? @mysql_select_db($_cfgs['db_name'],$link) : @mysqli_select_db($link,$_cfgs['db_name']);
                    if($dbflag){
                        $info .= "; OK : {$_cfgs['db_name']}";
                    }else{
                        $info = " - [".$ferrno($link)."] ".$ferror($link).' - '; 
                    }
                }else{
                    $info = basLang::show('devrun_linkmysqlerr'); 
                }
                if(!strstr($info,'; OK :')){
                    $a[$type]['res'] = $stat = FLAGNO;    
                }
            }
            $a[$type]['info'] = @$info;
        }
        return $a;
    }
    
    // GD2测试, self::runGdlib()
    static function runGdlib(){ 
        $w = 240; $h = 180; $im = imagecreate($w,$h);
        imagefill($im,0,0,imagecolorallocate($im,245,245,245)); //背景
        for($i=0;$i<5;$i++){
            $ctab = strtoupper(KEY_TAB24); 
            $char = substr($ctab,mt_rand(0,strlen($ctab)-1),1);
            $color = imagecolorallocate($im,rand(100,255),rand(0,100),rand(100,255));
            $xPos=rand(5,21);
            $yPos=rand(30,90);
            imagestring($im,5,10+$i*40+$xPos,$yPos,$char,$color);
        }
        for($i=0;$i<180;$i++){ //加入干扰象素
            $color = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
            imagesetpixel($im,rand(10,$w-10),rand(10,$h-10),$color);
        } 
        imagerectangle($im,0,0,$w-1,$h-1,imagecolorallocate($im,rand(30,240),rand(30,240),rand(30,240)));
        header("Content-type: image/png");
        imagepng($im); imagedestroy($im);
    }
    
    // 内存测试, self::runMemory($inpval)
    static function runMemory($max){ 
        $timer = microtime(1);
        $t = 'Test string : memory_get_usage! ';
        $sUnit = ''; // *32=1M
        for($i=0;$i<1024*16;$i++) $sUnit .= $t;
        $mbit = ($max-1)*1024*1024; $t = '';
        while($max){
            $t .= $sUnit; $m = memory_get_usage(); 
            if($m>$mbit){
                $timer = round(microtime(1)-$timer,3);
                $len = round(strlen($t)/1000/1000,3);
                return "\nMemory test(".round($m/1024/1024,3)."/{$max}M) is OK! len:{$len}M/$timer(s).";
            }
        }
    }

    // 超时测试, self::runMTask($inpval)
    static function runMTask($max,$start=0){
        if(!$start) $start = $now = microtime(1);
        while(($now-$start)<$max){
            sleep(1);
            $now = microtime(1);
        }
        return "\n run-Test($max)s; ".date('H:i:s',$start)."~".date('H:i:s',$now)." ";
    }

    static function bomScan($bomroot,$rsub='',$flag=0) {  
        if(empty($rsub)) return;
        static $frstr,$bonum; 
        if(empty($frstr)){ 
            $frstr = "\n<ul>\n<li><b> ====== [root] ====== </b></li>\n";
            $bonum = 0;
        }
        $full = "$bomroot/$rsub";
        $handle = opendir($full);
        while($file=readdir($handle)){ //echo "<br>aa:$file";
            if(in_array($file,array('.','..','.svn',))) continue;
            $bonum++; if($bonum>1000) die("<p>".basLang::show('devrun_tmfiles')."</p>");
            if(is_dir("$full/$file")){
                $real = basDebug::hidInfo(realpath("$full/$file"));
                echo "\n<ul>\n";
                echo "<li><b>$real:</b></li>\n";
                self::bomScan($full,$file,$flag+1);
                echo "</ul>\n";
            }else{
                $fext = strtolower(strrchr($file,'.'));
                $fskip = array('.gif','.jpg','.jpeg','.png','.swf','.flv','.avi','.mpg','.doc','.docx','.zip','.rar','.gz');
                $size = round(filesize("$full/$file")/1024*100)/100 .' KB';
                $sutf = in_array($fext,$fskip) ? '' : self::bomCheck($full,$file);
                $ifile = "<li>$file <i class='size'>($size)</i>$sutf</li>\n";
                if(!$flag) $frstr .= $ifile;
                else echo $ifile;
            }
        }
        if(!$flag) echo "$frstr</ul>\n";
        closedir($handle);
    }
    static function bomDetect($data) {  
        $charset[1] = substr($data, 0, 1);  
        $charset[2] = substr($data, 1, 1);  
        $charset[3] = substr($data, 2, 1);  
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) return true;
        return false;
    }  
    static function bomCheck($now,$file) {   
        $bomroot = @$_GET['bomroot'];
        $bompath = @$_GET['bompath'];
        if(!file_exists("$now/$file")) return;
        $fp = fopen("$now/$file","r"); $data = fread($fp,8192); fclose($fp); 
        $fbom = self::bomDetect($data);
        $cset = mb_detect_encoding($data,array('ASCII','GB2312','BIG5','GBK','UTF-8'));
        $reutf8 = $cset=='UTF-8' ? "<i class='utf8'>[UTF-8]</i>" : '';
        $reubom = $fbom ? "<i class='red'>[BOM]</i>" : '';
        if($reubom && filesize("$now/$file")<=10*1024*1024 && !strstr($file,'.(bak)')){
            $bomfile = str_replace($bomroot,'',"$now/$file");
            $reubom = "<a href='?act=bomcheck&bomroot=$bomroot&bompath=$bompath&bomfile=$bomfile'>$reubom</a>";    
        }
        return "$reutf8$reubom";
    } 
    static function bomRemove($file) {  
        $filename = str_replace('//','/',"$file");
        $data = comFiles::get($filename); 
        $fbom = self::bomDetect($data);  
        if($fbom){ 
            $orgext = strrchr($file,'.'); 
            $objext = str_replace('.','.(bak)',$orgext); 
            $objname = str_replace($orgext,$objext,$filename); 
            copy($filename,$objname); //有BOM才备份
            $data = substr($data,3);  
            $filenum = fopen($filename,"w");  
            flock($filenum, LOCK_EX);  
            fwrite($filenum, $data);  
            fclose($filenum); 
            return true; 
        }
        return false;
    } 

}

