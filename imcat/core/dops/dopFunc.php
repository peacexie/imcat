<?php
namespace imcat;

// dopFunc : 基本操作 static函数
class dopFunc{    

    // 得到`默认模型`ID
    static function getDefmod($tab=''){
        $groups = glbConfig::read('groups');
        if($mod=basReq::val('mod')){
            return $mod;
        }elseif(isset($groups['news'])){
            return 'news';
        }elseif(isset($groups['cargo'])){
            return 'cargo';
        }elseif(isset($groups['about'])){
            return 'about';
        }else{
           return 'demo'; 
        }
    }

    // 得到`字段存文件`的内容
    static function getFsval($mod,$kid,$fid='cfile'){
        $cfdir = comStore::getResDir($mod,$kid,1,0)."/fs_$fid.data";
        $cfile = comFiles::get($cfdir);
        return $cfile; 
    }

    static function getMinfo($mod,$kid='',$fid=''){
        $tmp = glbDBExt::getTable($mod,'arr');
        $tab = $tmp[0]; $kid = $tmp[1];
        $fid || $fid = $kid;
        $info = glbDBObj::dbObj()->table($tab)->where("$fid='$kid'")->find(); 
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
            $url = PATH_ROOT."/plus/api/redir.php?$mod.{$kid}";
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
        $opstr = strpos($opbar,'</option>') ? "<select name='fs_do' class='form-control w100'>$opbar</select>" : $opbar;
        $opbar = "<div class='w180 tc right flgOpbar'>$opstr";
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
            $val = basStr::filTitle($val,array('%'));     
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
    static function modFile($mod,$type=''){ //模型脚本
        require DIR_ROOT."/cfgs/scfile/sc_fadm.php"; //sc_fadm/cs_fmem
        if(isset($scfgs[$mod])){
            $re = $scfgs[$mod];
        }elseif(file_exists($_fex=DIR_ROOT."/extra/emod/{$mod}.php")){
            $re = $_fex;
        }elseif(file_exists($_fex=DIR_IMCAT."/flow/emod/{$mod}.php")){
            $re = $_fex;
        }else{
            $re = ''; //DIR_IMCAT."/flow/dops/{$type}_{$mod}.php";
        }
        return $re;
    }
    
    // modAct .. ???? 
    static function modAct($act,$mod,$type){ //act脚本
        if(     file_exists($_fex=DIR_ROOT."/extra/eact/{$mod}_{$act}.php")){
            $re = $_fex;
        }elseif(file_exists($_fex=DIR_IMCAT."/flow/eact/{$mod}_{$act}.php")){
            $re = $_fex;
        }else{
            $re = DIR_IMCAT."/flow/dops/{$type}_{$act}.php";
        }
        return $re;
    }
    
}
