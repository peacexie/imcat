<?php
namespace imcat;

// ...类
class updInfo{    
    
    static $server_file = '/dset/_upd_server.htm';
    static $client_file = '/dset/_upd_client.htm';
    static $modstat_file = '/dset/_upd_modstat.php'; 
    static $space_file = '/dset/_upd_spaceinfo.php';
    
    static $updtcfg = array(
        'sync'  => '3d', //3d
        'stat'  => '3h', //3h
        'space' => '24h', //24h
    );
    
    //3.0.2015.1225
    static function verComp($new,$old){
        $f = version_compare($old,$new);
        return $f;
    }
    
    // ServerInfo
    static function getServerInfo(){
        global $_cbase;
        $nf = self::getLangFile(self::$server_file);
        $data = self::getCacheData($nf,'sync');
        if(empty($data)){
            // ● [资讯]2015-0501：微信接口基本完成 [2015-05-05] 
            $list = glbDBObj::dbObj()->table('docs_news')->where("catid='nsys'")->limit(3)->order('did DESC')->select();
            if($list){foreach($list as $r){
                $url = $_cbase['run']['rsite'].vopUrl::fout("chn:news.$r[did]");
                $a = "<a href='$url' target='_blank'>$r[title]</a>";
                $data .= "<br>● $a [".date('Y-m-d',$r['atime'])."]\n";
            }}
            comFiles::put(DIR_DTMP.$nf,$data);
        }
        return $data;
    }    
    
