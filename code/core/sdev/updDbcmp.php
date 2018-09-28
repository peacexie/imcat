<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// ...类updDbcmp
class updDbcmp{    
    
    static $cpcfgs = array();
    
    static function uimpCheck(){
        bootPerm_ys('pstools','','<p><a href="../adbug/binfo.php?login">login</a></p>');
        if(empty(self::$cpcfgs)){
            $db = glbDBObj::dbObj();
            $ocfgs = glbConfig::read('outdb','ex');
            $ocfgs = $ocfgs['psyn']['odbcfgs'];
            $ocfgs = array_merge($db->config,$ocfgs); 
            $res = devRun::runMydb3($ocfgs); 
            $res = @$res[$ocfgs['db_driver']];
            self::$cpcfgs = array('new'=>$db->config, 'old'=>$ocfgs, 'res'=>$res,);  
        }
        return self::$cpcfgs;   
    }
    
    static function uimpInit(){
        $dbcfgs = self::uimpCheck();
        // new
        $cnew = devData::struExp(0); 
        $cnew = updBase::listTab($cnew,array(),0); 
        updBase::cacSave($cnew,'uimp_new');
        // old
        $cold = devData::struExp(0,$dbcfgs['old']); 
        $cold = updBase::listTab($cold,array(),0);
        updBase::cacSave($cold,'uimp_old');
    }
    
    // $cfgs : array('__ufuncs'=>'mufunc')
    static function uimpTabs($new,$old,$cfgs=''){
        $res = array(); 
        foreach($new as $tab=>$v){
            if(!isset($old[$tab])) continue;
            $flag = devBase::_tabIncfg($tab,$cfgs);
            if(!($flag)) continue;
            $fields = array();
            foreach($v as $k2=>$v2){
                if(is_numeric($k2)) continue; 
                if(isset($old[$tab][$k2])){
                    $fields[] = $k2;
                }
            }
            $fstr = implode('`,`',$fields);
            $res[] = "INSERT INTO dbnew.{pre}$tab{ext} (`$fstr`) \n SELECT `$fstr` FROM dbold.{pre}$tab{ext};\n";
        }
        return $res;
    }
    
    static function cmpIndex($new,$old,$seq=0){
        $res = array('add'=>array(),'old'=>array(),'eq'=>array(),); 
        $arr = array('new'=>'add','old'=>'old');
        foreach($arr as $key=>$ak){
            foreach($$key as $tab=>$v){
                $tmp = $key=='new' ? $old : $new;
                if(!isset($tmp[$tab])) continue;
                foreach($v as $k2=>$v2){
                    if(!is_numeric($k2)) continue; 
                    if(!in_array($v2,$tmp[$tab])){
                        $res[$ak][$tab][] = $v2;
                    }elseif($key=='new'){
                        $res['eq'][$tab][] = $v2;
                    }
                }
            }    
        }
        if(!$seq){ //显示相等的
            unset($res['eq']);
        }
        return $res;    
    }
    
    static function cmpFSkip($new,$old){
        $rep = array("DEFAULT ''","COMMENT ''");
        $arr = array('new','old');
        foreach($arr as $key){
            $$key = preg_replace("/COMMENT \'[^>]{0,48}\'/","COMMENT ''",$$key);
            $$key = str_replace($rep,'',$$key);        
        }
        return trim($new)==trim($old);
    }
    
    static function cmpField($new,$old,$seq=0){
        $res = array('edit'=>array(),'add'=>array(),'old'=>array(),'skip'=>array(),'eq'=>array(),); 
        foreach($new as $tab=>$v){
            if(!isset($old[$tab])) continue; 
            foreach($v as $k2=>$v2){
                if(is_numeric($k2)) continue; 
                if(!isset($old[$tab][$k2])){
                    $res['add'][$tab][$k2] = $v2;
                }elseif($old[$tab][$k2]==$v2){
                    $res['eq'][$tab][] = $v2;
                    unset($old[$tab][$k2]);
                }elseif($old[$tab][$k2]!=$v2){ 
                    $k3 = self::cmpFSkip($v2,$old[$tab][$k2]) ? 'skip' : 'edit';
                    $res[$k3][$tab][$k2] = array('old'=>$old[$tab][$k2],'new'=>$v2,);
                    unset($old[$tab][$k2]);
                }
            } 
            if(!empty($old[$tab])){ 
                foreach($old[$tab] as $k3=>$v3){
                    if(is_numeric($k3)) continue; 
                    $res['old'][$tab][$k3] = $v3;    
                }
            }
        }
        if(!$seq){ //显示相等的
            unset($res['eq']);
        }
        return $res;
    }
    
    static function cmpTable($new,$old){
        $res = array();
        $arr = array('new'=>'add','old'=>'old');
        foreach($arr as $key=>$ak){
            if(!empty($$key)){
            foreach($$key as $k=>$v){
                $tmp = $key=='new' ? $old : $new;
                if(!isset($tmp[$k])){
                    $res[$ak][] = $k;
                }
            }}
        }
        return $res;
    }

}

