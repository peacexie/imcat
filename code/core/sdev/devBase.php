<?php
namespace imcat;

// ...类
class devBase{    

    // 显示一个文件里的一个func
    static function docFunc1($file,$fmeth){ 
        if(empty($file) || empty($fmeth)) return '';
        $res = self::docFuncs($file,1); 
        $arr = $res[0]; $str = $res[1];
        $f1 = $f2 = '';
        if($arr){
        foreach($arr as $no=>$func){ 
            if(strpos($func," $fmeth(")){
                $f1 = $func;
                $f2 = isset($arr[$no+1]) ? $arr[$no+1] : '';
                break;
            }
        }}
        if(empty($f1) || empty($str)) return '';
        $p1 = strpos($str,$f1); 
        $re = $f2 ? substr($str,$p1,strpos($str,$f2)-$p1) : substr($str,$p1);
        return $re;    
    }

    // 显示一个文件里的function
    static function docFuncs($file,$rea=0){ 
        $str = comFiles::get($file); $org = $str; 
        $str = preg_replace('/\/\*(.*?)\*\//is','',$str);
        preg_match_all("/((public|protected|private|static)\s+)*function\s+[0-9a-zA-Z_\$]{1,32}[^{]{0,96}/",$str,$m);
        if($rea) return array($m[0],$str);
        $re = ''; 
        if($m[0]){
        foreach($m[0] as $f){ 
            //$f = str_replace(array("\t"),array(" "),$f);
            $re .= "\n<li>$f</li>";
        }}
        return $re;    
    }
    
    //一个根目录下，所有子目录$subs里的类库文件
    static function docClass($root,$subs){
        $da = $subs; $nav = ""; $class = ',';
        $farr = array('.php','.js','.fun','.class'); 
        foreach($da as $dir){ 
            $nav .= "\n<ul class='ul'><b class='li'>$dir</b>";
            $handle = opendir("$root/$dir/");
            while($file = readdir($handle)) {
                if($file=='.'||$file=='..') continue;
                $fp = "$root/$dir/$file"; $forg = $file;
                $fext = strtolower(strrchr($file,'.'));
                $file = str_replace($farr,'',$file);
                if(in_array($file,array('index'))) continue;
                if(file_exists($fp) && in_array($fext,$farr)){
                    echo "\n<a name='$file'></a>";
                    echo "\n<ul><h3>$dir/$forg</h3>";
                    echo self::docFuncs($fp);
                    echo "</ul>";
                    if(strstr($class,",$file,")){ 
                        $sfile = "<span class='red' title='".basLang::show('devbase_clsrepeat')."'>$file</span>";
                    }else{
                        $sfile = "$file";
                        $class .= "$file,";
                    }
                    $nav .= "\n<li class='li'><a href='#$file'>$sfile</a></li>";
                }
            }
            closedir($handle);
            $nav .= "</ul>";
        }
        return $nav;
    }
    
    // 自动加命名空间
    static function nspList(){
        $root = DIR_CODE.'/core';
        $dirs = comFiles::listDir($root); 
        $subs = array_keys($dirs['dir']);
        $maps = array();
        foreach($subs as $dir){ 
            $handle = opendir("$root/$dir/");
            while($file = readdir($handle)) {
                if(!strstr($file,'.php')) continue;
                $fp = "$root/$dir/$file"; 
                if(file_exists($fp)){
                    $class = str_replace('.php','',$file);
                    if(in_array($class,array('index'))) continue;
                    $maps[$class] = "core\\$dir"; 
                }
            }
            closedir($handle);
        } 
        $ckeys = array_keys($maps); 
        $re = $tip = '';
        foreach($maps as $class=>$nsp){
            $file = '/'.str_replace("\\","/",$nsp)."/$class.php";
            $str = comFiles::get(DIR_CODE.$file); 
            $str = preg_replace(array("/#.*$/m","/\/\/.*$/m","/\/\*.*\*\//sU"),array('','',''),$str); //删除注释干扰
            $str = str_replace(array("class $class","$class.php","'$class'","\"$class\""),'',$str);
            $re .= "<ul>\n<b>$file : </b>";
            $re .= "\n<li>namespace $nsp;</li>";
            foreach($ckeys as $class2){
                if(strstr($str,$class2)){
                    if($nsp!=$maps[$class2]){
                        $ire = "\n<li>use $maps[$class2]\\$class2;</li>"; 
                        if($class==$class2){ 
                            $tip .= "$ire --- ??? ";
                        }else{
                            $re .= "$ire";     
                        }
                    }
                }
            }
            $re .= "\n</ul>";
        }
        $tip && $re = "$tip<hr>$re";
        return $re;
    }

