<?php

// 计划任务类
 
class comCron{
	
	private $stamp = 0; 
	private $clock = '/store/_cron_lock.txt'; 
	private $rgap = '8m'; //时间间隔：5-10min
	
	private $db = NULL; 
	private $tab = 'bext_cron'; 
	private $jobs = array(); 
	private $jres = array(); 
		
	//function __destory(){  }
	function __construct($file=0,$upd=1){ 
		$this->init($file);
		$this->run();
		$upd && $this->update();
	}
	
	// init
	function init($file=0){
		$this->db = db();
		$this->stamp = time();
		if($file){
			$this->jobs = $this->db->table($this->tab)->where("kid='$file'")->select();
			return;
		} //print_r($this->jobs);
		if(!tagCache::chkUpd($this->clock,$this->rgap)){
			$this->jobs = $this->db->table($this->tab)->where("(exnext<'".$this->stamp."') AND enable=1")->select();
		}
	}
	
	// 运行列表
	function run(){
		if(!empty($this->jobs)){
			foreach($this->jobs as $row){
				$rdo = $this->rone($row['kid']);
				$next = $this->rnext($row,$rdo);
				$this->jres[$row['kid']] = array(
					'rdo' => $rdo, 
					'next' => $next,
				);
			}
		}
	}
	
	// 运行一个任务
	function rone($file){
		$db = $this->db;
		$stamp = $this->stamp;
		$file = "/adpt/cron/$file.php"; 
		if(file_exists(DIR_CODE.$file)){
			include_once(DIR_CODE.$file);
		}
		return empty($rdo) ? 'fail' : $rdo;
	}
	
	// 计算下次运行时间
	// excunit=w,d,h  excycle=1-127
	function rnext($row,$rdo){
		$cfgs = array(
			'w' => 7*86400,
			'd' => 86400,
			'h' => 3600,
		);
		$cunit = isset($cfgs[$row['excunit']]) ? $cfgs[$row['excunit']] : 30*86400;
		$cycle = intval($row['excycle'])>0 ? intval($row['excycle']) : 12;
		$ctime = $cycle * $cunit; //echo "$ctime = $cycle * $cunit";
		$stfix = date('i')*60 + date('s'); $stfix = $stfix > 1800 ? (3600-$stfix) : ($stfix-3600); //修正整点
		$exsecs = intval(substr($row['exsecs'],0,2))*60 + intval(substr($row['exsecs'],3,2)); 
		$next = $this->stamp + $stfix + $ctime + $exsecs; 
		return $next;
	}
	
	// update
	function update(){
		if(empty($this->jres)) return;
		foreach($this->jres as $file=>$row){
			$data = array('exlast'=>$this->stamp);
			if($row['rdo']=='pass') $data['exnext'] = $row['next'];
			$this->db->table($this->tab)->data($data)->where("kid='$file'")->update(); 
		}
		comFiles::put(DIR_DTMP.$this->clock,date('Y-m-d H:i:s'));
	}

}

/*

UPDATE bext_cron
	SET exnext = CASE kid
		WHEN '1' THEN '3'
		WHEN 2 THEN 4
		WHEN 3 THEN 5
	END,
	exlast = CASE kid
		WHEN 1 THEN '{$this->stamp}'
		WHEN 2 THEN 'New Title 2'
		WHEN 3 THEN 'New Title 3'
	END
WHERE id IN (1,2,3)

$display_order = array(
	1 => 4,
	2 => 1,
	3 => 2,
	4 => 3,
	5 => 9,
	6 => 5,
	7 => 8,
	8 => 9
);
$ids = implode(',', array_keys($display_order));
$sql = "UPDATE categories SET display_order = CASE id ";
foreach ($display_order as $id => $ordinal) {
	$sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
}
$sql .= "END WHERE id IN ($ids)";
echo $sql;

*/
