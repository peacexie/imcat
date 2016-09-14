<?php

// Cache - 读写
class glbConfig{	

	public static $_CACHES_YS = array();//将读取过的缓存暂存可重用
	
	//先检查再读缓存
	static function cread($mod){ 
		$_groups = glbConfig::read('groups'); 
		$mcfg = isset($_groups[$mod]) ? glbConfig::read($mod) : array();
		return $mcfg;
	}

	// read config
	// $_demo = read('demo');
	// $_sy_keepid = glbConfig::read('keepid','sy');
	// $dbcfg = read('db','cfg'); 
	static function read($file,$dir='modcm',$type='inc'){ 
		global $_cbase; 
		$modid = $file;
		if(in_array($dir,array('modcm','modex'))){ 
			$key = "_$file";
			$file = "/$dir/".$key.($dir=='modcm' ? ".cfg.php" : "cfg_php");
			$base = DIR_DTMP;
		}elseif(in_array($dir,array('_c'))){ //栏目配置
			$key = "_c_$file"; 
			$file = "/modex/$key.cfg.php";
			$base = DIR_DTMP;
			if(!file_exists(DIR_DTMP.$file)) return array();
		}elseif(in_array($dir,array('cfg'))){
			$key = "_cfg_$file"; $kk = "_cfgs"; 
			$file = "/cfgs/boot/cfg_$file.php";
			$base = DIR_CODE;
		}elseif(in_array($dir,array('dset'))){
			$key = "_$file";
			$file = "/dset/$key.cfg.php";
			$base = DIR_DTMP;
		}elseif(in_array($dir,array('sy','ex'))){
			$key = "_{$dir}_$file";
			$file = "/cfgs".($dir=='sy' ? "/sycfg" : "/excfg")."/".substr($key,1).".php";
			$base = DIR_CODE;
		}elseif(in_array($dir,array('va','vc','ve'))){
			$tpldir = $_cbase['tpl']['tpl_dir'];
			$key = "{$tpldir}_$file"; $kk = "_{$dir}_$file"; 
			$file = vopTpls::pinc("_config/{$dir}_$file",'',0); //pcfg("{$dir}_$file",0); 
			$base = DIR_CODE;
		//}else{
			//$modid = $file; $key = "_$file"; 
			//if(!strstr($key,'.')) $file = "$key.cfg.php";
			//else $file = $key; 
		}
		$file = "$base$file"; //echo "$dir,<br>";
		if($type=='inc'){ //返回数组(php用)
			if(!isset(self::$_CACHES_YS[$key])){
				/*if(in_array($dir,array('sy','ex'))){
					$flang = str_replace('.php','-'.$_cbase['sys']['lang'].'.php',$file);
					$file = file_exists($flang) ? $flang : $file;
				}*/
				if(file_exists($file)){ // inc大文件，其实很占时间
					require($file); 
				}else{ 
					//glbError::show("Cache File[$file] Not Found!"); 
					return array();
				}
				$tmp = self::$_CACHES_YS[$key] = isset($$kk) ? $$kk : $$key; 
				if(is_array($tmp) && (!empty($tmp['i'])) && is_string($tmp['i'])){
					 self::$_CACHES_YS[$key]['i'] = self::tmpItems($modid);
				}
			}
			return self::$_CACHES_YS[$key];
		}elseif($type=='json'){ //返回json(js用)
			require($file); $temp = $$key; // inc大文件，其实很占时间
			if(isset($temp['i'])&&is_string($temp['i'])){
				$file1 = DIR_DTMP."/modcm/$key.cfg.php"; 
				$data = comFiles::get($file1);
			}else{
				$arr = $temp['i'];
				$data = comParse::jsonEncode($arr);
			}
			return "var {$key}_obj = {'cfg':{'title':'$temp[title]','deep':'$temp[deep]'},'i':$data};";
		}else{
			return comFiles::get($file);
		}
	}

