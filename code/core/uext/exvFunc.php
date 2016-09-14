<?php

// 显示相关函数; 单独函数可先用new exvFunc();自动加载
class exvFunc{

	// comjs.php使用
	static function actMods($act,$key){
		if(strstr($act,';')){ 
			$a = explode(';',$act);
			foreach($a as $v){ 
				if(strstr($v,$key)){ 
					return str_replace("$key:","",$v);
				}
			}
			return '';
		}else{
			return basReq::val('mod','');	
		}
	}

}
