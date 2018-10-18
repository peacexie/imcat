<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// ...类
class updAdmin extends updBase{    
    
    // doVerset,delete .... 
    static function doVerset($cfg){ 
        define('indo_Verset',1); 
        // update ver
        $vnew = $upc['vnew'];
        $key = "[$]_cbase\[\'sys\'\]\[\'ver\'\]\s*\=\s*[\"'].*?[\"'];";
        $val = "\$_cbase['sys']['ver']     = '$vnew';";
        $path = DIR_ROOT.'/cfgs/boot/const.cfg.php';
        $data = comFiles::get($path);
        $data = preg_replace("/$key/is", $val, $data);
        $data = comFiles::put($path, $data);
        // delete files
        $upcfgs = glbConfig::read('updvnow','sy');
        $dellist = $upcfgs['dellist'];
        foreach($dellist as $file){
            if(strpos($file,':')){
                $tmp = explode(':',$file);
                $arf = explode(',',$tmp[1]);
                foreach($arf as $file){
                    @unlink($tmp.$file);
                }
            }else{
                comFiles::delDir(DIR_PROJ."/$file",1);
            }
        }
        // copy root files
        $new = self::cacGet("tab_proj.php-cdemo",$cfg['path']."/xvars/dtmp/store");
        foreach($new as $k=>$v){
            $f = @copy($cfg['path']."/$k",DIR_PROJ."/$k");
        }
    }
    
    // 初始化
    static function doFileInit($cfg,$step){
        $part = substr($step,0,4);
        $dcfg = array('code'=>DIR_IMCAT,'root'=>DIR_ROOT);
        $pnew = $cfg['path']."/$part"; 
        $pold = $dcfg[$part]; 
        $new = self::cacGet("tab_$part.php-cdemo",$cfg['path']."/xvars/dtmp/store");
        if(empty($new)) $new = self::listDir($pnew); 
        $old = self::cacGet("tab_$part.php-cdemo",DIR_DTMP."/dset");
        foreach($new as $k=>$v){
            if(file_exists($pold."/$k")){
                $old[$k] = md5_file($pold."/$k");
            }
        }
        self::cacSave($new,"file_$part.new");
        self::cacSave($old,"file_$part.old");
    }
    // 补全文件/覆盖文件
    static function doFileAE($cfg,$step,$method){
        $part = substr($step,0,4);
        $dcfg = array('code'=>DIR_IMCAT,'root'=>DIR_ROOT);
        $pnew = $cfg['path']."/$part"; 
        $pold = $dcfg[$part];
        $new = self::cacGet("file_$part.new");
        $old = self::cacGet("file_$part.old");
        $res = self::$method($new,$old,$pnew,$pold);
        $str = "";
        foreach($res as $file=>$val){
            $str .= "$file -&gt; ".($val ? 'OK' : 'Fail')."<br>";
        }
        return $str;
    }
    // 比较文件
    static function doFileComp($cfg,$step){
        $part = substr($step,0,4);
        $dcfg = array('code'=>DIR_IMCAT,'root'=>DIR_ROOT);
        $pnew = $cfg['path']."/$part"; 
        $pold = $dcfg[$part];
        $new = self::cacGet("file_$part.new");
        $old = self::cacGet("file_$part.old");
        $res = self::fileComp($new,$old,$pnew,$pold);
        $str = ""; 
        foreach($res as $file=>$val){
            $str .= "$file -&gt; $val -&gt; <a href='?act=cmpfile&file=$file&part=$part' target='x'>[".basLang::show('core.upd_comp')."]</a><br>";
        }
        return $str;
    }
    
    // 补全dtmp目录
    static function doDirDtmp($cfg){
        $pnew = $cfg['path']."/xvars/dtmp/"; 
        $res = self::dirFix($pnew,DIR_DTMP);
        $str = ""; 
        foreach($res as $dir=>$val){
            $str .= DIR_DTMP." : $dir -&gt; ".($val ? 'OK' : 'Fail')."<br>";
        }
        return $str;
    }
    