    static function scanDblang(){
        $re = array();
        $path = DIR_DTMP."/dbexp";
        $lists = comFiles::listScan($path,'');
        foreach($lists as $file=>$fv){
            $tbfix = substr($file,5,4);
            $tbname = str_replace('.dbsql','',substr($file,5)); 
            if(!in_array(substr($file,5,4),array('base','bext'))) continue;
            $old = include DIR_CODE."/lang/dbins/$tbname-cn.php";
            $data = comFiles::get("$path/$file");
            $arr = basStr::getMatch($data,'dbstr','c196');
            $arb = basArray::lenOrder($arr);
            echo "<br>\n<b>$file:</b><br>\n";
            for($i=15; $i>=1; $i--) { 
                if(empty($arb[$i])) continue;
                echo "&nbsp; &nbsp; // $i: <br>\n";
                foreach($arb[$i] as $val){
                    if(in_array($val,$old)) continue;
                    echo "&nbsp; &nbsp; '$val',<br>\n";
                }
            }
        }
    }
    static function scanCnchr($dir,$sdir=array(),$skip=array()){
        $re = array();
        $lists = comFiles::listScan($dir,'',$sdir);
        foreach($lists as $file=>$fv){
            if(!( strpos($file,'.php')||strpos($file,'.js')||strpos($file,'.htm') )) continue;
            if(in_array(str_replace('.php','',basename($file)),$skip)) continue;
            if(strpos($file,'-cn.')) continue;
            $data = comFiles::get("$dir/$file");
            $data = preg_replace('/\/\*(.*?)\*\//is','',$data);
            $data = preg_replace('/\/\/[^\r\n]+/is','',$data);
            $data = preg_replace("/<\!--.*?-->/is","",$data);
            $res = basStr::getMatch($data,'dbstr','c196');
            if(!empty($res)){
                echo "<b>$file</b><br>\n";
                dump($res);
            }
        }
    }
    static function scanInit($dir,$burl='',$sub=''){
        $re = array();
        $lists = comFiles::listScan($dir,'',array('tpls','a3rd','skin'));
        foreach($lists as $file=>$fv){
            if(!strpos($file,'.php')) continue;
            $url = "$burl/$file"; 
            self::scanCheck($url,$file,$re);
        }
        return $re;
    }
    static function scanMkvs($dir=''){
        $mkvs = vopTpls::entry($dir,'ehlist','all'); 
        $burl = vopTpls::etr1(1,$dir).'?'; 
        $re = array();
        foreach($mkvs as $mod=>$keys){ foreach($keys as $key=>$val){
            if($key=='m'){
                $mkv = $mod;
            }elseif($key=='first'){
                $mkv = ''; //?
            }else{
                $mkv = "$mod-$key";
            }
            if(empty($mkv)) continue;
            $url = "$burl$mkv"; 
            self::scanCheck($url,$mkv,$re);
        }} //die();
        return $re;
    }
    static function scanCheck($url,$key,&$re){
        $data = comHttp::doGet($url,3); 
        $taga = array('Fatal error','Warning','Notice');
        foreach($taga as $tag){
            $tag = "<b>$tag</b>:";
            if(strstr($data,$tag)){
                $detail = basElm::getPos($data,array($tag,'<br />'));
                $detail = basDebug::hidInfo($detail);
                $re[$key] = "<a href='$url'>$tag</a>$detail";    
            }
        }        
    }

