<?php

// Files类
class comFiles{	

	//重定义file_get_contents来兼容不支持此函数的PHP
	static function get($fname){
		//return file_get_contents($fname);
		if(!file_exists($fname) || is_dir($fname)){
			return '';
		}else{
			$fp = fopen($fname, 'r');
			$size = filesize($fname);
			if($size==0) return '';
			$ct = fread($fp, $size);
			fclose($fp);
			return $ct;
		}
	}

	//重定义file_put_contents来兼容不支持此函数的PHP
	static function put($fname, $data){
		//return file_put_contents($fname, $data);
		$fp = @fopen($fname, "w");
		if(!$fp){
			$re = FALSE;
		}elseif(flock($fp,LOCK_EX)){ // 排它性的锁定
			fwrite($fp, $data);
			flock($fp,LOCK_UN); // release lock
			fclose($fp);
			$re = TRUE;
		}else{
			$re = FALSE;
		}
		return $re;
	}

	// 移除BOM
	static function killBOM($str){ 
		$len = strlen($str); 
		if($len==0) return $str;
		$start = 0;
		for($i=0; $i<$len; $i++) {
			$chr = ord($str[$i]);
			if(in_array($chr,array(239,187,191))){
				$start++;
			}else{
				return $start ? substr($str,$start) : $str;
				break;
			}
		}
		return $str;
	}

	static function getTIcon($file){ 
		$cfg = glbConfig::read('filetype','ex');
		$type = $icon = 'unknow';
		$ext = strtolower(strrchr($file,"."));
		$ext = substr($ext,1);
		foreach($cfg as $k=>$v){
			if(in_array($ext,$v)){
				$icon = $v[0];
				$type = $k;
				break;
			}
		}
		return array('type'=>$type,'icon'=>$icon); //'unknow';
	}

	static function listScan($dir,$sub='',$skips=array()){
		$re = array();
		$handle = opendir($dir);
		while ($file = readdir($handle)) {
			if($file=='.'||$file=='..') continue;
			$key = "{$sub}$file";
			$fp = "$dir/$file"; 
			if(is_dir($fp)){ //不用:file_exists
				if(empty($sub) && !empty($skips) && in_array($file,$skips)) continue; 
				$re = array_merge($re,self::listScan($fp,"$sub$file/"));
			}else{
				$mtime = filemtime($fp);
				$re[$key] = array($mtime,filesize($fp));
			}
		}
		closedir($handle);
		return $re;
	}

	static function listDir($dir){
		if(!is_dir($dir)) return array(); 
		$re = array('dir'=>array(),'file'=>array());
		// --- scandir();
		$handle = opendir($dir);
		while ($file = readdir($handle)) {
			if($file=='.'||$file=='..') continue;
			$fp = "$dir/$file";
			$mtime = filemtime($fp);
			if(is_file($fp)){
				$re['file'][$file] = array($mtime,filesize($fp));
			}else{
				$re['dir'][$file] = $mtime; 
			}
		}
		closedir($handle);
		return $re;
	}
	
	// 目录状态统计(大小,文件数,目录数)
	static function statDir($path){
		$msize = 0;
		$fcount = 0;
		$dcount = 0;
		if ($handle = opendir ($path)){
			while (false !== ($file = readdir($handle))){
				$nextpath = $path . '/' . $file;
				if ($file != '.' && $file != '..' && !is_link ($nextpath)){
					if (is_dir ($nextpath)){
						$dcount++;
						$result = self::statDir($nextpath);
						$msize += $result['nsize'];
						$fcount += $result['cfile'];
						$dcount += $result['cdir'];
					}elseif (is_file ($nextpath)){
						$msize += filesize ($nextpath);
						$fcount++;
					}
				}
			}
		}
		closedir($handle);
		$total['nsize'] = $msize;
		$total['cfile'] = $fcount;
		$total['cdir'] = $dcount;
		return $total;
	}

	static function chkDirs($subs,$flag='',$isfile=1){ 
		if(empty($subs)) return;
		if(strstr($subs,'../')) return;
		if($isfile){
			return self::chkDirs(dirname($subs),$flag,0);
		}
		$check = basStr::filKey($subs,'!()-@_~/'); //.+,;^`
		if($check!=$subs) return;
		$path = self::cfgDirPath($flag,'dir');
		$path || $path = DIR_DTMP;
		$a = explode('/',$subs); 
		$i = 0; $tmp = '';
		if(count($a)>0){
			foreach($a as $d){ 
				//if(empty($d)) return;
				if(!is_dir($path."$tmp/$d")){ 
					mkdir($path."$tmp/$d", 0777, true);	
					foreach(array('htm','html') as $var) @touch($path."$tmp/$d".'/index.'.$var);	
				}
				$tmp .= "/$d"; $i++;
				if($i==12) break;
			}
		}
	}

