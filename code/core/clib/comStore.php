<?php

// Store存储类
class comStore{    

    public static $objs = array(); // 2.3~2.7s vs 1.9~2.2s

    // 移动:从临时文件到正式附件地址 $cls::method(); 在php5.2下报错
    static function moveUres($org,$obj){
        $clsName = self::rsClass();
        if($clsName=='rsLocal'){
            $re = rename($org,DIR_URES.'/'.$obj);
        }else{
            $rsObj = self::rsClass($clsName);
            $re = $rsObj->moveUres($org,$obj);
        }
        return $re;
    }
    // 删除:删除id下的附件资源
    static function delFiles($mod,$kid){
        $clsName = self::rsClass();
        $dir = comStore::getResDir($mod,$kid,0);
        if($clsName=='rsLocal'){
            $re = comFiles::delDir(DIR_URES.'/'.$dir,1);
        }else{
            $rsObj = self::rsClass($clsName);
            $re = $rsObj->delFiles($dir);
        }
        return $re;
    } 
    static function rsClass($clsName=''){
        if(empty($clsName)){
            $scfg = glbConfig::read('store','ex'); //store
            $clsName = $scfg['type'];
            return $clsName;
        }else{ // 加载
            if(empty(self::$objs[$clsName])){
                require_once DIR_CODE."/adpt/store/$clsName.php";
                self::$objs[$clsName] = new $clsName();
            }
            return self::$objs[$clsName];
        }
    }

    /**
     * 上传到的临时目录，后续再移动到正式目录
     * @return string
     */
    static function getTmpDir($isfull=1){
        $user = usrBase::userObj();
        $sid = empty($user->sinit['sid']) ? usrPerm::getUniqueid('Cook','sip') : $user->sinit['sid'];
        $path = "@udoc/$sid"; //$modFix-
        comFiles::chkDirs($path,'dtmp',0);
        return ($isfull ? DIR_DTMP.'/' : '')."$path"; //PATH_ROOT
    }
    
    static function fixTmpDir($path){
        $pos = strpos($path,"/@udoc/");
        $path = PATH_DTMP.substr($path,$pos);
        return $path;
    }
    
    /**
     * 上传资源目录
     * @return string
     */
    static function getResDir($mod,$kid,$isfull=1,$chkdir=0){
        $grs = glbConfig::read('groups'); 
        $mcfgs = empty($grs[$mod]) ? array() : $grs[$mod];
        if(empty($kid)){
            die(__FUNCTION__);
        }
        $kpath = $kid; 
        $fmts = glbConfig::read('frame.resfmt','sy'); // docs,users; types; advs,coms 
        $fmt = (!empty($mcfgs['pid']) && in_array($mcfgs['pid'],array('docs','users'))) ? 1 : 0;
        foreach($fmts as $k=>$v){ // 默认:fmt=1 : yyyy/md-noid
            if(in_array($mod,$v)){ $fmt=$k; break; }
        }
        if(!empty($fmt) && strpos($kid,'-')>0){ 
            $ka = explode('-',$kid);
            if($fmt==1) $kpath = $ka[0].'/'.$ka[1].(empty($ka[2])?'':'-'.$ka[2]);
            if($fmt==2) $kpath = $ka[0].'-'.$ka[1].(empty($ka[2])?'':'/'.$ka[2]);
            if($fmt==3) $kpath = $ka[0].'/'.$ka[1].(empty($ka[2])?'':'/'.$ka[2]);
            if($fmt==6){ // /html/news-16/ab-8899.html
                $repath = "$mod-".substr($kpath,2,2)."/".substr($kpath,5);
            }else{
                $repath = "$mod/$kpath"; 
            }
        }else{
            $repath = "$mod/$kpath"; //empty($kpath);
        }
        $chkdir && comFiles::chkDirs($repath,'ures',0);
        return ($isfull ? DIR_URES.'/' : '').$repath;
    }
    
