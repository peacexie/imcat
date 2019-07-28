<?php
namespace imcat;

// glbCUpd
class glbCUpd{    

    public static $_mitems = 72;//最大Item个数,超过用josn
    public static $_fields = 'kid,title,etab,type,dbtype,dblen,dbdef,vreg,vtip,vmax,fmsize,fmline,fmtitle,fmextra,fmexstr,cfgs';
    public static $_icons = array('1'=>'book','2'=>'folder-open-o','3'=>'file-text-o',);

    // upd rebuld
    static function upd_rebuld(){
        self::upd_groups();
        foreach($g1 as $k=>$v){
            if(in_array($k,array('score','sadm','smem','suser',))){ 
                self::upd_paras($k);
            }
            if($v['pid']=='groups') continue;
            //if($v['pid']=='types' && !empty($v['etab'])) continue; 
            self::upd_model($k); 
            self::upd_menus($mod);
        }
    }

    // upd config
    static function upd_groups(){
        $grps = glbDBObj::dbObj()->table('base_model')->where("enable=1")->order('pid,top,kid')->select(); 
        $str = '';
        foreach($grps as $k=>$v){
            $arr[$v['kid']] = array('pid'=>$v['pid'],'title'=>$v['title'],'top'=>$v['top'],); 
            $arr[$v['kid']]['etab'] = $v['etab']; //'docs','types'
            $arr[$v['kid']]['deep'] = $v['deep']; //'docs','types','menus','advs'
            foreach(array('pmod') as $k){ //'cfgs','pmod','cradd','crdel'
                if(!empty($v[$k])) $arr[$v['kid']][$k] = $v[$k];
            }
        }
        glbConfig::save($arr,'groups');
    }
    
    // upd grade
    static function upd_grade(){
        $_groups = glbConfig::read('groups');
        $list = glbDBObj::dbObj()->table('base_grade')->where("enable='1'")->order('model,top')->select(); 
        $arr = array();
        foreach($list as $k=>$v){ 
            $v['grade'] = $v['kid'];
            $dfs = array('top','enable','note','aip','atime','auser','eip','etime','euser');
            foreach($dfs as $fk) unset($v[$fk]);
            foreach($v as $vk=>$vv) if(!$vv) $v[$vk] = '';
            $arr[$v['kid']] = $v;
            //self::upd_ipfile($arr[$v['kid']]);
        }
        glbConfig::save($arr,'grade','dset'); 
    }
    
    // upd model
    static function upd_model($mod=0){ 
        $_groups = glbConfig::read('groups');
        if(empty($mod)) return;
        $v = glbDBObj::dbObj()->table('base_model')->where("kid='$mod'")->find(); 
        $arr = array('kid'=>$v['kid'],'pid'=>$v['pid'],'title'=>$v['title'],'enable'=>$v['enable']); 
        $arr['etab'] = $v['etab']; //'docs','types'
        $arr['deep'] = $v['deep']; //'docs','types','menus','advs'
        foreach(array('cfgs','pmod','cradd','crdel') as $k){
            if(!empty($v[$k])) $arr[$k] = $v[$k];
        } 
        if(in_array($v['pid'],array('docs','users','coms','types','score','sadm','smem','suser',))){ 
            $arr['f'] = self::upd_fields($mod);
        }
        if(in_array($v['pid'],array('docs','users'))){ 
            $ccfg = self::upd_cfield($mod);
            if(!empty($ccfg)) glbConfig::save($ccfg,"c_$mod",'modex');
        }
        $_cfg = array('advs'=>'itype','docs'=>'itype','users'=>'iuser','types'=>'itype','menus'=>'imenu');
        if(isset($_cfg[$v['pid']])){
            $func = 'upd_'.$_cfg[$v['pid']]; 
            $itms = self::$func($mod,$v);
            if(is_array($itms)){
                $arr['i'] = $itms; 
            }else{
                glbConfig::tmpItems($mod,$itms);
                $arr['i'] = "$mod"; 
            }
            if($v['pid']=='advs'){
                $arr['f'] = self::upd_afield($v);
            }
        }
        if(!empty($v['cfgs'])) $arr['cfgs']=$v['cfgs']; 
        glbConfig::save($arr,$mod);
    }
    static function upd_afield($cfg){     
        $f = glbConfig::read('fadvs','sy');
        if($cfg['etab']==1){ unset($f['detail'],$f['mpic']); }
        if($cfg['etab']==2){ unset($f['detail']); }
        if($cfg['etab']==3){ 
            unset($f['mpic']); 
            $f['url']['title'] = basLang::show('core.cupd_reprule');
            $f['url']['vreg'] = '';
            $f['url']['vtip'] = basLang::show('core.msg_eg').'{root}[=]http://txjia.com/<br>'.basLang::show('core.msg_or').'/path/[=]/peace/dev/imcat/root/';
        }
        return $f;
    }        
    // upd fields（考虑继承父级参数?）
    static function upd_cfield($mod=0){
        $f = array();
        $list = glbDBObj::dbObj()->field(self::$_fields.",catid")->table('bext_fields')->where("model='$mod' AND enable='1'")->order('catid,top')->select(); 
        foreach($list as $k=>$v){
            $cid = $v['kid']; $catid = $v['catid']; 
            foreach($v as $i=>$u){ //kid,top,cfgs
                $f[$catid][$cid][$i] = $u;
            } 
            if(!empty($v['cfgs'])) $f[$catid][$cid]['cfgs'] = $v['cfgs'];
            if(!empty($v['fmextra'])) $f[$catid][$cid]['fmextra'] = $v['fmextra'];
            if(!empty($v['fmexstr'])) $f[$catid][$cid]['fmexstr'] = $v['fmexstr'];
        }
        return $f;
    }
    