    // 添加数据表
    static function doDbInit($cfg){
        devData::struExp('/dbexp/');
        $pnew = $cfg['path']."/xvars/dborg/_stru_tables.dbsql"; 
        $pold = DIR_VARS.'/dbexp/_stru_tables.dbsql';
        $new = self::listTab($pnew); 
        $old = self::listTab($pold);
        self::cacSave($new,"dbcfg_new");
        self::cacSave($old,"dbcfg_old");
    }
    // 添加数据表
    static function doDbTable($cfg){
        $new = self::cacGet("dbcfg_new");
        $old = self::cacGet("dbcfg_old");
        $res = self::dbTable($new,$old,$cfg);
        $str = ""; 
        foreach($res as $tab=>$vals){
            $str .= "<li> ● [$tab] :: ".($vals['f1'] ? 'OK' : 'Fail')." :: ".($vals['f2'] ? 'OK' : 'Fail')." ●<br> {$vals['sql']};</li>";
        }
        return $str;
    }
    // [添加]表字段
    static function doDbFAdd($cfg){
        $dbcfgs = self::cacGet("dbedit_cols");
        $str = "";  
        foreach($dbcfgs as $tab=>$vals){
            if(!empty($vals['add'])){
                //try{ $f1 = $db->query($vals['del']); }catch(Exception $e){}
                if(!strpos($cfg['steps'],'`db_fadd`')){
                    $f1 = glbDBObj::dbObj()->query($vals['add']); 
                }
                $str .= "<li> ● {$tab} ●<pre>{$vals['add']}</pre></li>";    
            }
        }
        return $str;
    }
    // [补全]表字段
    static function doDbFComp($cfg){
        $dbcfgs = self::cacGet("dbedit_cols");
        $str = ""; //edit idx sql
        foreach($dbcfgs as $tab=>$vals){
            $istr = '';
            if(!empty($vals['edit'])){
                $istr .= "{$vals['edit']}\n";
            }
            if(!empty($vals['idx'])){
                $istr .= "{$vals['idx']}\n";
            }
            if(!empty($vals['sql'])){
                $istr .= "{$vals['sql']}\n";
            }
            $istr && $str .= "<li> ● {$tab} <a href='?act=cmptable&tab=$tab' target='x'>[".basLang::show('core.upd_comp')."]</a>●<pre>{$istr}</pre></li>";    
        }
        return $str;
    }
    
