<?php

// ...类
class updBase{    
    
    static $copys = array(
        'cfgs/sycfg/sy_updvnow.php' => 'code',
    );
    static $comps = array(
        'cfgs/boot/cfg_load.php' => 'code',
        'cfgs/boot/const.cfg.php' => 'code',
        'run/_init.php' => 'root',
    );
    static $skipdirs = array('tpls','skin');
    static $prereset = '/store/_upd_reset.txt'; 
    
    // 检测锁定
    static function preCheck(){
        $data = comFiles::get(DIR_DTMP.self::$prereset);
        $dcfg = basElm::text2arr($data);
        if(@$dcfg['done']=='locked'){ 
            $msg = basLang::show('updbase_lock1')."<br>";
            $msg .= "[".basDebug::hidInfo(DIR_DTMP)."/store/]:[_upd_reset.txt]<br>";
            $msg .= basLang::show('updbase_tip1')."<br>";
            $msg .= basLang::show('updbase_tip2');
            basMsg::show($msg,'die');
        }
        if(empty($dcfg['path']) || empty($dcfg['steps'])) return '';
        if(!is_dir($dcfg['path'])) return '';
        return $dcfg;
    }    
    // 检测路径
    static function preReset($path){
        $path = str_replace("\\","/",$path);
        $link = " &gt; <a href='?'>".basLang::show('updbase_back')."</a>";
        if(!$path || !is_dir($path)){ 
            basMsg::show(basLang::show('updbase_setdirerr',$path)."$link ",'die');
        }
        if(!is_dir(DIR_DTMP.'/update/')) mkdir(DIR_DTMP.'/update/');
        if(strstr($path,DIR_ROOT) || strstr($path,DIR_CODE)) die("Error Dir [$path]!");
        include $path."/root/cfgs/boot/const.cfg.php"; 
        $vnew = $_cbase['sys']['ver'];
        include DIR_ROOT.'/cfgs/boot/const.cfg.php'; 
        $vold = $_cbase['sys']['ver'];
        if(version_compare($vold,$vnew)>=0){ 
            basMsg::show(basLang::show('updbase_verbig'),'die');
        }
        $br = "\r\n";
        $data = "path=$path{$br}steps=`{$br}dbdata=`{$br}vnew=$vnew{$br}vold=$vold{$br}done=update";
        $f = comFiles::put(DIR_DTMP.self::$prereset,$data);
        if(!$f){ 
            basMsg::show(basLang::show('updbase_notwrite',DIR_DTMP."/update/")."$link ",'die');
        }
        self::prePsyn($path,0);
    }
    // 同步比较文件
    static function prePsyn($path,$iscmp=1){
        $re = array(); 
        $dcfg = array('code'=>DIR_CODE,'root'=>DIR_ROOT);
        if(empty($iscmp)){// copy
            self::$copys = array(
                'cfgs/sycfg/sy_updvnow.php' => 'root',
            );
            foreach(self::$copys as $k=>$part){
                $pnew = $path."/$part/$k"; 
                $pold = $dcfg[$part]."/$k";
                $re['copy'] = @copy($pnew,$pold);
            }
        }else{// comp
            $re['comp'] = '<br>'.basLang::show('updbase_compare');  
            self::$comps = array(
                'cfgs/boot/cfg_load.php' => 'root',
                'cfgs/boot/const.cfg.php' => 'root',
                'cfgs/boot/_paths.php' => 'root',
            );
            foreach(self::$comps as $k=>$part){
                $re['comp'] .= " : <a href='?act=cmpfile&file=$k&part=$part' target=x>".basename($k)."</a>";
            }
        }
        return $re;
    }
    
