<?php
/*
*/
// Media类
class vopMedia{

    // {media:[type=map][val=3.44%2C44.24][w=33][h=44][ext=地点1]/media}
    static function repShow($cstr){ 
        preg_match_all("/\{media\:([^\n]{12,1200}\])\/media\}/i", $cstr, $m);
        $cfgs = self::cfgTypes();
        if(!empty($m[0])){
            foreach($m[0] as $k=>$itm){ 
                $porg = $m[1][$k]; 
                $mtype = self::onePara($porg,'type'); 
                if(!isset($cfgs[$mtype])) continue;
                $sres = self::_repItem($porg,$mtype);
                $cstr = str_replace($itm,$sres,$cstr); 
            }
        }
        $cstr = comStore::revSaveDir($cstr);
        return $cstr;
    }    

    static function _repItem($porg,$mtype){
        $mapi = cfg('sys_map');
        $pw = self::onePara($porg,'w'); $pw = $pw>80 ? $pw : '480'; //100%;
        $ph = self::onePara($porg,'h'); $ph = $ph>60 ? $ph : '360';
        $val = self::_itmUri($porg);
        if(in_array($mtype,array('iframe','map'))){
            if($mtype=='map'){
                $title = self::onePara($porg,'ext'); $point = $val; 
                $val = PATH_ROOT."/plus/map/index.php?api=$mapi&point=$point&title=$title";
            }
            $sres = "<iframe src='$val' width='$pw' height='$ph'></iframe>";
        }else{ //if(in_array($mtype,array('swf','audio','ckvdo'))){ //'flv',
            if($mtype=='audio') $ph = intval(self::onePara($porg,'h')); $ph = $ph>10 ? $ph : '30'; //重新取一次
            $tpl = self::_itmTpl($mtype);
            $sres = self::_itmRep($tpl,$val,$pw,$ph); 
            $exMethed = '_ex'.ucfirst($mtype);
            if(method_exists(__CLASS__,$exMethed)){
                $sres = self::$exMethed($sres,$val,$mtype,$porg);
            }
        }
        return $sres;
    }
    
    static function _exCkvdo($sres,$val,$mtype,$porg){
        global $_cbase;
        $playid = $mtype.'_'.substr(basStr::filKey($val),-12,12).'_'.basKeyid::kidRand('f',8);
        $sres = str_replace(array('{$id}'),$playid,$sres); 
        $ckjs = '/ckplayer/ckplayer/ckplayer.js';
        if($mtype=='ckvdo' && !strstr($_cbase['run']['jsimp'],$ckjs)){
            $_cbase['run']['jsimp'] .= "$ckjs,"; 
            $sres = basJscss::jscode('',PATH_VENDUI.$ckjs).$sres; 
        }
        return $sres;
    }
        
    static function _itmTpl($file){
        return comFiles::get(DIR_CODE."/cfgs/player/$file.htm"); 
    }
    
    static function _itmRep($org,$val,$pw,$ph){
        $re = str_replace(array('{$url}','{$w}','{$h}'),array($val,$pw,$ph),$org);
        $re = str_replace(array('{uiroot}','{$uiroot}'),PATH_VENDUI,$re);
        return $re; 
    }
    
    static function _itmUri($org){
        $val = self::onePara($org,'val'); 
        $val = urldecode($val); 
        return $val;
    }    
    
    static function onePara($str,$key){
        preg_match("/\[$key\=([^\]]{1,255})\]/i", $str, $m);
        return empty($m[1]) ? '' : $m[1];
    }
    
    static function cfgTypes(){
        return basLang::ucfg('cfglibs.dopmedia'); //;
    }    
}
