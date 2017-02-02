<?php

// 计划任务类
 
class comCron{
    
    public $stamp = 0; 
    public $rgap = '8m'; //时间间隔：5-10min
    public $frun = '/store/_rlock_cron.txt';
    
    public $db = NULL; 
    public $tab = 'bext_cron'; 
    public $jobs = array(); 
    public $jres = array(); 

    // 
    static function run($file=''){
        $row['kid'] = $file;
        $re = self::rone($row);
    }

    //function __destory(){  }
    function __construct($upd=1){ 
        $this->init();
        $this->rlist();
        $upd && $this->update();
    }
    
    // init
    function init(){
        $this->db = db();
        $this->stamp = time();
        if(!tagCache::chkUpd($this->frun,$this->rgap)){ 
            $whr = " exnext<'".$this->stamp."' AND enable=1 AND hkflag=0"; //echo($whr);
            $this->jobs = $this->db->table($this->tab)->where($whr)->select();
        } 
    }
    
    // 运行列表
    function rlist(){
        if(!empty($this->jobs)){
            foreach($this->jobs as $row){
                $rdo = $this->rone($row);
                $next = $this->rnext($row,$rdo);
                $this->jres[$row['kid']] = array(
                    'rdo' => $rdo, 
                    'next' => $next,
                );
            }
        }
    }
    
    // 计算下次运行时间
    // excunit=w,d,h  excycle=1-127
    function rnext($row,$rdo){
        $ctime = tagCache::CTime($row['excycle'].$row['excunit']);
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
            $this->db->table($this->tab)->data($data)->where("kid='$file'")->update(0); 
        } 
        comFiles::put(DIR_DTMP.$this->frun,date('Y-m-d H:i:s'));
    }

    
    // 运行一个任务
    static function rone($row,$data=array()){
        $db = db();
        $stamp = time();
        $file = "/adpt/cron/{$row['kid']}.php"; 
        if(!empty($row['cfgs'])){
            // run-sql
        }elseif(file_exists(DIR_CODE.$file)){
            include_once(DIR_CODE.$file);
        }else{
            // logger???
        }
        return empty($rdo) ? 'fail' : $rdo;
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