    // 数据表 处理
    static function ddData($cfg){
        $new = self::cacGet("dbcfg_new");
        $old = self::cacGet("dbcfg_old");
        $res = array(); $tnav = ''; $tnum = 0; $fdir = 0;
        foreach($new as $tab=>$val){
            if(!isset($old[$tab])) continue;
            $f1 = in_array(substr($tab,0,5),array('base_','bext_'));
            if($f1){
                if(!file_exists(DIR_VARS."/dbexp/data~$tab.dbsql")) devData::data1ExpInsert("/dbexp/data~",$tab); 
                $len = 2; //in_array($tab,array('bext_cron')) ? 1 : 2; 
                if($fdir){ header("Location:?act=cmpdata&ntab=$tab"); die(); }
                $dnew = self::listData($cfg['path']."/xvars/dborg/data~$tab.dbsql",$len);
                $dold = self::listData(DIR_VARS."/dbexp/data~$tab.dbsql",$len);
                foreach($dnew as $k=>$v){
                    if($tab=='base_paras' && strpos($k,':prsafe')) continue;
                    if(!isset($dold[$k])){
                        $res[$tab]['add'][$k] = $v;
                    }elseif($dold[$k]!==$v){
                        $res[$tab]['edit'][$k] = array('old'=>$dold[$k],'new'=>$v,);
                    }
                }
                $ntab = basReq::val('ntab');
                $nact = basReq::val('nact'); //rep,[add]
                if($ntab==$tab){
                    if(!empty($res[$tab]['add']) && !empty($nact)){ self::ddDone($res[$tab],$tab,'add',1); }
                    if(!empty($res[$tab]['edit']) && $nact=='rep'){ self::ddDone($res[$tab],$tab,'edit',1); }
                    if(!empty($nact)){
                        self::setStep($cfg,array('key'=>'dbdata','val'=>$ntab));
                        $fdir = 1; 
                    }else{
                        $res['addsql'] = @self::ddDone($res[$tab],$tab,'add');
                        $res['repsql'] = @self::ddDone($res[$tab],$tab,'edit');
                    }
                }
                if(strpos($cfg['dbdata'],$tab)){
                    $tnav .= "<i>$tab</i>\n";
                }else{
                    $tnav .= "<i><a href='?act=cmpdata&ntab=$tab'>$tab</a></i>\n";
                    $tnum++;
                }
            }
        }
        if($fdir){ header("Location:?act=cmpdata"); die(); } //最后一个$nact,要跳到这里来执行
        $res['tnav'] = $tnav;
        $res['tnum'] = $tnum;
        return $res;
    }
    // 数据表 比较
    static function ddComp($dorg,$tab){
        $data = empty($dorg[$tab]['edit']) ? array() : $dorg[$tab]['edit'];  
        $res = array(); $n = 0; $str = '';
        foreach($data as $key=>$val){
            $res['new'][$key] = $val['new'];
            $res['old'][$key] = $val['old'];
            $n++;
        }
        @$res = basArray::cmpArr($res['new'],$res['old'],'item');
        $res['ne'] = $n;
        $data = empty($dorg[$tab]['add']) ? array() : $dorg[$tab]['add']; 
        foreach($data as $key=>$val){
            $str .= "$key => $val\n";
        }
        $res['add'] = $str;
        $res['na'] = count($data);
        return $res;
    }
    // 数据表 更新
    static function ddDone($data, $tab, $act='', $run=0){
        $db = glbDBObj::dbObj();
        $sql = ($act=='add' ? 'INSERT' : 'REPLACE')." INTO `{$db->pre}$tab{$db->ext}` VALUES";
        if(empty($data[$act])) return '';
        $data = $data[$act]; $i=0;
        foreach($data as $key=>$val){
            $i++;
            $val = $act=='add' ? $val : $val['new'];
            $ta = explode(':',$key);
            $row = "\n('";
            foreach($ta as $v1){
                $row .= "$v1','";
            }
            $row .= "$val".($i==count($data) ? ';' : ',');
            $sql .= $row;
        } 
        if($run){
            return $db->query($sql); 
        }else{
            return $sql; //str_replace(array("\n"),array("\n<br>"),$sql); 
        }
    }
    
    // 比较Table
    static function compTable($cfg){
        $tab = basReq::val('tab');
        $new = self::cacGet("dbcfg_new");
        $old = self::cacGet("dbcfg_old");
        $new = self::dbFields(self::dbCreate($tab,$new[$tab]));
        $old = self::dbFields(devData::stru1Exp($tab)); 
        return basArray::cmpArr($new,$old,'item');
    }
    
    // 比较文件
    static function compFile($cfg){
        $part = basReq::val('part');
        $file = basReq::val('file','','Html');
        $dcfg = array('code'=>DIR_IMCAT,'root'=>DIR_ROOT);
        $pnew = $cfg['path']."/$part/$file"; 
        $pold = $dcfg[$part]."/$file";
        $new = file($pnew); $old = file($pold); 
        return basArray::cmpArr($new,$old,'code');
    }
    
    //static $updreset = ''; 
    static function setStep($cfg,$step,$dir=0){
        if(is_array($step) && $step['key']=='dbdata'){
            $steps = $cfg['steps']; $step = $step['val'];
            $dbdata = str_replace("$step`$step`","$step`",$cfg['dbdata']."$step`");
        }else{
            $steps = str_replace("$step`$step`","$step`",$cfg['steps']."$step`");
            $dbdata = $cfg['dbdata'];
        }
        $br = "\r\n";
        $data = "path={$cfg['path']}{$br}steps=$steps{$br}dbdata=$dbdata{$br}vnew={$cfg['vnew']}{$br}vold={$cfg['vold']}{$br}done={$cfg['done']}";
        $f = comFiles::put(DIR_DTMP.self::$prereset,$data);
        $dir && header('Location:?');
    }
    
}
