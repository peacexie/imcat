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
			return req('mod','');	
		}
	}

	// navShow-显示
	static function navShow($arr,$act='1',$url=''){
		glbHtml::page();
		echo "<base target='_blank'/>";
		echo "<ur>\n";
		foreach ($arr as $key => $val) {
			echo "<li>";
			if($act==1){
				echo "<a href='?$val'>$val</a>";
			}elseif($act=='{key}'){
				$urli = str_replace('{key}',$key,$url);
				echo "<a href='$urli'>$val</a>"; 
			}elseif($act=='act'){
				echo "<a href='?act=$val'>$val</a>"; 
			}else{
				echo "$key = ($val)";
			}
			echo "</li>\n"; 
		}
		echo "</ur>\n";
		glbHtml::page('end');
	}

}
