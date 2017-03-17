<?php

// ...类
class updInfo{    
    
    static $server_file = '/dset/_upd_server.htm';
    static $client_file = '/dset/_upd_client.htm';
    static $modstat_file = '/dset/_upd_modstat.php'; 
    static $space_file = '/dset/_upd_spaceinfo.php';
    
    static $updtcfg = array(
        'sync'  => '10d', //10d
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
        $nf = self::getLangFile(self::$server_file);
        $data = self::getCacheData($nf,'sync');
        if(empty($data)){
            // ● [资讯]2015-0501：微信接口基本完成 [2015-05-05] 
            $list = db()->table('docs_news')->where("catid='nsystem'")->limit(3)->order('did DESC')->select();
            if($list){foreach($list as $r){
                $url = cfg('run.rsite').surl("chn:news.$r[did]");
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
            $sver = comHttp::doGet("$surl?act=version",8); 
            $sdata = comHttp::doGet("$surl?act=server",8);
            $linkb = "● ".lang('updinfo_nowver')."V{$nver}"; 
            $slang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2)=='zh' ? 'dev' : 'doc';
            $linkr = "<a href='".$_cbase['server']['txmao']."/$slang.php?start' target='_blank' title='".lang('updinfo_viewdown')."'>V$sver</a>";
            if(strstr($sdata,'<br>● <a') && strlen($sver)>=3 && strlen($sver)<=18){ 
                $data = "{$linkb}".lang('updinfo_remver',$linkr)."\n$sdata";
            }else{
                $data = "{$linkb}".lang('updinfo_remerr')."\n";
            }
            comFiles::put(DIR_DTMP.$nf,$data);
        }
        return $data;
    }    

    // SysInfo sys
    static function getModStat(){
        $nf = self::getLangFile(self::$modstat_file);
        $data = self::getCacheData($nf,'stat','array');
        if(empty($data)){
            $mcfgs = self::getModConfigs(); // ● [订单] 当天:11，3天:44，总计:99
            $tcfgs = self::getTimeConfigs();
            $data = array();
            foreach($mcfgs as $pmod=>$mods){foreach($mods as $mod){foreach($tcfgs as $tk=>$tv){
                $key = "{$mod}_$tk";
                $whr = "atime>='$tv'";
                $data[$key] = db()->table("{$pmod}_$mod")->where($whr)->count();
            }}}
            $dstr = var_export($data,1);
            $dstr = "<?php\n\$data = $dstr\n?>";
            comFiles::put(DIR_DTMP.$nf,$dstr);
        }
        return $data;
    }

    // SpaceInfo
    static function getSpaceInfo(){
        $nf = self::getLangFile(self::$space_file);
        $data = self::getCacheData($nf,'space','array');
        if(empty($data)){
            $db = db();
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
            $data['total'] = cfg('ucfg.space');
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
        $str = "\n<tr><td>".lang('updinfo_allspace')."</td><td colspan=8 class='tc'>{$data['total']}M ".lang('updinfo_uesspace',$data['sum'])."</td>";
        $str .= "<td colspan=2><a href='?mkv=uhome&act=uspace'>".lang('updinfo_upd')."</a></td></tr>\n";
        foreach($data['dir'] as $key=>$val){
            $s1 .= "<td width='15%' colspan=2>$key</td>";
            $s2 .= "<td colspan=2>$val</td>";
        }
        $str .= "<tr><td rowspan=2>".lang('updinfo_dir')."</td>$s1</tr>\n<tr>$s2</tr>\n";
        $str .= "<tr><td>".lang('updinfo_dbinfo')."</td>";
        foreach($data['db'] as $key=>$val){
            $str .= "<td colspan=3>$key=$val</td>";
        }
        $str .= "</tr>"; // <td></td>
        echo $str;
    }

    // showSysInfo
    static function showModStat($key){
        $_groups = read('groups');
        $data = self::getModStat(); 
        $mcfgs = self::getModConfigs();
        $tcfgs = self::getTimeConfigs();
        foreach($mcfgs[$key] as $mod){ 
            $link = "<a href='?file=dops/a&mod=$mod'>{$_groups[$mod]['title']}</a>";
            $v = array();
            foreach($tcfgs as $tk=>$tv){
                $v[$tk] = empty($data[$mod."_$tk"]) ? 0 : $data[$mod."_$tk"];
            }
            echo "● [$link] ".lang('updinfo_st1day').":$v[d1], ".lang('updinfo_st3day').":$v[d3], ".lang('updinfo_st7day').":$v[d7], ".lang('updinfo_stall').":$v[all]\n<br>"; 
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
        $mcfgs = read('modstat','sy');
        return $mcfgs;
    }
    // getCacheData
    static function getCacheData($file,$updkey,$type='data'){
        $file = DIR_DTMP.$file;
        $updtime = self::$updtcfg[$updkey];
        $upath = tagCache::chkUpd($file,$updtime,0);
        if($upath && $type=='data'){
            $data = comFiles::get($file);    
        }elseif($upath){
            include($file);
        }else{
            $data = array();    
        }
        $res = empty($data) ? ($type=='data' ? '' : array()) : $data;
        return $res;
    }
    // getLangFile
    static function getLangFile($file){
        $lang = cfg('sys.lang');
        $file = str_replace(array(".htm",".php"),array("-$lang.htm","-$lang.php"),$file);
        return $file;
    }
    
    // -
    static function minsTable(){
        $list = db()->table('bext_mins')->where("1=1")->order('top')->select();
        $res = array();
        foreach ($list as $row) {
            $kid = $row['kid'];
            unset($row['kid']);
            $res[$kid] = $row;
        }
        return $res;
    }

    // -
    static function minsFatch(){
        global $_cbase;
        $api = req('api',$_cbase['server']['txmao']."/root/plus/api/update.php"); 
        //$api = $_cbase['run']['roots'].'/plus/api/update.php';
        $dtmp = comHttp::doGet("$api?act=table"); 
        $data = comParse::jsonDecode($dtmp);
        //return empty($data) ? $dtmp : $data;
        $kid = req('kid');
        if($kid){
            return self::minsFile($api,$kid);
        }
        if(!is_array($data)) return $data;
        $res = self::minsUpdate($api,$data);
        return $res;
    }

    // -
    static function minsUpdate($api,$data){
        $db = db(); $res = array();
        foreach ($data as $key => $row) {
            $row = str_replace("'",'',$row);
            $frow = $db->table('bext_mins')->where("kid='$key'")->find();
            $imsg = "[$key]$row[title]";
            if(empty($frow)){
                $flag = 'update'; 
                $row['kid'] = $key;
                $db->table('bext_mins')->data($row)->insert();
                $files = self::minsFile($api,$key);
            }else{
                $flag = 'skip';
                $files = $frow['files'];
            }
            $res[] = "$flag : $imsg : ($files)";
        }
        return $res;
    }

    static function minsFile($api,$kid){
        $arr = array('dbsql','php','rar','html','htm');
        $rea = array();
        foreach ($arr as $ext) {
            $data = comHttp::doGet("$api?act=down&kid=$kid.$ext&aud=1");
            if(strlen($data)>24){
                $data = comFiles::put(DIR_DTMP."/update/ins~$kid.$ext",$data); 
                $rea[] = $ext;
            }
        }
        return empty($rea) ? '' : implode(',',$rea);
    }

    static function minsDUrls(&$api,$kid,$files){
        global $_cbase;
        if($api=='(system)'){
            $url = $_cbase['server']['txmao']."/root/plus/api/update.php";
        }else{
            $url = $api;
            $api = basEnv::topDomain($url);
        }
        if(!empty($files)){
            $links = "";
            $arr = explode(',',$files);
            foreach ($arr as $ext) {
                $ilnk = "<a href='$url?act=down&kid=$kid.$ext' target=_blank>$ext</a>";
                $links .= (empty($links)?'':' , ').$ilnk;
            }
        }else{
            $links = "---";
        }
        return $links;
    }

    static function minsSMods($cfgs,$re=1){
        $cfgs = str_replace(array(',',':',),array('+','=',),$cfgs);
        $arr = basElm::text2arr($cfgs);
        $arr = str_replace(array('+',),array(',',),$arr);
        $res = array();
        foreach ($arr as $grp => $vals) {
            $tab = $grp=='mods' ? 'model' : 'menu';
            $itmes = explode(',',$vals);
            foreach ($itmes as $itme) {
                $fx = self::minsMFlag($itme,$tab);
                $fmsg = $fx['flag']=='insok' ? $fx['title'] : "({$fx['fmsg']})";
                $res[$grp][$itme] = "$itme:$fmsg ";
            }
        }
        return $res;
    }

    // insok-已安装,close-已关闭,noins-未安装
    static function minsMFlag($mod,$tab,$tlink=''){
        $row = db()->table("base_$tab")->where("kid='$mod'")->find();
        if(empty($row)){
            $title = '`Unknow`'; 
            $flag = 'noins';
        }else{
            $title = $row['title']; 
            $flag = empty($row['enable']) ? 'close' : 'insok';
        }
        $flnk = '';
        if($tlink){
            $acs = "&acm=$mod";
            if($flag=='noins'){
                $flnk = "<a class='c00F' href='{$tlink}=Install$acs' ".vopCell::vOpen(0,0,"Install [$mod]").">Install</a>";
            }elseif($flag=='close'){
                $flnk = "<a class='c0F0' href='{$tlink}=Open$acs' ".vopCell::vOpen(0,0,"set:Open [$mod]").">set:Open</a>";
            }elseif($flag=='insok'){
                $flnk = "<a class='cF00' href='{$tlink}=Close$acs' ".vopCell::vOpen(0,0,"set:Close [$mod]").">set:Close</a>";
            }            
        }
        $lcb = basLang::ucfg('cfgbase.uinfstate'); 
        return array(
            'title' => $title,
            'flag' => $flag,
            'fmsg' => $lcb[$flag],
            'link' => $flnk,
        );
    }
    
    static function minsList($kid=''){
        $fnow = "ins~$kid.php";
        $_groups = read('groups'); 
        $_muadm = read('muadm.i');
        $re = array('abtn'=>array(),'slist'=>'','ins'=>0,);
        $icfg = include(DIR_DTMP.'/update/'.$fnow); 
        $burl = "?file=admin/upgrade&mod=install&kid=$kid";
        if(!empty($icfg['mods'])){
        foreach ($icfg['mods'] as $mod => $mcfg) {
            $pid = $mcfg['pid'];
            $fx = updInfo::minsMFlag($mod,'model',"$burl&acg=mods&pid=$pid&act");
            $re['abtn'][$mod] = $fx['link'];
            $re['slist'] .= "<br>\n --- Model: [$mod]{$mcfg['title']}, Parent: [$pid]{$_groups[$pid]['title']}";
        }}
        if(!empty($icfg['menus'])){
        $arm = comTypes::getSubs($_muadm,'0','1');
        foreach ($icfg['menus'] as $menu => $mcfg) {
            $pid = $mcfg['pid'];
            $opbar = "<select name='$menu'>".basElm::setOption($arm,$pid)."</select>";
            $fx = updInfo::minsMFlag($menu,'menu',"$burl&acg=menus&pid=$pid&act");
            $re['abtn'][$menu] = $fx['link']; //{$_muadm[$pid]['title']}
            @$re['slist'] .= "<br>\n --- Menu: [$menu]{$mcfg['title']}, Parent: [$pid] @$opbar ";
        }} 
        $re['notes'] = empty($icfg['notes']) ? '---' : $icfg['notes'];
        return $re;
    }

}

/*
if(!empty($frow)){, 
$row['kid'] = basKeyid::kidTemp();
*/

