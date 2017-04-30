<?php

// basDebug
class basDebug{    

    /* *****************************************************************************
      *** debug通用代码 
    - var,bug前缀
    - by Peace(XieYS) 2012-02-18
    ***************************************************************************** */
    
    // *** 显示变量 
    static function varShow($val,$flag=''){    
        echo "\r\n<pre style='line-height:150%;'>"; 
        if($flag) echo "[$flag]\r\n"; $flag = 0;
        if(is_bool($val)) echo "[bool] "; 
        elseif(is_numeric($val)) echo "[num] ";
        elseif(is_string($val)) echo "[str:".strlen($val)."] ";
        elseif(is_array($val)) echo "[arr:".count($val)."] "; 
        elseif(is_object($val)) echo "[obj] ";
        else{ echo "[num] "; } // ,"\n"
        echo str_replace(array('<','>'),array('&lt;','&gt;'),var_export($val,1));
        echo "</pre>\r\n";
    }
    // *** 变量列表
    static function varList($act,$url){
        $cnt=0; $sv=''; $sc=''; $se=''; $sf=1;
        foreach ($GLOBALS as $k => $v) { 
            if((is_array($v)&&$act=='arrs')||(is_object($v)&&$act=='objs')){
                echo "\r\n<a href='$url&_deKey=$k'>$k</a><br>";
                $cnt++;
            }else if((!is_array($v))&&(!is_object($v))&&$act=='vars'){
                if(isset($_SERVER[$k])) { $sf=0; $sv .= "$k, "; }
                if(isset($_COOKIE[$k])) { $sf=0; $sc .= "$k, "; }
                if(isset($_SESSION[$k])) { $sf=0; $se .= "$k, "; }
                if($sf) echo "\r\n$k=[".htmlspecialchars($v)."]<br>";  
                $cnt++; $sf=1;
            } 
        }
        $s = empty($sv)?'':"\r\n[_SERVER]: $sv<br>"; 
        $s .= empty($sc)?'':"\r\n[_COOKIE]: $sc<br>"; 
        $s .= empty($se)?'':"\r\n[_SESSION]: $se<br>"; 
        echo $s;
        return $cnt;
    }
    // *** 系统变量-控制台
    static function varMain($url="?_deTest=peaceTest"){ // eg: ?action=$action 
        $_deAct = isset($_GET['_deAct'])?$_GET['_deAct']:'vars'; 
        $_deKey = isset($_GET['_deKey'])?$_GET['_deKey']:'';     
        if($_deAct=='info'){ ob_end_clean(); phpinfo(); die(); }
        echo "\n<div style='line-height:150%;'> Debug Menu: ";
        foreach(array('vars','arrs','objs','info') as $k) echo "\n<a href='$url&_deAct=$k'>$k</a>&nbsp;"; 
        echo "\nDebug Now: [".($_deKey?$_deKey:$_deAct)."]\n<hr>";
        if($_deKey!='') self::varShow($GLOBALS[$_deKey]); 
        else $_deKey = self::varList($_deAct,$url);
        echo "\n<hr>\n All: [$_deKey] End.";
        echo " Memory: ".round(memory_get_usage()/1024/1024, 3)." MB\n";
    }
    // 运行统计信息
    // tq : tpl, qstr, auto
    static function runInfo($tq='auto'){ 
        $run = cfg('run');
        $qtime = $run['qtime'];
        $rtime = microtime(1) - $run['timer'];
        if($rtime>1){
            $unit = 's'; 
            $qtime = number_format($qtime,4);
            $rtime = number_format($rtime,4);
        }else{
            $unit = 'ms';
            $qtime = number_format($qtime*1000,3);
            $rtime = number_format($rtime*1000,3);
        } // Done in 0.253444 sec(s), 12 queries .
        $info = "Done:$qtime/$rtime($unit); ";
        $info .= "".$run['query']."(queries)/".round(memory_get_usage()/1024/1024, 3)."(MB); ";
        $route = empty($_SERVER['PATH_INFO']) ? '' : "Route:".$_SERVER['PATH_INFO']."; ";
        $tpl = "Tpl:".(empty($run['tplname']) ? '(null)' : $run['tplname'])."; "; //tpl 
        $qstr = "[".(empty($_SERVER['QUERY_STRING']) ? '(null)' : $_SERVER['QUERY_STRING'])."] "; //qstr
        $auto = empty($route) ? (empty($run['tplname']) ? $qstr : $tpl) : $route;
        $info .= $$tq."Upd:".date('Y-m-d H:i:s')." "; // str_replace('T',' ',date(DATE_ATOM))
        return $info;
    }
    // 运行Load
    static function runLoad($pre=0){  
        $aclass = cfg('run.aclass');
        $fix = $pre ? 'pre' : '!--';
        $tmp = self::hidInfo($aclass);
        echo "\n".($pre ? '<pre>' : '<!--'); 
        print_r($tmp); 
        echo "</".($pre ? '</pre>' : '-->'); 
        
    }
    // 调试停止
    static function bugStop($url="?_deTest=peaceTest"){ 
        $_deAct = isset($_GET['_deAct'])?$_GET['_deAct']:''; 
        if($_deAct) self::varMain("$url&_deAct=$_deAct");
        else{
            foreach(array('vars','arrs','objs','info') as $k) echo "\n<a href='$url&_deAct=$k'>$k</a>&nbsp;"; 
            echo "\nMemory: ".round(memory_get_usage()/1024/1024, 3)." MB\n";
        }
        die();
    }
    // *** 系统参数
    static function bugPars($pars='',$from=array('GET','SESSION')){
        $re = ''; //'GET','POST','COOKIE','SESSION'
        $arr = explode(';',str_replace(array(','),';',$pars)); 
        foreach($arr as $k){
            foreach($from as $m){ 
                $f = "_$m"; $n = $GLOBALS[$f]; 
                $v = isset($n[$k]) ? $n[$k] : false; 
                if($v!==false) $re .= "$k($m)=$v; ";
            } 
        }
        return $re;
    }
    // *** 捕获系统状态信息
    static function bugInfo(){
        $info = array(); 
        $info['ram'] = memory_get_usage(); 
        $info['run'] = microtime(1); 
        $info['vp'] = $_SERVER['REQUEST_URI'];
        $info['rp'] = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'(null referer)';
        foreach(array('vp','rp') as $k){
            $info[$k] = str_replace(array("'","\\"),array("`","#"),$info[$k]);
        }
        $info['ip'] = basEnv::userIP(1);
        $info['ua'] = basEnv::userAG();
        return $info;
    }
    // *** 错误信息($msg):mod:file-保存到文件; db-数据库; (空)-输出
    static function bugLogs($act='',$msg=array(),$path='detmp',$mod=''){ 
        global $_cbase;
        $mod || $mod = $_cbase['debug']['log_save']; //show,db,file
        $info = self::bugInfo();
        $info['run'] = 1000*($info['run'] - $_cbase['run']['timer']);
        if(is_array($msg)){
            $re = '';
            foreach($msg as $k=>$v){ $v = is_array($v) ? '[Array]' : $v; $re .= "$k=$v; "; }
            $msg = $re;
        }
        if($mod!='db'){
            $data = "\nact=$act@".date('Y-m-d H:i:s',$_cbase['run']['stamp'])."<br>";
            $data .= "\nused=$info[run]/$info[ram]<br>";
            $data .= "\npage=$info[vp]<br>\nrp=$info[rp]<br>";
            $data .= "\nip=$info[ip]<br>\nua=$info[ua]<br>";
            $data .= "\n$msg<br>\n";
        }
        if(empty($mod) || $mod=='show'){ // show
            $dcss = "border:1px solid #F00; background-color:#FFFFCC; padding:8px; margin:5px; clear:both; display:block;";    
            print_r("\n\n<div style=\"$dcss\">$data</div>\n\n");
        }elseif($mod=='db'){
            $db = db();
            $kid = basKeyid::kidTemp();
            $vals = "'$kid','$act','$info[run]','$info[vp]','$info[rp]','".basReq::in($msg)."','$info[ip]','{$_cbase['run']['stamp']}','$info[ua]'";
            $db->db->run("INSERT INTO ".$db->table("logs_$path",2)."(kid,`act`,used,page,pref,note,aip,atime,aua)VALUES($vals)");     
        }else{ // file
            $data = str_replace("<br>\n","\n",$data);
            if(!$path){
                $file = "debug/".date('Y-md').".debug"; 
            }elseif(!strstr($path,"/")){
                $file = "debug/$path"; 
            }else{
                $file = $path; 
            } 
            if(!strstr($file,'debug/')) $ftmp = "debug/$file";
            else $ftmp = "$file"; 
            comFiles::chkDirs(str_replace("//","/",$ftmp),'dtmp');
            $file = str_replace("//","/",DIR_DTMP."/$ftmp"); 
            $fh = fopen($file, "a+");
            fwrite($fh, "\n$data");
            fclose($fh);
        }
    }
    
    // 隐藏:根路径,表前后缀
    static function hidInfo($str='',$db=0){
        if(!$db){ // DIR_IMPS, DIR_VARS ,dirname(DIR_PROJ)
            $str = str_replace(array(DIR_PROJ,DIR_IMPS,DIR_VARS),'~',$str); 
        }else{
            $cdb = read('db','cfg'); 
            foreach(array('pre','suf') as $key){
                $fix = $cdb["db_{$key}fix"];
                if(!empty($fix)){
                    $str = str_replace(array(" $fix","$fix ","`$fix","$fix`"),array(" {pre}","{suf} ","`{pre}","{suf}`",),$str);
                }
            }
        }
        return $str;
    }

    
}

