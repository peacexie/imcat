<?php 

// 编码转化,加密类
class comConvert{				  
	
	// 加载-table数据 /ximp/utabs/jianfan.imp_txt
	// re: arr, str, sql
	static function impData($name,$part='',$re='str'){   
		$f1 = 'start(!@~)'; $f2 = '(!@~)isend'; $f0 = '(split!@~flag)'; // 标记 
		if(substr($name,0,6)=='/ximp/'){
			$file = DIR_STATIC.$name; 
			if(file_exists($file)){
				$data = comFiles::get($file);
			} 
		}elseif(!empty($name)){
			$data = $name; 
		}
		if(empty($data)) return '';
		$p1 = strpos($data,$f1)+10; $p2 = strpos($data,$f2);
		$data = substr($data,$p1,$p2-$p1);
		if($part){
			$len = strlen($part)+2;
			$p1 = strpos($data,"[$part]")+$len; $p2 = strpos($data,"[/$part]");
			if(!$p1 || !$p2 || $p2<=$p1){
				$data = '';
			}else{
				$data = substr($data,$p1,$p2-$p1);
			}
		}
		if(strpos($data,$f0)>0){
			$data = explode($f0,$data);
		}
		if($re=='str'){
			$data = preg_replace('/\s/','',$data); //去空白
		}elseif($re=='sql'){
			$data = basSql::filNotes($data);
		}elseif($re=='arr'){
			$data = basElm::line2arr($data,0);
		} //echo "\n\n\nkey=$part\n$data\n";
		return $data;  
	} 
	
	// 自动转换字符集 支持数组转换
	static function autoCSet($str,$from='gbk',$to='utf-8'){
		if(empty($str) || empty($from) || empty($to)) return $str;
		$from = strtoupper($from)=='UTF8'? 'utf-8':$from;
		$to = strtoupper($to)=='UTF8'? 'utf-8':$to;
		if( strtoupper($from) === strtoupper($to) || (is_scalar($str) && !is_string($str)) ){
			return $str; //如果编码相同或者非字符串标量则不转换
		}
		if(is_string($str) ) {
			if(function_exists('iconv')){
				return iconv($from,$to."//IGNORE",$str); 
			}elseif(function_exists('mb_convert_encoding')){
				return mb_convert_encoding ($str, $to, $from);
			}else{
				return $str;
			}
		}elseif(is_array($str)){
			foreach ( $str as $key => $val ) {
				$str[$key] = self::autoCSet($val,$from,$to);
				// 不考虑key转化
			}
			return $str;
		}else{
			return $str;
		}
	}
	
	// sn:3369512d-e369-czyx-xmao-2016-5w76dm47
	static function sysSn($area='czyx',$corp='xmao'){ 
		$time = str_replace('-','',basKeyid::kidTemp('6'));
		$enc = md5("$area.$time.$corp");
		$res = substr($enc,7,8).'-'.substr($enc,23,4);
		$res .= "-$area-$corp-";
		$res .= substr($time,0,4).'-'.substr($time,4,8);
		//echo "$time<br>$enc<br>$res";
		return $res;
	}
	
	static function sysPass($uid='',$upw='',$mod=''){ 
		if(empty($uid) || empty($upw)) return '(null)';
		$pfix = $mod=='adminer' ? cfg('safe.pass').$mod : 'pass';
		return self::sysEncode("$upw@$uid",$pfix,24);
	}
	// *** 加密MD5
	// $methods:md5,sha1,(md5比sha1快点)
	// $ukey = in_array($ukey,array('pass','form','api','js','other'))
	static function sysEncode($str,$ukey='other',$len=16,$methods='md5,sha1'){
		$safe = cfg('safe');
		$fmd5 = false; $fsh1 = false;
		$a = explode(',',$methods);
		foreach($a as $m){ 
			if(!in_array($m,array('md5','sha1'))) $m = 'sha1';
			if($m=='md5') $fmd5 = true; if($m=='fsh1') $fsh1 = true;
			$str = $m($str);
		}
		if(!$fmd5) $str = md5($str); if(!$fsh1) $str = sha1($str);
		$ukey || $ukey = 'other';
		$ukey = isset($safe[$ukey]) ? $safe[$ukey] : $ukey;
		$skey = $safe['site'];
		$ostr = "$skey@$str@$len@$ukey";
		$str = sha1($ostr).hash('ripemd128',$ostr); // 72位
		$len || $len = 16;
		return substr($str,(strlen($str)-$len)/2,$len); 
	}
	