    // upd fields
    static function upd_fields($mod=0){
        $_groups = glbConfig::read('groups');
        if(isset($_groups[$mod]) && in_array($_groups[$mod]['pid'],array('docs','users','coms','types'))){ 
            $tabid = 'base_fields';
            $fields = self::$_fields;
        }else{
            $tabid = 'base_paras';
            $fields = self::$_fields.",`key`,val";
        }
        $f = array();
        $list = glbDBObj::dbObj()->field($fields)->table($tabid)->where("model='$mod' AND enable='1'")->order('top')->select(); 
        foreach($list as $k=>$v){
            $cid = $v['kid'];
            foreach($v as $i=>$u){ //kid,top,cfgs
                $f[$cid][$i] = $u;
            } 
            if(!empty($v['cfgs'])) $f[$cid]['cfgs'] = $v['cfgs'];
            if(!empty($v['fmextra'])) $f[$cid]['fmextra'] = $v['fmextra'];
            if(!empty($v['fmexstr'])) $f[$cid]['fmexstr'] = $v['fmexstr'];
        }
        return $f;
    }
    
    // upd itype,icatalog
    static function upd_itype($mod,$cfg,$pid=0){
        $db = glbDBObj::dbObj();
        if(in_array($cfg['pid'],array('docs','advs'))){
            $tabid = 'base_catalog';
        }else{
            $tabid = (empty($cfg['etab']) ? 'types_common' : 'types_'.$mod);
        }
        $filed = 'kid,pid,title,deep,frame,`char`,cfgs';
        if(strstr($cfg['cfgs'],'cats=')){
            preg_match("/cats=(\w+)/",$cfg['cfgs'],$pts);
            $mod = $pts[1]; //dump($cfg); die('xx');
        }
        $where = "model='$mod' AND enable='1'";
        $arr = $db->field($filed)->table($tabid)->where($where)->order('deep,top,kid')->select();
        $res = comTypes::arrLays($arr,self::$_mitems);
        return $res;
    }
    // upd iuser
    static function upd_iuser($mod,$cfg,$pid=0){
        $db = glbDBObj::dbObj();
        $tabid = 'base_grade'; 
        $arr = array(); 
        $where = "model='$mod' AND enable='1'";
        $list = $db->field('kid,title')->table($tabid)->where($where)->order('top')->select();
        if($list){
        foreach($list as $v){
          $k = $v['kid']; 
          if(!empty($v['cfgs'])) $arr[$k]['cfgs'] = $v['cfgs'];
          $arr[$k] = $v;
        }}
        return $arr;
    }
    // upd imenu
    static function upd_imenu($mod,$cfg,$pid=0){
        $db = glbDBObj::dbObj();
        $tabid = 'base_menu';
        $fileds = 'kid,pid,title,icon,deep,cfgs';
        $where = "model='$mod' AND enable='1'";
        $arr = $db->field($fileds)->table($tabid)->where($where)->order('deep,top')->select();
        $res = comTypes::arrLays($arr,self::$_mitems);
        return $res;
    }
    
