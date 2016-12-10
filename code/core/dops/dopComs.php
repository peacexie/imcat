<?php
(!defined('RUN_INIT')) && die('No Init');

// dopComs(data OP for Coms)
class dopComs extends dopBase{	

	public $kfix = 'yyyy-md-';
	public $ktmp = '31';
	//public $tbuacc = '';
	public $_kid = 'cid';
	public $_kno = 'cno';	

	//function __destory(){  }
	function __construct($cfg,$percheck=array()){ 
		$mod = $cfg['kid'];
		parent::__construct($cfg,$cfg['pid']."_$mod");
		if(!empty($percheck)){
			dopCheck::addInit($cfg,$percheck);
		}
		$this->typfid = $this->so->typfid = 'pid';
		$this->dskey  = $this->so->dskey  = 'title'; 
		$this->order  = $this->so->order  = req('order','atime');
		//$this->tbuacc  = 'users_uacc';
	}
	// 翻页条,批量操作
	function pgbar($idfirst,$idend,$exop=""){ //\ndnow|删除当前
		$pg = $this->pg->show($idfirst,$idend);
		$op = "".basElm::setOption(lang('flow.op_op3')."\n$exop",'',lang('flow.op0_bacth'));
		dopFunc::pageBar($pg,$op);
	}
	// 搜索条 // check,fields
	function sobar($msg='',$width=30){ 
		$mod = $this->mod;
		$sbar = "\n".$this->so->Type(90,'-pKey-'); 
		$sbar .= "\n&nbsp; ".$this->so->Word(80,80,lang('flow.op0_filt'));
		$sbar .= "\n&nbsp; ".$this->so->Show(60);
		$sbar .= "\n&nbsp; ".$this->so->Order(array('cid' => lang('flow.dops_ordkidd'),'cid-a' => lang('flow.dops_ordkida'),));
		$this->so->Form($sbar,$msg,$width);
	}

	// PKey pMod,pKey资料
	// hshow : 隐藏 显示开关
	// hrel ：隐藏 关联信息
	function fmPKey($hshow=0,$head=1,$hrel=0){ 
		$_groups = read('groups');
		$mod = $this->mod;
		$pid = @$this->fmo['pid']; 
		$pid || $pid = req('pid');
		$pmod = @$_groups[$mod]['pmod'];
		if($pmod){
			$pmname = $_groups[$pmod]['title'];
			$ptitle = $pid ? dopFunc::vgetTitle($pmod,$pid) : '';
			$item = "<input name='fm[pid]' type='text' id='fm[pid]' value='$pid' reg='fix:xid' readonly tip='".lang('flow.dc_c1024')."'> &nbsp; ";
			$item .= "<input type='text' id='fm2[refname]' value='$ptitle' class='txt w240' readonly>";
			$item .= "<input type='button' value='".lang('flow.dops_reldata')."' onclick=\"pickOpen('$pmod','','fm[pid]','fm2[refname]',1)\" class='btn'>";
		}else{
			$item = "<input id='fm[pid]' name='fm[pid]' type='text' value='".lang('flow.dc_norel')."' maxlength=24 class='txt w240 dis' disabled />";
		}
		glbHtml::fmae_row(lang('flow.dops_reldata'),$item,$hrel);
		glbHtml::fmae_row(lang('flow.dops_ishow'),$this->fmShow(),$hshow);
		if($head) echo "<tr><th>".lang('flow.dops_detail')."</th><th class='tr'>---</th></tr>\n";
	}

	// 属性设置
	function fmProp($head=1,$hid=0){ 
		dopFunc::fmSafe();
		$mod = $this->mod;
		if($head) echo "<tr><th nowrap>".lang('flow.title_attrset')."</th><th class='tr'>---</th></tr>\n";
		$this->fmAE3($hid);
	}
	// PKey pMod,pKey资料
	function svPKey($type='add'){ 
		$pid = preg_replace('/[^0-9A-Za-z\.\-]/','',@$this->fme['pid']);
		$pid && $this->fmv['pid'] = $pid;	
	}
	// svEKey，
	function svEKey(){
		$cid = req('cid');
		$this->svMoveFiles($cid);
		return preg_replace('/[^0-9A-Za-z\.\-]/','',$cid);
	}
	
}
