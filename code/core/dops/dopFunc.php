<?php
// dopFunc : 基本操作 static函数
class dopFunc{    

    // 得到`字段存文件`的内容
    static function getFsval($mod,$kid,$fid='cfile'){
        $cfdir = comStore::getResDir($mod,$kid,1,0)."/fs_$fid.data";
        $cfile = comFiles::get($cfdir);
        return $cfile; 
    }

    static function getMinfo($mod,$kid='',$fid=''){
        $fid || $fid = glbDBExt::getKeyid($mod);
        $info = glbDBObj::dbObj()->table(glbDBExt::getTable($mod))->where("$fid='$kid'")->find(); 
        return $info; 
    }

    static function joinDext(&$re,$mod,$kk='kid'){
        $ids = '';
        foreach($re as $k=>$v){
            $ids .= (empty($ids) ? '' : ',')."'".$v[$kk]."'";
        } 
        if(empty($ids)) return;
        $re1 = glbDBObj::dbObj()->table(glbDBExt::getTable($mod,1))->where("$kk IN($ids)")->select(); 
        $re2 = array();
        foreach($re1 as $k1=>$v1){ 
            $re2[$v1[$kk]] = $v1;
        } 
        foreach($re as $k=>$v){
            if(isset($re2[$v[$kk]])){
                $re[$k] = $v + $re2[$v[$kk]];
            }
        } 
    }

    static function vgetLink($title,$mod='',$kid='',$link=''){
        if(empty($link)){ 
            $url = PATH_ROOT."/plus/ajax/redir.php?$mod.{$kid}";
        }elseif($link=='#'){
            return $title;
        }elseif(strpos($link,'://')){
            $url = $link;    
        }else{
            $url = PATH_ROOT.$link;    
        }
        $re = "<a href='$url' target='_blank'>$title</a>"; 
        return $re; 
    }

    // 获取字段值(标题,公司名,会员名)
    static function vgetTitle($mod,$val='',$field=''){
        $mcfg = glbConfig::read($mod); 
        $field || $field ="title,company,uid,uname,mname,mtel,memail"; 
        $field = self::vchkFields($field,self::vgetFields($mcfg['f'],'all','all'));
        $field = implode(',',array_keys($field)); 
        $field = explode(',',$field); $field = $field[0];
        $kid = substr($mcfg['pid'],0,1).'id'; if($kid=='uid') $kid='uname';
        $r = glbDBObj::dbObj()->table(glbDBExt::getTable($mod))->field($field)->where("$kid='$val'")->find(); 
        return empty($r[$field]) ? '' : $r[$field] ; 
    }
    
    // 获取字段(某种类型)
    static function vgetFields($fcfgs,$type='input,select',$dbtype='varchar'){
        $re = array();
        foreach($fcfgs as $k=>$v){
            if($type!='all' && !strstr($type,$v['type'])) continue;
            if($dbtype!='all' && !strstr($dbtype,$v['dbtype'])) continue;
            if(!empty($v['etab'])) continue;
            $re[$k] = $v['title'];    
        } 
        return $re;
    }
    // 检查字段(是否在可能的字段内)
    static function vchkFields($obj,$orgt){
        $org = array_keys($orgt);
        if(empty($obj)) return $orgt;
        $fields = is_string($obj) ? explode(',',$obj) : $obj;
        $re = array();
        foreach($fields as $k){
            if(in_array($k,$org)){
                $re[$k] = $orgt[$k];            
            }
        } 
        return $re;
    }
    // 排序字段()
    static function vordFields($obj){
        //$obj 
        foreach($obj as $k=>$v){
            $obj["$k-a"] = "$v".basLang::show('flow.dops_ordasc');    
        }
        return array_merge($obj,array('atime'=>basLang::show('flow.dops_ordtimd'),'atime-a'=>basLang::show('flow.dops_ordtima')));
    }
    
    // 表单默认值 showdef=1/0
    static function fmDefval($obj,$key='show',$def=''){
        $data = $obj->fmo;
        if(isset($data[$key])){
            return $data[$key];
        }else{
            $val = ''; $key = "{$key}def"; 
            $arr = basElm::text2arr(@$obj->cfg['cfgs']);
            return isset($arr[$key]) ? $arr[$key] : $def;
        }
    }
    
    // fmSafe使用
    static function fmSafe($pid=''){ 
        //$cfg,$aurl;
        //return $str;
    }
    // svSafe使用
    static function svSafe($pid=''){ 
        //$cfg,$aurl;
        //return $str;
    }    

