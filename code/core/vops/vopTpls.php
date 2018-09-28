<?php
namespace imcat;

/*

*/
// 模版相关
class vopTpls{

    // 显示解析后的模板内容
    static function show($file,$ext='',$data=array()){
        global $_cbase; 
        $fpath = self::cinc($file,$ext); 
        extract($data, EXTR_OVERWRITE); 
        ob_start(); 
        include $fpath;
        $res = ob_get_contents();
        ob_end_clean(); 
        return $res;
    }
    // 包含html区块（通过模板解析）
    // vopTpls::cinc('_pub:home/home',0,1);
    // include vopTpls::cinc('_pub:home/home');
    static function cinc($file,$ext='',$inc=0){
        global $_cbase; 
        $ext || $ext = $_cbase['tpl']['tpl_ext']; 
        $cac = '/_vinc/'.substr($file,strpos($file,':')+1);
        $tplfull = DIR_CTPL.$cac.$_cbase['tpl']['tpc_ext'];
        if(!file_exists($tplfull) || !$_cbase['tpl']['tpc_on']){
            $template = vopTpls::pinc($file,$ext); 
            $template = comFiles::get($template); 
            $btpl = new vopComp();
            $template = $btpl->bcore($template);
            comFiles::chkDirs($cac,'ctpl',1); 
            comFiles::put($tplfull, $template); //写入缓存
        }
        $_cbase['run']['tplname'] = $file;
        if($inc){
            include $tplfull;
        }else{
            return $tplfull;
        }
    }

    //获得模版或缓存路径:type=tpl,tpc;
    static function path($type='',$root=1){
        global $_cbase;
        $tpl = empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
        return ($root ? ($type=='tpc' ? DIR_CTPL : DIR_SKIN) : '').'/'.$tpl;  
    }
    
    // include_once:扩展函数：{php vopTpls::pinc('chn:tex_keres');} -=> chn/_config/tex_keres.php
    // 得到_config路径：      {php include vopTpls::pinc('_config/va_home'); } -=> _config/va_home.php
    // 得到include需要的路径：{php include vopTpls::pinc('tools/a_cfgs'); } -=> tools/a_cfgs.php
    // {imp:"_pub:stpl/_lay_info"} -=> code/cogs/stinc/tools/_lay_info.htm
    static function pinc($finc,$ext='',$refull=1){
        global $_cbase;
        $tpl = empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
        if(strpos($finc,':')){
            $a = explode(':',$finc);
            $tpl = $a[0];
            $finc = $a[1];
        }else{
            $a = array(0,0);
        } 
        $ext = strpos($finc,'.') ? '' : (empty($ext) ? '.php' : $ext);
        if(!strpos($finc,'/')){ // 'chn:tex_keres' -=> {chn}/_config/tex_keres
            include_once DIR_SKIN."/$tpl/_config/$finc$ext";
        }elseif(strpos($tpl,']')){ // [root]:tools/exdiy/rplan
            $tpl = str_replace(array('[',']'),'',$tpl); 
            $tpl = comStore::cfgDirPath($tpl); 
            return "$tpl/$finc$ext";
        }else{ // me(tools/a_cfgs) -=> tools/a_cfgs(.php)
            return ($refull ? DIR_SKIN : '')."/$tpl/$finc$ext";
        }
    }
    
    //兼容方法
    static function pcfg($mod='',$root=1){ return self::pinc("_config/{$mod}",'',$root); }
    
    //设置当前tpl:set tpl path
    static function set($dir=''){
        global $_cbase; 
        //$dir = $dir ? $dir : basReq::val('tpldir');
        if($dir){
            $_cbase['tpl']['tpl_dir'] = $dir;    
        }
        return empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
    }
    
    //获得默认模板
    static function def($type='adm'){
        global $_cbase;
        $tpldir = empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
        if(!empty($tpldir)){
            return $tpldir;
        }else{
            $vcfg = vopTpls::etr1('show'); 
            return $vcfg['_deadmin_'];    
        }
    }
    
    //type=all,show,tpl;title;0,1,
    static function etr1($type=0,$dir=''){
        global $_cbase; 
        $vcfg = glbConfig::read('vopcfg','sy'); 
        if(strlen($type)<3){ // 0,1,''
            $_cbase['run']['tplcfg'] = $vcfg['tpl'][$dir]; // 处理伪静态使用
            $etr = PATH_PROJ.$vcfg['tpl'][$dir][1];
            return $type ? $_cbase['run']['rsite'].$etr : $etr; //type=1 > full
        }elseif(in_array($type,array('show','tpl'))){
            return $vcfg[$type];
        }elseif($type=='title'){
            return $vcfg['tpl'][$dir][0];
        }else{ //all
            return $vcfg;
        }
    }
    
