<?php

// ...类
class devScan{	

	// cdbStrus(); // 无主索引数据表, varchar(>255)字段, 组合索引数据表 检测
	static function cdbStrus($part){
		$db = db();
		$tabs = $db->tables();
		$re = ''; $ns = ''; $np = 0;
		foreach($tabs as $tab){ 
			$fa = $db->fields($tab);
			foreach($fa as $k=>$v){ //print_r($v);
				$len = str_replace(array('varchar(',')'),'',$v['type']); 
				if(strstr($v['type'],'varchar(') && intval($len)>255) $ns .= ",$k($len) "; 
				if(!empty($v['primary'])) $np++; 
			}
			if($part=='cdbV255' && $ns) $re .= "\n<br>$tab : ".substr($ns,1);
			if($part=='cdbPKey' && !$np) $re .= "\n<br>$tab : $np(PRIMARY)";
			if($part=='cdbMKey' && $np>1) $re .= "\n<br>$tab : $np(PRIMARY)";
			$ns = ''; $np = 0;
		}
		return $re;
	}	
	
	// clrTmps();
	static function clrTmps(){
		$arr = array('@test','@udoc','dbexp','debug','cache','weixin'); 
		foreach($arr as $dir){ //,'update','updsvr'
			comFiles::delDir(DIR_DTMP."/$dir",0);
		}
		comFiles::put(DIR_DTMP.updBase::$prereset,"done=locked");
		//comFiles::delDir(DIR_URES,0); //,"@setup_flag.txt"
		//comFiles::delDir(DIR_HTML,0);
	}	
	
	// clrLogs();
	static function clrLogs(){
		$db = db();
		$stnow = time();
		// 432000=5day, 86400=1天 active_online
		$db->table('active_admin')->where("stime<'".($stnow-86400)."'")->delete(); 
		$db->table('active_online')->where("stime<'".($stnow-86400)."'")->delete(); 	
		$db->table('active_session')->where("exp<'".($stnow-3600)."'")->delete();
		$logtabs = array(
			'logs_dbsql','logs_syact','logs_detmp','logs_jifen',
			'plus_smsend','plus_emsend','plus_paylog',
			'exd_crlog','exd_oilog','exd_pslog','xtest_keyid',
		);
		foreach($logtabs as $tab){
			$db->table($tab)->where("atime<'".$stnow."'")->delete();
		}
		$tabinfo = $db->tables(); //print_r($tabinfo);
		$db->table('bext_dbdict')->where("tabid NOT IN('".implode("','",$tabinfo)."')")->delete();
		foreach(array('wex_locate','wex_msgget','wex_msgsend','wex_qrcode') as $tabid){
			$db->table($tabid)->where("atime<'".($stnow-3600)."'")->delete();
		}
	}

	// clrCTpl(); //advs, tagc, 
	static function clrCTpl($part=''){
		if(empty($part)){
			$vcfgs = vopTpls::etr1('tpl'); 
		}else{
			$vcfgs = array("_$part"=>'');	
		}
		foreach($vcfgs as $dir=>$suit){ 
			comFiles::delDir(DIR_CTPL."/$dir",0);
		}
		comFiles::delDir(DIR_CTPL."/_vinc",0);
		comFiles::delDir(DIR_CTPL."/_tagc",0);
		comFiles::delDir(DIR_CTPL."/demodir",0);
	}
	
	// rstTabcode()
	static function rstTabcode(){
		$dcfg = array(
			'code'=>DIR_CODE,
			'root'=>DIR_ROOT,
			'skin'=>DIR_SKIN,
		);
		$ptab = DIR_DTMP."/store"; 
		foreach($dcfg as $key=>$path){
			$arr = updBase::listDir($path); 
			updBase::cacSave($arr,"tab_$key.php-cdemo","$ptab");
		}
		$arr = array();
		$cfgs = read('pubcfg','sy');
		foreach($cfgs['copy'] as $file){
			$arr[$file] = md5(comFiles::get(DIR_PROJ."/$file"));
		}
		updBase::cacSave($arr,"tab_proj.php-cdemo",$ptab);
	}
	// rstTabmini()
	static function rstTabmini(){
		$dir = DIR_DTMP."/dbexp";
		$files = comFiles::listDir($dir,'file');
		if(empty($files)) return;
		foreach ($files as $file => $value) {
			if(strpos($file,'~bext_mins')) unlink("$dir/$file");
			if(strpos($file,'gbak~')===0) unlink("$dir/$file");
		}
	}
	// rstRndata();
	static function rstRndata($path='/dbexp/data~'){
		$cfgs = read('pubcfg','sy');
		foreach($cfgs['rndata'] as $tab=>$cfg){
			if(strpos($tab,':')) $tab = substr($tab,0,strpos($tab,':'));
			$file = str_replace("\\","/",DIR_DTMP.$path."$tab.dbsql");
			$list = db()->table($tab)->field($cfg[1])->where($cfg[0])->select();
			if($list){	
				$data = $dbak = comFiles::get($file);
				foreach($list as $row){
					$fa = explode(',',$cfg[1]);
					foreach($fa as $fk){
						$old = $row[$fk];
						$new = empty($cfg[2][$fk]) ? devBase::_drndData($old) : $cfg[2][$fk]; 
						$data = str_replace("'$old'","'$new'",$data);
					}
				}
				if($data!=$dbak) comFiles::put($file,$data); 
			}
		}
	}
	
