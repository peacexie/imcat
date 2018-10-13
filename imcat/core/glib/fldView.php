<?php
namespace imcat;

// Fields
class fldView{    

    // 认证str
    static function mkpar($mod='',$kid=''){
        global $_cbase;
        $groups = glbConfig::read('groups');
        $mod = $mod ? $mod : basReq::val('mod');
        $mod = $mod ? $mod : $_cbase['run']['dopmod'];
        $pid = $groups[$mod]['pid'];
        $smod = $mod ? "&mod=$mod" : "&mod=$mod"; 
        $skid = $kid ? "&kid=$kid" : "&kid=".basReq::val(substr($pid,0,1).'id')."";
        return "$smod$skid";
    }
    
    // 认证str
    static function vstr($cfg,$size){
        $vstr = ''; 
        $tmp = explode('(|)',$cfg['vtip'].'(|)');
        $cfg['vtip'] = $tmp[0]; 
        if(!empty($cfg['vmax'])) $vstr .= " maxlength='$cfg[vmax]' ";
        if(!empty($cfg['vreg'])) $vstr .= " reg='$cfg[vreg]' ";
        if(!empty($cfg['vtip'])) $vstr .= " tip='$cfg[vtip]' ";
        if(isset($size[0])){
            $vstr .= " style='width:$size[0]px'; ";
        }
        return array($vstr,$tmp[1]);
    }
    // 编辑器-格式化(um不要格式化的)
    static function iedifmt($val){
        global $_cbase;
        $eid = empty($_cbase['sys_editor']) ? 'kind' : $_cbase['sys_editor'];
        if($eid=='um'){
            return $val;
        }else{
            return basStr::filForm($val);
        }
    }
    // 编辑器
    static function ieditor($k,$cfg,$val,$size,$vstr){ 
        global $_cbase;
        $val = comStore::revSaveDir($val,1);
        $item = '';    $smk = self::mkpar();
        $sys_editor = $_cbase['sys_editor'];
        $eid = empty($sys_editor) ? 'kind' : $sys_editor;
        echo basJscss::imp(PATH_BASE."?editor-api_$eid&eid=$eid$smk",'','js'); //&lang=
        $size = str_replace('x',',',$cfg['fmsize']); if(empty($size)) $size = '480x120';
        $bsbar = strstr(@$cfg['fmexstr'],'full') ? 'full' : 'base';
        $item = strstr(@$cfg['fmexstr'],'exbar') ? "<div id='fm_{$k}_bar' class='edt_bar'></div>" : '';
        if($eid=='um'){
            $item .= basJscss::imp('/edt_um/themes/default/css/umeditor.css','vendui');
            $item .= "<textarea id='fm[$k]' name='fm[$k]' style='display:none;'></textarea>\n";
            $item .= "<script type='text/plain' id='fm_{$k}_' name='fm[$k]' style='width:{$cfg['fmsize'][0]}px;height:{$cfg['fmsize'][1]}px;'>".@$val."</script>\n";
        }else{
            $item .= "<textarea id='fm_{$k}_' name='fm[$k]'>".@$val."</textarea>\n";
        }
        $item .= basJscss::jscode("var editor_fm_{$k}_; $().ready(function(){edt_Init('fm[$k]','$bsbar',$size);});\n"); 
        return $item;
    }
    // 日期时间
    static function idatetm($k,$cfg,$val,$size,$vstr,$iinp){
        $item = '';    
        echo basJscss::imp('/My97DatePicker/WdatePicker.js','vendui'); 
        $fmt1 = empty($cfg['fmexstr']) ? 'Y-m-d' : $cfg['fmexstr'];
        $fmt2 = empty($fmt1) ? '' : ",dateFmt:'".str_replace(array('Y','m','d','H','i','s',),array('yyyy','MM','dd','HH','mm','ss',),$fmt1)."'"; 
        $val = empty($val) ? '' : date($fmt1,$val); 
        $iinp = "<input id='fm[$k]' name='fm[$k]' type='text' value='$val' class='txt' $vstr />";
        $item = "$iinp<span class='fldicon fdate' onClick=\"WdatePicker({el:'fm[$k]'$fmt2})\" /></span>";
        return $item;
    }
    // 类别pop选择
    static function iwinpop($k,$cfg,$val,$size,$vreg){
        global $_cbase; 
        $item = '';    
        $_md = empty($cfg['fmexstr']) ? 'china' : $cfg['fmexstr'];
        $_w = empty($size[0]) ? 420 : $size[0];
        $_n = empty($size[1]) ? 1 : $size[1];
        $_cfg = empty($cfg['cfgs']) ? '' : ",'{".$cfg['cfgs']."}'";
        $item = "<span id='fm_{$k}__pop'>$vreg</span>";
        $_cbase['run']['jtype_mods'] .= ",$_md";
        $_cbase['run']['jtype_init'] .= "var fm_{$k}__obj; popInit('fm[{$k}]','$_md',$_w,$_n,'$val'$_cfg);\n";
        return $item;
    }
    // file
    static function ifile($k,$cfg,$val,$size,$vstr,$mod='',$kid=''){
        global $_cbase;
        $val = comStore::revSaveDir($val);
        $item = '';    $smk = self::mkpar($mod,$kid);
        $ticon = comFiles::getTIcon($val);
        $id = $jsAct = $simg = '';
        if($ticon['icon']=='pic'){
            $id = $k.'_'.basename($val); $id = str_replace('.','___',str_replace('-','_',$id));
            $jsAct = " onmouseover=\"$('#$id').removeClass('idHidden');$('#$id').addClass('idShow');\" onmouseout=\"$('#$id').removeClass('idShow');$('#$id').addClass('idHidden');\" ";    
            $simg = "<br><span class='idHidden' id='$id'><img src='$val' onload='imgShow(this,360,240)' border='0' /></span>";
        }
        $icjs = self::ifpcjs();
        $ijs = $icjs ? $_cbase['run']['pm_upsize1'] : '';
        $item .= "<input id='fm_{$k}_' name='fm[{$k}]' type='text' value='$val' class='file' $vstr $jsAct>";
        $item .= self::ifpinp($k,$smk).$simg.$icjs;
        $item .= basJscss::jscode("$(function(){ $ijs;fup_jqui('fm_{$k}_',1);})");
        return $item;
    }
    static function ifpinp($k,$smk,$multi=''){
        $clear = $multi ? "mpic_clear('fm_{$k}_');" : "$('#fm_{$k}_').val('');";
        $item  = "<input type='file' id='fm_{$k}_f' name='fm_{$k}_files' $multi style='display:none;'>";
        $item .= "<input type='button' value='".basLang::show('admin.fv_upload')."' id='fm_{$k}_b'>";
        $item .= "<input type='button' value='".basLang::show('admin.fv_view')."' onclick=\"winOpen('".PATH_BASE."?file-fview&fid=fm_{$k}_$smk','".basLang::show('admin.fv_vfiles')."',720,560)\">"; 
        $item .= "<input type='button' value='".basLang::show('admin.fv_clear')."' onClick=\"$clear\">";
        return $item;
    }
    static function ifpcjs(){
        global $_cbase;
        if(!empty($_cbase['run']['pm_upsize1'])){
            return ''; 
        }
        // perm
        $user = user(array('Admin','Member'));
        $ucfg = usrPerm::pmUpload($user); // allowFiles => $ucfg['uptypes'], 
        $upsize1 = $ucfg['upsize1']=='(supper)'?120123:$ucfg['upsize1'];
        $_cbase['run']['pm_upsize1'] = "_cbase.run.pm_upsize1='$upsize1'; ";
        // js/css # /jqui/jquery-fileupload.css
        $item  = basJscss::imp("/base/assets/cssjs/multpic.js");
        $item .= basJscss::imp("/jqui/jquery-ui.min.js",'vendui');
        $item .= basJscss::imp("/jqui/jquery-fileupload.js",'vendui');
        return $item;
    }
    // pics
    static function ipics($k,$cfg,$val,$size,$vstr,$mod='',$kid=''){
        $val = comStore::revSaveDir($val);
        $cfg['cfgs'] = empty($cfg['cfgs']) ? '' : basElm::arr2text($cfg['cfgs'],';','|');
        $item = ''; $smk = self::mkpar($mod,$kid);
        $icjs = self::ifpcjs();
        $ijs = $icjs ? $_cbase['run']['pm_upsize1'] : '';
        $item .= "<div id='fm_{$k}_out' class='mpic_out'>"; 
        $item .= "<div id='fm_{$k}_show'>{$cfg['cfgs']}</div>"; 
        $item .= "<div id='fm_{$k}_tarea' class='clear'><textarea name='fm[$k]' id='fm_{$k}_' style='display:none;'>$val</textarea></div>"; 
        $item .= self::ifpinp($k,$smk,'multiple');
        $item .= "</div>$icjs"; 
        $item .= basJscss::jscode("$(function(){ $ijs;mpic_minit('fm_{$k}_');fup_jqui('fm_{$k}_',99);})");
        return $item;
    }
    static function ipick($k,$cfg,$val,$size,$vstr,$mod='',$kid=''){
        $pmod = empty($size[0]) ? '' : $size[0];
        $pcnt = empty($size[1]) ? 1 : intval($size[1]);
        $para = "type='checkbox' class='rdcb' checked"; $item = '';
        if($val){
            $arr = explode(',',$val);
            foreach($arr as $v){
                $title = dopFunc::vgetTitle($pmod,$v); 
                $item .= "<span class='ph5'><input name='fm[{$k}][]' onClick='pickMul(this,1)' value='$v' $para />$title</span>";
            }
        }
        $item = "<div id='fm_{$k}_refname'>$item</div><input name='fm_{$k}_modpicks' id='fm_{$k}_modpicks' type='hidden' value='$pmod'>";
        $item .= "<input type='button' value='".basLang::show('admin.fv_pick')."' onclick=\"pickOpen('fm_{$k}_modpicks','','fm[{$k}]','fm_{$k}_refname',$pcnt)\" class='btn'>";  
        // pmod,cnt, $ptitle = dopFunc::vgetTitle($pmod,$val); 
        return $item;
    }

