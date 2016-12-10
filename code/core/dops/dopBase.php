<?php
// dops : All Data & View OP Systems
// dopBase : 基本数据操作(Basic Data Operate) --- 列表,搜索,设置,编辑...
// for : docs,coms,user,advs,
class dopBase{	

	public $cfg = array();
	public $mod = '';
	public $file = '';
	public $aurl = array();
	public $btid = '';
	
	public $typfid = 'catalog';
	public $order = '';
	public $dskey = '';

	public $fmo = array(); // 原始数据(database),供表单设置初始值
	public $fmv = array(); // fields中：表单数据(基本表)
	public $fmu = array(); // fields中：表单数据(扩展表)
	public $fme = array(); // fields外数据：(ip,time,user,catid,grade...)
	
	public $pg = NULL;
	public $so = NULL;
	public $cv = NULL;
	
	// 栏目,等级; 搜索字段; 搜索like模式; 关键字; 范围字段:N,D; 最小范围; 最大范围；	
	public $skey = array('stype'=>'','sfid'=>'','sfop'=>'','sfkw'=>'','sfrng'=>'','srva'=>'','srvb'=>'');
	public $sopc = array('ll'=>'like%','lb'=>'%like%','lr'=>'%like','eq'=>'=','in'=>'IN');
	
	public $fext = array();
	public $db = NULL;

	//function __destory(){  }
	function __construct($cfg,$tabid=''){ 
		$this->db = db(); 
		$this->so = new dopBSo($cfg,$tabid);
		$this->cv = new dopBCv($cfg,$tabid);
		$this->so->skey = $this->cv->skey = $this->skey;
		$this->so->sopc = $this->cv->sopc = $this->sopc;
		$this->so->fext = $this->cv->fext = $this->fext = array(
			'aip' => array('title'=>lang('flow.log_aip'),'dbtype'=>'varchar',),
			'eip' => array('title'=>lang('flow.log_eip'),'dbtype'=>'varchar',),
			'atime' => array('title'=>lang('flow.log_atime'),'dbtype'=>'int',),
			'etime' => array('title'=>lang('flow.log_etime'),'dbtype'=>'int',),
		);
		$this->cfg = $cfg;
		$this->mod = empty($cfg['kid']) ? 0 : $cfg['kid'];
		$this->tbid = $tabid;
		$this->type = $cfg['pid']; 
		unset($GLOBALS['cfg']);
	}
	
	// 左上信息条
	function msgBar($msg='',$lnkadd=''){
		$mod = $this->mod;
		$file = req('file');
		$stype = req('stype');  
		$gname = $this->cfg['title'];
		empty($lnkadd) && $lnkadd = $this->cv->Url(lang('flow.dops_add2').'&gt;&gt;',0,"?file=$file&mod=$mod&view=form&stype=$stype&recbk=ref","");
		$lnkadd = str_replace("<a ","<a id='{$mod}_add' ",$lnkadd);
		if($msg && !strpos($msg,'<')) $msg = "<span class='cF00'>$msg</span>";
		$msg && $msg = $msg."<br>";
		$msg = "{$msg}[$gname]".lang('flow.dops_adm2')."<span class='span ph5'>|</span>$lnkadd";
		return $msg;
	}
		
	// 列表-记录集
	function getRecs($key='',$psize=0){
		$psize || $psize = cfg('show.apsize');
		$key = $key ? $key : $this->_kid; 
		$sfrom = "* FROM `".$this->db->pre.$this->tbid.$this->db->ext."` ";  
		$where = empty($this->so->whrstr) ? '' : (substr($this->so->whrstr,5)); //echo $where;
		$opkey = $this->order===$key ? 1 : 0;
		if(strpos($this->order,'-a')){
			$order = str_replace('-a','',$this->order);
			$odesc = 0;
		}else{
			$order = $this->order;
			$odesc = 1;
		} //echo "$sfrom,$where,($opkey),$odesc,$order";
		$pg = new comPager($sfrom,$where,$psize,$order); 
		$pg->set('odesc',$odesc); // odesc/opkey
		$pg->set('opkey',$opkey);
		$rs = $pg->exe();
		$this->pg = $pg;
		return $rs;
	}
	