    static function fileAdd($new,$old,$pnew,$pold){
        $re = array();
        foreach($new as $k=>$v){
            if(!isset($old[$k])){
                if($dpos=strpos($k,'/')){ 
                    $a = explode('/',$k); 
                    $path = $pold; $tmp = '';
                    foreach($a as $i=>$d){ 
                        if(!is_dir($path."$tmp/$d")){ 
                            mkdir($path."$tmp/$d", 0777, true);    
                            foreach(array('htm','html') as $var) @touch($path."$tmp/$d".'/index.'.$var);    
                        }
                        $tmp .= "/$d"; 
                        if($i>=count($a)-2) break;
                    }
                }
                $f = @copy("$pnew/$k","$pold/$k");
                $re[$k] = $f;
            }
        }
        return $re;
    }
    static function fileEdit($new,$old,$pnew,$pold){
        $re = array();
        $upcfgs = glbConfig::read('updvnow','sy');
        $compcfgs = $upcfgs['compcfgs'] + $upcfgs['dellist']; 
        foreach($new as $k=>$v){
            if(in_array($k,$compcfgs)) continue;
            if(!empty($old[$k]) && $old[$k]!=$new[$k]){
                $f = @copy("$pnew/$k","$pold/$k");
                $re[$k] = $f;
            }
        }
        return $re;
    }
    static function fileComp($new,$old,$pnew,$pold){
        $re = array();
        $upcfgs = glbConfig::read('updvnow','sy');
        $compcfgs = $upcfgs['compcfgs'];
        foreach($compcfgs as $k){
            if(!empty($old[$k]) && $old[$k]!=$new[$k]){
                similar_text(comFiles::get("$pnew/$k"),comFiles::get("$pold/$k"),$pre);
                $re[$k] = $pre;
            }
        }
        return $re;
    }
    //comFiles::copyDir($src,$dst)
    static function dirFix($pnew,$pold){
        $re = array();    
        $fl = comFiles::listDir($pnew);
        foreach($fl['dir'] as $dir=>$time){
            if(!is_dir("$pold/$dir")){
                $re[$dir] = mkdir("$pold/$dir");    
            }
        }
        return $re;
    }
    
    static function dbFields($row,$rem=1){
        $arr = basElm::line2arr($row);
        $re = array();
        foreach($arr as $r){
            if(empty($r) || strpos($r,'EXISTS `') || strpos($r,'ENGINE=MyISAM') || strpos($r,'TABLE `')) continue;
            $r = trim(str_replace(array("\r","\n"),'',$r));
            if(substr($r,0,3)=='-- ') continue; 
            if(substr($r,-1,1)==','){
                $r = substr($r,0,strlen($r)-1);    
            }
            if(empty($rem) && strpos($r,"COMMENT '")){
                $r = substr($r,    0, strpos($r," COMMENT '"))." COMMENT ''";
            } 
            $p = strpos($r,'` ');
            if(substr($r,0,1)=='`' && $p){
                $k1 = substr($r,1,$p-1);
                $t1 = substr($r,$p+2);
                $re[$k1] = $t1;
            }else{
                $re[] = $r;    
            }
        }
        return $re;
    }
    
    static function dbCreate($tab,$v){
        $db = glbDBObj::dbObj();
        $i = 0;
        $sql = "CREATE TABLE `{$db->pre}$tab{$db->ext}` (\n"; 
        foreach($v as $k2=>$v2){
            $i++;
            $sql .= (is_numeric($k2) ? ' ' : " `$k2`")." $v2".($i==count($v) ? '' : ',')."\n";
        }
        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;\n"; 
        return $sql;
    }
    static function dbTable($new,$old,$cfg){
        $db = glbDBObj::dbObj();
        $add = array();
        foreach($new as $tab=>$v){
            if(!isset($old[$tab])){
                $db->query("DROP TABLE IF EXISTS `{$db->pre}$tab{$db->ext}`;");
                $sql = self::dbCreate($tab,$v);
                $f1 = $db->query($sql); 
                $f2 = devData::dataImpInsert("/dborg/data~",$tab,$cfg['path'].'/vary/dtmp');
                $add[$tab] = array('f1'=>!$f1,'f2'=>$f2,'sql'=>$sql);
            } 
        }
        self::cacSave($add,"dbadd_tab");
        $re = array();
        foreach($new as $tab=>$v){
            if(isset($add[$tab])) continue;
            $sqlhead = "ALTER TABLE `{$db->pre}$tab{$db->ext}` \n";
            $acols = array();
            foreach($v as $k2=>$v2){
                if(!is_numeric($k2) && empty($old[$tab][$k2])){
                    $acols[$k2] = "ADD `$k2` $v2";    
                }
            }
            if(!empty($acols)){
                $i = 0;
                $sqla = $sqld = $sqlhead; 
                foreach($acols as $k2=>$v2){
                    $i++;
                    $sqla .= " $v2".($i==count($acols) ? ';' : ',')."\n";
                    //$sqld .= " DROP COLUMN `$k2`".($i==count($acols) ? ';' : ',')."\n"; 
                } 
                $re[$tab]['add'] = $sqla;
                //$re[$tab]['del'] = $sqld;
            } 
            foreach($v as $k2=>$v2){
                if(isset($acols[$k2])) continue;
                if($v2!=@$old[$tab][$k2]){ 
                    $oldval = empty($old[$tab][$k2]) ? '[null]' : $old[$tab][$k2];
                    $re[$tab]['edit'] = (empty($re[$tab]['edit']) ? '' : $re[$tab]['edit'])."\n$oldval -=> $v2";
                    if(!is_numeric($k2)){
                        $re[$tab]['sql'] = (empty($re[$tab]['sql']) ? '' : $re[$tab]['sql'])."\n CHANGE `$k2` `$k2` $v2;";
                    }else{
                        $re[$tab]['idx'] = (empty($re[$tab]['idx']) ? '' : $re[$tab]['idx'])."\n$v2;";
                    }
                }
            }
        }
        self::cacSave($re,"dbedit_cols");
        return $add;    
    }
    
