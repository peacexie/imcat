<?php

// Element基本html元素类
class basElm{	

	// option,"upd|更新;del|删除;show|启用", 符号;\n表示一行, 符号,|=分开键值
	//		"upd,更新;del|删除\nshow=启用"
	static function setOption($cfgs,$val='',$title='-请选择-'){
		$cfgs = self::text2arr($cfgs); //print_r($cfgs);
		if($title) $cfgs = array(''=>$title) + $cfgs; 
		$str = '';
		foreach($cfgs as $k=>$v){ 
			$v = is_string($v) ? $v : $v['title']; // isset($v['title']), 某些环境下出错...
			if(strstr($k,'^group^')){
				$str .= "\n<optgroup label='$v'></optgroup>";
			}else{
				$def = "$val"==="$k" ? 'selected' : ''; 
				$str .= "\n<option value='$k' $def>$v</option>";	
			}
		}
		return $str;
	}
	// checkbox,
	static function setCBox($k0,$cfgs,$val='',$brn=0){
		$cfgs = self::text2arr($cfgs);
		$varr = empty($val) ? array() : explode(',',$val);
		$str = "\n<input id='fm[$k0][]' name='fm[$k0][]' type='hidden' value='' />"; 
		$i = 0; $j = 0;
		foreach($cfgs as $k=>$v){ $i++; $j++;
			$v = is_string($v) ? $v : $v['title'];
			$def = in_array($k,$varr) ? " checked='checked' " : '';
			if($brn && $j>$brn) { $str .= "<br>"; $j=1; }
			$str .= "\n<label><input type='checkbox' class='rdcb' name='fm[$k0][]' id='fm_{$k0}_$i' value='$k' $def>$v</label>";
		}
		return $str;
	} // array_shift(), array_filter()
	// radio,
	static function setRadio($k0,$cfgs,$val='',$brn=0){
		$cfgs = self::text2arr($cfgs);
		$str = ""; $i = 0; $j = 0;
		foreach($cfgs as $k=>$v){ $i++; $j++;
			$v = is_string($v) ? $v : $v['title'];
			$def = "$val"==="$k" ? " checked='checked' " : '';
			if($brn && $j>$brn) { $str .= "<br>"; $j=1; }
			$str .= "\n<label><input type='radio' class='rdcb' name='fm[$k0]' id='fm_{$k0}_$i' value='$k' $def>$v</label> ";
		}
		return $str;
	}
	
	// arr2text: full,外观图;side,侧面图;inn,内部图;att,附件图;
	static function arr2text($arr,$row=';',$col=','){ 
		if(is_string($arr)) $arr = self::text2arr($arr);
		$s = '';
		foreach($arr as $k=>$v){
			$s .= "$k$col$v$row";
		}
		return $s;
	}
	// form: text2arr
	static function text2arr($text){ 
		$_groups = glbConfig::read('groups'); 
		if(is_array($text)){
			if(isset($text['title']) && !empty($text['cfgs'])) return self::text2arr($text['cfgs']);
			return $text;
		}elseif(empty($text)){ 
			return array();
		}elseif(isset($_groups[$text])){ 
			$cfg = glbConfig::read($text); 
			$rei = $cfg['i']; 
			foreach($rei as $k=>$v){ 
				$re[$k] = $v['title'];
			}
		}elseif(strstr($text,'bext_paras.')){ //bext_paras.logmode_cn[.userkey]
			$re = glbDBExt::getExtp(substr($text,11));
		}elseif(strpos($text,'.') && !(strpos($text,"\n")||strpos($text,"\r"))){ //corder.ordstat
			$t = explode('.',$text);
			$mcfg = glbConfig::read($t[0]); 
			$re = self::text2arr($mcfg['f'][$t[1]]['cfgs']);
		}else{
			$a = self::line2arr($text,1,';'); $re = array();
			foreach($a as $v){ //fmadm=1234567890,abcdefg
				$v = str_replace(array(' '),'',$v);
				$t = explode("=",str_replace(array('|',','),'=',$v)); 
				$len0 = strlen($t[0]); $t[1] = isset($t[1]) ? $t[1] : $t[0]; //无=则[val=text],兼容[array('0'=>'')]，
				if($len0 && isset($t[1])) $re[$t[0]] = substr($v,$len0+1); 
			}
		} // 不用array()-eval[]了*/
		return $re;
	}
	static function line2arr($text,$rea=1,$extf=''){ 
		if(!empty($extf)){ $text = str_replace($extf,"\n",$text); }
		$text = str_replace(array("\r\n","\r"),"\n",$text);
		$text = str_replace(array("\n\n\n\n","\n\n\n","\n\n"),"\n",$text);
		if($rea) $text = explode("\n",$text);
		return $text;
	}