    // ClientInfo
    static function getClientInfo(){
        global $_cbase;
        $nf = self::getLangFile(self::$client_file);
        $data = self::getCacheData($nf,'sync','data');
        if(empty($data)){
            // ● 当前版本：3.0.2015.1225（官方版本：3.0.2015.1225）
            $surl = $_cbase['server']['txmao']."/root/plus/api/update.php";
            $nver = $_cbase['sys']['ver']; 
            $sver = comHttp::doGet("$surl?act=version",2); 
            $sdata = comHttp::doGet("$surl?act=server",2);
            $linkb = "● ".basLang::show('updinfo_nowver')."V{$nver}"; 
            $slang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2)=='zh' ? 'dev' : 'doc';
            $linkr = "<a href='".$_cbase['server']['txmao']."/$slang.php?start' target='_blank' title='".basLang::show('updinfo_viewdown')."'>V$sver</a>";
            if(strstr($sdata,'<br>● <a') && strlen($sver)>=3 && strlen($sver)<=18){ 
                $data = "{$linkb}".basLang::show('updinfo_remver',$linkr)."\n$sdata";
                comFiles::put(DIR_DTMP.$nf,$data);
            }else{
                //$data = "{$linkb}".basLang::show('updinfo_remerr')."\n";
                $data = comFiles::get(DIR_DTMP.$nf);
            }
        }
        return $data;
    }    

    // SysInfo sys
    static function getModStat(){
        $_groups = glbConfig::read('groups');
        $nf = self::getLangFile(self::$modstat_file);
        $data = self::getCacheData($nf,'stat','array');
        if(empty($data)){
            $mcfgs = self::getModConfigs(); // ● [订单] 当天:11，3天:44，总计:99
            $tcfgs = self::getTimeConfigs();
            $data = array();
            //foreach($mcfgs as $pid=>$mods){foreach($mods as $mod){
            foreach($_groups as $mod=>$row){ 
                $pid = $row['pid'];
                if(isset($mcfgs[$pid]) && !in_array($mod,$mcfgs[$pid])){
                    foreach($tcfgs as $tk=>$tv){
                        $key = "{$mod}_$tk";
                        $whr = "atime>='$tv'";
                        $data[$key] = glbDBObj::dbObj()->table("{$pid}_$mod")->where($whr)->count();
                    }
                }
            }
            $dstr = var_export($data,1);
            $dstr = "<?php\n\$data = $dstr\n?>";
            comFiles::put(DIR_DTMP.$nf,$dstr);
        }
        return $data;
    }

    // SpaceInfo
    static function getSpaceInfo(){
        global $_cbase; 
        $nf = self::getLangFile(self::$space_file);
        $data = self::getCacheData($nf,'space','array');
        if(empty($data)){
            $db = glbDBObj::dbObj();
            $sum = 0;
            $data = array('db'=>array('idx'=>0,'free'=>0,'data'=>0));
            $tabinfo = $db->tables(1); 
            foreach($tabinfo as $r){ 
                $data['db']['data'] += $r['Data_length'];
                $data['db']['idx'] += $r['Index_length'];
                $data['db']['free'] += $r['Data_free'];
            }
            $sum = $data['db']['data'];
            $paths = read('pubcfg.parts','sy');
            foreach($paths as $part=>$dirs){
                $data['dir'][$part] = 0;
                foreach($dirs as $dir){
                    $dir2 = comStore::cfgDirPath($dir);
                    $idir = comFiles::statDir($dir2);
                    $data['dir'][$part] += $idir['nsize'];
                    $sum += $idir['nsize'];
                }
            }
            $paths = array('html'=>DIR_HTML,'ures'=>DIR_URES);
            foreach($paths as $dir=>$dir2){
                $idir = comFiles::statDir($dir2);
                $data['dir'][$dir] = $idir['nsize'];
                $sum += $idir['nsize'];
            }
            foreach(array('db','dir') as $key){foreach($data[$key] as $k=>$v){
                $data[$key][$k] = basStr::showNumber($v,'Byte');
            }}
            $data['total'] = $_cbase['ucfg']['space'];
            $data['sum'] = basStr::showNumber($sum,'Byte');
            $dstr = var_export($data,1);
            $dstr = "<?php\n\$data = $dstr\n?>";
            comFiles::put(DIR_DTMP.$nf,$dstr);
        }
        return $data;
    }

    // showSpaceInfo
    static function showSpaceInfo(){
        $data = self::getSpaceInfo();
        $s1 = $s2 = ''; 
        $str = "\n<tr><td>".basLang::show('updinfo_allspace')."</td><td colspan=8 class='tc'>{$data['total']}M ".basLang::show('updinfo_uesspace',$data['sum'])."</td>";
        $str .= "<td colspan=2><a href='?mkv=uhome&act=uspace'>".basLang::show('updinfo_upd')."</a></td></tr>\n";
        foreach($data['dir'] as $key=>$val){
            $s1 .= "<td colspan=2>$key</td>";
            $s2 .= "<td colspan=2>$val</td>";
        }
        $str .= "<tr><td rowspan=2>".basLang::show('updinfo_dir')."</td>$s1</tr>\n<tr>$s2</tr>\n";
        $str .= "<tr><td>".basLang::show('updinfo_dbinfo')."</td>";
        foreach($data['db'] as $key=>$val){
            $str .= "<td colspan=3>$key=$val</td>";
        }
        $str .= "</tr>";
        echo $str;
    }

    // showSysInfo
    static function showModStat($key){
        $_groups = glbConfig::read('groups');
        $mcfgs = self::getModConfigs();
        $data = self::getModStat();
        $tcfgs = self::getTimeConfigs();
        foreach($_groups as $mod=>$row){ 
            if($row['pid']!=$key || in_array($mod,$mcfgs[$key])) continue; //
            $link = "<a href='?mkv=dops-a&mod=$mod'>{$_groups[$mod]['title']}</a>";
            $v = array();
            foreach($tcfgs as $tk=>$tv){
                $v[$tk] = empty($data[$mod."_$tk"]) ? 0 : $data[$mod."_$tk"];
            }
            echo "● [$link] ".basLang::show('updinfo_st1day').":$v[d1], ".basLang::show('updinfo_st3day').":$v[d3], ".basLang::show('updinfo_st7day').":$v[d7], ".basLang::show('updinfo_stall').":$v[all]\n<br>"; 
        }
    }

    // getTimeConfigs
    static function getTimeConfigs(){
        $hour0 = strtotime(date('Y-m-d')); 
        $tcfgs = array('d1'=>$hour0,'d3'=>$hour0-2*86400,'d7'=>$hour0-6*86400,'all'=>0);
        return $tcfgs;
    }

    // getModConfigs
    static function getModConfigs(){
        $mcfgs = glbConfig::read('modstat','sy');
        return $mcfgs;
    }
    // getCacheData
    static function getCacheData($file,$updkey,$type='data'){
        $updtime = self::$updtcfg[$updkey];
        $upath = extCache::cfGet($file,$updtime,'dtmp');
        if($upath && $type=='data'){
            $data = comFiles::get($upath);    
        }elseif($upath){
            include $upath;
        }else{
            $data = array();    
        }
        $res = empty($data) ? ($type=='data' ? '' : array()) : $data;
        return $res;
    }
    // getLangFile
    static function getLangFile($file){
        global $_cbase;
        $lang = $_cbase['sys']['lang'];
        $file = str_replace(array(".htm",".php"),array("-$lang.htm","-$lang.php"),$file);
        return $file;
    }

}

/*
if(!empty($frow)){, 
$row['kid'] = basKeyid::kidTemp();
*/

