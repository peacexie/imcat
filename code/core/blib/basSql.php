<?php

// basSql类
class basSql{	
	
	// type=a/e; $re='arr/str'; ip=''
	static function logData($type='a',$ip='',$time=0,$user=''){ //,$re='arr'
		global $_cbase;
		$unow = usrBase::userObj();
		$cfg = array(
			'ip' => $ip ? $ip : $_cbase['run']['userip'],
			'time' => $time ? $time : $_cbase['run']['stamp'],
			'user' => $user ? $user : @$unow->uinfo['uname'],
		);
		$re = array();
		foreach($cfg as $k=>$v){
			$re["$type$k"] = $v;	
		}
		return $re;
	}
	
	// 2013-12-31; 5(天)
	static function whrDate($key,$val,$op,$cfg='isdate'){
		//'fmextra' => 'datetm'
		global $_cbase;
		$val = preg_replace('/[^0-9A-Za-z,\.\-\ \:]/','',$val);
		if( in_array($key,array('atime','etime')) || @$cfg=='isdate' || @$cfg['f'][$key]['mfextra']=='datetm' ){
			if(is_numeric($val)){
				$valbase = strtotime(date('Y-m-d',$_cbase['run']['timer']));
				$val = $valbase - $val*86400;
			}else{ 
				$val = strtotime($val); 
			}
			if($op=='<' && strstr($val,':')) $val += 86401;	
		} //echo "<br>$val:$sql:".date('Y-m-d H:i:s',$val);
		$sql = " AND $key$op='$val'"; 
		return $sql;
	}
	
	/**
	 * 地图中参照物周边的查询子串
	 * @param int $x,$y	 	参照目标的坐标
	 * @param int $diff			指定范围，单位为km或度
	 * @param int $mode	 	计算模式，0按度数，1按实际距离//???
	 * @param string $fname		查询的字段名（包含表别名前缀）
	 * m.didu_0>=22.456 AND m.didu_0<=52.456 AND m.didu_1>=50.33 AND m.didu_1<=150.33
	 */
	public static function whrMap($x,$y,$diff,$mode,$fname){		
		if(!$diff) return '';
		$mode = empty($mode) ? 0 : 1;
		$x = floatval($x);
		$y = floatval($y);
		$dfx = $dfy = $diff = abs(floatval($diff));
		if($mode == 1){
			$radius = 6371;//km
			$dfx = $diff / (2 * $radius * M_PI) * 360;
			$dfy = $diff / (2 * $radius * M_PI * cos(deg2rad($x))) * 360;
		}
		$re = $fname.'_x>='.($x - $dfx).' AND '.$fname.'_x<='.($x + $dfx);
		$re .= " AND {$fname}_y>=".($y - $dfy)." AND {$fname}_y<=".($y + $dfy)."";
		return $re;
		//$dmin = $y - $dfy; $dmax = $y + $dfy; // $dmin<-180 // $dmax>180 // $dfx>30 || $dfy>60
	}
	
	// 栏目，类别系的子类别
	static function whrTree($items,$key,$val){
		$ids = comTypes::getSubs($items,$val); 
		$ids = array_keys($ids); $ids[] = $val;
		$sql = " AND $key IN('".implode("','",$ids)."')";  
		return $sql;
	}
	
	// 单选/多选/单选按钮:可用用文字搜索
	static function whrScbr($cfgs,$sfid,$sfkw){
		$cfg = $cfgs[$sfid]; 
		$arr = basElm::text2arr($cfg); 
		if(isset($arr[$sfkw])){
			$sql = " AND $sfid='$sfkw' ";
		}else{
			$keys = array();
			foreach($arr as $k=>$v){
				similar_text($v,$sfkw,$per); 
				if($per>60){
					$keys[] = $k;
				}
			}
			if(!empty($keys)){
				$sql = " AND $sfid IN('".implode("','",$keys)."')";
			}else{
				$sql = " AND $sfid='((.null.))'";
			}
		} //print_r($sql);
		return $sql;
	}
	
	// ids : array, id1,id2...
	static function whrInids($ids,$sp=','){
		$arr = is_array($ids) ? $ids : explode($sp,$ids);
		$arr = array_unique(array_filter($arr));
		$arr = str_replace("'","",$arr);
		$str = empty($arr) ? '' : "'".implode("','",$arr)."'";
		return $str;
	}
	
	// 格式化sql		
	static function fmtShow($sql,$hlight=0){
		if($hlight=='2'){
			$arr = array("INNER JOIN","WHERE","ORDER BY","GROUP BY","LIMIT","FORCE INDEX",);
			$sql = basStr::filTrim($sql); 
			foreach($arr as $v) $sql = str_replace($v,"\n$v",$sql);	
			$sql = str_replace(") AND",") \n AND",$sql); 
		}else{
			require_once(DIR_VENDOR.'/sql-formatter/lib/SqlFormatter.php');
			$sql = SqlFormatter::format($sql, $hlight);			
		}
		return $sql;
	}
	static function fmtCount($str,$field='_rec_count_'){
		return $str ? preg_match('/^(.+?)\s+GROUP\s+BY(.+)$/is',$str,$arr) ? "SELECT COUNT(DISTINCT $arr[2]) ".stristr($arr[1],'FROM') : ("SELECT COUNT(*) AS $field ".stristr($str,'FROM')) : '';
	}
	
	/**
	 * 处理搜索关键字，处理后：可搜索%_特殊字符，
	 * demo: AND (a.subject ".fmtKeyWD($keyword).")";
	 *
	 * @param  string $keyword 要转换的字符串
	 * @param  string $multi =1时，*，空格当成通配符处理
	 * @return string $sqlstr 返回sql子字符串，包含 LIKE
	 */
	static function fmtKeyWD($keyword,$multi=1){
		$keyword = addcslashes($keyword,'%_');
		$multi && $keyword = str_replace(array(' ','*'),'%',$keyword);
		return " LIKE '%$keyword%' ";
	}
	
}