    // fields-公共函数
    // form: item，一项的内容。
    static function fitem($k,$cfg,$vals=array()){
        global $_cbase;
        $_groups = glbConfig::read('groups');
        $size = fldCfgs::getSizeArray($cfg);
        $tmp = self::vstr($cfg,$size); 
        $vstr = $tmp[0]; $vmsg = $tmp[1]; $item = '';
        $val = isset($vals[$k]) ? self::iedifmt($vals[$k]) : (isset($cfg['dbdef']) ? $cfg['dbdef'] : '');
        if($val==='' && !empty($cfg['dbdef'])) $val = $cfg['dbdef'];
        $iinp = "<input id='fm[$k]' name='fm[$k]' type='text' value='$val' class='txt' $vstr />";
        $ihid = "<input id='fm[$k]' name='fm[$k]' type='hidden' value='$val' />";
        $extra = @$cfg['fmextra']; //参考dopBase::chkFields()
        if($extra=='editor'){ //编辑器
            $item = self::ieditor($k,$cfg,$val,$size,$vstr);
        }elseif($extra=='datetm'){ //日期
            $item = self::idatetm($k,$cfg,$val,$size,$vstr,$iinp);
        }elseif($extra=='color'){ //颜色设置
            $_fid = empty($cfg['fmexstr']) ? 'title' : $cfg['fmexstr'];
            $item = "$ihid<span class='fldicon fcolor' onClick=\"colorPick('fm[$k]','fm[$_fid]')\">&nbsp;</span>";
            $item .= "<span id='fm[$k]_pop' class='color_out' style='display:none'></span>";
            if(strlen($val)>2) { $item .= basJscss::jscode("colorSet('$val','$_fid')"); } 
        }elseif($extra=='map'){ //地图
            $sys_map = $_cbase['sys_map'];
            $mpid = empty($sys_map) ? 'baidu' : $sys_map;
            $item = "$iinp<span class='fldicon fmap' onClick=\"mapPick('$mpid','fm[$k]');\">&nbsp;</span>";
        }elseif($extra=='repeat'){ //检查重名 
            $act = "onclick=\"repeatCheck('".str_replace(',',"','",@$cfg['cfgs'])."','$k');\""; ;
            $item = "$ihid <input type='button' value='".basLang::show('admin.fv_chkrep')."' id='fm_repeat_$k' $act class='btn'> ";
        }elseif($extra=='winpop'){ //winpop
            $item = self::iwinpop($k,$cfg,$val,$size,@$cfg['vreg']);
        }elseif($extra=='pics'){ //pics
            $item = self::ipics($k,$cfg,$val,$size,$vstr); 
        }elseif($extra=='pick'){ //pick
            $item = self::ipick($k,$cfg,$val,$size,$vstr); 
        }elseif($cfg['type']=='hidden'){ //隐藏
            $item = $ihid;
        }elseif($cfg['type']=='passwd'){
            $item = "<input id='fm[$k]' name='fm[$k]' type='password' autocomplete='off' class='txt' $vstr />";
        }elseif($cfg['type']=='select' && isset($_groups[@$cfg['cfgs']])){ //类别选择
            $item = comTypes::getOpt($cfg['cfgs'],$val);
            $item = "<select id='fm[$k]' name='fm[$k]' type='text' $vstr >$item</select>";
        }elseif($cfg['type']=='select'){ 
            $item = "<select id='fm[$k]' name='fm[$k]' type='text' $vstr >";
            $item .= basElm::setOption(@$cfg['cfgs'],$val)."</select>"; 
        }elseif($cfg['type']=='cbox'){
            $item = basElm:: setCBox($k,$cfg['cfgs'],$val); 
        }elseif($cfg['type']=='radio'){
            $item = basElm::setRadio($k,$cfg['cfgs'],$val);  
        }elseif($cfg['type']=='text'){
            $rows = empty($size[1]) ? '5' : $size[1]; //wrap='off' 
            $item = "<textarea id='fm[$k]' name='fm[$k]' rows='$rows' class='txt' $vstr/>".$val."</textarea>"; 
        }elseif($cfg['type']=='file'){    
            $item = self::ifile($k,$cfg,$val,$size,$vstr);
        }else{
            $item = $iinp;
        } 
        $item .= $vmsg;
        if(empty($cfg['fmline']) && !empty($cfg['fmtitle'])) $item = $cfg['title'].$item; 
        return $item;
    }
    // form: (增加/修改):字段列表
    // 处理分组等。
    static function lists($mod,$vals=array(),$catid='',$ufields=array()){
        global $_cbase;
        if(!empty($ufields)){
            $mfields = $ufields; 
        }elseif($catid){ 
            $ccfg = glbConfig::read($mod,'_c'); 
            if(empty($ccfg[$catid])) return;
            $mfields = $ccfg[$catid]; 
        }else{
            ${"_$mod"} = glbConfig::read($mod); 
            $mfields = ${"_$mod"}['f']; 
        }
        $skip = array('0');
        $_cbase['run']['jtype_mods'] = '';
        $_cbase['run']['jtype_init'] = '';
        foreach($mfields as $k=>$v){ 
            if($v['type']=='parts'){ //(分段字段...)
                echo "<tr><th>$v[title]</th><th class='tr'>$v[vtip]</th></tr>\n";
                continue;
            }elseif(!in_array($k,$skip)){
                $item = self::fitem($k,$v,$vals);
                $item = self::fnext($mfields,$k,$vals,$item,$skip);
                glbHtml::fmae_row($v['title'],$item);
            }
        }
        if(!empty($_cbase['run']['jtype_mods'])){
            $jpath = PATH_BASE."?ajax-comjs&act=jsTypes&mod=".$_cbase['run']['jtype_mods']."";
            echo basJscss::jscode("jQuery.getScript('$jpath',function(){\n".$_cbase['run']['jtype_init']."})"); 
        }    
    }
    // form: next field
    static function fnext($mfields,$k,$vals,$item,&$skip){
        $nkey = basArray::nextKey($mfields,$k);
        if($nkey && (empty($mfields[$nkey]['fmline']) || $mfields[$nkey]['type']=='hidden')){ 
            $item .= "\n &nbsp; ".self::fitem($nkey,$mfields[$nkey],$vals);
            $skip[] = $nkey;
            return self::fnext($mfields,$nkey,$vals,$item,$skip);
        }else{
            return $item;
        }
    }
    