	// 格式化标签
	static function fmtFlag($flag){  
		if(is_string($flag) && strpos($flag,'(*)')){ $af = explode('(*)',$flag); }
		else{ $af = is_array($flag) ? $flag : array("<$flag>","</$flag>"); }
		return $af;
	} 
	// 获取xml/html标签 的一个值 或 一个innerHTML
	// 标签法 ($data,'<div class="content">(*)</div>') 或 ($data,'class="content"(*)</div>','>')
	static function getVal($xStr,$flag,$fix=0){  
		$af = self::fmtFlag($flag); 	
		$p1 = strpos($xStr, $af[0]); $len1 = strlen($af[0]);
		if(empty($fix)){
			$p1 = $p1+$len1;
		}elseif(is_numeric($fix)){
			$p1 = $p1+$len1+$fix;
		}else{
 			$tmp = substr($xStr,$p1+$len1);
		 	$at['op'] = substr($fix,0,1); // +,-
			$at['val'] = substr($fix,1);  // <td>, </div>
			$pt = strpos($tmp, $at['val']); $lent = strlen($at['val']); 
			$pt = $at['op']=='+' ? $pt : $pt+$lent; 
			$p1 += $len1+$pt;
		}
		$xStr = substr($xStr,$p1); 
		$tag = '<'.strtolower(substr($af[1],2,strlen($af[1])-3)); // </div> -=> <div
		$a2 = explode($af[1],$xStr); $p2 = 0; // $a[1] = </div>, id="link" 
		foreach($a2 as $v2){
			if(strstr(strtolower($v2),$tag)){
				$p2 += strlen($v2)+strlen($af[1]);
			}else{
				$p2 += strlen($v2); 
				break;
			}
		} 
		$xStr = substr($xStr,0,$p2);
		return $xStr;
	}  
	// 获取xml/html标签 的一个值 或 一个innerHTML
	// 定点法 ($data,'<div class="content">(*)id="link"')
	static function getPos($xStr,$flag){  
		$af = self::fmtFlag($flag); 
		$p1 = strpos($xStr, $af[0]); 
		$xStr = is_int($p1) ? substr($xStr,$p1+strlen($af[0])) : '';
		$p2 = strpos($xStr, $af[1]); 
		$xStr = is_int($p2) ? substr($xStr, 0, $p2) : '';
		return $xStr;
	}
	// 获取xml/html标签 的一组值 或 一组innerHTML 加参数$no获取单个下标
	// 标签法 ($data,'<li class="cls22">(*)</li>') 或 ($data,'<li class(*)</li>')
	static function getArr($xStr,$flag,$no=-1){  
		$af = self::fmtFlag($flag); 
		$a2 = explode($af[0],$xStr); $re = array();
		foreach($a2 as $k2=>$v2){
			if($k2==0) continue;
			$p1 = substr($af[0],-1)!='>' ? strpos($v2,'>') : 0; 
			$p2 = strpos($v2,$af[1]);
			if(is_int($p2)){ 
				$re[] = substr($v2,$p1,$p2-$p1);
			}
		}
		if($no==-1) return $re;
		else return isset($re[$no]) ? $re[$no] : '';
	}
	// 获取xml/html标签 的一组值 或 一组innerHTML 加参数$no获取单个下标
	// 正则法 ($data,'<li class="cls1">(*)</li>') //  [\\d]{1,4}  
	static function getPreg($xStr,$flg1,$reg="[^\n|\r]{1,1200}",$no=-1){  
		$flag = preg_quote($flg1); // =\(\*\)
		$flag = str_replace("/","\\/",$flag);
		$flag = str_replace('\(\*\)',"($reg)",$flag); //echo "(($flag))";
		preg_match_all("/$flag/i",$xStr,$m);
		if($no==-2){
			return $m;
		}elseif($no==-1){
			return $m[1];
		}else{ 
			return isset($m[1][$no]) ? $m[1][$no] : '';
		}
	}
	// 获取xml/html标签 的一组属性 加参数$no获取单个下标
	// ($data,'witdh','val',1) 或 ($data,'href','url')
	static function getAttr($str,$flag,$reg='key',$no=-1){ 
		if(strlen($reg)<=3){
			$a = array(
				'key' => '[\w]{2,24}',
				'url' => '[^"|\'|>| ]{1,120}', //"\S*", 
				'val' => '[^>]{1,255}',
			);
			$reg = $a[$reg];
		} 
		preg_match_all("/\b$flag=[\"|']*($reg)[\"|']*/i",$str,$attr); 
		$ra = empty($attr[1]) ? array() : $attr[1];
		$re = ($no==-1 || strlen($no)==0) ? $ra : (isset($ra[$no]) ? $ra[$no] : ''); 
		return $re;
	}

}

