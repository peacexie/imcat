<?php

// 积分
 
class extJifen{
	
	//private $stamp = 0; 
		
	//function __destory(){  }
	function __construct($file=0,$upd=1){ 
		//$this->init($file);
	}
	
	static function update(){
		$db = glbDBObj::dbObj(); 
		$list = $db->table('bext_paras')->where("pid='jifen_grade'")->order('top')->select();
		$arr = array(); 
		foreach($list as $r){
			$arr[$r['kid']] = array('title'=>$r['title'],'numa'=>$r['numa'],'icon'=>$r['cfgs'],);
		}
		glbConfig::save($arr,'jifen','dset'); 
		return $arr;
	}
	
	static function grade($mark=0,$re='title'){
		$jfcfg = glbConfig::read('jifen','dset'); 
		$jftitle = lang('core.no_rank');
		$jfnow = array('kid'=>'-null-','title'=>$jftitle,'icon'=>'-null-'); 
		foreach($jfcfg as $k=>$v){
			if($v['numa']>=$mark){
				$jftitle = $v['title'];	
				$jfnow = array('kid'=>$k,'title'=>$jftitle,'icon'=>$v['cfgs']);
				return;
			}
		}
		return $re=='arr' ? $jfnow : $jftitle;
	}
	
	// act : add,del
	static function main($mcfg,$act,$msg=''){
		$db = glbDBObj::dbObj(); 
		$key = "cr$act";
		if(empty($mcfg[$key])){
			return;	
		}else{
			$mcfg[$key] = intval($mcfg[$key]);
		}
		// addcr
		$op = $act=='add' ? '+' : '-';
		$sql = "UPDATE ".$db->table('users_uacc',2)." SET ujifen=ujifen{$op}".$mcfg[$key]." WHERE uname='{$mcfg['auser']}'";
		$db->query($sql);
		// logger
		$data = basSql::logData('a');
		$data['kid'] = basKeyid::kidTemp('3.4').basKeyid::kidRand('24',4);
		$data['act'] = $act; 
		$data['uto'] = $mcfg['auser'];
		$data['jifen'] = $mcfg[$key];
		$data['jfmod'] = $mcfg['kid'];
		$data['note'] = $msg ? $msg : "{$mcfg['kid']}:$act";
		$db->table("logs_jifen")->data($data)->insert();
		//echo "<pre>"; print_r($mcfg); print_r($mcfg); die();
	}
	

}

/*

*/