    // upd relat
    static function upd_relat(){ 
        $list = glbDBObj::dbObj()->table('bext_relat')->order('top,kid')->select(); 
        $re = array();
        foreach($list as $r){
            $kid = $r['kid'];
            $re[$kid] = array();
            foreach($r as $k=>$v){
                if(in_array($k,array('mod1','mod2','title','note'))){
                    $re[$kid][$k] = $v;
                }
            }
            glbConfig::tmpItems($kid,basElm::text2arr($r['cfgs']));
        }
        glbConfig::tmpItems('relat',$re);
        return $re;
    }
    
    static function upd_paras($pid, $re='save'){ 
        global $_cbase;
        $_groups = glbConfig::read('groups');
        $str = ''; $arr = array();
        foreach($_groups as $k=>$v){ 
            if($v['pid']==$pid){ 
                $cfg = glbConfig::read($k);
                if(empty($cfg['f'])) continue;
                foreach($cfg['f'] as $k2=>$v2){
                    $k3 = strstr($v2['key'],'[') ? str_replace(array('[',']'),array("['","']"),$v2['key']) : "['".$v2['key']."']";
                    $res = glbDBObj::dbObj()->table('base_paras')->where("kid='$k2'")->find();
                    $val = str_replace(array('"',"\\"),array("\\\"","\\\\"),$res['val']);
                    $str .= "\n\$_cbase$k3 = \"$val\";";
                    $arr[$k2] = $res['val'];
                    if(isset($v2['kid']) && substr($v2['kid'],0,5)=='safe_'){
                        $_sk = substr($v2['kid'],5);
                        $_cbase['run']['_safe'][$_sk] = $val;
                    }
                }
                $str .= "\n";
            }    
        } 
        if($re=='save'){
            glbConfig::save($str,$pid,'dset');
        }else{
            return $arr;    
        }
    }
    static function upd_menus($mod,$cfg=array()){ 
        if(empty($cfg)) $cfg = glbConfig::read($mod);
        if($mod=='muadm'){ 
            $s0 = $s1 =  $js1 = $js2 = $js3 = '';
            $mperm = array(); 
            foreach($cfg['i'] as $k1=>$v1){ 
                if(!empty($v1['cfgs']) && strstr($v1['cfgs'],'?mkv')){
                    $mperm[$k1] = self::upd_imperm($v1['cfgs']);
                }
                if($v1['deep']=='1'){
                    $icon = " <i class='fa fa-".(empty($v1['icon']) ? self::$_icons[1] : $v1['icon'])."'></i>";
                    $s1 .= "<a class=\"atm_$k1\" onclick=\"admSetTab('$k1')\">$icon $v1[title]</a>";
                    $js1 .= ",$k1";
                    $js2 .= ",$v1[title]";
                    $js3 .= ",$v1[icon]";
                    $s0 .= "<div id='left_$k1'>";
                    if(method_exists('\\imcat\\exaFunc',$func="admenu_$k1")){ exaFunc::$func($s0); }
                    elseif($k1=='m1adv'){ self::upd_madvs($s0); }
                    else{ self::upd_mitms($s0,$cfg,$k1); }
                    $s0 .= "</div>";
                }
            }
            $data = "\nvar admNavTab = '$js1';";
            $data .= "\nvar admNavName = '$js2';";
            $data .= "\nvar admNavIcon = '$js3';";
            $data .= "\nvar admHtmTop = '".basJscss::jsShow($s1,0)."';";
            $data .= "\nvar admHtmLeft = '".basJscss::jsShow($s0,0)."';";
            // $data .= "\ndocument.write(admHtmTop);";
            glbConfig::save($data,"{$mod}",'dset','.js');
            glbConfig::save($mperm,"{$mod}_perm",'dset'); 
            return $s0;
        }
        
    }
    