	//加密解密，$key：密钥；$de：是否解密；$expire 过期时间
	static function sysRevert($str, $de=0, $key='', $exp=0){
		$key || $key = cfg('safe.other'); 
		$nn = 4; $key = md5($key); $res = '';
		$keya = md5(substr($key,0,16)); $keyb = md5(substr($key,16,16));
		if($de){
			$str = str_replace(array('.','_',),array('+','/',),$str);
			$keyc = substr($str,0,$nn);
			$ckey = $keya.md5($keya.$keyc); $ckn = strlen($ckey);
			$str =  base64_decode(substr($str,$nn));
		}else{
			$str = serialize($str);
			$keyc = substr(md5(microtime()), -$nn);
			$ckey = $keya.md5($keya.$keyc); $ckn = strlen($ckey);
			$str =  sprintf('%010d', $exp ? $exp+time() : 0).substr(md5($str.$keyb),0,16).$str;
		}
		$mm = strlen($str); $box = range(0,255); $rnd = array();
		for($i=0; $i<=255; $i++){ 
			$rnd[$i] = ord($ckey[$i % $ckn]);
		}
		for($j=$i=0; $i<256; $i++) {
			$j = ($j + $box[$i] + $rnd[$i]) % 256;
			$tmp = $box[$i]; $box[$i] = $box[$j]; $box[$j] = $tmp;
		}
		for($a=$j=$i=0; $i<$mm; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a]; $box[$a] = $box[$j]; $box[$j] = $tmp;
			$res .= chr(ord($str[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($de){
			if(substr($res,0,10)==0 || substr($res,0,10)-time()>0) {
				return unserialize(substr($res,26));
			}
			return '';
		}else{
			$res = base64_encode($res); 
			return $keyc.str_replace(array('+','/','='),array('.','_',''),$res);
		}
	}
	
	//base64编码(并加密/解密)
	static function sysBase64($s,$de=0,$key=''){
		if(empty($s)) return $s;
		$safe = cfg('safe');
		$org = basKeyid::kidRTable('f','org');
		$rnd = $safe['rndtab'].'.-'; $re = ''; 
		$fix = ($key ? $key : $safe['rndch6'])."^";
		if($de){ 
			for($i=0;$i<strlen($s);$i++){
				$ch = $s{$i}; $p = strpos($rnd,$ch); 
				$re .= $p===false ? $ch : $org{$p}; 
			}
			$s = $re;
		}else{
			$s = $fix.$s;	
		}
		$s = comParse::urlBase64($s,$de); 
		if($de){ 
			$s = substr($s,strlen($fix));
		}else{
			for($i=0;$i<strlen($s);$i++){
				$ch = $s{$i}; $p = strpos($org,$ch); 
				$re .= $p===false ? $ch : $rnd{$p}; 
			}
			$s = $re;
		}
		return $s;
	}

	static function jianfanMain($str,$type='j2f',$cset='3'){
		$jf=""; $p = 0; $len = strlen($str);
		if($type=='j2f'){
			$tab1 = self::jfcfgTab('Jian');
			$tab2 = self::jfcfgTab('Fan');
		}else{
			$tab2 = self::jfcfgTab('Jian');
			$tab1 = self::jfcfgTab('Fan');
		}
		for($i=0;$i<$len;$i++){   
			$ch = substr($str,$p,1);
			if(ord($ch)<128){ 
				$jf .= $ch;
				$p++;
			}else{
				$ch = substr($str,$p,$cset);
				$tp = strpos($tab1,",$ch"); 
				if($tp) $ch = substr($tab2,$tp+1,$cset);
				$jf .= "$ch";  
				$p += $cset; 
			}
			if($p>=$len) break;
		}   
		return $jf; 
	}
	
	static function pinyinOne($chr){
		$tab = self::pycfgTab();
		$p = strpos($tab,$chr); 
		$t = substr($tab,0,$p);
		$t = strrchr($t,"(");
		$p = strpos($t,")");
		$t = substr($t,1,$p-1);
		return $t;   
	}
	// 内页广告: 0-neiyeguanggao, 1-n, 9-nygg
	static function pinyinMain($str,$cset='3',$first=0){
		$cset = $cset=='3' ? 'utf-8' : 'gbk';
		$arr = basStr::strArr($str, $cset);
		$len = count($arr); $py=""; 
		for($i=0;$i<$len;$i++){
			if(ord($arr[$i])<128){
				$py .= $arr[$i];
			}else{
				$tmp = self::pinyinOne($arr[$i]); 
				$py .= $first ? substr($tmp,0,1) : $tmp;
			}
			if($first===1 && $py) return substr($py,0,1); 
		}
		return $py; 
	}
	
	static function jfcfgTab($key){
		static $jfcfg_tab;
		if(!isset($jfcfg_tab)){ 
			$jfcfg_tab = self::impData('/ximp/utabs/jianfan.imp_txt');
		}
		$id = $key=='Fan' ? 1 : 0;
		return $jfcfg_tab[$id];
	}
	static function pycfgTab(){
		static $pycfg_tab;
		if(!isset($pycfg_tab)){ 
			$pycfg_tab = self::impData('/ximp/utabs/pinyin.imp_txt');
		}
		return $pycfg_tab;
	}

}

