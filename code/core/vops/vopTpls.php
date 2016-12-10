<?php
/*

*/
// 模版相关
class vopTpls{
	
	//获得模版或缓存路径:type=tpl,tpc;
	static function path($type='',$root=1){
		$tpldir = cfg('tpl.tpl_dir');
		return ($root ? ($type=='tpc' ? DIR_CTPL : DIR_SKIN) : '').'/'.$tpldir;  
	}
	
	// include_once:扩展函数：{php vopTpls::pinc('chn:tex_keres');} -=> chn/b_func/tex_keres.php
	// 得到_config路径：      {php include(vopTpls::pinc('_config/va_home')); } -=> _config/va_home.php
	// 得到include需要的路径：{php include(vopTpls::pinc('d_tools/a_cfgs')); } -=> d_tools/a_cfgs.php
	static function pinc($finc,$ext='',$refull=1){
		$fpos = strpos($finc,'/');
		if(strpos($finc,':')){
			$a = explode(':',$finc);
			$tpl = $a[0];
			$finc = $a[1];
		}else{
			$tpl = cfg('tpl.tpl_dir');
		}
		$ext = empty($ext) ? '.php' : $ext;
		if($fpos){ 
			return ($refull ? DIR_SKIN : '')."/$tpl/$finc$ext";
		}else{ 
			include_once(DIR_SKIN."/$tpl/b_func/$finc$ext");
		}
	}
	
	//兼容方法
	static function pcfg($mod='',$root=1){ return self::pinc("_config/{$mod}",'',$root); }
	
	//设置当前tpl:set tpl path
	static function set($dir=''){
		global $_cbase; 
		//$dir = $dir ? $dir : req('tpldir');
		if($dir){
			$_cbase['tpl']['tpl_dir'] = $dir;	
		}
		return empty($_cbase['tpl']['tpl_dir']) ? '' : $_cbase['tpl']['tpl_dir'];
	}
	
	//获得默认模板
	static function def($type='adm'){
		$tpldir = cfg('tpl.tpl_dir');
		if(!empty($tpldir)){
			return $tpldir;
		}else{
			$vcfg = vopTpls::etr1('show'); 
			return $vcfg['_deadmin_'];	
		}
	}
	
	//type=res,show,tpl;title;0,1,
	static function etr1($type=0,$dir=''){
		$vcfg = read('vopfmt','ex'); 
		if(strlen($type)<3){ // 0,1,''
			$etr = PATH_PROJ.$vcfg['tpl'][$dir][1];
			if($type){ //$full
				$etr = cfg('run.rsite').$etr;
			}
			return $etr;
		}elseif(in_array($type,array('show','tpl'))){
			return $vcfg[$type];
		}elseif($type=='title'){
			return $vcfg['tpl'][$dir][0];
		}else{ //all
			return $vcfg;
		}
	}
	
	// entry 
	// $cb=emumem/ehlist
	static function entry($dir='',$cb='emumem',$mode=''){
		$dir = $dir ? $dir : cfg('tpl.tpl_dir');	
		$dir = DIR_SKIN."/$dir/_config";
		$list = comFiles::listDir($dir);
		$re = array();
		foreach($list['file'] as $file=>$v){ 
			if(strpos($file,'.maobak')) continue;
			$key = str_replace('.php','',$file);
			$kc = "_$key"; $km = substr($key,3);
			include("$dir/$file"); $cfg[$km] = $$kc;
			if(!in_array($key,array('va_docs'))){ //,'va_home'
				$re = $re + self::$cb($cfg[$km],$km,$mode); 
		}	} 
		if(!empty($cfg['home']['close'])){
			foreach($cfg['home']['close'] as $km){
				unset($re[$km]);
			}
		}
		if(!empty($cfg['home']['imcfg'])){
			foreach($cfg['home']['imcfg'] as $km=>$from){
				$re = $re + self::$cb($cfg[$from],$km,$mode); 
			}
		}
		return $re;
	}
	// emumem
	static function emumem($cfg,$km,$mode){
		$re = array();
		foreach(array('c','v') as $k) unset($cfg[$k]); //'d','m','t','first'
		foreach($cfg as $ki=>$kv){ 
			if(empty($kv) || $km=='home') continue;
			$kv = (is_array($kv) && isset($kv[0])) ? $kv[0] : $kv;
			$re["$km-$ki"] = $kv;
		} 
		return $re;
	}
	// $mode=dynamic/static/both/all/
	static function ehlist($cfg,$km,$mode){
		$re = array();
		if(in_array($mode,array('static','dynamic')) && $cfg['c']['vmode']!=$mode) return $re;
		if($mode=='both' && !in_array($cfg['c']['vmode'],array('static','dynamic'))) return $re;
		foreach(array('c','v','d') as $k) unset($cfg[$k]); //,'m','t','first'
		foreach($cfg as $ki=>$kv){ 
			if(empty($kv)) continue;
			if($km=='home' && $ki!='m') continue;
			$kv = (is_array($kv) && isset($kv[0])) ? $kv[0] : $kv;
			if($ki=='t'){ 
				$re[$km] = empty($re[$km]) ? array() : $re[$km];
				$re[$km] = $re[$km] + self::etypes($km,$kv,""); 
			}else{ 
				$re[$km]["$ki"] = $kv;
			}
		}
		return $re;
	}
	// etypes
	static function etypes($km,$kval,$fix=''){
		$re = array();
		$mcfg = read($km); 
		foreach($mcfg['i'] as $ki=>$kv){ 
			$re["$fix$ki"] = $kval; 
		}
		return $re;
	}
	
	static function check($tpl,$die=1){
		static $tplchks;
		if(empty($tplchks[$tpl])){
			$vopfmt = read('vopfmt','ex'); 
			if(empty($vopfmt['tpl'][$tpl])){ //无tpl配置
				$tplchks[$tpl]['cfg'] = 1;
			}
			$fp = DIR_SKIN."/$tpl/_config/va_home.php";
			if(!file_exists($fp)){
				$tplchks[$tpl]['dir'] = 1;
			}
			if(empty($tplchks[$tpl])){
				$tplchks[$tpl]['ok'] = 1;	
			}
		} //print_r($tplchks);
		if($die && empty($tplchks[$tpl]['ok'])){
			vopShow::msg("[excfg/ex_vopfmt.php]/[$tpl/_config] Config Error!");
		} 
		return $tplchks[$tpl];
	}

}