	// SetID，advs,docs,coms:共用此属性。
	function fmSetID(){ 
		$tabid = $this->cfg['pid']=='users' ? $this->tbuacc : $this->tbid;
		$_key = $this->cfg['pid']=='users' ? 'uid' : $this->_kid;
		$val = @$this->fmo[$_key];  
		if(empty($val)){ //echo $this->kfix.$this->ktmp;
			$kar = glbDBExt::dbAutID($tabid,$this->kfix,$this->ktmp);
			$kid = $kar[0]; $kno = $kar[1];
			$dis = "class='txt w180'";
		}else{
			$kid = $this->fmo[$_key]; 
			$kno = $this->fmo[$this->_kno];
			$dis = "class='txt w160 disc' readonly";	
		} 
		$item = "<input id='fm[$_key]' name='fm[$_key]' type='text' value='$kid' maxlength=24 $dis tip='".lang('flow.tip_slogs')."' />";
		$item .= "\n<input name='fm[$this->_kno]' type='hidden' value='$kno' />";
		return $item;
	}	
	// 表单-类别
	function fmType($key='catid',$w=150){ 
		$dval = empty($this->fmo[$key]) ? req('stype') : $this->fmo[$key];
		$str = "\n<select name='fm[$key]' id='fm[$key]' class='w$w' reg='tit:2-12'>"; 
		$str .= comTypes::getOpt($this->cfg['i'],$dval); 
		$str .= "</select>";
		return $str;
	}
	// def：0/1; 当前资料优先, 再次是模型设置优先, 最后用$def默认值
	function fmShow($def=1){
		$val = dopFunc::fmDefval($this,'show',$def);
		$item = basElm::setOption("1=".lang('flow.op_show')."\n0=".lang('flow.op_hide')."",$val,lang('flow.op0_show')); 
		$item = "\n<select name='fm[show]' class='w80' reg='n+i:' tip='".lang('flow.tip_ssel')."'>$item</select>";
		return $item;
	}
	function fmAELogs($type,$key){
		$run = cfg('run');
		$user = user();
		if($type=='date'){
			$val = empty($this->fmo[$key]) ? $run['stamp'] : $this->fmo[$key];
			$val = date('Y-m-d H:i:s',$val); 
			echo basJscss::imp('/My97DatePicker/WdatePicker.js','vendui'); 
			$iinp = "<input id='fm[$key]' name='fm[$key]' type='text' value='$val' class='txt w140' />";
			$item = "$iinp<span class='fldicon fdate' onClick=\"WdatePicker({el:'fm[$key]',dateFmt:'yyyy-MM-dd HH:mm:ss'})\" /></span>";
		}elseif($type=='user'){
			$val = empty($this->fmo[$key]) ? @$user->uinfo['uname'] : $this->fmo[$key];
			$item = "<input id='fm[$key]' name='fm[$key]' type='text' value='$val' maxlength=24 tip='".lang('flow.tip_suser')."' />";
		}elseif($type=='uip'){
			$val = empty($this->fmo[$key]) ? $run['userip'] : $this->fmo[$key];
			$item = "<input id='fm[$key]' name='fm[$key]' type='text' value='$val' maxlength=255 tip='".lang('flow.tip_sip')."' />";
		}
		return $item;
	}
	function fmAE3($hid=0){
		glbHtml::fmae_row(lang('flow.log_optime'),lang('flow.log_opadd').$this->fmAELogs('date','atime').' &nbsp; '.lang('flow.log_opedit').$this->fmAELogs('date','etime'),$hid);
		glbHtml::fmae_row(lang('flow.log_opuser'),  lang('flow.log_opadd').$this->fmAELogs('user','auser').' &nbsp; '.lang('flow.log_opedit').$this->fmAELogs('user','euser'),$hid);
		glbHtml::fmae_row(lang('flow.log_opip'),  lang('flow.log_opadd').$this->fmAELogs('uip','aip')   .' &nbsp; '.lang('flow.log_opedit').$this->fmAELogs('uip','eip'),   $hid);
	}