    // form: relat : 
    // ::relat("relpb,fm[catid],fm[brand]","fm[xinghao],$mod,$did")
    // ::relat("fm[grade]","fm[ftype],$mod,$uname,fm[grade]"); 
    static function relat($relat,$exchg=''){
        $jcmd = "relInit('".str_replace(",","','",$relat)."');";
        $jchg = "relCatid('".str_replace(",","','",$exchg)."');";
        if(strpos($relat,',')){ //relat+fields
            $tmpa = explode(',',$relat);
            $jpath = PATH_BASE."?ajax-comjs&act=jsRelat&mod=$tmpa[0]";
            $jstr = "\n $jcmd $jchg \$(jsElm.jeID('{$tmpa[1]}')).change(function(){ $jcmd $jchg });";
            $jstr = "jQuery.getScript('$jpath',function(){ {$jstr} })";
        }else{ //fields
            $jstr = "\n $jchg \$(jsElm.jeID('$relat')).change(function(){ $jchg });";
        }
        echo basJscss::jscode($jstr);
    }
}

//jQuery.getScript('/index.php?ajax-comjs&act=jsRelat&mod=relyc',function(){
//relInit('relyc','fm[ygrade]','fm[course]');
//$(jsElm.jeID('fm[ygrade]')).change(function(){ relInit('relyc','fm[ygrade]','fm[course]'); });
//});
