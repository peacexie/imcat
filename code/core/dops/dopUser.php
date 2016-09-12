<?php
(!defined('RUN_MODE')) && die('No Init');

// dopUser(data OP for User)
class dopUser extends dopBase{	

	public $kfix = 'yyyy-md-';
	public $ktmp = '31';
	public $tbuacc = '';
	public $_kid = 'uid'; //uid在uacc表
	public $_kno = 'uno';
	
	//function __destory(){  }
	function __construct($cfg){ 
		$mod = $cfg['kid'];
		parent::__construct($cfg,$cfg['pid']."_$mod");
		$this->typfid = $this->so->typfid = 'grade';
		$this->dskey  = $this->so->dskey  = 'mname'; 
		$this->order  = $this->so->order  = basReq::val('order','uid');
		$this->tbuacc  = 'users_uacc';
	}
	// 翻页条,批量操作
	function pgbar($idfirst,$idend){
		$pg = $this->pg->show($idfirst,$idend);
		$op = "".basElm::setOption(lang('flow.op_op3'),'',lang('flow.op0_bacth'));
		dopFunc::pageBar($pg,$op);
	}
	// 搜索条 // check,fields
	function sobar($msg='',$width=30){ 
		$mod = $this->mod;
		$sbar = "\n".$this->so->Type(90,lang('du_gradeo')); 
		if(method_exists($this,"sobar_$mod")){ //中间部分定制
			$sbar .= $this->{"sobar_$mod"}($msg,$width);
		}else{
			$sbar .= "\n&nbsp; ".$this->so->Word(80,80,lang('flow.op0_filt'));
			$sbar .= "\n&nbsp; ".$this->so->Show(60);
		}
		$sbar .= "\n&nbsp; ".$this->so->Order(array('uname' => lang('flow.dops_orduidd'),'uname-a' => lang('flow.dops_orduida'),));
		$this->so->Form($sbar,$msg,$width);
	}
	// 搜索条_company
	function sobar_company($msg='',$width=30){ 
		$sbar = "\n&nbsp; ".$this->so->Field('ftype',$w=90);
		$sbar .= "\n&nbsp; ".$this->so->Word(80,80,lang('flow.op0_filt'));
		$sbar .= "<br />\n".$this->so->Show();
		$sbar .= "\n&nbsp; ".$this->so->Area(90);
		return $sbar;
	}

	// Account Info 账户资料
	function fmAccount(){ 
		glbHtml::fmae_row('ID',$this->fmSetID()); //'显示:'.$this->fmShow().
		//echo "<tr><th>账户资料</th><th class='tr'>---</th></tr>\n";
		glbHtml::fmae_row(lang('du_userid'),$this->fmSetUname().' &nbsp; '.lang('du_upass').$this->fmSetPW());
		//glbHtml::fmae_row('问题',$this->fmSetQA('getpwq').' &nbsp; 答案'.$this->fmSetQA('getpwa'));
		glbHtml::fmae_row(lang('du_gradet'),$this->fmType('grade',180).' &nbsp; '.lang('flow.dops_ishow').$this->fmShow());
		echo "<tr><th>".lang('flow.dops_detail')."</th><th class='tr'>---</th></tr>\n";
	}
	
	/*
	function fmSetQA($key){
		$val = $key=='getpwa' ? '' : @$this->fmo[$key]; 
		$nulmsg = $key=='getpwq' ? '' : "两个同时不为空则修改问题和答案";
		if(empty($val)){
			$nultip = ""; 
		}else{
			$nultip = "nul:"; 
		}
		$item = "<input id='fm[$key]' name='fm[$key]' type='text' value='$val' maxlength=24 class='txt w180' reg='{$nultip}key:6-24' tip='6-24字符' />$nulmsg";
		return $item;
	}*/
	function fmSetPW(){
		if(empty($this->fmo['uname'])){
			$nultip = ""; 
			$nulmsg = lang('admin.fad_up624');
		}else{
			$nultip = "nul:"; 
			$nulmsg = lang('du_empty');
		}
		$item = "<input id='fm[upass]' name='fm[upass]' type='text' value='' maxlength=24 class='txt w180' reg='{$nultip}key:6-24' tip='".lang('admin.fad_up624')."' />$nulmsg";
		return $item;
	}