	// save config
	static function save($data,$file,$dir='modcm',$type='php'){
		$key = "_$file";
		$file = "$dir/_$file.cfg";
		comFiles::chkDirs($file,'tmp'); 
		$file = "/$file";
		if($type=='php'){
			if(is_array($data)){
				$data = var_export($data,1);
				$data = "\$$key = $data;";
			}
			$data = "<?php\n$data\n?>"; 
			$file .= ".php";
		}else{
			$file .= $type;
		} 
		comFiles::put(DIR_DTMP."$file",$data);
	}
	
	// ~tmp items
	static function tmpItems($mod,$itms=array()){
		$file = "modex/_$mod.cfg_php";
		comFiles::chkDirs($file,'tmp'); 
		$file = DIR_DTMP."/$file";
		if(!empty($itms)){ //save
			$data = comParse::jsonEncode($itms); 
			comFiles::put($file,$data); 
		}else{ //get
			$data = comFiles::get($file); 
	 		$itms = comParse::jsonDecode($data); 
			return $itms;
		}
	}
	
	//返回模型中cfg的数组
	static function mcfg($mod,$re='array'){ 
		$mcfg = self::read($mod);
		if($re=='text') return @$mcfg['cfgs'];
		$cfgs = basElm::text2arr(@$mcfg['cfgs']);
		if($re!='array'){
			return @$cfgs[$re];
		}else{
			return $cfgs;	
		}
	}
	
	//$_vc = vcfg('home'); //'news'
	static function vcfg($mod){ 
		global $_cbase; 
		$renull['c']['vmode'] = 'close';
		$tpldir = $_cbase['tpl']['tpl_dir'];
		if(empty($tpldir)) return $renull;
		$key = "{$tpldir}_$mod"; //检查缓存
		if(isset(self::$_CACHES_YS[$key])) return self::$_CACHES_YS[$key];
		$_groups = self::read('groups'); 
		if(!file_exists(vopTpls::pinc('_config/va_home'))) return array();
		$hcfgs = self::read('home','va'); 
		if($mod=='home'){ //首页
			$re = $hcfgs;
		}elseif(in_array($mod,$hcfgs['close'])){ //关闭模块
			$re = $renull; 
		}elseif(isset($hcfgs['imcfg'][$mod])){ 
			$re = self::read($hcfgs['imcfg'][$mod],'vc'); //导入模块
		}elseif(in_array($mod,$hcfgs['extra'])){ //扩展模块
			$re = self::read($mod,'ve'); 			
		}elseif(file_exists(vopTpls::pinc("_config/vc_$mod"))){ //常规模块
			$re = self::read($mod,'vc'); 
		}elseif(isset($_groups[$mod]) && $_groups[$mod]['pid']=='docs'){ //默认文档处理,(按va_docs)
			$re = self::read('docs','va');  
		}else{ //没有找到规则-当做关闭
			$re = $renull;	
		} //echo "$mod:"; print_r($re['c']); echo "<br>";
		$re['c']['etr'] = vopTpls::etr1(0,$tpldir);
		if(isset($re['c']['vmode']) && $re['c']['vmode']=='static' && empty($re['c']['stext'])){ 
			$re['c']['stext'] = $hcfgs['c']['stext']; //模块未设置后缀,则继承home的后缀
		}
		self::$_CACHES_YS[$key] = $re;
		return $re;
	}
	
	// $cset = get('cbase','sys.cset');
	// $v1 = get('groups/cbase', 'adpic.pid'); --- 只取一个值时可用这个
	static function get($mod,$key='is-arr',$defval=''){ 
		global $_cbase; 
		$org = $mod=='cbase' ? $_cbase : self::read($mod);
		if($key=='is-arr'){
			return $org;
		}else{
			$re = basArray::get($org,$key);
			return $re ? $re : $defval;
		}
		//return  ? $org : basArray::get($org,$key);
	}
		
}
