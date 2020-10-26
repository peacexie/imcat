<?php
namespace imcat;

class fldEdit{

    //public $cfg = array();
    
    // 多级下拉(多选未处理?)
    static function layTypes($mod, $key, $val='', $deep=5, $title='-(def)-'){
        $title = $title=='-(def)-' ? basLang::show('core.opt_first') : $title;
        $res = "\n<input name='fm[$key]' id='fm_{$key}_' type='hidden' value='$val' />";
        for($i=1;$i<=$deep;$i++){
            $acts = $i>1 ? "style='display:none'" : '';
            $acts .= " onChange=\"laySet('$mod','$key',this)\" ";
            $res .= "\n<select id='lt_{$key}_{$i}' $acts><option value=''>-$title-</option></select>";
        } // layTypes.init(mod,key)
        return $res;
    }

    static function fmOrgData($tabid,$mod,$kid,$fm=array(),$catid=''){ 
        $_groups = glbConfig::read('groups'); 
        if(empty($kid)){
            if(!empty($fm['from'])){
                $ademo = fldCfgs::addDemo('init_docs')+fldCfgs::addDemo('init_dext')+fldCfgs::addDemo('init_coms')+fldCfgs::addDemo('init_users');
            }
            $def = array('title'=>'','dbtype'=>'varchar','val'=>'','key'=>''); 
            $from = isset($ademo[$fm['from']]) ? $ademo[$fm['from']] : array();
            if(isset($_groups[$fm['from']])){
                $from['title'] = $_groups[$fm['from']]['title'];
            } //如果键名为字符，且键名相同，数组相加会将最先出现的值作为结果
            $fm = $fm + $from + $def;
        }else{
            $cawhr = ($catid) ? "AND catid='$catid'" : ""; 
            $fm = glbDBObj::dbObj()->table($tabid)->where("model='$mod' AND kid='$kid' $cawhr")->find(); 
        } 
        return $fm;
    }
    
