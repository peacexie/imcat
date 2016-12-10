<?php
/*

*/
// 标签缓存类
class tagCache{
	
	static function showAdv($mkey){ 
		$mk = explode(':',$mkey);
		if(empty($mk[1])) return '';
		$file = tagCache::caPath($mk[0],$mk[1],1);
		$data = file_exists($file) ? comFiles::get($file) : "$mkey";
		$re = basJscss::jsShow($data, 0);
		return $re;
	}
	
	static function jsTag($k,$mkv,$para){ 
		preg_match("/\[cache\,([a-z0-9]+)\]/", $para, $m);  
		if(!empty($m[0]) && !empty($m[1])){ // && intval($m[1])>0
			$pkey = str_replace(array("[List]","[Page]","[One]"),'',$para);
			$path = self::ctPath($pkey,req('tpldir')); 
			$fpath = self::chkUpd($path,$m[1]); 
			$data = $path ? comFiles::get($fpath) : ''; 
			$para = str_replace($m[0],'',$para); 	
		}else{
			$path = $data = ''; //无缓存,无数据		
		}
		if(empty($data)){
			$data = self::jsData($k,$para); 
			if($path) self::setCache($path,$data);	 
		}
		$re = basJscss::jsShow($data, 0);
		return $re;
	}
	
	static function jsData($k,$data){ 
		ob_start();
		$vop = new vopShow(0);
		$vop->rjs($data);
		$re = ob_get_contents();
		ob_end_clean(); 
		return $re;
	}
	
	static function comTag($type,$mkv,&$paras){ 
		$cac = 0; $cex = $path = $fmkv = ''; 
		foreach($paras as $k=>$v){ 
			if($v[0]=='cache' && !empty($v[1])){
				$cac = $v[1];
				unset($paras[$k]);
			}
			if($v[0]=='stype' && !isset($v[1])){
				$fmkv = "-$mkv";
			}
			$cex .= '['.implode(',',$v).']';	
		} 
		if($cac){ 
			global $_cbase;
			$nowtpl = $_cbase['run']['tplnow']; 
			$tpl_dir = $_cbase['tpl']['tpl_dir']; 
			$path = self::ctPath("[{$nowtpl}][$type]{$cex}",$tpl_dir);
			$cfile = self::chkUpd($path,$cac,'ctpl'); 
			$data = $cfile ? unserialize(comFiles::get($cfile)) : ''; 
		}else{
			$data = ''; //无数据
		} 
		return array($path,$data);
	}
	
	static function ctPath($para,$tpldir){ 
		$fext = str_replace(array('/','+','*','|','?',':'),array('~','-','.','!','$',';'),$para); 
		$fext = str_replace(array('[modid,','[limit,','[cache,','[show,'),array('[m','[n','[c','[s'),$fext); 
		$fext = basStr::filTitle($fext); //del:&,#
		if(strlen($fext)>150) $fext = substr($fext,0,20).'~'.md5($fext);
		$path = "/_tagc/$tpldir$fext.cac_htm"; //".(substr($fmd5,0,1))."/
		return $path;
	}
	static function caPath($mod,$type,$full=0){ 
		$path = "/_advs/$mod/$type.cfg_htm"; 
		$full && $path = DIR_CTPL.$path;
		return $path;
	}
	
	static function setCache($file,$data,$isa=0){
		if($isa){
			$data['page_bar'] = cfg('page.bar');
			$data = serialize($data); //var_export
		}
		comFiles::chkDirs($file,'ctpl');
		comFiles::put(DIR_CTPL.$file,$data);
	}
	
	// $ctime : //30s,60m,3h,6h,12h,24h,7d; 默认单位m
	static function chkUpd($file,$ctime=30,$bdir='dtmp'){ 
		if(is_numeric($ctime) || strpos($ctime,'m')){
			$ctime = intval($ctime)*60; 
		}elseif(strpos($ctime,'h')){
			$ctime = intval($ctime)*3600;
		}elseif(strpos($ctime,'d')){
			$ctime = intval($ctime)*86400;
		}else{ 
			$ctime = intval($ctime);
		}
		$ctime<=0 && $ctime = 1800; 
		$stamp = time();
		$cfg = array(
			'dtmp'=>DIR_DTMP,
			'ures'=>DIR_URES,
			'html'=>DIR_HTML,
			'ctpl'=>DIR_CTPL,
		);
		$bdir = empty($bdir) ? '' : (isset($cfg[$bdir]) ? $cfg[$bdir] : $bdir); 
		if(file_exists($bdir.$file)){ 
			$last = filemtime($bdir.$file);
			if($last + $ctime > $stamp){ 
				return $bdir.$file;
			}
		}
		return '';
	}

}
