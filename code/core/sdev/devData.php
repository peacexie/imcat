<?php

// ...类
class devData{    

    static $spsql = "---<split>---";

    // runSql
    static function run1Sql($sql,$rep=''){
        if(empty($sql)) return true;
        $db = db(); 
        //if($rep=='Update'){
            $sql = str_replace("INSERT INTO `{pre}","REPLACE INTO `{pre}",$sql); 
        //}
        $sp = self::$spsql;
        $sql = str_replace(array("`{pre}","{ext}`"),array("`$db->pre","$db->ext`"),$sql);
        $arr = strpos($sql,$sp)>0 ? explode($sp,$sql) : array($sql);
        try {
            $n = 0;
            foreach ($arr as $key => $sql) {
                $db->query($sql,'run'); 
                $n++;
            }
            return $n;
        }catch (Exception $e){ 
            return $e->getMessage();
        }
    }

    // 导出一个模型数据
    static function exp1Mod($mod,$expdt=1){
        if(empty($mod)) return array();
        $data['model_'.$mod] = self::exp1Tab('base_model',"kid='$mod'"); 
        $data['fields_'.$mod] = self::exp1Tab('base_fields',"model='$mod'"); 
        $data['catalog_'.$mod] = self::exp1Tab('base_catalog',"model='$mod'"); 
        $data['grade_'.$mod] = self::exp1Tab('base_grade',"model='$mod'"); 
        $data['fldext_'.$mod] = self::exp1Tab('bext_fields',"model='$mod'"); 
        $tabm = glbDBExt::getTable($mod,0);
        $tabe = glbDBExt::getTable($mod,1);
        $tabs = $tabm==$tabe ? $tabm : "$tabm,$tabe";
        $tarr = explode(',',$tabs);
        foreach ($tarr as $k=>$tab) {
            $data['stru_'.$tab] = self::exp1Tab($tab,'_stru_');
            $expdt && $data['data_'.$tab] = self::exp1Tab($tab);
        }
        return $data;
    }

    // 导出一个表数据:部分数据+表结构
    static function exp1Tab($tab,$whr=''){
        if(empty($tab)) return '';
        if($whr=='_stru_'){
            $flag2 = "CREATE TABLE `";
            $ctmp = self::stru1Exp($tab);
            $ctmp = str_replace("$flag2",self::$spsql."\n$flag2",$ctmp); 
            return $ctmp;
        }
        $shead = $sins = "";
        $list = db()->table($tab)->where($whr)->select(); 
        if($list){ //分块未考虑... 
            $shead = devBase::_tabHead($tab); $i = 0; 
            foreach($list as $row){
                if(defined('MINI_CUT_DETAIL')&&strpos(MINI_CUT_DETAIL,$tab)) $row['detail']='~';
                $i++; $end = $i==count($list) ? ';' : ',';
                $sins .= devBase::_dinsRow($row)."$end\n";
            }
        }
        return $shead.$sins;
    }
    
    // struExp('/dbexp/');导出结构到文件或返回string
    static function struExp($path,$dbcfg=array()){ 
        $db = db($dbcfg);
        $dbTabs = $db->tables();
        $re = "";
        foreach($dbTabs as $tab){ 
            $re .= "\n".self::stru1Exp($tab,$dbcfg).";\n";
        }
        if($path){
            $path = DIR_DTMP.$path.'_stru_tables.dbsql';
            comFiles::put($path,$re);
        }else{
            return $re;
        }
    }
    
    // stru1Exp('tablename'); 导出单个表结构
    static function stru1Exp($tab,$dbcfg=array()){ 
        $db = db($dbcfg);
        $tabfull = $db->pre.$tab.$db->ext; 
        $stru = "DROP TABLE IF EXISTS `{pre}$tab{ext}`;";
        $stru .= "\n".$db->create($tab); 
        $stru = str_replace("`$tabfull`","`{pre}$tab{ext}`",$stru);
        return $stru;
    }
    