    // 翻页条
    static function pageBar($pgbar,$opbar,$opname='(null)',$jsFunc='fmSelAll'){
        $opname = $opname=='(null)' ? basLang::show('flow.dops_exeu') : $opname;
        $pgbar = "<div class='pg_bar'>$pgbar</div>";
        $opstr = strpos($opbar,'</option>') ? "<select name='fs_do' class='w100'>$opbar</select>" : $opbar;
        $opbar = "<div class='w180 tc right'>$opstr";
        $opbar .= ($opname ? "<input name='bsend' class='btn' type='submit' value='$opname' />" : '')."</div>";
        echo "\n<tr><td class='tc' nowrap>\n";
        if($jsFunc) echo "<input name='fs_act' type='checkbox' class='rdcb' onClick='$jsFunc(this)' /></td>";
        echo "<td colspan='15'>$opbar$pgbar</td>\n";
        echo "\n</tr>";
    }

    // svFmtval。
    static function svFmtval($f,$mod,$k,$val){
        $fext = @$f[$k]['fmextra']; //参考fldView::fitem()
        if(is_array($val)) $val = implode(',',$val); //array
        if($fext=='editor'){
            $val = basReq::fmt($val,'','Html',2000123); //2000123=2M,  MEDIUMTEXT最大长度为16,777,215。
            $val = basReq::in($val);
        }elseif($fext=='datetm'){  
            $totime = strtotime(basReq::fmt($val,'1979-09-13'));
            $val = empty($val) ? $_SERVER["REQUEST_TIME"] : (is_numeric($val) ? $val : $totime); 
        }elseif($fext=='color'){
            $val = preg_replace('/[^0-9A-Fa-f]/','',$val);
        }elseif($fext=='map'){
            $val = preg_replace('/[^0-9,\.]/','',$val);
        }elseif($fext=='winpop'){
            $val = preg_replace('/[^0-9A-Za-z,]/','',$val);    
        }elseif($fext=='pics'){
            $val = basStr::filSafe4($val);     
        }elseif($fext=='pick'){
            $val = is_array($val) ? implode(',',$val) : $val;
            $val = basStr::filTitle($val);     
        }elseif(in_array($f[$k]['type'],array('select','cbox','radio'))){
            $val = is_array($val) ? implode(',',$val) : $val;
            $val = basStr::filTitle($val); 
        }elseif(in_array($f[$k]['type'],array('file'))){
            $val = str_replace(array('<','>','"',"'","\\","\r","\n",'*','|','?'),'',$val); 
        }elseif(in_array($f[$k]['dbtype'],array('int','float'))){
            $val = basReq::fmt($val,'0','N');
        }elseif($f[$k]['dbtype']=='file'){
            $val = basStr::filHtml($val); 
        }elseif($f[$k]['dbtype']=='text'){ 
            $val = basReq::fmt($val,'','Html',24123); //24K
            $val = basReq::in($val);
        }elseif($f[$k]['dbtype']=='varchar'){
            $val = basStr::filTitle($val);
        }else{
            $val = basReq::fmt($val,'','Html'); //255
            $val = basReq::in($val);
        }
        return $val;        
    }
    
    // modFile .. ???? 
    static function modFile($scbase,$mod,$type){ //模型脚本
        if(defined('RUN_ADMIN')){ //后台
            $udir = 'umod'; //用户扩展
            $edir = 'emod'; //系统扩展
            $ecfg = 'sc_fadm';
        }else{ //会员
            $udir = 'umou'; //用户扩展
            $edir = 'emod'; //系统扩展
            $ecfg = 'cs_fmem';
        }
        $re = '';
        require DIR_ROOT."/cfgs/scfile/$ecfg.php"; //$_scfile
        if(file_exists($_fex="$scbase/$udir/{$mod}.php")){
            $re = $_fex; 
        }elseif(file_exists($_fex="$scbase/$edir/{$mod}.php")){
            $re = $_fex; 
        }elseif(file_exists($_fex="$scbase/dops/{$type}_{$mod}.php")){
            $re = $_fex; 
        }elseif(isset($scfgs[$mod]) && file_exists($scbase.$scfgs[$mod])){
            $re = $scbase.$scfgs[$mod]; //以上配置:再次
        } 
        return $re;
    }
    
    // modAct .. ???? 
    static function modAct($scbase,$act,$mod,$type){ //act脚本
        if(defined('RUN_ADMIN')){ //后台
            $udir = 'uact';  //用户扩展
            $edir = 'eact'; //系统扩展
        }else{ //会员
            $udir = 'uacu'; //用户扩展 
            $edir = 'eacu'; //系统扩展
        }
        if(file_exists($_fex="$scbase/$udir/{$mod}_{$act}.php")){
            return $_fex; //扩展目录
        }elseif(file_exists($_fex="$scbase/$edir/{$mod}_{$act}.php")){
            return $_fex; //扩展脚本
        }elseif(file_exists($_fex="$scbase/dops/{$type}_{$act}.php")){
            return $_fex;
        }else{
            return '';    
        }
    }
    
}
