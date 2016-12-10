<?php

// ...类
class devSetup{	

	static $fsetuped = '/store/_setup_lock.txt'; 
	static $flagfile = '/store/_setup_step.txt'; 
	static $flagdata = "###Start###\nstep1=Null\nstep2=Null\nstep3=Null\nstep4=Null\nstep5=Null\n###End###"; 
	static $demo_tabs = array(
		'dext_demo','docs_demo', 'dext_news','docs_news', 'dext_cargo','docs_cargo', 
		'dext_keres','docs_keres', 'dext_faqs','dext_faqs', 
	); 

	// 安装/更新一个模块
	static function ins1Item($act,$mod,$type,$kid,$pid){
		if($act=='Install'){
			$fun = $type=='mods' ? 'ins1Mod' : 'ins1Menu'; 
			$idata = comFiles::get(DIR_DTMP.'/update/'."ins~$kid.dbsql"); 
			$res = self::$fun($mod,$idata,$pid); 
		}else{ // &act=Close&acg=mods&mod=inrem
			$tab = $type=='mods' ? 'base_model' : 'base_menu';
			$data = array('enable'=>$act=='Close'?0:1);
			db()->table($tab)->data($data)->where("kid='$mod'")->update();
		}
		if($type=='mods'){
			glbCUpd::upd_groups(); 
			glbCUpd::upd_paras($mod);
		}else{
			glbCUpd::upd_model('muadm');
			glbCUpd::upd_menus('muadm'); 
		}
	}

	// 导入一项数据
	static function ins1Data($data,$key=''){
		if(is_array($data) && isset($data[$key])){ return $data[$key]; }
		return comConvert::impData($data,$key,'sql');
	}

	// 导入一个模型数据
	static function ins1Mod($mod,$data=array(),$pid=0){
		if(empty($mod)) return array();
		// config
		$cfgs = array('model','fields','catalog','grade','fldext');
		foreach ($cfgs as $key) { 
			$idata = self::ins1Data($data,"{$key}_$mod"); 
			$flag = devData::run1Sql($idata);
		}
		#if($type=='Update') return 'OK!';
		$tabm = "{$pid}_$mod";
		$tabs = $pid=='docs' ? "$tabm,dext_$mod" : $tabm; 
		$tarr = explode(',',$tabs);
		foreach ($tarr as $tab) {
			$idata = self::ins1Data($data,"stru_$tab"); 
			$flag = devData::run1Sql($idata);
		}
		return 'OK!';
	}

	// 导入一个菜单数据
	static function ins1Menu($menu,$data=array(),$pid=0){
		if(empty($menu)) return array();
		$idata = self::ins1Data($data,"menu_$menu");
		$mpid = req($menu); if(empty($mpid)) $mpid = $pid;
		$idata = str_replace(",'(pid-$menu)',",",'$mpid',",$idata);
		$flag = devData::run1Sql($idata);
		//echo "\n\n::(pid-$menu):".req($menu);
		return 'OK!';
	}

	// 导出安装模组 模型/菜单
	static function expGroup($mods,$menus='',$xxx=''){
		if(strlen("$mods$menus")==0) return '';
		$data = $mids = $ares = array(); 
		$_groups = read('groups'); 
		$_muadm = read('muadm.i'); 
		// menu
		$marr = explode(',',$menus);
		foreach ($marr as $menu) {
			if(empty($menu) || empty($_muadm[$menu])) continue;
			$mpid = $_muadm[$menu]['pid']; 
			$mdata = devData::exp1Tab('base_menu',"kid='$menu' OR pid='$menu'"); 
			$mpid && $data['menu_'.$menu] = str_replace(",'$mpid',",",'(pid-$menu)',",$mdata);
			$ares['menus'][$menu] = $_muadm[$menu];
		}
		// model
		$marr = explode(',',$mods);
		foreach ($marr as $mod) {
			if(empty($mod) || empty($_groups[$mod])) continue;
			$imod = devData::exp1Mod($mod,0);
			$data = array_merge($data,$imod);
			$ares['mods'][$mod] = $_groups[$mod];
		}
		// save
		$fp = "/dbexp/ins~$mods-$menus";
		$dstr = "\nstart(!@~)\n";
		foreach ($data as $key => $val) {
			if(empty($val)) continue;
			$dstr .= "\n\n".str_repeat('#',32)."\n[$key]\n$val\n[/$key]\n";
			$ares['keys'][] = $key;
		}
		$dstr .= "\n(!@~)isend\n";
		$dres = "\n$dstr\n"; 
		comFiles::put(DIR_DTMP."$fp.dbsql",$dres);
		$ares['keys'] = implode(',',$ares['keys']);
		$ares['notes'] = '';
		$ares = "<?php\nreturn ".var_export($ares,1).";\n?>";
		comFiles::put(DIR_DTMP."$fp.php",$ares);
		return $fp;
	}

