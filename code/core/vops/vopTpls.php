<?php
/*

*/
// 模版相关
class vopTpls{
    
    //获得模版或缓存路径:type=tpl,tpc;
    static function path($type='',$root=1){
        $tpldir = cfg('tpl.tpl_dir');
        return ($root ? ($type=='tpc' ? DIR_CTPL : DIR_SKIN) : '').'/'.$tpldir;  
    }
    
    // include_once:扩展函数：{php vopTpls::pinc('chn:tex_keres');} -=> chn/b_func/tex_keres.php
    // 得到_config路径：      {php include(vopTpls::pinc('_config/va_home')); } -=> _config/va_home.php
    // 得到include需要的路径：{php include(vopTpls::pinc('d_tools/a_cfgs')); } -=> d_tools/a_cfgs.php
    // {imp:"_pub:stpl/_lay_info"} -=> code/cogs/stinc/d_tools/_lay_info.htm
    static function pinc($finc,$ext='',$refull=1){
        $tpl = cfg('tpl.tpl_dir');
        if(strpos($finc,':')){
            $a = explode(':',$finc);
            $tpl = $a[0];
            $finc = $a[1];
        }else{
            $a = array(0,0);
        } 
        $ext = strpos($finc,'.') ? '' : (empty($ext) ? '.php' : $ext);
        if(!strpos($finc,'/')){ // 'chn:tex_keres' -=> {chn}/b_func/tex_keres
            include_once(DIR_SKIN."/$tpl/b_func/$finc$ext");
        }elseif(strpos($tpl,']')){ // [root]:tools/exdiy/rplan
            $tpl = str_replace(array('[',']'),'',$tpl); 
            $tpl = comStore::cfgDirPath($tpl); 
            return "$tpl/$finc$ext";
        }else{ // me(d_tools/a_cfgs) -=> d_tools/a_cfgs(.php)
            return ($refull ? DIR_SKIN : '')."/$tpl/$finc$ext";
        }
    }
    
    //兼容方法
    static function pcfg($mod='',$root=1){ return self::pinc("_config/{$mod}",'',$root); }
    
    //设置当前tpl:set tpl path
    static function set($dir=''){
        global $_cbase; 
        //$dir = $dir ? $dir : req('tpldir');
        if($dir){
            $_cbase['tpl']['tpl_dir'] = $dir;    
        }
        return empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
    }
    
    //获得默认模板
    static function def($type='adm'){
        $tpldir = cfg('tpl.tpl_dir');
        if(!empty($tpldir)){
            return $tpldir;
        }else{
            $vcfg = vopTpls::etr1('show'); 
            return $vcfg['_deadmin_'];    
        }
    }
    
    //type=res,show,tpl;title;0,1,
    static function etr1($type=0,$dir=''){
        $vcfg = read('vopcfg','sy'); 
        if(strlen($type)<3){ // 0,1,''
            $etr = PATH_PROJ.$vcfg['tpl'][$dir][1];
            if($type){ //$full
                $etr = cfg('run.rsite').$etr;
            }
            return $etr;
        }elseif(in_array($type,array('show','tpl'))){
            return $vcfg[$type];
        }elseif($type=='title'){
            return $vcfg['tpl'][$dir][0];
        }else{ //all
            return $vcfg;
        }
    }
    
    // entry 
    // $cb=emumem/ehlist
    static function entry($dir='',$cb='emumem',$mode=''){
        $dir = $dir ? $dir : cfg('tpl.tpl_dir');    
        $dir = DIR_SKIN."/$dir/_config";
        $list = comFiles::listDir($dir);
        $re = array();
        foreach($list['file'] as $file=>$v){ 
            if(strpos($file,'.maobak')) continue;
            $key = str_replace('.php','',$file);
            $kc = "_$key"; $km = substr($key,3);
            include("$dir/$file"); $cfg[$km] = $$kc;
            if(!in_array($key,array('va_docs'))){ //,'va_home'
                $re = $re + self::$cb($cfg[$km],$km,$mode); 
        }    } 
        if(!empty($cfg['home']['close'])){
            foreach($cfg['home']['close'] as $km){
                unset($re[$km]);
            }
        }
        if(!empty($cfg['home']['imcfg'])){
            foreach($cfg['home']['imcfg'] as $km=>$from){
                $re = $re + self::$cb($cfg[$from],$km,$mode); 
            }
        }
        return $re;
    }
    // emumem // 针对会员中心-菜单权限
    static function emumem($cfg,$km,$mode){
        $re = array();
        foreach(array('c','v') as $k) unset($cfg[$k]); //'d','m','t','first'
        foreach($cfg as $ki=>$kv){ 
            if(empty($kv) || $km=='home') continue;
            $kv = (is_array($kv) && isset($kv[0])) ? $kv[0] : $kv;
            $re["$km-$ki"] = $kv;
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
            $mcfg = read($km); 
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
            $vopcfg = read('vopcfg','sy'); 
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

}