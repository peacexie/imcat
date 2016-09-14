<?php

// basKeyid类
class basKeyid{	
	
	/* *****************************************************************************
	  *** format格式化ID 
	- fmt,get前缀
	- by Peace(XieYS) 2012-02-23
	----------------------------------------------------------------------------- -/
	 特殊字符表
	《!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~》// All:32
	《"&<>》HTML
	《:/?=&#%》URL/COOKIE 
	《\/*?"<>|》(WIN)FILE
	《'$[]{}》SQL,PHP,下标,变量
	《|:"';》session存数据库,特殊字符
	《!()+,-.;@^_`~》安全13个 
	《*+-./@_》7个js-escape安全escape
	《-._@》4个id安全字符:xie_ys@08-cms.com
	***************************************************************************** */
	
	// *** 格式化N位
	static function fmt099($str,$len){
		$s = "";
		for($i=1;$i<=$len-strlen($str);$i++) { 
			$s = $s."0";
		}
		return $s.$str;
	}
	// *** 格式化xBase进制
	static function fmtBase32($kTab,$xNum,$xBase,$xLen=6,$xDir='Right'){
		$sKey = $kTab=='' ? KEY_TAB32 : $kTab; $s0 = "";
		if($xBase==0){$xBase=16;}
		$n = $xNum; $m0 = pow($xBase,$xLen);
		if($n>$m0) {
			if($xDir=="Right") {
				$m1 = substr($n,strlen($m0)-$xLen,$xLen);  
				if($m1>$m0){ $m1 = substr($m1,1,strlen($m1)-1); }
			}else{
				$m1 = substr($n,0,$xLen);   
				if($m1>$m0){ $m1 = substr($m1,0,strlen($m1)-1); }
			}
			$n = $m1;
		}
		for($i=$xLen-1; $i>=0; $i--) {
			if($i>0){
				$ni = intval($n/(pow($xBase,$i)));
			}else{
				$ni = $n;
			}
			$si = $sKey{$ni};
			$s0 = $s0.$si; 
			$n = $n % pow($xBase,$i);
		} 
		return $s0;
	}
	// *** Time时间相关
	static function getYYYYMMDD($xDate=''){
		if(strlen($xDate)>4) $str = date("Ymd",strtotime($xDate));
		else $str = date("Ymd"); 
		return $str;
	}
	// re : 31829.010991;
	static function getTimer(){
		$t1 = date("His"); //084830 --- time() 函数返回当前时间的 Unix 时间戳。
		$a1 = explode(' ', microtime());  //0.81250100 1283820399
		$n = substr($t1,0,2)*3600+substr($t1,2,2)*60+substr($t1,4,2);
		return $n+$a1[0]; //31874.515625
	}
	// re: 837;
	static function getMSec($type=0){
		if($type) return microtime()*1000; //562.519
		$s = microtime(); 
		return substr($s,2,3); //562
	}
	
	/* *****************************************************************************
	  *** KeyID通用代码 
	- fmt,kid前缀
	- by Peace(XieYS) 2012-02-21
	***************************************************************************** */
	
