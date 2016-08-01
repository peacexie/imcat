<?php
(!defined('RUN_MODE')) && die('No Init');

// dopCheck(data OP for Extra)
class dopCheck extends dopBase{	

	public $excfg = array();
	public $user = NULL;
	public $uname = '';
	public $ugrade = '';

	static function addInit($cfg=array(),$percheck=array()){ 
		$chk = new self($cfg);
		if(empty($chk->excfg)) return;
		foreach ($chk->excfg as $key => $val) {
			if(empty($val)) continue;
			$method = 'chk'.ucfirst(strtolower($key));
			if(substr($key,0,3)=='ap_'){
				$ngrade = substr($key,2);
				if($chk->ugrade==$ngrade){
					$chk->chkAllpub($val);
					unset($chk->excfg['allpub']);
				}
			}elseif(method_exists($chk,$method)){
				$chk->$method($val);
			}
		}
		#dump($chk->excfg);
	}

	function __construct($cfg=array()){ 
		parent::__construct($cfg);
		$this->excfg = basElm::text2arr($this->cfg['cfgs']);
		$this->user = usrBase::userObj('Member'); 
		$this->uname = empty($user->uinfo['uname']) ? '(null)' : $user->uinfo['uname'];
		$this->ugrade = empty($user->uperm['grade']) ? '(null)' : $user->uperm['grade'];
		$this->tabid = glbDBExt::getTable($this->cfg['kid']); 
	}

	// showdef=1

	// login=1(登录发布)
	// login=cvip,ccom(会员cvip,ccom等级:登录发布)
	function chkLogin($ngrades=0){ 
		$clogin = 1; 
		if(!is_numeric($ngrades)){
			if(strpos("(,$ngrades,)",",{$this->ugrade},")<=0){
				glbHtml::end("操作无权限[$ngrades]！");
			}
		}else{ 
			// stop
			if(strpos($this->ugrade,'stop')>0){
				glbHtml::end("操作无权限[$this->ugrade]！");
			}
			if($this->user->userFlag!='Login'){
				glbHtml::end("操作无权限[login]，请先登录！");
			}
		}
	}
	
	// ap_ccom=500(会员ccom等级:发布总量) -> (500,'ccom')
	// allpub=100(会员发布总量)
	// skip_allpub=cvip,ovip(cvip,ovip不检测)
	function chkAllpub($num=0){ 
		if(!empty($this->excfg['skip_allpub'])){
			if(strpos("(,{$this->excfg['skip_allpub']},)",",{$this->ugrade},")>0){
				return;
			}
		}
		$cnt = $this->db->table($this->tabid)->where("auser='{$this->uname}'")->count();
		if($cnt>=$num){
			glbHtml::end("超过发布限额，发布总量[$num]条！");
		}
	}

	// ippub=5(ip日发布量)
	// skip_ippub=cvip,ovip(cvip,ovip不检测)
	function chkIppub($num=0){ 
		if(!empty($this->excfg['skip_ippub'])){
			if(strpos("(,{$this->excfg['skip_ippub']},)",",{$this->ugrade},")>0){
				return;
			}
		}
		$cnt = $this->db->table($this->tabid)->where("aip='".basEnv::userIP()."' AND atime>='".(time()-86400)."'")->count();
		if($cnt>=$num){
			glbHtml::end("超过发布限额，ip日发布量[$num]条！");
		}
	}

	// iprep=3(ip重复发布时间间隔)
	// skip_iprep=cvip,ovip(cvip,ovip不检测)
	function chkIprep($num=0){ 
		if(!empty($this->excfg['skip_iprep'])){
			if(strpos("(,{$this->excfg['skip_iprep']},)",",{$this->ugrade},")>0){
				return;
			}
		}
		$cnt = $this->db->table($this->tabid)->where("aip='".basEnv::userIP()."' AND atime>='".(time()-$num)."'")->count();
		//dump($cnt); dump($num);
		if($cnt>0){
			glbHtml::end("重复发布时间间隔限额，<br>时间间隔需>[$num]秒！");
		}
	}

	static function headComm(){
		global $_cbase;
		glbHtml::page($_cbase['sys_name'],1);
		glbHtml::page('imadm','sadm.css');
		glbHtml::page('body',' style="padding:8px 5px 5px 5px;overflow-y:scroll;overflow-x:hidden;"'); 
	}

}

/*
showdef=1
login=1
ap_ccom=500  ap_xxx在allpub前面
allpub=100   skip_allpub=cvip,ovip
ippub=5      skip_ippub
iprep=3      skip_iprep
*/
