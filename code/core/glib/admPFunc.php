<?php

// admPFunc
class admPFunc{    

    // fileNav
    static function modList($pmods,$type='relmod'){    
        $_groups = glbConfig::read('groups'); 
        $a = array(); $pid = '';
        foreach($pmods as $pmod){
        foreach($_groups as $k=>$v){
            if($v['pid']==$pmod){
                if($pid!=$v['pid'] && count($pmods>1)){
                    $a["^group^$v[pid]"] = "$v[pid]-{$_groups[$v['pid']]['title']}";
                }
                $a[$k] = "[$k]$v[title]";
                $pid = $v['pid'];
            }
        } }
        return $a;
    }
    
    // fileNav
    static function fileNav($now,$cfg=array()){
        $gap = "<span class='span ph5'>|</span>";
        $_cfg = basLang::ucfg('nava'); 
        if(is_string($cfg) && isset($_cfg[$cfg])) $cfg = $_cfg[$cfg];
        $str = ''; 
        foreach($cfg as $file=>$title){
            $cur = strstr($file,$now) ? "class='cur'" : '';
            if(strpos($file,'root}')){
                $file = str_replace(array('{root}','{$root}',),array(PATH_PROJ,PATH_PROJ,),$file);
            }else{
                $file = "?file=$file";
            }
            $str .= ($str ? $gap : '')."<a href='$file' $cur>$title</a>";    
        }
        return $str;
    }
    
    // fileNav
    static function fileNavTitle($now,$cfg=array()){
        $_cfg = basLang::ucfg('nava'); 
        if(is_string($cfg) && isset($_cfg[$cfg])) $cfg = $_cfg[$cfg];
        foreach($cfg as $file=>$title){
            if(strstr($file,$now)){ 
                return $title;
            }
        }
        return '';
    }

}