	// supCfgs
	static function supCfgs(){ 
		$setflag = DIR_DTMP.self::$flagfile;
		file_exists($setflag) || comFiles::put($setflag,self::$flagdata);
		$text = comFiles::get($setflag);
		$data = basElm::text2arr($text); 
		$jstr = "var ";	$okcnt = 0; $nstep = '-1';
		foreach($data as $k=>$v){ 
			if(strstr($k,'###')) continue;
			if($v=='OK'){ 
				$okcnt++;
				$nstep = str_replace('step','',$k);
			}
			$jstr .= "$k='$v', ";
		}
		if($nstep==5 || self::isSetuped()){
			basMsg::show(lang('devsetup_deltip')."<br>[".basDebug::hidInfo(DIR_DTMP)."/store/]".lang('devsetup_dt1')."<br>_setup_step.txt ".lang('devsetup_dt2')." _setup_lock.txt".lang('devsetup_dt3')."",'die');
		}
		$pvInfo = devRun::verPHP(); //定义了FLAGYES/FLAGNO常量
		$jstr .= "\nfYES='".FLAGYES."',\nfNO='".FLAGNO."',";
		$jstr .= "\nfRes='".(($okcnt && $okcnt==$nstep) ? lang('devsetup_donen') : lang('devsetup_nosetup'))."',";
		
		$files = comFiles::listDir(DIR_DTMP.'/dborg'); 
		$all_tabs = array_keys($files['file']); 
		$demo_tabs = self::$demo_tabs;
		$base_tabs = array();
		foreach($all_tabs as $tab1){
			if(substr($tab1,0,5)=='data~'){
				$tab1 = str_replace('.dbsql','',substr($tab1,5));
				if(!in_array($tab1,$demo_tabs)) $base_tabs[] = $tab1;
			}
		}
		$jstr .= "\ndemo_tabs='".implode(',',$demo_tabs)."',\nbase_tabs='".implode(',',$base_tabs)."';\n";
		#print_r($all_tabs); print_r($demo_tabs); print_r($base_tabs); 
		return array($data,'okcnt'=>$okcnt,'jstr'=>$jstr);	
	}
	
	// supMark
	static function supMark($step,$val='OK'){ 
		$setflag = DIR_DTMP.self::$flagfile;
		$text = comFiles::get($setflag);
		$text = preg_replace("/step{$step}\=\S+/is", "step{$step}=$val", $text);
		comFiles::put($setflag,$text);
		if($step==4){ glbCUpd::upd_groups(); }
		if($step==5){ 
			comFiles::put(DIR_DTMP.self::$fsetuped,date('Y-m-d H:i:s'));
			vopStatic::advMod('adtext',"(all)");
			vopStatic::advMod('adpic',"(all)");
			vopStatic::advMod('adblock',"(all)");
		}
	}
	
	// supCheck
	static function supCheck(){ 
		$a = array('verPHP','verGdlib'); 
		foreach($a as $k){ 
			$re = devRun::$k(); 
			$re = $re['res']; 
			if($re!=FLAGYES){ 
				$re = array('res'=>'','msg'=>lang('devsetup_chkenv'));
				self::ajaxStop($re);
			}
		}
		$cfg = devRun::runPath($k); //print_r($cfg);
		foreach($cfg as $re){ 
			$re = $re['res'];
			if($re!=FLAGYES){ 
				$re = array('res'=>'','msg'=>lang('devsetup_chkdir'));
				self::ajaxStop($re);
			}
		}
		$a3 = devRun::runMydb3(); $n3 = 0;
		foreach($a3 as $k=>$re1){ 
			$re = $re1['res']; 
			if($re==FLAGYES) $n3++;
		}
		$re = $n3 ? 'OK' : ''; //print_r($re); 
		$msg = $n3 ? '' : lang('devsetup_chkmysql');
		$re = array('res'=>$re,'msg'=>$msg);
		self::ajaxStop($re);
	}
	
	// supStru 
	static function supStru(){ 
		if(self::isSetuped()){ die('isRunning...'); }
		$re = devData::struImp('/dborg/');
		if(!empty($re[1])){
			$msg = implode('<br>',$re[1]);
			$re = array('res'=>'','msg'=>$msg);
		}elseif(empty($re[0])){
			$re = array('res'=>'','msg'=>lang('devsetup_noframe'));	
		}else{
			$re = array('res'=>'OK','msg'=>'');
		}
		self::ajaxStop($re);
	}
	