    // struImp('/dbexp/'); 从文件导入结构
    static function struImp($path){
        $db = db(); 
        $file = DIR_DTMP.$path.'_stru_tables.dbsql';
        $data = comFiles::get($file);
        $fix1 = 'DROP TABLE IF EXISTS `'; 
        $fix2 = 'CREATE TABLE `';
        $pre = '{pre}'; $suf = '{ext}';
        $arr = explode($fix1.$pre,$data);
        $errs = array(); $oks = 0;
        foreach($arr as $sql){
        if(strstr($sql,$fix2.$pre)){
            $sql = $fix1.$pre.$sql;
            $arr2 = explode($fix2.$pre,$sql);
            $sqla = $arr2[0];             $sqla = str_replace(array($pre,$suf),array($db->pre,$db->ext),$sqla);
            $sqlb = "$fix2$pre".$arr2[1]; $sqlb = str_replace(array($pre,$suf),array($db->pre,$db->ext),$sqlb);
            try {
                if($db->config['db_type']=='sqlite'){
                    $sqla = basSql::sqlite_tabcreate($sqla);
                    $sqlb = basSql::sqlite_tabcreate($sqlb);
                }
                $db->query($sqla,'run');
                $db->query($sqlb,'run');
                $oks++;
            }catch (Exception $e){ echo 'eer, ';
                $errs[] = $e->getMessage();
            }
        } }
        return array($oks,$errs);
    }
    
    // 
    static function dataExpGroup($path,$dbcfg=array(),$pfull=0){
        $db = db($dbcfg);
        $dbTabs = $db->tables(); 
        $fix = array($db->pre,$db->ext); //array('{pre}','{ext}')
        $groups = devBase::_tabGroup($dbTabs); 
        foreach($groups as $group){ 
            $cfgs = array("{$group}_"); $data = '';
            foreach($dbTabs as $tab){ 
                if(defined('MINI_DEL_TABLES')&&strpos(MINI_DEL_TABLES,$tab)) continue;
                $flag = devBase::_tabIncfg($tab,$cfgs); 
                if(empty($cfgs) || $flag){ 
                    $tabfull = $db->pre.$tab.$db->ext; 
                    $list = $db->table($tab)->select(); 
                    if($list){ 
                        $thead = devBase::_tabHead($tab,$fix,'REPLACE'); 
                        $tdata = ''; $i = 0;  
                        foreach($list as $row){
                            if(defined('MINI_CUT_DETAIL')&&strpos(MINI_CUT_DETAIL,$tab)) $row['detail']='~';
                            $i++; $end = $i==count($list) ? ';' : ',';
                            $tdata .= devBase::_dinsRow($row)."$end\n";
                        }
                        if($tdata) $data .= "$thead$tdata";
            }    }    }
            if(!empty($data)){
                $pathi = $pfull ? $path : DIR_DTMP.$path;
                $fp = fopen(str_replace("\\","/",$pathi."$group.dbsql"), 'w');
                fwrite($fp, $data);
                fclose($fp);
            }
        } //groups
    }
    
    // dataExp('/dbexp/'); 导出指定表(所有表)数据
    static function dataExp($path,$cfgs='',$mode='in',$dbcfg=array(),$pfull=0){
        $db = db($dbcfg);
        $dbTabs = $db->tables();
        $groups = devBase::_tabGroup($dbTabs); 
        if(empty($cfgs)){
            $cfgs = '';
        }elseif(is_string($cfgs)){
            $cfgs = in_array($cfgs,$groups) ? "{$cfgs}_" : $cfgs;
        }// is_array
        foreach($dbTabs as $tab){
            if(defined('MINI_DEL_TABLES')&&strpos(MINI_DEL_TABLES,$tab)) continue;
            $flag = devBase::_tabIncfg($tab,$cfgs); 
            if(empty($cfgs) || ($flag && $mode=='in') || (!$flag && $mode=='notin')){
                self::data1ExpInsert($path,$tab,$dbcfg,$pfull); 
            }
        }
    }    
    