	// *** 随机源table
	static function kidTable($Type='24'){
		$sSpe = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~'; //32个 
		$sSp1 = '!()+,-.;@^_`~'; // 安全13个 
		$sSp2 = '*+-./@_'; // 7个js-escape安全escape
		$sSp3 = '+-.@_'; // 5个绝对安全
		$sfula = KEY_NUM10.KEY_CHR26.strtoupper(KEY_CHR26);
		switch ($Type){ 
			case "0" : $sOrg = KEY_NUM10; break; 
			case "a" : $sOrg = KEY_CHR26; break;
			case "h" : $sOrg = KEY_NUM16; break; 
			case "H" : $sOrg = strtoupper(KEY_NUM16); break; 
			case "A" : $sOrg = strtoupper(KEY_CHR26); break; 
			case "k" : $sOrg = KEY_TAB32; break;
			case "30": $sOrg = KEY_TAB30; break;
			case "24": $sOrg = KEY_TAB24; break; 
			case "s" : $sOrg = $sSpe; break; 
			case "1" : $sOrg = $sSp1; break; 
			case "2" : $sOrg = $sSp2; break; 
			case "3" : $sOrg = $sSp2; break; 
			case "f" : $sOrg = $sfula; break; 
			case "fs" : $sOrg = $sfula.$sSpe; break; 
			case "fs1" : $sOrg = $sfula.$sSp1; break; 
			case "fs2" : $sOrg = $sfula.$sSp2; break; 
			case "fs3" : $sOrg = $sfula.$sSp3; break; 
			default  : $sOrg = KEY_TAB24."abcdf"; break; 
		}
		return $sOrg;
	}
	// *** 打乱的table
	// *** obj : 打乱obj...
	static function kidRTable($Type='24',$re='rnd',$obj=''){
		$sOrg = $bOrg = self::kidTable($Type);
		if($re=='org') return $sOrg;
		$sLen = strlen($sOrg); //$pLen = intval($sLen/2); //$str = '';
		for($j=0;$j<2;$j++){  //两次打乱
		for($i=0;$i<$sLen;$i++){
			$ch = $sOrg{mt_rand(0,$sLen-1)};
			$sOrg = $ch.str_replace($ch,'',$sOrg);
		} }
		if(empty($obj)) return $sOrg;
		//$sLen = strlen($obj);
		for($i=0;$i<$sLen;$i++){
			$ch1 = $sOrg{mt_rand(0,$sLen-1)};
			$ch2 = $bOrg{mt_rand(0,$sLen-1)};
			$obj = str_replace($ch1,$ch2,$obj);
		}
		return $obj;
	}
	// *** 随机码
	static function kidRand($Type='24',$Len=16){
		$sOrg = self::kidTable($Type);
		$sLen = strlen($sOrg); $str = '';
		if($Len<=0){
			for($i=0;$i<$sLen;$i++){
				$ch = $sOrg{mt_rand(0,$sLen-1)}; //echo $ch;
				$str .= $ch; 
				$sOrg = str_replace($ch,'',$sOrg);
				if($nLen==1){ $str .= $sOrg; break; }
			}
		}else{
			for($i=0;$i<$Len;$i++){
				$str .= $sOrg{mt_rand(0,$sLen-1)};
			}
		}
		return $str;
	}
	
	// *** 摸版ID
	// YYYY,YY,MM,DD,MD; HNSX,HNS
	static function kidTemp($xFmt='(def)',$xTime=''){
		$ktab32 = str_replace('e','',KEY_TAB32).'z';
		$sTmp = strtolower($xFmt); 
		if(empty($xTime)){
			$sDate = date("Ymd");
			$sTime = date("His"); 
		}else{
			$xTime = is_numeric($xTime) ? $xTime : strtotime($xTime);
			$sDate = date("Ymd",$xTime);
			$sTime = date("His",$xTime);
		} 
		$y4 = date("Y",strtotime($sDate)); 
		$md = $ktab32{substr($sDate,4,2)}.$ktab32{substr($sDate,6,2)}; 
		$h = $ktab32{substr($sTime,0,2)}; 
		$m = $ktab32{intval(substr($sTime,2,2)/2)+1};
		$s9 = substr($sTime,2,4).substr(microtime(),2,6); 
		$s6 = ''; for($i=0;$i<7;$i=$i+3) $s6 .= self::fmtBase32('',substr($s9,$i,3),32,2,'');
		if($xFmt=='0'){						   //2012-md
			$sTmp = "$y4-$md";
		}elseif($xFmt=='m-dh'){				   //2012m-dh
			$sTmp = "$y4".substr($md,0,1)."-".substr($md,1,1)."$h";
		}elseif($xFmt=='h'){					  //2012-9B-F
			$sTmp = "$y4-$md-$h";
		}elseif($xFmt=='hm'){					 //2012-9C-GC				
			$sTmp = "$y4-$md-$h$m";
		}elseif($xFmt=='hms'){					//2012-9C-GCD
			$ms = floor((substr($sTime,2,2)*60+substr($sTime,4,2))/4)+1; //3600-1
			$sTmp = "$y4-$md-$h".self::fmtBase32($ktab32,$ms,32,2,'');
		}elseif($xFmt=='3.4'){					//2012-9D-H33.AVEX 
			$ms = floor((substr($sTime,2,2)*60+substr($sTime,4,2))/4)+1;  
			$sTmp = "$y4-$md-$h".self::fmtBase32($ktab32,$ms,32,2,'').'.'.substr($s6,2,4);
		}elseif($xFmt=='4.5'){					//2012-9F-F289.06999
			$sTmp = "$y4-$md-$h".substr($s9,0,3).".".substr($s9,3,5);
		}elseif($xFmt=='5.6'){					//2012-9G-H1230.756894
			$sTmp = "$y4-$md-$h".substr($s9,0,4).".".substr($s9,4,6);
		}elseif(strpos(',4,5,6,7,',"$xFmt,")){  //2012-7H-DTX...
			$sTmp = "$y4-$md-$h".substr($s6,0,$xFmt-1);
		}else{									//2012-7K-DTX0PBD
			$sTmp = "$y4-$md-$h".$s6;
		}
		return $sTmp;
	} 
	