	// svAKey，
	function svAKey(){
		$tabid = $this->cfg['pid']=='users' ? $this->tbuacc : $this->tbid;
		$_key = $this->cfg['pid']=='users' ? 'uid' : $this->_kid;
		if(!empty($this->fme[$_key]) && !empty($this->fme[$this->_kno])){	
			$val = preg_replace('/[^0-9A-Za-z\.\-]/','',$this->fme[$_key]);
			if(!$this->db->table($tabid)->where($_key."='$val'")->find()){
				$kid = $val; 
				$kno = preg_replace('/[^0-9A-Za-z\.\-]/','',$this->fme[$this->_kno]);
			}
		}
		if(empty($kid) || empty($kno)){
			$kar = glbDBExt::dbAutID($tabid,$this->kfix,$this->ktmp);
			$kid = $kar[0]; $kno = $kar[1];	
		}
		$this->fmv[$_key] = $kid;	
		$this->fmv[$this->_kno] = $kno;
		$this->svMoveFiles($kid);
		comJifen::main(array_merge($this->cfg,$this->fme),'add','');
		return $this->fmv[$_key];
	}
	function svTypes(){
		$field = $this->cfg['pid']=='users' ? 'grade' : 'catid'; 
		if(isset($this->fme[$field])){
			$type = $this->fmv[$field] = preg_replace('/[^0-9A-Za-z\.\-]/','',$this->fme[$field]);
			$ccfg = read($this->mod,'_c'); //类别扩展属性 
			if(empty($ccfg[$type])) return;
			$cfield = $ccfg[$type];
			foreach($cfield as $k=>$v){
				if(isset($this->fme[$k])){ 
					$this->fmv[$k] = $this->fme[$k];
				}
			}
		}
	}
	// Fields。
	function svFields(){
		if(empty($_POST['fm'])) glbHtml::end('svFields@'.__CLASS__);
		$f = $this->cfg['f'];
		if(in_array($this->cfg['pid'],array('users','docs'))){
			$fc = read($this->mod,'_c'); 
			$fk = $this->cfg['pid']=='users' ? 'grade' : 'catid';
			$fv = @$_POST['fm'][$fk];
			if(isset($fc[$fv])){
				$f = $f+$fc[$fv]; 
			}
		}
		foreach($_POST['fm'] as $k=>$v){
		if(isset($f[$k])){ //$val = $v;
			if($f[$k]['dbtype']=='nodb') continue;
			$val = dopFunc::svFmtval($f,$this->mod,$k,$v);
			if(empty($f[$k]['etab'])) $this->fmv[$k] = $val;		
			else					  $this->fmu[$k] = $val;
		}else{
			$this->fme[$k] = $v;
		} }
		return;
	}
	function svMoveFiles($kid=''){
		$fall = $this->cfg['f'];
		foreach($fall as $f=>$cfg){ 
			$ext = @$cfg['fmextra'];
			$arr = array('fmv','fmu');
			foreach($arr as $k){
				if(($ext=='editor' || $ext=='pics' || $cfg['type']=='file')){
					$tmp = &$this->$k; 
					if(!empty($tmp[$f])){ 
						$tmp[$f] = comStore::moveTmpDir($tmp[$f], $this->mod, $kid, $ext=='editor'?1:0); 
						break;
					}
				}
			}
		} 
	}
	// FALogs。
	function svAELogs(){
		$a = array('atime','etime','auser','euser','aip','eip');
		foreach($a as $k){
		if(isset($this->fme[$k])){ 
			$val = $this->fme[$k];	
			$val = preg_replace('/[^0-9A-Za-z,\.\-\ \:]/','',$val);
			if(in_array($k,array('atime','etime'))) $val = empty($val) ? $_cbase['run']['stamp'] : strtotime($val); 
			$this->fmv[$k] = $val;	
		} }
	}
	// svPrep 预处理
	function svPrep(){
		dopFunc::svSafe();
		$this->svFields();
		$this->svTypes();
		$this->svAELogs();
		if(isset($this->fme['show'])){ 
			$this->fmv['show'] = intval($this->fme['show']);
		}
	}
	// svEnd。
	function svEnd($id,$show=1){
		$this->svMoveFiles($id);
		if(in_array($this->cfg['pid'],array('docs','users'))){ //,'types'
			//是否判定cheched?
			$urls = vopStatic::updKid($this->mod,$id,'upd');
			$js = '';
			foreach($urls as $tpl=>$mkv){
				$js .= basJscss::jscode(0,PATH_ROOT."/plus/ajax/cron.php?static=updkid&tpldir=$tpl&mkv=$mkv");
			}
		}
		if($show) echo $js;
		return $js;
	}
	// opShow。
	function opShow($id,$op){
		$v = $op=='show' ? '1' : '0';
		$this->db->table($this->tbid)->data(array('show'=>$v))->where("$this->_kid='$id'")->update();
		return 1;
	}
	// opDelete。
	function opDelete($id){
		$this->opDelPubpre($id); //删之前执行
		$this->db->table($this->tbid)->where("$this->_kid='$id'")->delete(); 
		$this->opDelPublic($id); //删之后执行
		return 1;
	}
	// opDelPubpre。
	function opDelPubpre($id){ 
		if(!in_array($this->cfg['pid'],array('docs','users','coms'))) return;
		$fme = $this->db->field('aip,auser,atime')->table($this->tbid)->where("$this->_kid='$id'")->find();
		if(!empty($fme)) comJifen::main(array_merge($this->cfg,$fme),'del'); 
	}
	// opDelPublic。
	function opDelPublic($id){
		if(in_array($this->cfg['pid'],array('docs','users'))){ //,'types'
			vopStatic::clrFile($this->mod,$id);
			//if($this->cfg['pid']=='types') return;
			$res = vopStatic::updKid($this->mod,$id,'del');
			foreach($res as $file){
				@unlink($file);
			}
		}
	}

}
