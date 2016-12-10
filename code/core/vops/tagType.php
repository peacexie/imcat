<?php
(!defined('RUN_INIT')) && die('No Init');

/*
{tag:flag4=[listType,re1][modid,china][idfix,top]}
idfix: top,all, sun:idx, sall:idx, id+id+id
	   [idfix,sun:p1012][idfix,sall:p1012]
keywd: [keywd,er]  
*/
// 标签解析 (类别列表)类
class tagType extends tagBase{
	
	//protected $re = array();
	
	function __construct($paras=array()) {
		parent::__construct($paras); 
		$this->re = $this->mcfg['i'];
		$this->pIdfix();
		$this->pKeywd();
	}
	
	function pIdfix(){ 
		$cfg = $this->p1Cfg('idfix');
		if(!empty($cfg)){
			$fix = $cfg[1];
			if($fix=='top'){
				$this->re = comTypes::getSubs($this->re,'0','1');
			}elseif(strstr($fix,'+')){
				$fix2 = explode('+',$fix);
				foreach($this->re as $k=>$v){
					if(!in_array($k,$fix2)){
						unset($this->re[$k]);
					}
				}
			}elseif(strstr($fix,'sun:')){
				$fix2 = str_replace('sun:','',$fix);
				$deep = @$this->re[$fix2]['deep']+1;
				$this->re = comTypes::getSubs($this->re,$fix2,$deep);
			}elseif(strstr($fix,'sall:')){
				$fix2 = str_replace('sall:','',$fix);
				$this->re = comTypes::getSubs($this->re,$fix2);	
			}	
		}
	}
	
	function pKeywd(){ 
		$cfg = $this->p1Cfg('keywd');
		if(!empty($cfg)){
			$fix = $cfg[1]; 
			foreach($this->re as $k=>$v){
				if(strstr($k,$fix) || strstr($v['title'],$fix)){
					//;	
				}else{
					unset($this->re[$k]);	
				}
			}
		}
	}
	
	function getData(){ 
		$re = array();
		foreach($this->re as $k=>$v){
			$v['kid'] = $k;
			$re[] = $v;
		}
		return $this->getJoin($re);
	}

}
