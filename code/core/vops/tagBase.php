<?php
/*
*/
// 标签解析 基类
class tagBase{
	
	public $modid = '';
	public $paras = array();
	public $mcfg = array();

	public $sqlArr = array();
	public $sqlAll = '';
	public $whrArr = array();
	public $whrStr = '';
	public $jonArr = array();
	
	public $re = array();
	public $exFld1 = array('aip','eip','atime','etime','show',);
	public $exFld2 = array(); //did,cid,uid,aid,kid ... 
	public $db = NULL;
	
	function __construct($paras=array()) {
		$this->paras = $paras;
		$this->db = glbDBObj::dbObj(); 
		$this->setModid();
		$this->setFrom();
		$this->pJoin();
		$this->mcfg = glbConfig::read($this->modid);
	}
	
	// 所有公用: [idfix,top] -=> array('idfix','top')
	function p1Cfg($key='modid'){
		foreach($this->paras as $k=>$p1){
			if($p1[0]==$key){ 
				return $p1;
			}
		}
		return array();
	}
	// 所有公用: 
	function setModid(){
		$para = $this->p1Cfg('modid');
		if(empty($para)){
			$modid = $this->mod; //ucfg['mod']; //vopUrl::imkv(vopUrl::iget());
		}else{
			$modid = $para[1];
			if(isset($para[2])){
				$ext = $para[2];
				if(strstr($ext,'get')){
					$mod2 = basReq::val($modid,'','Key');
					global $_cbase;
					if($mod2 && isset($_cbase[$mod2])) $modid = $mod2; 
				}
			}
		}
		$this->modid = $modid;
	}
	// List, Page, One: 
	function setFrom(){ 
		$db = glbDBObj::dbObj(); 
		$_groups = glbConfig::read('groups');
		$mod = $this->modid;
		if($_groups[$mod]['pid']=='docs'){
			$tabid = 'docs_'.$mod;
			$ordef = 'did';
			$exFld2[] = 'did';
		}elseif($_groups[$mod]['pid']=='users'){
			$tabid = 'users_'.$mod;
			$ordef = 'atime';
			$exFld2[] = 'uid';
		}elseif($_groups[$mod]['pid']=='advs'){
			$tabid = 'advs_'.$mod;
			$ordef = 'atime';
			$exFld2[] = 'aid';
		}elseif($_groups[$mod]['pid']=='coms'){	
			$tabid = 'coms_'.$mod;
			$ordef = 'cid';
			$exFld2[] = 'cid';
		}elseif($_groups[$mod]['pid']=='types'){	
			$tabid = empty($_groups[$mod]['etab']) ? 'types_common' : 'types_'.$mod;
			$ordef = 'kid'; //不使用
			$exFld2[] = 'kid';
		}
		$this->sqlArr['tabid'] = $tabid;
		$this->sqlArr['prefix'] = $this->db->pre; 
		$this->sqlArr['suffix'] = $this->db->ext; 
		$this->sqlArr['ordef'] = $ordef;
		$this->exFld2 = $exFld2;
	}
	
	// Join (目前仅支持一个join), 最后加参数 id1+id2+id3
	// [join,INNER JOIN docs_news p ON p.did=m.pid]  -=>  原型
	// [join,mod,field,val]  -=>  INNER JOIN users_person u ON u.uname=d.auser  (有pid关系)
	// [join,detail]	  -=>  INNER JOIN dext_news	d ON d.did=m.did 
	function pJoin(){ 
		//global $_cbase; 
		//$_groups = glbConfig::read('groups');
		//$mod = $this->modid;
		//$pid = $_groups[$mod]['pid'];
		$cfg = $this->p1Cfg('join');
		$this->jonArr = $cfg; //print_r($cfg);
		/*
		$join = ''; 
		if(empty($cfg[1])){ 
			$join = '';
			$cid = 0;
		}elseif($cfg[1]=='detail' && in_array($pid,array('docs','users'))){
			$char = substr($pid,0,1);
			$join = 'INNER JOIN '.glbDBExt::getTable($mod,1)." d ON d.{$char}id=m.{$char}id";
			$cid = 2; 
		}elseif(isset($_groups[$cfg[1]])){ //pid
			$join = 'INNER JOIN '.glbDBExt::getTable($cfg[1]).' p ON p.'.$cfg[2].'=m.pid';
			$cid = 3;
		}elseif(!empty($cfg[1])){
			$join = $cfg[1];
			$cid = 2;
		}
		$cols = empty($cfg[$cid]) ? '*' : str_replace('+',',',$cfg[$cid]);
		//分析
		if($join){ 
			$a = explode(' ',$join);
			$a['fields'] = $cols;
			$a['full'] = str_replace($a[2],$this->sqlArr['prefix'].$a[2].$this->sqlArr['suffix'],$join);
			$this->jonArr = $a; 
		}*/

	}
	