    // entry 
    // $cb=enmkv/ehlist
    static function entry($dir='',$cb='enmkv',$mode=''){
        global $_cbase;
        $dir = $dir ? $dir : $_cbase['tpl']['tpl_dir'];    
        $dir = DIR_SKIN."/$dir/_config";
        $list = comFiles::listDir($dir);
        $re = array();
        foreach($list['file'] as $file=>$v){ 
            if(strpos($file,'.maobak')) continue;
            $key = str_replace('.php','',$file);
            $kc = "_$key"; $km = substr($key,3);
            include "$dir/$file"; $cfg[$km] = $$kc;
            if(!in_array($key,array('va_docs'))){ //,'va_home'
                $re = $re + self::$cb($cfg[$km],$km,$mode); 
        }    } 
        if(!empty($cfg['c']['close'])){
            foreach($cfg['c']['close'] as $km){
                unset($re[$km]);
            }
        }
        if(!empty($cfg['c']['imcfg'])){
            foreach($cfg['c']['imcfg'] as $km=>$from){
                $re = $re + self::$cb($cfg[$from],$km,$mode); 
            }
        }
        return $re;
    }
    // enmkv // 针对:会员中心/管理后台-菜单权限
    static function enmkv($cfg,$km,$mode){
        $re = array(); 
        foreach(array('c','v','u') as $k) unset($cfg[$k]); //'d','m','t','first'
        foreach($cfg as $ki=>$kv){ 
            $kv = (is_array($kv) && isset($kv[0])) ? $kv[0] : $kv;
            $re["$km-$ki"] = $kv;
        } 
        return $re;
    }
    // enflow // 针对:管理后台-菜单权限
    static function enflow(){
        global $_cbase; 
        $re = array('dops-m'=>'','dops-a'=>'');
        if(!empty($_cbase['mkv']['hcfg']['pmods'])){
            foreach($_cbase['mkv']['hcfg']['pmods'] as $mod){
                $re["$mod-m"] = '';
                $fps = comFiles::listDir(DIR_CODE.'/flow/'.$mod); 
                foreach($fps['file'] as $fp=>$itm){
                    if($fp=='index.php') continue;
                    $key = "$mod-".str_replace('.php','',$fp);
                    $re[$key] = '';
                } 
            } 
        }
        return $re;
    }
    // $mode=dynamic/static/both/all/
    static function ehlist($cfg,$km,$mode){
        if(in_array($mode,array('static','dynamic')) && $cfg['c']['vmode']!=$mode) return array();
        if($mode=='both' && !in_array($cfg['c']['vmode'],array('static','dynamic'))) return array();
        $re[$km] = array();
        // 展开:types
        if(!empty($cfg['t'])){
            $mcfg = glbConfig::read($km); 
            foreach($mcfg['i'] as $ki=>$kv){ 
                if(!isset($cfg[$ki])) $cfg[$ki] = $cfg['t']; 
            }
        }
        foreach($cfg as $ki=>$kv){
            if(empty($kv) || $km=='home' && $ki!='m') continue;
            if($ki=='m'){ 
                $re[$km]['m'] = (is_array($kv) && isset($kv[0])) ? $kv[0] : $kv;
            }elseif(strlen($ki)==1){
                continue;
            }else{ 
                if(is_array($kv)){
                    foreach($kv as $i=>$v){ 
                        $kn = empty($i) ? $ki : "$ki-$i";
                        $re[$km][$kn] = $v; 
                    }    
                }else{
                    $re[$km][$ki] = $kv; 
                }
            }
        }
        return $re;
    }
    
    static function check($tpl,$die=1){
        static $tplchks;
        if(empty($tplchks[$tpl])){
            $vopcfg = glbConfig::read('vopcfg','sy'); 
            if(empty($vopcfg['tpl'][$tpl])){ //无tpl配置
                $tplchks[$tpl]['cfg'] = 1;
            }
            $fp = DIR_SKIN."/$tpl/_config/va_home.php";
            if(!file_exists($fp)){
                $tplchks[$tpl]['dir'] = 1;
            }
            if(empty($tplchks[$tpl])){
                $tplchks[$tpl]['ok'] = 1;    
            }
        }
        if($die && empty($tplchks[$tpl]['ok'])){
            vopShow::msg("Config Error! <br>[cfgs/sycfg/sy_vopcfg.php] : _sy_vopcfg['tpl'][$tpl]");
        } 
        return $tplchks[$tpl];
    }

    static function impCtrl($mod){
        global $_cbase; 
        $hcfg = $_cbase['mkv']['hcfg'];
        $tpdir = DIR_SKIN.'/'.$_cbase['tpl']['tpl_dir'];
        $arr = array(); 
        $arr[] = $mod.'Ctrl';
        if(!empty($hcfg['_defCtrl'])) $arr[] = $hcfg['_defCtrl'];
        foreach ($arr as $class) {
            $fp = $tpdir."/_ctrls/$class.php";
            if(file_exists($fp)){
                include_once $fp;
                return "\\imcat\\{$_cbase['tpl']['tpl_dir']}\\$class";
            }
        }
        return 0;
    }

}