<?php
namespace imcat;

// 模版相关
class vopTpls{

    ### 主要方法 ～～～～～～～～～～～

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
    // vopTpls::cinc('base:home/home', 1);
    // include vopTpls::cinc('base:home/home');
    static function cinc($file, $inc=0){
        global $_cbase; 
        $cac = '/'.str_replace(':', '/', $file); 
        $tplfull = DIR_CTPL.$cac.$_cbase['tpl']['tpc_ext'];
        if(!file_exists($tplfull) || !$_cbase['tpl']['tpc_on']){
            $template = vopTpls::tinc("$file.htm",0); 
            $template = comFiles::get($template); 
            $btpl = new vopComp();
            $template = $btpl->bcore($template);
            comFiles::chkDirs($cac,'ctpl',1); 
            comFiles::put($tplfull, "<?php \n".NSP_INIT." \n?>\n".$template); //写入缓存
        }
        $_cbase['run']['tplname'] = $file;
        if($inc){
            include $tplfull;
        }else{
            return $tplfull;
        }
    }
    
    // tinc('comm:_config/top_book'); -=> include-tpl
    // tinc('dir/modtpl.htm', 0); -=> tpl-path 
    static function tinc($fp, $inc=1, $refull=1){
        global $_cbase;
        $ext = strpos($fp,'.') ? '' : '.php';
        if(!strpos($fp,'/')) { $fp = "_config/$fp"; }
        if(!strpos($fp,':')){
            $fp = "/{$_cbase['tpl']['vdir']}/$fp"; 
        }else{ // tpl:dir/file
            $fp = '/'.str_replace(':', '/', $fp);
        }
        $vbase = empty($_cbase['tpl']['vbase']) ? DIR_VIEWS : $_cbase['tpl']['vbase'];
        if($inc){
            include_once $vbase."$fp$ext";
            return;
        }else{
            return ($refull ? $vbase : '')."$fp$ext";
        }
    }

    //获得模版或缓存路径:type=tpl,tpc;
    static function path($type='', $root=1){
        global $_cbase;
        $tpl = empty($_cbase['tpl']['vdir']) ? '/(null-tpl)' : '/'.$_cbase['tpl']['vdir'];
        $vbase = empty($_cbase['tpl']['vbase']) ? DIR_VIEWS : $_cbase['tpl']['vbase'];
        return ($root ? ($type=='tpc' ? DIR_CTPL : $vbase) : '').$tpl;  
    }


    //设置当前tpl:set tpl path
    static function set($dir=''){
        global $_cbase; 
        //$dir = $dir ? $dir : basReq::val('tpldir');
        if($dir){
            $_cbase['tpl']['vdir'] = $dir;    
        }
        return empty($_cbase['tpl']['vdir']) ? '' : $_cbase['tpl']['vdir'];
    }
    
    //获得默认模板
    static function def($type='adm'){
        global $_cbase;
        $tpldir = empty($_cbase['tpl']['vdir']) ? '' : $_cbase['tpl']['vdir'];
        if(!empty($tpldir)){
            return $tpldir;
        }else{
            $vcfg = vopTpls::etr1('show'); 
            return $vcfg['_deadmin_'];    
        }
    }
    
    ### 相关方法 ～～～～～～～～～～～

    static function check($tpl,$die=1){
        static $tplchks;
        if(empty($tplchks[$tpl])){
            $vopcfg = glbConfig::read('vopcfg','sy'); 
            if(empty($vopcfg['tpl'][$tpl])){ //无tpl配置
                $tplchks[$tpl]['cfg'] = 1;
            }
            $vbase = empty($_cbase['tpl']['vbase']) ? DIR_VIEWS : $_cbase['tpl']['vbase'];
            $fp = $vbase."/$tpl/_config/va_home.php";
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
        $tpdir = DIR_VIEWS.'/'.$_cbase['tpl']['vdir'];
        $arr = array(); 
        $arr[] = $mod.'Ctrl';
        if(!empty($hcfg['_defCtrl'])) $arr[] = $hcfg['_defCtrl'];
        foreach ($arr as $class) {
            $fp = $tpdir."/_ctrls/$class.php";
            if(file_exists($fp)){
                include_once $fp;
                return "\\imcat\\{$_cbase['tpl']['vdir']}\\$class";
            }
        }
        return 0;
    }

    ### 入口相关 ～～～～～～～～～～～

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
        $dir = $dir ? $dir : $_cbase['tpl']['vdir'];    
        $dir = DIR_VIEWS."/$dir/_config";
        $list = comFiles::listDir($dir);
        $re = array();
        foreach($list['file'] as $file=>$v){ 
            if(strpos($file,'.maobak')) continue;
            $key = str_replace('.php','',$file);
            $kc = "_$key"; $km = substr($key,3);
            if(!in_array($km,array('va_','ve_','vc_')) || !isset($$kc)) return $re;
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
                $fps = comFiles::listDir(DIR_IMCAT.'/flow/'.$mod); 
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
    
}