    static function upd_madvs(&$s0){ //按栏目显示菜单项
        $_groups = glbConfig::read('groups');
        foreach($_groups as $k2=>$v2){ 
        if($v2['pid']=='advs'){
            $cfg = glbConfig::read($k2);
            $icon = "<i class='fa fa-".self::$_icons[2]."'></i>";
            $s0 .= "<ul class='adf_mnu2' id='left_$k2'>";
            $s0 .= "<li class='adf_dir'>$icon <a href='?dops-a&amp;mod=$k2' target='adf_main'>$v2[title]</a></li>";
            foreach($cfg['i'] as $k3=>$v3){ 
            if(empty($v3['pid'])){ //顶级
                $icon = "<i class='fa fa-".self::$_icons[3]."'></i>";
                $s0 .= "<li id='left_$k3'>$icon ";
                $s0 .= "<a href='?dops-a&amp;mod=$k2&stype=$k3' target='adf_main'>{$v3['title']}</a> - ";
                $s0 .= "<a onclick=\"admJsClick(this)\">".basLang::show('core.msg_add')."</a></li>";
            }}
            $s0 .= "</ul>";
        }}
    }
    static function upd_mitms(&$s0,$cfg,$k1){ //后台配置的菜单项
        foreach($cfg['i'] as $k2=>$v2){ 
        if($v2['pid']==$k1){
            $icon = " <i class='fa fa-".(empty($v2['icon']) ? self::$_icons[$v2['deep']] : $v2['icon'])."'></i>";
            $s0 .= "<ul class='adf_mnu2' id='left_$k2'>";
            $s0 .= "<li class='adf_dir'>$icon $v2[title]</li>";
            foreach($cfg['i'] as $k3=>$v3){ 
            if($v3['pid']==$k2){
                $icon = " <i class='fa fa-".(empty($v3['icon']) ? self::$_icons[$v3['deep']] : $v3['icon'])."'></i>";
                $s0 .= "<li id='left_$k3'>$icon ";
                $t = self::upd_mlink($v3);
                $s0 .= "$t</li>";
            }}
            $s0 .= "</ul>";
        }}
    }
    static function upd_mlink($v3){ //处理一项链接
        $t = str_replace(array('{root}','{$root}',),array(PATH_PROJ,PATH_PROJ,),$v3['cfgs']);
        if(strstr($t,'</a>')){
            if(!strstr($t,'target=')){
                $t = str_replace("<a","<a target='adf_main'",$t);
            }
        }elseif(strstr($t,'(!)')){ //站点介绍(!)?dops-a&mod=about(!)frame|blank|jsadd
            $ta = basElm::line2arr($t); $t = '';
            foreach($ta as $row){
                $tb = explode("(!)","$row(!)(!)");
                if(strstr($tb[2],'frame')){
                    $_u = str_replace('?','?#',$tb[1]);
                    $t .= (empty($t) ? '' : ' - ')."<a href='$_u' target='_frame'>$tb[0]</a>";
                }elseif(strstr($tb[2],'blank')){
                    $t .= (empty($t) ? '' : ' - ')."<a href='$tb[1]' target='_blank'>$tb[0]</a>";
                }elseif(strstr($tb[2],'jsadd')){
                    $t .= (empty($t) ? '' : ' - ')."<a onClick=\"admJsClick(this)\">$tb[0]</a>";
                }else{
                    $t .= (empty($t) ? '' : ' - ')."<a href='$tb[1]' target='adf_main'>$tb[0]</a>";    
                }
            } 
        }else{
            $t = "<a href='$t' target='adf_main'>$v3[title]</a>";
        }
        return $t;
    }
    static function upd_imperm($cfgs){
        preg_match_all("/\?([\w|\/|\-]{5,36})/i",$cfgs,$ma);
        if(!empty($ma[1])){
            $rea = array_unique($ma[1]);
            $re = implode(',',$rea);
        }
        return empty($re) ? array() : $re;
    }
    /*static function upd_ipfile(&$icfg){
        static $_mpm;
        if(empty($_mpm)){
            $_mpm = glbConfig::read('muadm_perm','dset'); 
        }
        $pmadm =  $icfg['pmadm']; 
        $pfile = ",{$icfg['pfile']}";
        $a = explode(',',$pmadm); 
        if(!empty($a)){
            foreach($a as $k){
                if(!empty($_mpm[$k]) && !strstr($pfile,$_mpm[$k])) $pfile .= ",$_mpm[$k]";
            }
        }
        $icfg['pfile'] = str_replace(array(',,,',',,'),',',"$pfile,");
    }*/

    // 
    static function upd_parex($pid){
        $db = glbDBObj::dbObj();
        $tabid = 'bext_paras';
        $keys = array('pid','title','detail','numa','numb','cfgs','note');
        $arr = array();
        $list = $db->table($tabid)->where("pid='$pid' AND enable='1'")->order('top')->select(); 
        foreach($list as $v){ 
            $kid = str_replace('-','_',$v['kid']);
            foreach($keys as $k){
                $arr[$kid][$k] = $v[$k];
            }
        }
        if(!empty($arr)) glbConfig::save($arr,"parex_$pid",'dset');
    }
    
}