	// rstCache();
	static function rstCache(){
		glbCUpd::upd_paras('score'); 
	}
	
	// rstIDPW();
	static function rstIDPW($uname='',$upass=''){
		$db = db();
		$enc = comConvert::sysPass($uname,$upass,'adminer');
		$db->table('users_uacc')->data(array('uname'=>$uname,'upass'=>$enc))->where("aip='(reset)'")->update();
		$db->table('users_adminer')->data(array('uname'=>$uname))->where("aip='(reset)'")->update();
	}	
	
	// 替换配置文件中的变量值
	static function rstVals($file,$pars=array(),$merge=1){
		if(!file_exists($file)) return; 
		$defs = array(
			'user'=>'user_id', 'pass'=>'u_pass', 'host'=>'127.0.0.1',
			'uid'=>'user_id', 'upw'=>'u_pass', 'pwd'=>'u_pass',
			'ak'=>'user_id', 'sk'=>'u_pass',
		);
		$vals = array();
		foreach($pars as $k=>$v){
			if(is_numeric($k)){
				$vals[$v] = isset($defs[$v]) ? $defs[$v] : "uset_$v";
			}else{
				$vals[$k] = $v;	
			}
		}
		if($merge){
		foreach($defs as $k=>$v){
			if(!isset($vals[$k])) $vals[$k] = $v;	
		} }
		$data = comFiles::get($file);
		foreach($vals as $k=>$v){ 
			$key = preg_quote($k); //echo "$k=$key, ";
			$data = preg_replace("/[$]$key\s*\=\s*.*?;/is", "\${$key} = '$v';", $data);
			$data = preg_replace("/(\[(['|\"]?)$key(['|\"]?)\])\s*\=\s*.*?;/is", "\\1 = '$v';", $data);
		}
		comFiles::put($file,$data);
		return "<pre>".str_replace('<','&lt;',$data)."</pre>";
	}
	
	static function rstDemo($pdir){
		$cfgs = read('pubcfg','sy');
		foreach($cfgs['cdemo'] as $v=>$rep){
			$fp = file_exists("$pdir/$v-cdemo") ? "$pdir/$v-cdemo" : "$pdir/$v";
			$data = comFiles::get($fp);
			if(!empty($rep)){
				$data = str_replace($rep[0],$rep[1],$data);
			}
			comFiles::put("$pdir/$v",$data);
		}
	}
	
	// 重新发布目录
	static function rstPub(){
		$cfgs = read('pubcfg','sy');
		$part = req('part','main'); //part=main/vars/vimp
		$parts = $cfgs['parts'];
		$pdir = dirname(DIR_PROJ).'/'.date('Y-md-H').$part.'-'.date('is'); 
		mkdir($pdir,0777);
		$arr = $cfgs['dirs']; 
		foreach($arr as $key=>$path){ 
			if(in_array($key,$parts[$part])){
				$sdir = basename($path); //echo "<br>$sdir;"; 
				$skip = isset($cfgs['skip'][$key]) ? $cfgs['skip'][$key] : array();
				if(in_array($key,array('ures','html','ctpl'))){
					mkdir("$pdir/$sdir",0777);
				}else{
					comFiles::copyDir($path,"$pdir/$sdir",$skip,$cfgs['skfiles']);
				}
				if($part=='vary'){ 
					copy(DIR_CODE.'/index.php',"$pdir/$sdir/index.php");
					copy(DIR_STATIC.'/@setup_flag.txt',"$pdir/$sdir/@setup_flag.txt");
					foreach($skip as $dir){
						if(in_array($dir,array('updsvr','08exp'))) continue;
						mkdir("$pdir/$sdir/$dir");
						comFiles::put("$pdir/$sdir/$dir/.gitkeep.txt",'');
					}
				}
			}
		}
		if($part=='main'){ 
			foreach($cfgs['copy'] as $file){
				copy(DIR_PROJ."/$file","$pdir/$file");
			}
			foreach($cfgs['ids'] as $v){
				self::rstVals("$pdir/".$v[0]."$v[1]",$v[2]);
			}
			self::rstDemo($pdir);
			comFiles::copyDir(DIR_PROJ.'/@read',"$pdir/@read",array());
		}
	}
	
}