	// SetID，会员使用-其它无此属性。
	function fmSetUname(){
		$mod = $this->mod;
		$val = @$this->fmo['uname']; 
		$len = empty($val) ? 'var:3-15' : 'var:2-24';
		$prop = " id='fm[uname]' name='fm[uname]' type='text' maxlength=24 reg='$len' tip='".lang('admin.fad_uid31546')."' ";
		if(empty($val)){
			$uname = 'm'.basKeyid::kidRand('24',5);
			$vstr = "url='".PATH_ROOT."/plus/ajax/cajax.php?act=userExists&mod=$mod'"; 
			$item = "<input value='$uname' class='txt w180' $prop $vstr/>";
		}else{
			$item = "<input value='$val' class='txt w180 disc' $prop readonly/>";
		}
		return $item;
	}

	// 属性设置
	function fmProp(){ 
		dopFunc::fmSafe();
		$mod = $this->mod;
		echo "<tr><th nowrap>".lang('flow.title_attrset')."</th><th class='tr'>---</th></tr>\n";
		$this->fmAE3();
	}
	// Account Info 账户资料
	function svAccount($type='add'){ 
		$mod = $this->fme['umods'] = $this->mod; 
		$accs = array('uid','uno','uname','upass','umods'); //,'getpwq','getpwa'
		$data = array();
		foreach($accs as $k){
			$$k = $data[$k]= $this->fme[$k];
		}
		//$uname = $data['uname'];
		//$upass = $data['upass'];
		//$getpwq = $data['getpwq'];
		//$getpwa = $data['getpwa'];
		$upassOrg = $upass; //备份
		$upass = $data['upass'] = comConvert::sysPass($uname,$upass,$mod);
		//$data['getpwa'] = comConvert::sysPass($getpwq,$getpwa,$mod);
		#if($type=='edit' && empty($upassOrg)){
			#unset($data['upass']);
		//}elseif($type=='edit' && (empty($getpwq)||empty($getpwa))){
			//unset($data['getpwq']);
			//unset($data['getpwa']);
		#}
		foreach($accs as $k){
			if(in_array($k,array('uid','uname'))){
				$this->fmv[$k] = $$k;
				continue; //,'uname'
			}
			unset($this->fmv[$k]);
		}
		$this->fmv['uid'] = $uid;
		if($type=='edit' && !empty($upassOrg)){
			$this->db->table($this->tbuacc)->data(array('upass'=>$upass))->where("uid='$uid'")->update();
		}elseif($type=='add'){
			$this->db->table($this->tbuacc)->data(basReq::in($data))->insert();
		}
	}
	// svEKey，
	function svEKey(){
		$this->svMoveFiles($this->fme['uid']);
		return preg_replace('/[^0-9A-Za-z\.\-]/','',$this->fme['uid']);
	}
	
	// opDelete。
	function opDelete($id){
		parent::opDelete($id);
		$row = $this->db->table($this->tbuacc)->where("uid='$id'")->find();
		if(!empty($row)){
			$this->db->table('users_uppt')->where("uname='$row[uname]'")->delete();
			$this->db->table($this->tbuacc)->where("uid='$id'")->delete(); 
		}
		return 1;
	}
	// opCopy。
	function opCopy($id){ //docs,users
		// get-kid
		$kar = glbDBExt::dbAutID($this->tbid,$this->kfix,$this->ktmp);
		$kid = $kar[0]; $kno = $kar[1];	
		// insert-2
		foreach(array($this->tbid, $this->tbuacc) as $tabid){
			$fm = $this->db->table($tabid)->where("uid='$id'")->find();
			$fm['uid'] = $kid; 
			$fm['uname'] = substr($fm['uname'],0,3).'_'.basKeyid::kidRand('24',8);
			//upass
			$this->db->table($tabid)->data(basReq::in($fm))->insert();	
		}
		return 1;
	}
	
	// edit-pass。
	static function editPass($mod,$uname){ //docs,users
		$pwold = basReq::val('pwold');
		$pwnew = basReq::val('pwnew');
		$pwrep = basReq::val('pwrep'); 
		$oldpass = comConvert::sysPass($uname,$pwold,$mod);
		$newpass = comConvert::sysPass($uname,$pwnew,$mod);
		if(empty($pwnew) || $pwnew!==$pwrep){
			$remsg = lang('du_noteq');
		}elseif($this->db->table("users_uacc")->where("uname='$uname' AND upass='$oldpass'")->find()){
			$this->db->table("users_uacc")->data(array('upass'=>$newpass))->where("uname='$uname'")->update();
			$remsg = lang('du_editok');
		}else{
			$remsg = lang('du_erold');
		}
		return $remsg;
	}

	
}
