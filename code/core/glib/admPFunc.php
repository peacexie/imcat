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
            $file = str_replace('/','-',$file);
			$cur = strstr($file,$now) ? "class='cur'" : '';
            if(strpos($file,'root}')){
                $file = str_replace(array('{root}','{$root}',),array(PATH_PROJ,PATH_PROJ,),$file);
            }else{
                $file = "?mkv=$file";
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

    // ------------------- used in cajax.php 

    // fieldExists
    static function fieldExists($kid,$mod,$_groups){
        $db = db();
        $sy_kids = read('keepid','sy');
        if($re=basKeyid::keepCheck($kid,1,1,0)){ //$key,$chk,$fix,$grp
            die($re);
        }elseif($cmod = $db->table('base_fields')->where("model='$mod' AND kid='$kid'")->find()){
            die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_exsists'));
        }elseif(isset($_groups[$kid]) && $_groups[$kid]['pid']=='types'){ //系统模型
            die("success");
        }elseif(isset($_groups[$kid]) || strstr($sy_kids,",$kid,")){ //系统模型
            die("[$kid]".lang('plus.cajax_sysuesed')."[mod]");
        }elseif(isset($_groups[$kid]) && $_groups[$kid]['pid']!='types'){ //系统模型
            die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_syskeep')."[k1])");
        }elseif(in_array($kid,fldCfgs::setKeeps())){
            die("[$kid]".lang('plus.cajax_mykeys'));
        }else{
            die("success");
        }
    }

    static function fieldCatid($kid,$mod){
        $catid = req('catid',''); 
        $ccfg = read($mod,'_c'); 
        $mfields = @$ccfg[$catid]; 
        if($re=basKeyid::keepCheck($kid,1,0,1)){ //$key,$chk,$fix,$grp
            die($re);
        }elseif(!empty($mfields) && isset($mfields[$kid])){
            die(lang('plus.cajax_field')."[$kid]".lang('plus.cajax_exsists'));
        }else{
            die("success");
        }
    }

    static function keyExists($kid,$mod,$_groups){
        $db = db();
        $tab = req('tab'); 
        $_f1 = in_array($tab,array('base_catalog','base_fields','base_grade','base_menu','base_model','base_paras','types_common'));
        $_k2 = str_replace('types_','',$tab);
        $_f2 = isset($_groups[$_k2]); 
        if(!$_f1 && !$_f2) die(lang('plus.cajax_erparam'));
        $kre = strtolower(basReq::ark('fm','kre','Key')); //@$fm['kre']
        if(!$kid && $kre) $kid=$kre;
        $old = req('old_val'); 
        if($re=basKeyid::keepCheck($kid,1,0,1,($_k2?2:3))){ //$key,$chk,$fix,$grp
            die($re);
        }elseif($kid===$old){
            die("success");
        }elseif(in_array($kid, fldCfgs::setKeeps())){
            echo "[$kid]".lang('plus.cajax_mykeys')."[mysql]";
        }elseif($flag=$db->table($tab)->where((empty($mod) ? '' : "model='$mod' AND ")."kid='$kid'")->find()){
            echo "[$kid]".lang('plus.cajax_beuesed');
        }else{
            die("success");
        }
    }

    static function infoRepeat($mod,$_groups){
        $db = db();
        $fid = req('fid');
        $kwd = req('kwd'); // mod,kid(docs,user,coms,advs)
        $msg = lang('plus.cajax_erparam');
        if(!isset($_groups[$mod])) die("var _repeat_res = '$msg';");
        $mcfg = read($mod); 
        $para = "[$mod:$fid=$kwd]";
        if(empty($mcfg['f'][$fid])){
            $re = "$para $msg!";    
        }elseif($kwd && $tab=glbDBExt::getTable($mod)){
            $flag = $db->table($tab)->where("$fid='$kwd'")->find();
            $re = empty($flag) ? "success" : lang('plus.cajax_repeat');
        }else{
            $re = "$para $msg!";    
        }
        echo "var _repeat_res = '$re';";
    }

    static function cfield($kid,$mod){
        $db = db();
        $_cfg = read($mod);
        $_pid = $_cfg['pid']; 
        $_tmp = array(
            'docs' =>'did',
            'users'=>'uid',
        ); //'coms' =>'cid',
        if(!isset($_tmp[$_pid])) glbHtml::end(lang('plus.cajax_erparam').':mod@dop.php');
        $data = $db->table("{$_pid}_$mod")->where("$_tmp[$_pid]='$kid'")->find(); 
        fldView::lists($mod,$data,req('catid'));
    }

}