    // data1Exp("/dborg/data~",'base_fields'); 导出单个表数据到文件
    static function data1ExpInsert($path,$tab,$dbcfg=array(),$pfull=0){
        $db = db($dbcfg);
        $tabfull = $db->pre.$tab.$db->ext; 
        $path = $pfull ? $path : DIR_DTMP.$path;
        $list = $db->table($tab)->select(); 
        if($list){ //分块未考虑... 
            $shead = devBase::_tabHead($tab); $i = 0; 
            $fp = fopen(str_replace("\\","/",$path."$tab.dbsql"), 'w');
            fwrite($fp, $shead);
            foreach($list as $row){
                if(defined('MINI_CUT_DETAIL')&&strpos(MINI_CUT_DETAIL,$tab)) $row['detail']='~';
                $i++; $end = $i==count($list) ? ';' : ',';
                $rstr = devBase::_dinsRow($row)."$end\n";
                fwrite($fp, $rstr);
            }
            fclose($fp);
        }
    }
    
    // data1ExpFile("/dborg/data~",'base_fields'); 导出单个表数据到文件
    static function data1ExpFile($path,$tab,$dbcfg=array()){
        $db = db($dbcfg);
        $tabfull = $db->pre.$tab.$db->ext; 
        $path = DIR_DTMP.$path;
        try{
            $file = str_replace("\\","/",$path."$tab.dbsql"); 
            $sql="SELECT * FROM {$tabfull} INTO OUTFILE '$file' ".devBase::_loadOpt();
            if(file_exists($file)){
                unlink($file);
            }
            $db->query($sql,'run');
            $data = comFiles::get($file);
            if(empty($data)){
                unlink($file);
            }
            return true;
        }catch(Exception $e){
            # $e->getMessage()
            return false;
        }
    }
    
    // dataImpFile("/dborg/data~",'base_fields'); 从文件导入数据
    static function dataImpFile($path,$tab,$dtmp=0){
        $db = db(); 
        $tabfull = $db->pre.$tab.$db->ext;
        $path = ($dtmp ? $dtmp : DIR_DTMP).$path;
        $file = str_replace("\\","/",$path."$tab.dbsql"); 
        $sqlClean = "DELETE FROM $tabfull";
        $sqlLoad = "LOAD DATA INFILE '$file' INTO TABLE $tabfull ".devBase::_loadOpt();
        $sqlLoad = self::dataImpLang($sqlLoad,$tab);
        try {
            $db->query($sqlClean,'run');
            $db->query($sqlLoad,'run');
            return true;
        }catch (Exception $e){
            return false;
        }      
    }
    
    // dataImpInsert("/dborg/data~",'base_fields'); 从文件导入数据
    static function dataImpInsert($path,$tab,$dtmp=0){
        $db = db(); 
        $tabfull = $db->pre.$tab.$db->ext;
        $path = ($dtmp ? $dtmp : DIR_DTMP).$path;
        $file = str_replace("\\","/",$path."$tab.dbsql"); 
        $fsql = comFiles::get($file);
        if(empty($fsql)){
            return false;
        }
        $sqlClean = "DELETE FROM $tabfull";
        $sqlLoad = str_replace(array("`{pre}{$tab}{ext}`"),array("`$tabfull`"),$fsql);
        $sqlLoad = self::dataImpLang($sqlLoad,$tab);
        if($db->config['db_type']=='sqlite'){
            $sqlLoad = basSql::sqlite_insbatch($sqlLoad);
        }
        try {
            $db->query($sqlClean,'run');
            $db->query($sqlLoad,'run');
            return true;
        }catch (Exception $e){
            return false;
        }      
    }

    static function dataImpLang($sql,$tabs){
        $lang = cfg('sys.lang');
        if($lang!=='cn'){ 
            $taba = explode(',',$tabs);
            $rcn = $ren = array();
            foreach ($taba as $tab) {
                $cnlang = DIR_CODE."/lang/dbins/$tab-cn.php"; 
                $oblang = DIR_CODE."/lang/dbins/$tab-$lang.php"; 
                if(file_exists($oblang)){
                    $rcn[$tab] = include($cnlang); 
                    $ren[$tab] = include($oblang); 
                }else{
                    return $sql;
                }
            }
            for($i=15; $i>=1; $i--) { 
                foreach ($taba as $tab) {
                    $tmp = basArray::lenParts($rcn[$tab],$ren[$tab],$i);
                    if(!empty($tmp[0])){
                        $sql = str_replace($tmp[0],$tmp[1],$sql); 
                    } 
                }
            }
        }
        return $sql;
    }

}

