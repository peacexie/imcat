<?php

class fldCfgs{

    // 字段类型
    static function viewTypes(){
        return basLang::ucfg('cfglibs.fcfg_type');
    }
    
    // 字段插件
    static function viewPlugs(){
        return basLang::ucfg('cfglibs.fcfg_plug');        
    }

    // 数据类型
    static function dbTypes(){
        return basLang::ucfg('cfglibs.fcfg_dbtype');        
    }
    
    // 字段认证
    static function regTypes(){
        return basLang::ucfg('cfglibs.fcfg_vreg');    
    }
    
    // 添加参数
    static function addParas(){ 
        return array('type','fmextra','etab','kid','from');
    }
    
    
    
    // -------------------------------------
    
    // 保留字段
    static function setKeeps($key=''){
        $arr_0 = array('info','cfgs','fields','items','catalog','files');
        $arr_n = array_keys(basLang::ucfg('fsystem'));
        $arr_f = glbConfig::read('fkeywd','sy'); 
        return array_merge($arr_0,$arr_n,$arr_f);
    }
    
    // ==================================================================== 
    
    /* 'ename'=>array('title'=>'英文名','etab'=>'0','type'=>'input','enable'=>'0','vmax'=>'96',
           'vreg'=>'str:2-60','vtip'=>'标题2-60字符','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
    // mymap|map|地图^varchar|255|-|str:|2|255 // nul:fix:image // tit:2-60 */
    static function addPick($mod,$re='str'){
        $_groups = glbConfig::read('groups'); 
        $mpid = $_groups[$mod]['pid']; 
        $ademo = self::addDemo('init_docs')+self::addDemo('init_dext')+self::addDemo('init_coms')+self::addDemo('init_users'); 
        $list = glbDBObj::dbObj()->table('base_fields')->where("model='$mod'")->select();
        $amods = array(-1); $b = array(); $s = ' &nbsp; '.basLang::show('flow.fc_rftype'); $a = array();
        if($list) foreach($list as $r) $amods[] = $r['kid'];
        foreach($_groups as $k=>$v){ 
        if($v['pid']=='types'){
            if(in_array("type_$k",$amods)) continue;
            $data = "$k|input|winpop|0";
            $s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v[title]</a>\r\n";
        }}
        $s .= "<br> &nbsp; ".basLang::show('flow.fc_rffield');
        foreach($ademo as $k=>$v){
            if(in_array($k,$amods)) continue;
            if(!in_array($k,$a)){
                $data = "$k|$v[type]|".@$v['fmextra']."|".@$v['etab'].""; 
                $s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v[title]</a>\r\n";
                $a[] = $k;
            }
        }
        return $s;
        //return $re=='str' ? $s : $a;
    }
    // 'exp_t01'=>'扩展参数-text-1', //<a href='#' onclick="gf_setDemoField('t400|input||0')" class='span'>XXXX</a>
    static function addType($mod,$catid){
        $ccfg = glbConfig::read($mod,'_c');
        $flist = basLang::ucfg('fsystem'); 
        $s = ' &nbsp; '.basLang::show('flow.fc_rffield');
        foreach($flist as $k=>$v){
            if(strstr($k,'exp_')){
                $a = explode('-',$v);
                $type = str_replace("checkbox","cbox",$a[1]);
                $data = "$k|$type||0";
                if(isset($ccfg[$catid][$k])){
                    $s .= " | <i class='span'>$v</i>\r\n";
                }else{
                    $s .= " | <a href='#' onclick=\"gf_setDemoField('$data')\" class='span'>$v</a>\r\n";
                }
            }
        }
        return $s;
    }
    static function addDemo($mod){
        $tmp = glbConfig::read('fdemo','sy');
        return $tmp[$mod];
    
    }
    
    static function getSizeArray($cfg=array()){
        if(empty($cfg['fmsize'])){
            $size = array();
        }elseif(strpos($cfg['fmsize'],'.')){ //news.8
            $size = explode('.',$cfg['fmsize']);
        }else{ //360x8
            $size = explode('x',$cfg['fmsize'].'x');    
        }
        return $size;
    }
    
}