	// [order,0,kid1+f02+f03]
	// order, odesc
	function pOrder(){ 
		$cfg = $this->p1Cfg('order');
		if(empty($cfg)){
			$order = '';
		}elseif(!empty($cfg[1])){
			$order = $cfg[1]; //认证?
		}elseif(empty($cfg[1]) && !empty($cfg[2])){
			$order = basReq::val('order','','Key',24); 
			$a = explode('+',$cfg[2]);
			if($order && !in_array($order,$a)){ //认证?
				$order = '';	
			}
		}else{
			$order = '';	
		}
		$order || $order = $this->sqlArr['ordef'];
		$odesc = basReq::val('odesc','1','N');
		$this->sqlArr['order'] = $order;
		$this->sqlArr['odesc'] = $odesc;
		$this->sqlArr['ofull'] = 'm.'.$order.($odesc ? ' DESC' : '');
	}
	
	// 
	function pStype(){ 
		$cfg = $this->p1Cfg('stype'); 
		if(empty($cfg)) return;
		//优先取标签属性,再去url的stype
		$stype = empty($cfg[1]) ? vopUrl::umkv('key','stype') : $cfg[1]; 
		if(empty($stype)) return;
		$_groups = glbConfig::read('groups'); 
		$pid = $_groups[$this->modid]['pid'];
		if($stype && in_array($pid,array('docs','advs'))){
			$sql = basSql::whrTree($this->mcfg['i'],'m.catid',$stype); 
			$this->whrArr[] = $sql ? substr($sql,5) : '';
		}elseif($stype && in_array($pid,array('users'))){
			$this->whrArr[] = "m.grade='$stype'";
		}else{ //交互等,无stype
			
		} 
		/*
		$ccfg = glbConfig::read($this->modid,'_c');
		if($fix && !empty($ccfg)){ 
			if(!empty($ccfg[$fix])){
				$exFlds = array_keys($ccfg[$fix]);
				if(!empty($exFlds)){
					$this->exFld2 = array_merge($this->exFld2,$exFlds); 
				}
			}
		}*/
	}
	
	// `-=>', [where,did=`2004-33-2ycx`]
	function pWhere(){ 
		$whr = ''; 
		foreach($this->whrArr as $w){
			$whr .= ' AND '.$w;
		}
		$cfg = $this->p1Cfg('where'); 
		if(!empty($cfg[1])){ 
			$whr .= (substr($cfg[1],0,5)!=' AND ' ? ' AND ':'').$cfg[1];  
		}
		if(substr($whr,0,5)==' AND ') $whr = substr($whr,5);
		$this->whrStr = $whr; 
	}
	
	function getJoin($re){ 
		if(empty($re)) return $re;
		$minfo = glbConfig::read($this->modid); 
		if(!empty($this->jonArr[1]) && $this->jonArr[1]=='detail'){
			if($minfo['pid']=='types'){
				$this->getJoinTypes($re);
			}
			if($minfo['pid']=='docs'){
				dopFunc::joinDext($re,$this->modid);
			}
		}
		return $re;
	}
	
	// glbDBExt::getTable($mod,$ext=0)
	function getJoinTypes(&$re){ 
		$ids = '';
		foreach($re as $k=>$v){
			$ids .= (empty($ids) ? '' : ',')."'".$v['kid']."'";
		} //echo "$ids"; print_r($re);
		if(empty($ids)) return;
		$re1 = $this->db->table(glbDBExt::getTable($this->modid,1))->where("kid IN($ids)")->select(); 
		$minfo = glbConfig::read($this->modid); 
		$re2 = array();
		foreach($re1 as $k1=>$v1){ 
			foreach($v1 as $k2=>$v2){
				if(isset($minfo['f'][$k2])){
					$re2[$v1['kid']][$k2] = $v2;
				}
			}
		}
		foreach($re as $k=>$v){
			if(isset($re2[$v['kid']])){
				$re[$k] += $re2[$v['kid']];
			}
		} //print_r($re);
	}
	
	function getData(){ 
		$type = $this->modid;
		$re[0] = array('kid'=>'docs','did'=>'yyyy-12-7890','title'=>'4-title'.$type);
		$re[1] = array('kid'=>'advs','aid'=>'yyyy-34-1234','title'=>'5-title'.$type);
		$re[2] = array('kid'=>'coms','cid'=>'yyyy-56-5678','title'=>'6-title'.$type);
		return $re;
	}

	function debug($key=''){ 
		if($key){
			echo "<pre>debug\n";
			print_r($this->$key);	
			echo "</pre>";
		}else{
			print_r($this->sqlArr);
			print_r($this->sqlAll);
			print_r($this->whrArr);
			print_r($this->whrStr);	
			if(isset($this->pgbar)) print_r($this->pgbar);	
		}
	}
	
}
