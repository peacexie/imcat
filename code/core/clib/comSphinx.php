<?php
(!defined('RUN_INIT')) && die('No Init');
include_once(DIR_STATIC.'/ximp/class/sphinxapi.cls_php'); 

// Sphinx搜索类
class comSphinx {
	
	public $cfgs = array();
	public $idxs = array();
	public $so = null;

	public function __construct() {
		//spcfgs
		$spcfgs = read('sphinx','ex');
		$this->cfgs = $spcfgs['cfgs'];
		$this->idxs = $spcfgs['index'];
		$host = $this->cfgs['host'];			//服务ip
		$port = $this->cfgs['port'];			//服务端口
		$mode = SPH_MATCH_EXTENDED2;			//匹配模式
		$ranker = SPH_RANK_PROXIMITY_BM25;		//统计相关度计算模式，仅使用BM25评分计算
		//初始化sphinx
		$this->so = new SphinxClient(); 
		$this->so->SetServer($host, $port); 
		$this->so->SetConnectTimeout(3);
		$this->so->SetArrayResult(true);
		$this->so->SetMatchMode($mode);
		$this->so->SetRankingMode($ranker);
	}
	
	/**
	 * 搜索
	 * @param string $qstr		关键词	    类似sql like'%$q%'
	 * @param string $spmod		sp_model	ex_sphinx.php里面设置
	 * @param array $fields		字段过滤  	show=>1, show=>array(1), tagid=>array(1,2,3), atime=>array('min'=>123,'max'=>456)
	 * @param array $opt 		选项		$offset=0, $limit=10, $ordby='@id desc'
	 */
	public function search($qstr, $spmod='', $fields=array(), $opt=array()) {
		$spidx = empty($this->idxs[$spmod]) ? '*' : $this->idxs[$spmod];
		// 字段过滤
		if(!empty($fields)) {
			foreach($fields as $fid=>$arr){
				if(is_array($arr) && isset($arr['min']) && isset($arr['max'])){
					$this->so->SetFilterRange($fid, $arr['min'] , $arr['max'], false);
				}else{
					$this->so->SetFilter($fid, is_array($arr) ? $arr : array($arr));
				}
			} 
		}
		// opt
		$offset = isset($opt['offset']) ? $opt['offset'] : 0;
		$limit = isset($opt['limit']) ? $opt['limit'] : 0;
		$ordby = isset($opt['ordby']) ? $opt['ordby'] : ''; 
		if($limit) {
			$this->so->SetLimits($offset, $limit, ($limit>1200) ? $limit : 1200);
		}
		if($ordby) { //排列
			$this->so->SetSortMode(SPH_SORT_EXTENDED, $ordby);
		} echo "$qstr:$spidx,$ordby";
		// res
		$res = $this->so->Query($qstr, $spidx);
		return $res;
	}
	
	public function idstr($res){
		$r = array();
		if(empty($res['matches'])) return 0;
		return implode(',', array_keys($res['matches']));
		//foreach($res['matches'] as $k=>$v) $r[] = $v['id'];
		//return implode(',', $r);
	}

	public function rec($res){
		// $ids && $query = $db->query(" SELECT * $fromsql WHERE spid in ($ids) "); 
		return implode(',', $r);
	}

	

}

/*

这里解释一下：
$sphinx->setFilter(‘tagid’, array(2,3,4));
是表示含有标签值2,3,4中的任意一个即符合筛选，这里是or关系。

$sphinx->setFilter(‘tagid’, array(2));
$sphinx->setFilter(‘tagid’, array(3));
$sphinx->setFilter(‘tagid’, array(4));
设置三个filter是标示，要同时满足2,3,4三个属性值才符合，这里是and关系。

	//sphinx全文搜索
	$cl = new search_interface();
	
	$fields = array();
	$fields['caid']=$caids;
	$checked != -1 && $fields['checked']=$checked;
	//$ccid7 && $fields['ccid7']=','.$ccid7.',';

	$fieldsrange = array();
	$outdays && $fieldsrange['createdate']= array(0,$timestamp - 86400 * $outdays);
	$indays && $fieldsrange['createdate'] = array($timestamp - 86400 * $indays,$timestamp);

	$res = $cl->search('@subject '.$keyword,$fields,$fieldsrange,intval(($page - 1) * $atpp),intval($atpp));

	$counts = $res['total']; var_dump($res);
	$ids = implode(',', $cl->ids($res));
	$ids && $query = $db->query(" SELECT * $fromsql WHERE aid in ($ids) "); 
*/