	// supImps
	static function supImps($tab){ 
		if(self::isSetuped()){ die('isRunning...'); }
		$re = devData::dataImpInsert("/dborg/data~",$tab);
		$re = array('res'=>'OK','msg'=>'');
		self::ajaxStop($re);
	}
	
	// supIdpw 
	static function supIdpw(){ 
		if(self::isSetuped()){ die('isRunning...'); }
		// Rnd_Keys
		$rcfg['sys_name']   = req('name');
		$rcfg['safe_site']  = 'name';  for($i=0;$i<5;$i++) $rcfg['safe_site']  .= '-'.basKeyid::kidRand('f',5);
		$rcfg['safe_pass']  = 'pass';  for($i=0;$i<5;$i++) $rcfg['safe_pass']  .= '-'.basKeyid::kidRand('fs3',5);
		$rcfg['safe_api']   = 'api';   for($i=0;$i<5;$i++) $rcfg['safe_api']   .= '-'.basKeyid::kidRand('f',5);
		$rcfg['safe_js']	= 'js';	for($i=0;$i<5;$i++) $rcfg['safe_js']	.= '-'.basKeyid::kidRand('f',5);
		$rcfg['safe_other'] = 'other'; for($i=0;$i<5;$i++) $rcfg['safe_other'] .= '-'.basKeyid::kidRand('f',5);
		$rcfg['safe_safil'] = 'safil'; for($i=0;$i<5;$i++) $rcfg['safe_safil'] .= '-'.basKeyid::kidRand('f',5);
		$rcfg['safe_rnum']  = basKeyid::kidRand('0',24);
		$rcfg['safe_adminer'] = 'adm'; for($i=0;$i<5;$i++) $rcfg['safe_adminer'] .= '-'.basKeyid::kidRand('fs3',5);
		$rcfg['safe_safix']  = '_'.basKeyid::kidRand('0',3);
		$rcfg['safe_rndtab']  = basKeyid::kidRTable('f');
		$rcfg['tout_admin']  = 1;
		$rcfg['tout_member']  = 4;
		foreach($rcfg as $k=>$v){
			db()->table('base_paras')->data(array('val'=>$v))->where("kid='$k'")->update();
		}
		global $_cbase;
		$_cbase['safe']['site']  = $rcfg['safe_site'];
		$_cbase['safe']['pass']  = $rcfg['safe_pass'];
		$_cbase['safe']['safil'] = $rcfg['safe_safil'];
		$_cbase['safe']['adminer'] = $rcfg['safe_adminer'];
		$_cbase['safe']['safix'] = $rcfg['safe_safix'];
		$_cbase['safe']['rndtab'] = $rcfg['safe_rndtab'];
		$_cbase['tout_admin'] = $rcfg['tout_admin'];  
		$_cbase['tout_member'] = $rcfg['tout_member'];	
		devScan::rstIDPW(req('uid'),req('upw'));
		self::updCache();
		$re = array('res'=>'OK','msg'=>'');
		self::ajaxStop($re);
	}
	
	static function setDbname(){
		$tm = time(); $h = date('H');
		if($h<6) $tm = $tm+8*3600; if($h>18) $tm = $tm-8*3600;
		$md = substr(basKeyid::kidTemp('0'),0,7);
		$dbname = "catv".cfg('sys.ver')."_".$md;
		$dbname = str_replace(array('.','-'),array('',''),$dbname);
		return $dbname;
	}

	// ajaxStop 
	static function ajaxStop($re){ 
		$act = req('act');
		$step = req('step'); 
		$re['with'] = "act=$act;step=$step";
		$re = comParse::jsonEncode($re);
		//glbHtml::head('json');
		die($re);
	}

	static function isSetuped(){ 
		return file_exists(DIR_DTMP.self::$fsetuped);
	}

	// updCache 
	static function updCache(){ 
		$g0 = db()->table('base_model')->where("enable='1'")->order('kid')->select();
		$skip = array('groups','plus','docs','coms','users','advs',);
		foreach($g0 as $k=>$v){
			$key = $v['kid'];
			if(in_array($key,array('score','sadm','smem','suser',))){ 
				glbCUpd::upd_paras($key);
			}elseif(in_array($v['pid'],$skip) || in_array($key,$skip)){
				continue;
			}else{ // pid in 'docs','coms','users','advs','types'
				glbCUpd::upd_model($key);
				//echo "$key, ";
			}
		}
		glbCUpd::upd_relat();
		glbCUpd::upd_menus('muadm');//menua
		admAFunc::umcVInit();
		glbCUpd::upd_grade();
	}

}
