<?php
(!defined('RUN_MODE')) && die('No Init');

// dopAdvs(data OP for Advs)
class dopAdvs extends dopBase{	

	public $kfix = 'yyyy-md-';
	public $ktmp = '31';
	//public $tbuacc = '';
	public $_kid = 'aid';
	public $_kno = 'ano';	

	//function __destory(){  }
	function __construct($cfg){ 
		$mod = $cfg['kid'];
		parent::__construct($cfg,$cfg['pid']."_$mod");
		$this->typfid = $this->so->typfid = 'catid';
		$this->dskey  = $this->so->dskey  = 'title'; 
		$this->order  = $this->so->order  = basReq::val('order','atime');
		//$this->tbuacc  = 'users_uacc';
	}
	// 翻页条,批量操作
	function pgbar($idfirst,$idend){
		$pg = $this->pg->show($idfirst,$idend);
		$op = "".basElm::setOption("show|显示\ndel|删除\nhidden|隐藏",'','-批量操作-');
		dopFunc::pageBar($pg,$op);
	}
	// 搜索条 // check,fields
	function sobar($msg='',$width=30){ 
		$file = basReq::val('file');
		$mod = $this->mod;
		$sbar = "\n".$this->so->Type(120,'-栏目-'); 
		$sbar .= "\n&nbsp; ".$this->so->Word(80,80,'-筛选-');
		$sbar .= "\n&nbsp; ".$this->so->Show(60);
		$sbar .= "\n&nbsp; ".$this->so->Order(array('aid' => '账号(降)','aid-a' => '账号(升)',));
		$updlink = ($this->cfg['etab']==4) ? '更新' : "<a href='?file=$file&mod=$mod&view=list&umod=upd'>更新</a>";
		$this->so->Form("[$updlink] | ".$sbar,$msg,$width);
	}
	
	// avLink // 
	function avLink($r){ 
		if($this->cfg['etab']==3){
			return '#';
		}elseif(!empty($r['pmod']) && !empty($r['pid']) && empty($r['url'])){
			return "/plus/ajax/redir.php?$r[pmod].$r[pid]";
		}else{
			return empty($r['url']) ? '#' : $r['url'];
		}
	}

	// PKey pMod,pKey资料
	function fmPKey(){ 
		global $_cbase;
		$_groups = glbConfig::read('groups');
		$mod = $this->mod;
		$val = @$this->fmo['pid']; $val || $val = basReq::val('pid');
		$pmod = @$this->fmo['pmod'];
		$arr = admPFunc::modList(array('docs','users'),$pmod); //'coms',
		$sels = "<select id='fm[pmod]' name='fm[pmod]'>".basElm::setOption($arr,$pmod)."</select>";
		$item = "<input name='fm[pid]' type='text' id='fm[pid]' value='$val'> &nbsp; ";
		//$item .= "<input type='text' id='fm[refname]' value='' class='txt w240'>";
		$item .= "$sels<input type='button' value='来源资料' onclick=\"pickOpen('fm[pmod]','','fm[pid]','fm[title]',1)\" class='btn'>";
		glbHtml::fmae_row('来源资料',$item);
		glbHtml::fmae_row('所在栏目',$this->fmType('catid').' &nbsp; 显示'.$this->fmShow());
		//echo "<tr><th>详情资料</th><th class='tr'>---</th></tr>\n";
	}

	// 属性设置
	function fmProp(){ 
		dopFunc::fmSafe();
		$mod = $this->mod;
		echo "<tr><th nowrap>属性设置</th><th class='tr'>---</th></tr>\n";
		$this->fmAE3();
	}
	// PKey pMod,pKey资料
	function svPKey($type='add'){ 
		$pid = preg_replace('/[^0-9A-Za-z\.\-]/','',@$this->fme['pid']);
		$pid && $this->fmv['pid'] = $pid;
		$this->fmv['pmod'] = @$this->fme['pmod'];
	}
	// svEKey，
	function svEKey(){
		$aid = basReq::val('aid');
		$this->svMoveFiles($aid);
		return preg_replace('/[^0-9A-Za-z\.\-]/','',$aid);
	}
	
}
