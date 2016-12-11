<?php
/*
单个模板扩展函数
*/ 
class tex_devs{ //extends tex_base
	
	static $dcfg = array('basic','main',); //'base','frame'
	static $data = array();

	#protected $prop1 = array();
	
	static function getKeyTitle($mod,$key){
		if(empty(self::$data[$mod])){
			foreach (self::$dcfg as $mkey) {
				$data = comFiles::get(vopTpls::pinc("c_demo/{$mod}_{$mkey}",'.tpl.htm'));
				if($data){
					self::$data[$mod] = $data;
					break;
				}
			}
		}
		$data = self::$data[$mod]; //dump($data);
		// 	'tplsuit' => '整套模版',
		preg_match_all("/['|\"]{1}{$key}['|\"]{1}\s*\=\>\s*['|\"]{1}([^\']+)['|\"]{1}\,/is", $data, $m);
		$re = empty($m[1][0]) ? "[$key]" : $m[1][0]; //dump($m);
		return $re;
	}

}