    function __construct($mod,$cfg=array()){ 
        $_groups = glbConfig::read('groups');
        $this->mod = $mod;
        $this->pmod = $_groups[$mod]['pid'];
        $this->ispara = basReq::val('ispara','0');
        $this->cfg = $cfg; 
        $this->type = $cfg['type'];
        $this->fmextra = $cfg['fmextra'];
        $this->etab = empty($cfg['etab']) ? 0 : 1;
    } 
    // iTypeOpts
    function fmTypeOpts(){ 
        $types = fldCfgs::viewTypes();
        $plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$this->fmextra]) ? $plugs[$this->fmextra] : basLang::show('admin.fe_noctrl');
        $etab = empty($this->etab)? basLang::show('admin.fe_mtab') : basLang::show('admin.fe_extab');
        $row  = "\n<select name='fm[type]'    class='w90'><option value='{$this->type}'>".$types[$this->type]."</option></select>";
        $row .= "\n<select name='fm[fmextra]' class='w90'><option value='{$this->fmextra}'>$plugstr</option></select>";
        $row .= "\n<select name='fm[etab]'    class='w90'><option value='{$this->etab}'>$etab</option></select>";
        glbHtml::fmae_row(basLang::show('admin.fe_ftype'),$row);
    }
    
    // iPlusPara
    function fmPlusPara(){ 
        $_groups = glbConfig::read('groups'); 
        $oldval = empty($this->cfg['fmexstr']) ? '' : $this->cfg['fmexstr'];
        if($this->fmextra=='winpop'){
            if(empty($oldval) && isset($_groups[$this->cfg['from']])){
                $oldval = $this->cfg['from'];
                //if(empty($this->cfg['title'])) $this->cfg['title'] = $_groups[$oldval]['title'];
            }    
            $arr = array(); 
            foreach($_groups as $k=>$v){
                if($v['pid']=='types') $arr[$k] = "$k:$v[title]";
            }
            $row = "<select name='fm[fmexstr]' class='w150'>".basElm::setOption($arr,$oldval,basLang::show('admin.fe_wdfrom'))."</select>";
            
        }elseif(in_array($this->fmextra,array('datetm','editor','color'))){ //,'map'
            $msgs = array('datetm'=>basLang::show('admin.fe_tmfmt').':Y-m-d H:i:s','editor'=>basLang::show('admin.fe_edtool').':full,exbar','color'=>basLang::show('admin.fe_colorto').':title');
            $row = "<input name='fm[fmexstr]' type='text' value='{$oldval}' class='txt w120' maxlength='120' /> {$msgs[$this->fmextra]}";            
        }else{ // in_array($this->type,array('input','text','hidden'))
            echo "<input name='fm[fmexstr]' type='hidden' value='' />";
            return;    
        }
        glbHtml::fmae_row(basLang::show('admin.fe_ctrlp'),$row);
    }    

    // iParaKeys
    function fmParaKeys(){ 
        if(empty($this->ispara)) return;
        $row = "<input name='fm[key]' type='text' value='{$this->cfg['key']}' class='txt w120' maxlength='24' id='fm[key]' tip='".basLang::show('admin.fe_prfmt')."'/>";
        $row .= " &nbsp; ".basLang::show('admin.fe_prval').": <input name='fm[val]' type='text' value='{$this->cfg['val']}' class='txt w120 disc' maxlength='12' id='fm[val]' disabled='disabled' />";
        glbHtml::fmae_row(basLang::show('admin.fe_prkey'),$row);
    }

    // iKeyName
    function fmKeyName(){ 
        $enable = empty($this->cfg['enable']) ? '0' : '1';
        $top = empty($this->cfg['top']) ? '888' : $this->cfg['top'];
        $kid = empty($this->cfg['kid']) ? '' : $this->cfg['kid'];
        $title = empty($this->cfg['title']) ? '' : $this->cfg['title'];
        $ienable = "<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($enable=='1' ? 'checked' : '')." />"; //<input name='fm_enable' type='hidden' value='$fm[enable]' />
        glbHtml::fmae_row(basLang::show('admin.fe_keyid'),"<input id='fm[kid]' name='fm[kid]' type='text' value='$kid' class='txt w150 disc' readonly /> &nbsp; ".basLang::show('admin.fe_uesable')."$ienable");
        $itop = "<input name='fm[top]' type='text' value='$top' class='txt w40' maxlength='5' reg='n+i' tip='".basLang::show('admin.fe_num25')."' />";
        glbHtml::fmae_row(basLang::show('admin.fe_fname'),"<input id='fm[title]' name='fm[title]' type='text' value='$title' class='txt w150' maxlength='12' reg='tit:2-12' tip='".basLang::show('admin.fe_let212')."' /> &nbsp; ".basLang::show('admin.fe_order')."$itop");
    }
        
    // iDbOpts
    function fmDbOpts(){ 
        if($this->fmextra=='datetm'){
            $opts = "<option value='int'>int.".basLang::show('admin.fe_int')."</option>";
            $flen = 11;
        }elseif(in_array($this->fmextra,array('winpop','map','color'))){ 
            $opts = "<option value='varchar'>varchar.".basLang::show('admin.fe_vchar')."</option>";
        }elseif(in_array($this->type,array('parts','repeat'))){ 
            $opts = "<option value='nodb'>nodb.".basLang::show('admin.fe_nodb')."</option>";
            $flen = 0;
        }elseif(in_array($this->type,array('passwd','file'))){ 
            $opts = "<option value='varchar'>varchar.".basLang::show('admin.fe_vchar')."</option>";
            $flen = 255;
        }elseif(in_array($this->type,array('text'))){ // $this->fmextra=='editor'
            $opts = "<option value='text'>text.(64K)".basLang::show('admin.fe_text')."</option>";
            $opts .= "<option value='mediumtext' ".($this->cfg['dbtype']=='mediumtext' ? 'selected' : '').">medium.(16M)".basLang::show('admin.fe_ltext')."</option>";
            $opts .= "<option value='file' ".($this->cfg['dbtype']=='file' ? 'selected' : '').">file.(1G)".basLang::show('admin.fe_svfile')."</option>";
            $opts .= "<option value='varchar' ".($this->cfg['dbtype']=='varchar' ? 'selected' : '').">varchar.".basLang::show('admin.fe_vchar')."</option>";
            $flen = $this->cfg['dbtype']=='varchar' ? $this->cfg['dblen'] : 0;            
        }else{
            $oldval = empty($this->cfg['dbtype']) ? 'varchar' : $this->cfg['dbtype'];
            $dbtypes = fldCfgs::dbTypes(); 
            foreach(array('text','mediumtext','nodb','file') as $dk){ unset($dbtypes[$dk]); }
            $opts = basElm::setOption($dbtypes,$oldval,basLang::show('admin.fe_dbtype'));
        }
        $dblen = isset($flen) ? $flen : (empty($this->cfg['dblen']) ? ($this->cfg['dbtype']=='varchar' ? 12 : '0') : $this->cfg['dblen']);
        @$dbdef = strlen($this->cfg['dbdef']) ? $this->cfg['dbdef'] : '';
        $row = "\n<select name='fm[dbtype]' class='w150' reg='str:1-255'>$opts</select>"; //$dise = "disabled='disabled'";
        $row .= " &nbsp;&nbsp; ".basLang::show('admin.fe_len')."<input name='fm[dblen]' type='text' value='$dblen' class='txt w40' maxlength='5' reg='n+i' tip='".basLang::show('admin.fe_num25')."' id='fm[dblen]'/>";
        $row .= "<br><input name='fm[dbdef]' type='text' value='$dbdef' class='txt w150' maxlength='48' id='fm[dbdef]' />";
        glbHtml::fmae_row(basLang::show('admin.fe_dbdef'),$row);
    }

    // iRegOpts
    function fmRegOpts(){     
        $vmax = empty($this->cfg['vmax']) ? '0' : $this->cfg['vmax'];
        $vtip = empty($this->cfg['vtip']) ? '' : $this->cfg['vtip'];
        $vreg = empty($this->cfg['vreg']) ? '' : $this->cfg['vreg'];    
        $fnull = '1'; $vtype = ''; $vmin = '0'; 
        if(!empty($this->cfg['vreg'])){
            $fnull = strstr($this->cfg['vreg'],'nul:') ? 'nul' : '0';
            $vtype = str_replace('nul:','',$this->cfg['vreg']);
            preg_match("/\:(\d+)\-/",$this->cfg['vreg'],$m);
            if(isset($m[1]) && is_numeric($m[1])) $vmin = $m[1];
             
        }
        if($this->fmextra=='file'){
            $rtypes = array('fix:file'=>basLang::show('admin.fe_file'), 'fix:image'=>basLang::show('admin.fe_pic'),);
        }elseif(in_array($this->cfg['dbtype'],array('float','int'))){ 
            $rtypes = basLang::ucfg('cfglibs.fedit_numtype'); 
        }else{
            $rtypes = fldCfgs::regTypes(); unset($rtypes['fix:file'],$rtypes['fix:image'],$rtypes['n+i'],$rtypes['n-i'],$rtypes['n+d'],$rtypes['n-d']);
        }
        $opts = basElm::setOption($rtypes,$vtype,basLang::show('admin.fe_vtype'));    
        $row = "\n<select name='fm_vtype' id='fm_vtype' class='w90' onChange='gf_setvType()'>$opts</select>";
        $row .= " &nbsp; ".basLang::show('admin.fe_len')."<input name='fm_vlen' type='text' value='$vmin' class='txt w30' maxlength='5' reg='n+i' tip='".basLang::show('admin.fe_minlen')."' id='fm_vlen' />";
        $row .= "-<input name='fm[vmax]' type='text' value='$vmax' class='txt w30' maxlength='5' reg='n+i' tip='".basLang::show('admin.fe_maxlen')."' id='fm[vmax]' />";
        $row .= "<input name='setreg2' type='button' class='btn' value='".basLang::show('admin.fe_set')."' onClick='gf_setvType()' />";
        $row .= "<br><input name='fm[vreg]' type='text' value='$vreg' class='txt w150' maxlength='255' id='fm[vreg]' tip='".basLang::show('admin.fe_vrule')."' /> ".basLang::show('admin.fe_vreg')."";
        $row .= "<br><input name='fm[vtip]' type='text' value='$vtip' class='txt w150' maxlength='255' tip='".basLang::show('admin.fe_vtips')."' />";
        $row .= "\n<select name='fm_null' class='w90' onChange='gf_setvMust(this)'>".basElm::setOption(array('0'=>basLang::show('admin.fe_must'),'nul'=>basLang::show('admin.fe_nomust'),),$fnull,'')."</select>";
        glbHtml::fmae_row(basLang::show('admin.fe_3title'),$row);
    }

    // iViewOpts
    function fmViewOpts(){
        $fmsize = empty($this->cfg['fmsize']) ? '' : $this->cfg['fmsize']; 
        $fmline = @$this->cfg['fmline'];
        $fmtitle = @$this->cfg['fmtitle'];
        $rtip = basLang::show('admin.fe_tipsize');
        $row = "<input name='fm[fmsize]' type='text' value='$fmsize' class='txt w90' maxlength='12' tip='$rtip' />";
        $row .= "\n<select name='fm[fmline]' value='$fmsize' class='w90' tip='".basLang::show('admin.fe_tipline')."'>".basElm::setOption(basLang::ucfg('cfglibs.fedit_vline'),$fmline,'')."</select>";
        $row .= "\n<select name='fm[fmtitle]' value='$fmtitle' class='w90' tip='".basLang::show('admin.fe_tiptitle')."'>".basElm::setOption(basLang::ucfg('cfglibs.fedit_vltitle'),$fmtitle,'')."</select>";
        glbHtml::fmae_row(basLang::show('admin.fe_shsize'),$row);
    }    

    // iRemCfgs
    function fmRemCfgs(){
        $cfgs = empty($this->cfg['cfgs']) ? '' : $this->cfg['cfgs'];
        $note = basLang::inc('uless','fldedit_note');
        glbHtml::fmae_row(basLang::show('admin.fe_selarr'),"<textarea name='fm[cfgs]' rows='5' cols='50' wrap='off'>$cfgs</textarea>");
        glbHtml::fmae_row(basLang::show('admin.fe_note'),"<textarea name='fm_note' rows='5' cols='50' wrap='wrap'>$note</textarea>");
    }

}