<?php

class fldEdit{

	//public $cfg = array();
	
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
			$db = glbDBObj::dbObj();
			$cawhr = ($catid) ? "AND catid='$catid'" : ""; //echo $cawhr;
			$fm = $db->table($tabid)->where("model='$mod' AND kid='$kid' $cawhr")->find(); 
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
		$plugs = fldCfgs::viewPlugs(); $plugstr = isset($plugs[$this->fmextra]) ? $plugs[$this->fmextra] : '(无控件)';
		$etab = empty($this->etab)? '主表' : '扩展表';
		$row  = "\n<select name='fm[type]'	class='w90'><option value='{$this->type}'>".$types[$this->type]."</option></select>";
		$row .= "\n<select name='fm[fmextra]' class='w90'><option value='{$this->fmextra}'>$plugstr</option></select>";
		$row .= "\n<select name='fm[etab]'	class='w90'><option value='{$this->etab}'>$etab</option></select>";
		glbHtml::fmae_row('字段类型',$row);
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
			$row = "<select name='fm[fmexstr]' class='w150'>".basElm::setOption($arr,$oldval,'-弹窗数据来源-')."</select>";
			
		}elseif(in_array($this->fmextra,array('datetm','editor','color'))){ //,'map'
			$msgs = array('datetm'=>'时间格式,如:yyyy-M-d H:i:s','editor'=>'编辑器工具栏:full,exbar','color'=>'颜色目标字段:title');
			$row = "<input name='fm[fmexstr]' type='text' value='{$oldval}' class='txt w120' maxlength='120' /> {$msgs[$this->fmextra]}";			
		}else{ // in_array($this->type,array('input','text','hidden'))
			echo "<input name='fm[fmexstr]' type='hidden' value='' />";
			return;	
		}
		glbHtml::fmae_row('控件参数',$row);
	}
	
	// iParaKeys
	function fmParaKeys(){ 
		if(empty($this->ispara)) return;
		$row = "<input name='fm[key]' type='text' value='{$this->cfg['key']}' class='txt w120' maxlength='24' id='fm[key]' tip='格式:【abc_def】;或:【[abc][def]】'/>";
		$row .= " &nbsp; 参数值: <input name='fm[val]' type='text' value='{$this->cfg['val']}' class='txt w120 disc' maxlength='12' id='fm[val]' disabled='disabled' />";
		glbHtml::fmae_row('参数键',$row);
	}
	
	// iKeyName
	function fmKeyName(){ 
		$enable = empty($this->cfg['enable']) ? '0' : '1';
		$top = empty($this->cfg['top']) ? '888' : $this->cfg['top'];
		$kid = empty($this->cfg['kid']) ? '' : $this->cfg['kid'];
		$title = empty($this->cfg['title']) ? '' : $this->cfg['title'];
		$ienable = "<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($enable=='1' ? 'checked' : '')." />"; //<input name='fm_enable' type='hidden' value='$fm[enable]' />
		glbHtml::fmae_row('Key标识',"<input id='fm[kid]' name='fm[kid]' type='text' value='$kid' class='txt w150 disc' readonly /> &nbsp; 启用$ienable");
		$itop = "<input name='fm[top]' type='text' value='$top' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' />";
		glbHtml::fmae_row('字段名称',"<input id='fm[title]' name='fm[title]' type='text' value='$title' class='txt w150' maxlength='12' reg='tit:2-12' tip='可含字母数字下划线<br>允许2-12字符,建议4-6字符' /> &nbsp; 顺序$itop");
	}
	
	// iDbOpts
	function fmDbOpts(){ 
		if($this->fmextra=='editor'){
			$opts = "<option value='text'>text.(64K)长文本</option>";
			$opts .= "<option value='mediumtext' ".($this->cfg['dbtype']=='mediumtext' ? 'selected' : '').">medium.(16M)长文本</option>";
			$flen = 0;
		}elseif($this->fmextra=='datetm'){
			$opts = "<option value='int'>int.整型</option>";
			$flen = 11;
		}elseif(in_array($this->fmextra,array('winpop','map','color'))){ 
			$opts = "<option value='varchar'>varchar.字符</option>";
		}elseif(in_array($this->type,array('parts','repeat'))){ 
			$opts = "<option value='nodb'>nodb.不存数据库</option>";
			$flen = 0;
		}elseif(in_array($this->type,array('passwd','file'))){ 
			$opts = "<option value='varchar'>varchar.字符</option>";
			$flen = 255;
		}elseif(in_array($this->type,array('text'))){ 
			$opts = "<option value='text'>text.(64K)长文本</option>";
			$flen = 0;			
		}else{
			$oldval = empty($this->cfg['dbtype']) ? 'xxxxx' : $this->cfg['dbtype'];
			$dbtypes = fldCfgs::dbTypes(); unset($dbtypes['text'],$dbtypes['mediumtext'],$dbtypes['nodb']);
			$opts = basElm::setOption($dbtypes,$oldval,'-数据类型-');
		}
		$dblen = isset($flen) ? $flen : (empty($this->cfg['dblen']) ? '0' : $this->cfg['dblen']);
		@$dbdef = strlen($this->cfg['dbdef']) ? $this->cfg['dbdef'] : '';	
		$row = "\n<select name='fm[dbtype]' class='w150' reg='str:1-255'>$opts</select>"; //$dise = "disabled='disabled'";
		$row .= " &nbsp;&nbsp; 长度<input name='fm[dblen]' type='text' value='$dblen' class='txt w40' maxlength='5' reg='n+i' tip='允许2-5数字' id='fm[dblen]'/>";
		$row .= "<br><input name='fm[dbdef]' type='text' value='$dbdef' class='txt w150' maxlength='48' id='fm[dbdef]' />";
		glbHtml::fmae_row('数据库<br>默认值',$row);
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
			$rtypes = array('fix:file'=>'文件', 'fix:image'=>'图片',);
		}elseif(in_array($this->cfg['dbtype'],array('float','int'))){ 
			$rtypes = array('n+i'=>'整数', 'n-i'=>'整数(含负)','n+d'=>'小数', 'n-d'=>'小数(含负)',);
		}else{
			$rtypes = fldCfgs::regTypes(); unset($rtypes['fix:file'],$rtypes['fix:image'],$rtypes['n+i'],$rtypes['n-i'],$rtypes['n+d'],$rtypes['n-d']);
		}
		$opts = basElm::setOption($rtypes,$vtype,'-认证类型-');	
		$row = "\n<select name='fm_vtype' id='fm_vtype' class='w90' onChange='gf_setvType()'>$opts</select>";
		$row .= " &nbsp; 长度<input name='fm_vlen' type='text' value='$vmin' class='txt w30' maxlength='5' reg='n+i' tip='认证允许最小长度' id='fm_vlen' />";
		$row .= "-<input name='fm[vmax]' type='text' value='$vmax' class='txt w30' maxlength='5' reg='n+i' tip='认证允许最大长度<br>不填写表示无限' id='fm[vmax]' />";
		$row .= "<input name='setreg2' type='button' class='btn' value='设置' onClick='gf_setvType()' />";
		$row .= "<br><input name='fm[vreg]' type='text' value='$vreg' class='txt w150' maxlength='255' id='fm[vreg]' tip='规则代码' /> 可用正则表达式";
		$row .= "<br><input name='fm[vtip]' type='text' value='$vtip' class='txt w150' maxlength='255' tip='认证表单提示<br>元素外提示用(|)分开' />";
		$row .= "\n<select name='fm_null' class='w90' onChange='gf_setvMust(this)'>".basElm::setOption(array('0'=>'必填','nul'=>'不必填',),$fnull,'')."</select>";
		glbHtml::fmae_row('认证类型<br>认证规则<br>认证提示',$row);
	}
	
	// iViewOpts
	function fmViewOpts(){
		$fmsize = empty($this->cfg['fmsize']) ? '' : $this->cfg['fmsize']; 
		$fmline = @$this->cfg['fmline'];
		$fmtitle = @$this->cfg['fmtitle'];
		$rtip = "输入框,select等显示的宽度,单位:px;<br>text宽和高(行数),<br>如[400x6]表示宽400px,6行高,<br>弹窗选择的宽度和多选个数,<br>[mod.n]表示资料选取的模型和多选个数.";
		$row = "<input name='fm[fmsize]' type='text' value='$fmsize' class='txt w90' maxlength='12' tip='$rtip' />";
		$row .= "\n<select name='fm[fmline]' value='$fmsize' class='w90' tip='默认一行显示一个项目'>".basElm::setOption(array('1'=>'独立行显示','0'=>'同行显示'),$fmline,'')."</select>";
		$row .= "\n<select name='fm[fmtitle]' value='$fmtitle' class='w90' tip='[同行显示]下有效'>".basElm::setOption(array('0'=>'隐藏提示','1'=>'显示提示'),$fmtitle,'')."</select>";
		glbHtml::fmae_row('显示大小',$row);
	}
	
	// iRemCfgs
	function fmRemCfgs(){
		$cfgs = empty($this->cfg['cfgs']) ? '' : $this->cfg['cfgs'];
		$note  = "格式1:选项值=选项标题,一行一个;\n";
		$note .= "格式2:模型id(栏目/类系);\n";
		$note .= "格式3:pid:\"cnhn\",w:640;\n";
		$note .= "格式4:bext_paras.logmode_cn, 取bext_paras资料;\n";
		$note .= "[选择数组]配置规范:\n";
		$note .= "*. [下拉选择][多选框][单选按钮]格式1或2或3:\n";
		$note .= "*. [开窗单选][开窗多选]格式3:";
		glbHtml::fmae_row('选择数组<br />格式见[备注]',"<textarea name='fm[cfgs]' rows='5' cols='50' wrap='off'>$cfgs</textarea>");
		glbHtml::fmae_row('备注',"<textarea name='fm_note' rows='5' cols='50' wrap='wrap'>$note</textarea>");
	}

}