    static function dbDict($tabinfo=array()){
        global $_cbase;
        if(empty($tabinfo)) $tabinfo = glbDBObj::dbObj()->tables(1); 
        $tdoc = comFiles::get(DIR_ROOT.'/cfgs/stinc/is_dbdoc.htm'); 
        $ttab = comFiles::get(DIR_ROOT.'/cfgs/stinc/is_dbtab.htm');
        $slist = $tlist = ''; 
        $clist = '<tr><td>'.basLang::show('core.dbdict_table').'</td><td>'.basLang::show('core.dbdict_field').'</td></tr>'; 
        foreach($tabinfo as $tab=>$r){ 
            $ra=''; $tabid = $r['Name']; $rows = $r['Rows'];
            $tblfields = glbDBExt::dbComment($tabid);
            $tabrem = empty($tblfields[0]['_rem']) ? "<span class='dfCCC'>$tabid</span>" : $tblfields[0]['_rem'];
            unset($tblfields[0]); $i=0;
            foreach($tblfields as $fk=>$fv){
                $frem = empty($fv['_rem']) ? "<span class='dfCCC'>(null)</span>" : $fv['_rem'];
                $ra .= "<tr><td>{$fv['name']}</td><td>$frem</td><td>{$fv['type']}</td><td>{$fv['notnull']}</td><td>{$fv['autoinc']}</td><td>{$fv['default']}</td></tr>\n";
            } 
            $slist .= str_replace(array('{fields}','{tabid}','{tabname}','{rows}'),array($ra,$tabid,$tabrem,$rows),$ttab);
            $tlist .= "<a href='#$tabid'>".($tabrem ? $tabrem : $tabid)."</a>\n"; 
        }
        $org = array('{tablists}','{tabmap}','{tabcnt}','{sysname}','{dict-title}');
        $obj = array($slist,$tlist,count($tabinfo),$_cbase['sys_name'],basLang::show('core.dbdict_title'));
        $data = str_replace($org,$obj,$tdoc); 
        $data = devData::dataImpLang($data,'base_model,base_fields,bext_dbdict');
        return $data;    
    }

    static function _tabHead($tab,$fix=array('{pre}','{ext}'),$type='INSERT'){
        $head = "\n-- {$tab}@".date('Y-m-d H:i:s')." -- \n";
        $head .= "$type INTO `$fix[0]{$tab}$fix[1]` VALUES \n";
        return $head;
    }
    static function _loadOpt($type=1){
        return "FIELDS TERMINATED BY ','OPTIONALLY ENCLOSED BY '''' LINES TERMINATED BY '\n' ";
    }
    static function _dinsRow($row){
        static $alldf;
        empty($alldf) && $alldf = get_defined_constants(0);
        $sql = "(";
        $values = array();
        foreach ($row as $key=>$value) {
            $values[] = basNodef::quoteSql($value);
        }
        $sql .= implode(',',$values).")";
        return $sql;
    }
    static function _drndData($str){
        for($i=0;$i<2;$i++){
            $str = basKeyid::kidRTable('0','rnd',$str);
            $str = basKeyid::kidRTable('A','rnd',$str);
            $str = basKeyid::kidRTable('a','rnd',$str);
        }
        return $str;
    }
    
    // $cfgs : 'userfiles,weituos,aalbums'
    // $cfgs : array('userfiles,weituos,aalbums','archives_','base_')
    static function _tabIncfg($tab,$cfgs=''){ 
        if(empty($cfgs)) return 1; 
        $cfgs = is_string($cfgs) ? array($cfgs) : $cfgs; 
        if(isset($cfg['__ufuncs'])){
            $func = $cfg['__ufuncs'];
            return $func($tab,$cfgs);
        } 
        foreach($cfgs as $val){ 
            if(strstr($val,',')){ 
                if(strpos("(,$val,)",",$tab,"))    return 1;
            }elseif(strpos($val,'_')){
                if(substr($tab,0,strlen($val))==$val) return 1;
            }else{
                if($val==$tab) return 1;
            }
        }
        return 0; 
    }
    
    static function _tabGroup($tabinfo=''){
        if(!$tabinfo){
            $tabinfo = glbDBObj::dbObj()->tables(); 
        }
        $re = array(); $fixa = array('-1'); 
        foreach($tabinfo as $row){
            $itab = empty($row['Name']) ? $row : $row['Name'];
            $fix = substr($itab,0,strpos($itab,'_'));
            if(!in_array($fix,$fixa)){ 
                $re[] = $fixa[] = $fix;
            }
        }
        return $re;
    }

    static function typChkall(){
        $groups = glbConfig::read('groups');
        $res = array();
        foreach($groups as $key=>$val){
            if($val['pid']=='types'){
                $irep = self::typCheck($key);
                $irep && $res[$key] = $irep;
            }
        }
        return $res;
    }
    static function typCheck($key){
        $mcfg = glbConfig::read($key); 
        $done = array('-1'); $re = '';
        foreach($mcfg['i'] as $k1=>$v1){
            $rs = '';
            foreach($mcfg['i'] as $k2=>$v2){
                if($k1==$k2) continue;
                if(strstr($k2,$k1) || strstr($k1,$k2)){ 
                    if(!(in_array($k1,$done) && in_array($k2,$done))){
                        $rs .= ",$k2-$v2[title]";
                        $done[] = $k1;
                        $done[] = $k2;
                    }
                }
            }
            if($rs) $re .= "$k1-$v1[title]$rs ; ";
        } 
        return $re;
    }

}