	static function kidAuto($xN=24){
		$str = date("Ymd"); 
		$sy = substr($str,0,4);
		$sm = substr($str,4,2);
		$sd = substr($str,6,2);
		$YMD = self::fmtBase32('',$sy*380+$sm*31+$sd,"16",6,"");
		$HMS = self::fmtBase32('',intval(self::getTimer()*100),16,6,"");
		$str = $YMD.$HMS;
		$n = $xN - strlen($str);
		if($n>0) { $str .= self::kidRand("",$n); }
		if($n<0) { $str .= substr($str,0,$xN); }
		return $str;
	}
	
	static function kidNext($kTab,$xOld,$xMin,$xStep,$mLen=5){
		$ktabs = $kTab=='' ? KEY_TAB32 : $kTab; 
		$nLen = strlen($xMin);
		$t1 = KEY_NUM10; $s="";
		$tf = $ktabs==KEY_TAB24 ? 'Chr' : "Num"; $tn = $t1; $hf = 0; // (进位) 
		if(strlen($xOld)<$nLen){ return $xMin; }
		else {
			$OldID = substr($xOld,0,strlen($xMin));
			for($i=$nLen;$i>=1;$i--) { 
				$c = substr($OldID,$i-1,1); 
				if(($tf!='Num')||($c>'9')||($i==1)) { $tf="Chr"; } 
				if(strlen($xMin)<$mLen){ $tf="Chr"; }
				if($tf=="Chr") { $tn = $ktabs; }
				$n = strpos($tn,$c); // 位置 0~N-1 或为空strlen($n)=0
				if(strlen($n)>0){
					$n = $n + $hf; 
					if($i==$nLen) { $n = $n+$xStep; }
					if($n>=strlen($tn)) {
						$n = $n-strlen($tn);
						$hf = 1;
					}else{
						$hf = 0;
					}
					$c = $tn{$n}; 
				}else{ //ILOZ不在列表中则
					$c = $c=='z' ? 'z' : chr(ord($c)+1); //echo "f.$c,";
				}
				$s = $c.$s;
			} 
			if($xOld>$s) return $xOld;
			return $s;
		}
	}
	
	static function keepCheck($key,$chk=1,$fix=1,$grp=0,$len=3){
		$groups = glbConfig::read('groups');
		$keepids = glbConfig::read('keepid','sy');
		if(strlen($key)<$len){
			return lang('core.kid_minlen',$len); //"请输入$len+个字符！";
		}
		if($chk && strpos($keepids,",$key,")){
			return lang('core.kid_keeped',$key); 
		}
		if($fix && strpos($key,"_")){
			$fix = strpos($key,0,strpos($key,'_'));
			if(strstr($keepids,",$fix,")) return lang('core.kid_preused',$key); 
		}
		if($grp && isset($groups[$key])){ 
			return lang('core.kid_ismodel',$key); 
		}
		return '';
	}

}