    static function cacSave($arr,$file,$path=''){
        $arr = is_array($arr) ? $arr : self::listDir($arr); 
        $data = var_export($arr,1);
        $data = "<?php\nreturn $data; \n"; 
        $path = empty($path) ? DIR_DTMP."/update" : $path; 
        $file = strpos($file,'.') ? $file : "$file.php";
        comFiles::put("$path/$file",$data);
    }
    static function cacGet($file,$path=''){ 
        $path = empty($path) ? DIR_DTMP."/update" : $path;
        $file = strpos($file,'.') ? $file : "$file.php";
        return file_exists("$path/$file") ? include "$path/$file" : array();
    }
    
    static function listDir($dir,$sub=''){
        $re = array();
        $handle = opendir($dir);
        while ($file = readdir($handle)) {
            if($file=='.'||$file=='..') continue;
            $key = "{$sub}$file";
            $fp = "$dir/$file";
            if(is_dir($fp)){
                if(empty($sub) && in_array($file,self::$skipdirs)) continue;
                $re = array_merge($re,self::listDir($fp,"$sub$file/")); 
            }else{
                $re[$key] = md5_file($fp);
            }
        }
        closedir($handle);
        return $re;
    }
    
    // file:完整路径,或字符串
    static function listTab($file,$cfg=array(),$rem=1){
        $data = file_exists($file) ? comFiles::get($file) : $file;
        $pre = isset($cfg['pre']) ? $cfg['pre'] : '{pre}'; 
        $ext = isset($cfg['ext']) ? $cfg['ext'] : '{ext}'; 
        $a = explode("CREATE TABLE `$pre",$data); 
        $re = array(); 
        foreach($a as $t){ 
            if(!strpos($t,"$ext` (")) continue;
            $b = explode("$ext` (",$t); 
            $re[$b[0]] = self::dbFields($b[1],$rem); 
        }
        return $re;
    }

    static function listData($file,$km=1){
        if(!file_exists($file)) return array();
        $arr = file($file);
        $re = array(); $n = 0;
        foreach($arr as $row){
            if(strlen($row)<10) continue;
            $row = str_replace(array("\r","\n"),'',$row);
            $row = substr($row,0,strlen($row)-1);    
            if(strstr($row,'INSERT INTO `{pre}')){
                 $n++; continue;
            }
            if($n){
                $rb = explode("','",$row);
                $key = ''; $m = 0;
                foreach($rb as $rv){
                    if($m<$km) $key .= (empty($key) ? '' : "','").$rv;
                    $m++;
                    if($m>=$km){ 
                        $len = strlen($key);
                        $key = str_replace(array("('","','"),array("",":"),$key);
                        $re[$key] = substr($row,$len+3);
                        break;
                    }
                }
            }
        }
        return $re;
    }

    // stamp:忽略时间戳, null:忽略01空数据, first:附加第一个(一般为id)
    static function listDataEx1($file,$km=0,$opt=array()){
        if(empty($opt)){
            $opt = array('stamp'=>1, 'null'=>0, 'first'=>1);
        }
        if(!file_exists($file)) return array();
        $data = comFiles::get($file);
        if(!empty($opt['stamp'])) $data = preg_replace("/\,\'\d{8,12}\'/i",",'0'",$data);
        $arr = explode("\n",$data); //file($file);
        $re = array(); $n = 0;
        foreach($arr as $row){
            if(strlen($row)<10) continue;
            $row = str_replace(array("\r","\n"),'',$row);
            $row = substr($row,0,strlen($row)-1);    
            if(strstr($row,'INSERT INTO `{pre}')){
                 $n++; continue;
            }
            if($n){
                $rb = explode("','",$row);
                $len = 0;
                foreach($rb as $m=>$rv){ 
                    $len += strlen($rv)+3; 
                    if($m==$km){ 
                        $val = substr($row,$len-1);
                        if(!empty($opt['null'])) $val = str_replace(array(",''",",'0'",",'1'"),"",$val);
                        if(!empty($opt['first'])) $val = "$rb[0]'):$val";
                        $re["$rv"] = $val; 
                    }
                }
            }
        }
        return $re;
    }

}