	//遍历删除目录和目录下所有文件
	static function delDir($dir,$delself=0,$keep='',$first=1){
		if(strlen($dir)<6) return false; //  /a/b  c:/a/b
		if(!defined('indo_Verset')){
			if(strstr($dir,DIR_CODE) || strstr($dir,DIR_ROOT) || strstr($dir,DIR_STATIC) || strstr($dir,DIR_VENDUI) || strstr($dir,DIR_VENDOR)) return false;
		}
		if($first){
			if(is_file($dir)){ return @unlink($dir); };
			if(in_array(substr($dir,-1,1),array('/',"\\"))){ $dir = substr($dir,strlen($dir)-1); }
			if(!is_dir($dir)){ return false; };
		}
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false){
			if ($file != "." && $file != ".."){
				if(in_array($file,array('index.htm','index.html')) && empty($delself)) continue;
				if($keep && strstr($keep,$file)) continue;
				is_dir("$dir/$file") ? self::delDir("$dir/$file",1,'',0):@unlink("$dir/$file");
			}
		}
		if(!empty($delself)){
			if (readdir($handle) == false){
				closedir($handle);
				@rmdir($dir);
			}
		}
	}
	
	// 是否可写 //if(is_writable($pfile)){}
	static function canWrite($dir){
		if(is_dir($dir)){
			$file = '/__'.basKeyid::kidTemp().'__.test';
			if($fp = @fopen($dir.$file, 'w')) {
				@fclose($fp);
				@unlink($dir.$file);
				$fwrite = 1;
			}else $fwrite = 0;
		}elseif(is_file($dir)){
			$fwrite = is_writable($dir);
		}else{
			$fwrite = 0;	
		}
		return $fwrite;
	}
	
	// 复制目录 : $src -> $dst
	// $skip : 忽略目录
	static function copyDir($src,$dst,$skip=array()) {  // 原目录，复制到的目录
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if(is_dir($src.'/'.$file)){
					if($skip && in_array($file,$skip)) continue;
					self::copyDir($src.'/'.$file, $dst.'/'.$file);
				}else{
					copy($src.'/'.$file, $dst.'/'.$file);
				}
			}
		}
		closedir($dir);
	}
	
	/**
	 * 上传到的临时目录，后续再移动到正式目录
	 * @return string
	 */
	static function getTmpDir($isfull=1){
		global $_cbase; 
		$user = usrBase::userObj();
		$sid = empty($user->sinit['sid']) ? usrPerm::getUniqueid('Cook','sip') : $user->sinit['sid'];
		$path = "@udoc/$sid"; //$modFix-
		self::chkDirs($path,'tmp',0);
		return ($isfull ? DIR_DTMP.'/' : '')."$path"; //PATH_ROOT
	}
	
	static function fixTmpDir($path){
		$pos = strpos($path,"/@udoc/");
		$path = PATH_DTMP.substr($path,$pos);
		return $path;
	}
	
	/**
	 * 上传资源目录
	 * @return string
	 */
	static function getResDir($mod,$kid='',$isfull=1,$chkdir=0){
		global $_cbase; 
		$user = usrBase::userObj();
		if(empty($kid)){
			$kid = basReq::val('did');
			$kid || $kid = basReq::val('uid');
			$kid || $kid = basReq::val('cid');
			$kid || $kid = basReq::val('kid');
		}
		$kpath = $kid; 
		$fmts = vopTpls::etr1('res'); $fmt = 1; //yyyy/md-noid默认
		foreach($fmts as $k=>$v){
			if(in_array($mod,$v)){ $fmt = $k; }
		}
		if(strpos($kid,'-')){
			$ka = explode('-',$kid);
			if($fmt==1) $kpath = $ka[0].'/'.$ka[1].'-'.$ka[2];
			if($fmt==2) $kpath = $ka[0].'-'.$ka[1].'/'.$ka[2];
			if($fmt==3) $kpath = $ka[0].'/'.$ka[1].'/'.$ka[2];	
		}else{ // 加了一层目录,有些人不喜欢...
			$groups = glbConfig::read('groups');
			if(isset($groups[$mod]) && in_array($groups[$mod]['pid'],array('docs','users'))){
				$kpath = (empty($fmt) ? '' : "home/")."$kid";
			}elseif(isset($groups[$mod]) && in_array($groups[$mod]['pid'],array('types'))){
				$kpath = '';
			}else{ // coms 
				$kpath = $kid;
			}
		}
		$repath = empty($kpath) ? $mod : "$mod/$kpath";
		$chkdir && self::chkDirs($repath,'res',0);
		return ($isfull ? DIR_URES.'/' : '').$repath;
	}
	
	//移动临时文件夹中的文件
	static function moveTmpDir($str,$mod='',$kid='',$ishtml=0){
		global $_cbase;
		if($ishtml){ //a,img,embed,value?,
			preg_match_all("/\s+(src|href|value)=(\S+)[\s|>]+/i",$str,$arr); //3
			$ar2 = empty($arr[2]) ? array() : str_replace(array("\\",'"',"'"),array(),$arr[2]); 
			//echo "<pre>"; print_r($arr); die();
		}else{
			if(strpos($str,';')){ //pics
				$ar2 = explode(';',$str);
				foreach($ar2 as $k=>$v){
					$art = explode(',',$v);
					if(empty($art[0])) unset($ar2[$k]);
					else $ar2[$k] = str_replace(array("\r","\n",' '),array('','',''),$ar2[$k]);
				}
			}else{
				$ar2 = array($str);
			}
		} 
		$ar2 = array_unique(array_filter($ar2));
		if(empty($ar2)) return $str;
		$fix = PATH_DTMP."/@udoc/";
		foreach($ar2 as $v){
			if($org=strstr($v,$fix)){
				$orgfile = DIR_DTMP.substr($org,strlen(PATH_DTMP));
				$obj = self::getResDir($mod,$kid,0,1)."/".basename($org);
				if(in_array($org,$_cbase['run']['tmpFile'])){
					$str = str_replace($v,'{resroot}/'.$obj,$str);
					continue;
				}elseif(is_file($orgfile)){ 
					if($re=rename($orgfile,DIR_URES.'/'.$obj)){
						$str = str_replace($v,'{resroot}/'.$obj,$str);
						$_cbase['run']['tmpFile'][] = $org;
						continue;
					}
				}
			}
			$cfg = array(
				array('tmp','/@udoc/'), 
				array('res',"/$mod/"), 
				array('htm',"/$mod/"),
				array('stc',"/"), 
				array('vui',"/"), 
				array('vnd',"/"), 
				array('web',"/"),
			);
			foreach($cfg as $cv){
				$str = self::moveRepRoot($str,$v,$cv[0],$cv[1]);
			}
		} //die();
		return $str;
	}
	
	//替换root路径
	static function moveRepRoot($str,$v,$key,$fix=''){
		global $_cbase; 
		$cfg = self::cfgDirPath($key,'arr');
		$res = $v;
		if(strpos($res,'://')>0){ //完整路径
			if(strpos($res,$_cbase['run']['rsite'])===0){ //本地
				$res = str_replace($_cbase['run']['rsite'],"",$res); 
			}else{ //外网(可处理远程图...)
				return $str;	
			}
		}
		if(strpos($res,$cfg[1].$fix)===0 && !empty($cfg[1])){
			$res = '{'.$key.'root}'.substr($res,strlen($cfg[1]));
			$str = str_replace($v,$res,$str);
		}
		return $str;
	}
	
	//part:dir,arr,else
	static function cfgDirPath($key,$part='dir'){
		@$cfg = array(
			'tmp'=>array(DIR_DTMP,	PATH_DTMP),
			'res'=>array(DIR_URES,	PATH_URES),
			'htm'=>array(DIR_HTML,	PATH_HTML),
			'stc'=>array(DIR_STATIC,  PATH_STATIC),
			'vui'=>array(DIR_VENDUI,  PATH_VENDUI),
			'vnd'=>array(DIR_VENDOR,  PATH_VENDOR),
			'tpl'=>array(vopTpls::path('tpl'),''), //可能没有定义
			'tpc'=>array(vopTpls::path('tpc'),''),
			'cod'=>array(DIR_CODE,	PATH_CODE),
			'web'=>array(DIR_ROOT,	PATH_ROOT),
		);
		$re = isset($cfg[$key]) ? $cfg[$key] : array('','');
		if($part=='arr') return $re;
		$id = $part=='dir' ? 0 : 1;
		return empty($re[$id]) ? $key : $re[$id];
	}
	
	//还原保存的文件夹
	static function revSaveDir($str,$part=''){
		$cfg = array(
			'tmp',
			'res',
			'htm',
			'stc',
			'vui',
			'vnd',
			'web',
		);
		
		foreach($cfg as $ck){
			$path = self::cfgDirPath($ck,$part);
			$str = str_replace(array('{'.$ck.'root}','{$'.$ck.'root}'),$path,$str); //self::moveRepRoot($str,$v,$cv[0],$cv[1]);
		}
		return $str;
	}
	
}