    //移动临时文件夹中的文件
    static function moveTmpDir($str,$mod,$kid,$ishtml=0){
        $ar2 = self::moveTmpFmt($str,$ishtml);
        if(empty($ar2)) return $str;
        foreach($ar2 as $v){
            if(self::moveTmpOne($str,$v,$mod,$kid)) continue;
            $cfg = array(
                array('dtmp','/@udoc/'), 
                array('ures',"/$mod"), 
                array('html',"/$mod"),
                array('static',"/"), 
                array('vendui',"/"), 
                array('vendor',"/"), 
                array('root',"/"),
            );
            foreach($cfg as $cv){
                $str = self::moveRepRoot($str,$v,$cv[0],$cv[1]);
            }
        } //die();
        return $str;
    }
    // deel:@udoc
    static function moveTmpOne(&$str,$v,$mod,$kid){
        global $_cbase;
        $fix = PATH_DTMP."/@udoc/";
        $flag = 0;
        if($org=strstr($v,$fix)){
            $orgfile = DIR_DTMP.substr($org,strlen(PATH_DTMP));
            $obj = self::getResDir($mod,$kid,0,1)."/".basename($org);
            if(in_array($org,$_cbase['run']['tmpFile'])){
                $str = str_replace($v,'{uresroot}/'.$obj,$str);
                $flag = 1; 
            }elseif(is_file($orgfile)){ 
                if($re=self::moveUres($orgfile,$obj)){
                    $str = str_replace($v,'{uresroot}/'.$obj,$str);
                    $_cbase['run']['tmpFile'][] = $org;
                    $flag = 1; 
                }
            }
        }
        return $flag;
    }
    // str2arr
    static function moveTmpFmt($str,$ishtml=0){
        if($ishtml){ //a,img,embed,value?,
            preg_match_all("/\s+(src|href|value)=(\S+)[\s|>]+/i",$str,$arr); //3
            $ar2 = empty($arr[2]) ? array() : str_replace(array("\\",'"',"'"),array(),$arr[2]); 
        }else{
            if(strpos($str,';')){ //pics
                $ar2 = explode(';',$str);
                foreach($ar2 as $k=>$v){
                    $art = explode(',',$v);
                    if(empty($art[0])) unset($ar2[$k]);
                    else $ar2[$k] = str_replace(array("\r","\n",' '),array('','',''),$ar2[$k]);
                }
            }else{
                $ar2 = array($str);
            }
        } 
        $ar2 = array_unique(array_filter($ar2));
        return $ar2;
    }
    
    //替换root路径
    static function moveRepRoot($str,$v,$key,$fix=''){
        global $_cbase;
        $rmain = $_cbase['run']['rmain'];
        $cfg = self::cfgDirPath($key,'arr');
        $res = $v;
        /*if(strpos($res,'://')>0){ //完整路径
            if(strpos($res,$rmain)===0){ //本地
                //$res = str_replace($rmain,"",$res); 
            }else{ //外网(可处理远程图...)
                //return $str;    
            }
        }*/
        if(strpos($res,$cfg[1].$fix)===0 && !empty($cfg[1])){
            $res = '{'.$key.'root}'.substr($res,strlen($cfg[1]));
            $str = str_replace($v,$res,$str);
        }
        return $str;
    }
    
    //part:dir,arr,else
    static function cfgDirPath($key,$part='dir'){
        $cfg = array(
            'root'=>array(DIR_ROOT,    PATH_ROOT),
            'code'=>array(DIR_CODE,    PATH_CODE),
            'skin'=>array(DIR_SKIN,    PATH_SKIN),
            'ctpl'=>array(DIR_CTPL,''),
            'dtmp'=>array(DIR_DTMP,    PATH_DTMP),
            'ures'=>array(DIR_URES,    PATH_URES),
            'html'=>array(DIR_HTML,    PATH_HTML),
            'vendor'=>array(DIR_VENDOR,  PATH_VENDOR),
            'vendui'=>array(DIR_VENDUI,  PATH_VENDUI),
            'static'=>array(DIR_STATIC,  PATH_STATIC),
            'tpl'=>array(vopTpls::path('tpl'),''), //可能没有定义
            'tpc'=>array(vopTpls::path('tpc'),''),
        );
        $re = isset($cfg[$key]) ? $cfg[$key] : $cfg;
        if($part=='arr') return $re;
        $id = $part=='dir' ? 0 : 1;
        return empty($re[$id]) ? $key : $re[$id];
    }
    
    //还原保存的路径
    static function revSaveDir($str,$part=''){
        $paths = self::cfgDirPath(0,'arr');
        foreach($paths as $ck=>$itm){
            if(in_array($ck,array('tpl','tpc','ctpl','code'))) continue;
            $path = $part=='dir' ? $itm[0] : $itm[1];
            $str = str_replace(array('{'.$ck.'root}','{$'.$ck.'root}'),$path,$str); 
        }
        return $str;
    }
    
}
