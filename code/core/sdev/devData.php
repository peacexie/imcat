<?php

// ...类
class devData{	
	
	// cdbStrus(); // 无主索引数据表, varchar(>255)字段, 组合索引数据表 检测
	static function cdbStrus($part){
		$db = glbDBObj::dbObj();
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
		$arr = array('@test','@udoc','dbexp','debug','cache','update','weixin'); // 
		foreach($arr as $dir){
			comFiles::delDir(DIR_DTMP."/$dir",0);
		}
		comFiles::put(DIR_DTMP.updBase::$prereset,"done=locked");
		//comFiles::delDir(DIR_URES,0); //,"@setup_flag.txt"
		//comFiles::delDir(DIR_HTML,0);
	}	
	
	// clrLogs();
	static function clrLogs(){
		$db = glbDBObj::dbObj();
		global $_cbase; 
		$stnow = $_cbase['run']['stamp'];
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
			comFiles::delDir(DIR_DTMP."/tpls/$dir",0);
		}
		comFiles::delDir(DIR_DTMP."/tpls/_vinc",0);
		comFiles::delDir(DIR_DTMP."/tpls/_tagc",0);
	}
	
	// rstTabcode()
	static function rstTabcode(){
		$dcfg = array(
			'code'=>DIR_CODE,
			'tpls'=>DIR_CODE."/tpls",
			'root'=>DIR_ROOT,
			'skin'=>DIR_ROOT."/skin",
		);
		$ptab = DIR_DTMP."/store"; 
		foreach($dcfg as $key=>$path){
			$arr = updBase::listDir($path); 
			updBase::cacSave($arr,"tab_$key.php-cdemo","$ptab");
		}
		$arr = array();
		$cfgs = glbConfig::read('pubcfg','sy');
		foreach($cfgs['copy'] as $file){
			$arr[$file] = md5(comFiles::get(DIR_PROJ."/$file"));
		}
		updBase::cacSave($arr,"tab_proj.php-cdemo",$ptab);
	}
	
	// rstRndata();
	static function rstRndata($path='/dbexp/data~'){
		$db = glbDBObj::dbObj();
		$cfgs = glbConfig::read('pubcfg','sy');
		foreach($cfgs['rndata'] as $tab=>$cfg){
			if(strpos($tab,':')) $tab = substr($tab,0,strpos($tab,':'));
			$file = str_replace("\\","/",DIR_DTMP.$path."$tab.dbsql");
			$list = $db->table($tab)->field($cfg[1])->where($cfg[0])->select();
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
		$db = glbDBObj::dbObj();
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
		$cfgs = glbConfig::read('pubcfg','sy');
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
		$cfgs = glbConfig::read('pubcfg','sy');
		$part = basReq::val('part','main'); //part=main/vars/vimp
		$parts = $cfgs['parts'];
		$pdir = dirname(DIR_PROJ).'/'.date('Y-md-H').$part.'-'.date('is'); 
		mkdir($pdir,0777);
		$arr = $cfgs['dirs']; 
		foreach($arr as $key=>$path){ 
			if(in_array($key,$parts[$part])){
				$sdir = basename($path); //echo "<br>$sdir;"; 
				if(in_array($key,array('ures','html'))){
					mkdir("$pdir/$sdir",0777);
				}else{
					$skip = isset($cfgs['skip'][$key]) ? $cfgs['skip'][$key] : array();
					comFiles::copyDir($path,"$pdir/$sdir",$skip);
				}
				if($part=='vary'){ 
					copy(DIR_CODE.'/index.php',"$pdir/$sdir/index.php");
					copy(DIR_STATIC.'/@setup_flag.txt',"$pdir/$sdir/@setup_flag.txt");
				}
			}
		}
		if($part=='main'){ 
			foreach($cfgs['copy'] as $file){
				copy(DIR_PROJ."/$file","$pdir/$file");
			}
			comFiles::copyDir(DIR_PROJ.'/@read',"$pdir/@read",array());
		}
		foreach($cfgs['del'] as $v){ 
			//if($v[0]==$key){
				comFiles::delDir("$pdir/".$v[0]."$v[1]",1);
			//}
		}
		if($part=='main'){ // ='main' !='vimp'
			foreach($cfgs['ids'] as $v){
				self::rstVals("$pdir/".$v[0]."$v[1]",$v[2]);
			}
			self::rstDemo($pdir);
		}
	}
	
	// struExp('/dbexp/');导出结构到文件或返回string
	static function struExp($path,$dbcfg=array()){ 
		$db = glbDBObj::dbObj($dbcfg);
		$dbTabs = $db->tables();
		$re = "";
		foreach($dbTabs as $tab){ 
			$re .= "\n".self::stru1Exp($tab,$dbcfg).";\n";
		}
		if($path){
			$path = DIR_DTMP.$path.'_stru_tables.dbsql';
			comFiles::put($path,$re);
		}else{
			return $re;
		}
	}
	
	// stru1Exp('tablename'); 导出单个表结构
	static function stru1Exp($tab,$dbcfg=array()){ 
		$db = glbDBObj::dbObj($dbcfg);
		$tabfull = $db->pre.$tab.$db->ext; 
		$stru = "DROP TABLE IF EXISTS `{pre}$tab{ext}`;";
		$stru .= "\n".$db->create($tab); 
		$stru = str_replace("`$tabfull`","`{pre}$tab{ext}`",$stru);
		return $stru;
	}
	
	// struImp('/dbexp/'); 从文件导入结构
	static function struImp($path){
		$db = glbDBObj::dbObj(); 
		$file = DIR_DTMP.$path.'_stru_tables.dbsql';
		$data = comFiles::get($file);
		$fix1 = 'DROP TABLE IF EXISTS `'; 
		$fix2 = 'CREATE TABLE `';
		$pre = '{pre}'; $suf = '{ext}';
		$arr = explode($fix1.$pre,$data);
		$errs = array(); $oks = 0;
		foreach($arr as $sql){
		if(strstr($sql,$fix2.$pre)){
			$sql = $fix1.$pre.$sql;
			$arr2 = explode($fix2.$pre,$sql);
			$sqla = $arr2[0];			 $sqla = str_replace(array($pre,$suf),array($db->pre,$db->ext),$sqla);
			$sqlb = "$fix2$pre".$arr2[1]; $sqlb = str_replace(array($pre,$suf),array($db->pre,$db->ext),$sqlb);
			#echo " \n \n <br>$sqla \n <br>$sqlb";
			try {
				$db->query($sqla,'run');
				$db->query($sqlb,'run');
				$oks++;
			}catch (Exception $e){
				$errs[] = $e->getMessage();
			}
		} }
		return array($oks,$errs);
	}
	
	// 
	static function dataExpGroup($path,$dbcfg=array(),$pfull=0){
		$db = glbDBObj::dbObj($dbcfg);
		$dbTabs = $db->tables(); 
		$fix = array($db->pre,$db->ext); //array('{pre}','{ext}')
		$groups = devBase::_tabGroup($dbTabs); 
		foreach($groups as $group){ 
			$cfgs = array("{$group}_"); $data = '';
			foreach($dbTabs as $tab){ 
				$flag = devBase::_tabIncfg($tab,$cfgs); 
				if(empty($cfgs) || $flag){ 
					$tabfull = $db->pre.$tab.$db->ext; 
					$list = $db->table($tab)->select(); 
					if($list){ 
						$thead = devBase::_tabHead($tab,$fix,'REPLACE'); 
						$tdata = ''; $i = 0;  
						foreach($list as $row){
							$i++; $end = $i==count($list) ? ';' : ',';
							$tdata .= devBase::_dinsRow($row)."$end\n";
						}
						if($tdata) $data .= "$thead$tdata";
			}	}	}
			if(!empty($data)){
				$pathi = $pfull ? $path : DIR_DTMP.$path;
				$fp = fopen(str_replace("\\","/",$pathi."$group.dbsql"), 'w');
				fwrite($fp, $data);
				fclose($fp);
			}
		} //groups
	}
	
	// dataExp('/dbexp/'); 导出指定表(所有表)数据
	static function dataExp($path,$cfgs='',$mode='in',$dbcfg=array(),$pfull=0){
		$db = glbDBObj::dbObj($dbcfg);
		$dbTabs = $db->tables();
		$groups = devBase::_tabGroup($dbTabs); 
		if(empty($cfgs)){
			$cfgs = '';
		}elseif(is_string($cfgs)){
			$cfgs = in_array($cfgs,$groups) ? "{$cfgs}_" : $cfgs;
		}// is_array
		foreach($dbTabs as $tab){ 
			$flag = devBase::_tabIncfg($tab,$cfgs); 
			if(empty($cfgs) || ($flag && $mode=='in') || (!$flag && $mode=='notin')){
				self::data1ExpInsert($path,$tab,$dbcfg,$pfull); 
			}
		}
	}	
	
	// data1Exp("/dborg/data~",'base_fields'); 导出单个表数据到文件
	static function data1ExpInsert($path,$tab,$dbcfg=array(),$pfull=0){
		$db = glbDBObj::dbObj($dbcfg);
		$tabfull = $db->pre.$tab.$db->ext; 
		$path = $pfull ? $path : DIR_DTMP.$path;
		$list = $db->table($tab)->select(); 
		if($list){ //分块未考虑... 
			$shead = devBase::_tabHead($tab); $i = 0; 
			$fp = fopen(str_replace("\\","/",$path."$tab.dbsql"), 'w');
			fwrite($fp, $shead);
			foreach($list as $row){
				$i++; $end = $i==count($list) ? ';' : ',';
				$rstr = devBase::_dinsRow($row)."$end\n";
				fwrite($fp, $rstr);
			}
			fclose($fp);
		}
	}
	
	// data1ExpFile("/dborg/data~",'base_fields'); 导出单个表数据到文件
	static function data1ExpFile($path,$tab,$dbcfg=array()){
		$db = glbDBObj::dbObj($dbcfg);
		$tabfull = $db->pre.$tab.$db->ext; 
		$path = DIR_DTMP.$path;
		try{
			$file = str_replace("\\","/",$path."$tab.dbsql"); 
			$sql="SELECT * FROM {$tabfull} INTO OUTFILE '$file' ".devBase::_loadOpt();
			if(file_exists($file)){
				unlink($file);
			}
			$db->query($sql,'run');
			$data = comFiles::get($file);
			if(empty($data)){
				unlink($file);
			}
			return true;
		}catch(Exception $e){
			#print_r($e->getMessage());;
			return false;
		}
	}
	
	// dataImpFile("/dborg/data~",'base_fields'); 从文件导入数据
	static function dataImpFile($path,$tab,$dtmp=0){
		$db = glbDBObj::dbObj(); 
		$tabfull = $db->pre.$tab.$db->ext;
		$path = ($dtmp ? $dtmp : DIR_DTMP).$path;
		$file = str_replace("\\","/",$path."$tab.dbsql"); 
		$sqlClean = "DELETE FROM $tabfull";
		$sqlLoad = "LOAD DATA INFILE '$file' INTO TABLE $tabfull ".devBase::_loadOpt();
		try {
			$db->query($sqlClean,'run');
			$db->query($sqlLoad,'run');
			return true;
		}catch (Exception $e){
			return false;
		}	  
	}
	
	// dataImpInsert("/dborg/data~",'base_fields'); 从文件导入数据
	static function dataImpInsert($path,$tab,$dtmp=0){
		$db = glbDBObj::dbObj(); 
		$tabfull = $db->pre.$tab.$db->ext;
		$path = ($dtmp ? $dtmp : DIR_DTMP).$path;
		$file = str_replace("\\","/",$path."$tab.dbsql"); 
		$fsql = comFiles::get($file);
		if(empty($fsql)){
			return false;
		}
		$sqlClean = "DELETE FROM $tabfull";
		$sqlLoad = str_replace(array("`{pre}{$tab}{ext}`"),array("`$tabfull`"),$fsql);
		try {
			$db->query($sqlClean,'run');
			$db->query($sqlLoad,'run');
			return true;
		}catch (Exception $e){
			return false;
		}	  
	}

}

