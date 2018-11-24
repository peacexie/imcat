<?php
namespace imcat;

// admAFunc 
class admAFunc{    
 
    static function mkvIperm($muid,$mprm){
        $db = glbDBObj::dbObj();
        $mprm = ":,$mprm,";
        $pmarr = array();
        $arr = $db->table('base_menu')->where("model='$muid' AND enable='1'")->order('deep,top')->select();
        foreach($arr as $k=>$row){
            if(!empty($row['cfgs']) &&strpos($mprm,','.$row['kid'].',')){
                preg_match_all("/mkv\=([\w|\-]+)/", $row['cfgs'], $ms);
                if(!empty($ms[1])){
                    foreach($ms[1] as $mv){
                        if(!in_array($mv,$pmarr)){
                            $pmarr[] = $mv;
                        }
                    }
                }
            }else{
                //echo $row['kid'].':flag<br>';
            }
        }
        return implode(',',$pmarr);
    }

    static function mkvInit($mod='adm'){
        $db = glbDBObj::dbObj();
        $tabid = 'base_menu'; 
        $tpl = $mod=='mkvu' ? 'umc' : 'adm';
        $mlist = vopTpls::entry($tpl); 
        if($tpl=='adm') $mlist = array_merge($mlist,vopTpls::enflow()); 
        $dbcfg = $db->table($tabid)->where("model='$mod'")->order('pid,top,kid')->select();
        $mcfg = array(); 
        foreach($dbcfg as $k=>$v){
            $mcfg[$v['kid']] = $v;
        } 
        foreach($mlist as $key=>$val){
            $mpos = strpos("$key)","-m)");
            $pid = $mpos ? 0 : substr($key,0,strpos($key,"-")).'-m';
            $deep = $mpos ? 1 : 2;
            $fm = array('pid'=>$pid,'enable'=>'1','deep'=>$deep,'note'=>$val,);
            if(isset($mcfg[$key])){
                unset($mcfg[$key]);
                $db->table($tabid)->data(basReq::in($fm))->where("model='$mod' AND kid='$key'")->update();  
            }else{ 
                $fm = array_merge($fm,array('kid'=>$key,'title'=>'','model'=>$mod,'cfgs'=>'','top'=>'888',));
                $db->table($tabid)->data(basReq::in($fm))->insert();
            }
        }
        foreach($mcfg as $key=>$val){
            $db->table($tabid)->where("model='$mod' AND kid='$key'")->delete(); 
        } 
    }
    
    // umcVmods
    
    // 所有[关联模型]为$mod的模型
    static function pmodSuns($mod='',$re='a'){
        $_groups = glbConfig::read('groups'); 
        $a = array(); if(!$mod) return $a;
        foreach($_groups as $k=>$v){
            if($mod==$v['pmod']){
                $a[] = $k;
            }
        }
        return $a;
    }
    // [关联模型]保存
    static function pmodSave($omod,$pmod=''){
        $oldPid = basReq::val('oldPid');
        if($oldPid && $oldPid===$pmod) return;
        if(empty($pmod)){ //取消
            glbDBExt::setOneField($omod,'pid','del');    
        }else{ //关联 drem(pid),demo(cnt_drem)
            $r = array('dbtype'=>'varchar(24)','dbdef'=>'','vreg'=>'0'); 
            glbDBExt::setOneField($omod,'pid','check',$r);    
        }
    }
    
    // modCopy, 
    // $fm:is_del,mod_id,
    // $cid:modid(pro),'',reset
    static function modCopy($mod, $tabid, $fm, $cid=''){
        $_groups = glbConfig::read('groups'); 
        $db = glbDBObj::dbObj();
        $org_arr = array('coms'=>'nrem','docs'=>'news','users'=>'person','types'=>'common','advs'=>'adpic');
        if($fm=='is_del'){
            $fm = $db->table($tabid)->where("kid='$cid'")->find();
            $dcnt = $db->table($tabid)->where("issys='0' AND kid='$cid'")->delete();    
            if($dcnt){ //删除成功...
                if(isset($org_arr[$mod])) glbDBExt::setfieldDemo($cid, "{$mod}_$cid"); 
                if($mod=='docs' && $fm['etab']) glbDBExt::setfieldDemo($cid, "dext_$cid"); 
                @unlink(DIR_DTMP."/modcm/_$cid.cfg.php"); //删除缓存
                if($mod=='docs' || $mod=='advs') $db->table('base_catalog')->where("model='$cid'")->delete();
                if($mod=='users') $db->table('base_grade')->where("model='$cid'")->delete();
            }
            return $dcnt ? basLang::show('admin.aaf_delok') : basLang::show('admin.aaf_errkeep');
        }elseif(is_string($fm) && isset($_groups[$fm]) && is_string($cid) && isset($_groups[$cid])){
            $fm_org = $db->table($tabid)->where("kid='$fm'")->find(); 
            $fm_now = $db->table($tabid)->where("kid='$cid'")->find();
            self::modCopy($mod, $tabid, 'is_del', $cid); //del
            self::modCopy($mod, $tabid, $fm_now, $fm); //copy
            return basLang::show('admin.aaf_resetok');
        }elseif(is_array($fm) && $cid && empty($fm['org_tab'])){ //copy
            $fm_org = $db->table($tabid)->where("kid='$cid'")->find();
            $fm['etab'] = $fm_org['etab'];
            $fm['org_tab'] = "{$mod}_$cid";
            self::modCopy($mod, $tabid, $fm, $cid); //add
            return basLang::show('admin.aaf_copyok');
        }elseif(is_array($fm)){ //add
            $id = basReq::in(@$fm['kid']);
            $org_tab = @$fm['org_tab']; unset($fm['org_tab']);
            $db->table($tabid)->data(basReq::in($fm))->insert();
            if(isset($org_arr[$mod])){
                $org_tab = empty($org_tab) ? "{$mod}_".$org_arr[$mod] : $org_tab;
                glbDBExt::setfieldDemo($id, "{$mod}_$id", $org_tab); 
                if($mod=='docs' && !empty($fm['etab'])){
                    glbDBExt::setfieldDemo($id, "dext_$id", str_replace('docs_','dext_',$org_tab));     
                }
            }
            return basLang::show('admin.aaf_addok');
        }else{
            return basLang::show('admin.aaf_error');    
        }
        return $msg;
        
    }
        
    // upd config
    static function grpNav($pid,$mod){
        global $_cbase; 
        $_groups = glbConfig::read('groups');
        $mkv = $_cbase['mkv']['mkv'];
        $str = ''; 
        $ggap = ''; $top0 = '1';
        foreach($_groups as $k=>$v){
            if($v['pid']==$pid){
                $top1 = substr($v['top'],0,1);
                $str .= (($top0!=$top1)?'<br>':$ggap)."<a href='?mod=$k'>$v[title]</a>";    
                $ggap = ' | '; $top0 = $top1;
            }
        }
        $str = str_replace("?mod=","?$mkv&mod=",$str);
        $str = str_replace("&mod=$mod","&mod=$mod' class='cur",$str); 
        return $str;
    }

    // types,catalog,menus使用
    static function typLay($cfg,$aurl,$pid){ 
        if(empty($pid)){
            return basLang::show('admin.aaf_ttype');
        }else{
            $str = "<a href='".basReq::getURep($aurl[1],'pid','0')."'>".basLang::show('admin.aaf_top')."</a>»";
            $lnk = "<a href='".basReq::getURep($aurl[1],'pid','[k]')."'>[v]</a>";
            $str .= comTypes::getLnks(comTypes::getLays($cfg['i'],$pid),$lnk);
        }
        return $str;
    }
    

}
