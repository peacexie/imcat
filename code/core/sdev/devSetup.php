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

	// 导入一个模型数据
	static function ins1Mod($mod,$data=array(),$type=''){
		if(empty($mod)) return array();
		// config
		$cfgs = array('model','fields','catalog','grade','fldext');
		foreach ($cfgs as $key) { //$data['model_'.$mod] = ...; 
			$idata = $data["{$key}_$mod"]; 
			$flag = devData::run1Sql($idata,$type);
		}
		if($type=='Update') return 'OK!';
		// stru_
		$tabm = glbDBExt::getTable($mod,0);
		$tabe = glbDBExt::getTable($mod,1);
		$tabs = $tabm==$tabe ? $tabm : "$tabm,$tabe";
		$tarr = explode(',',$tabs);
		foreach ($tarr as $tab) { // $data['stru_'.$tab] = ...
			$idata = $data["stru_$tab"]; 
			$flag = devData::run1Sql($idata);
		}
		return 'OK!';
	}

	// 导入一个菜单数据
	static function ins1Menu($menu,$data=array(),$type='',$pid=0){
		if(empty($menu)) return array();
		$idata = $data["menu_$menu"]; //print_r($idata);
		$mpid = basReq::val($menu); if(empty($mpid)) $mpid = $pid;
		$idata = str_replace(",'(pid-$menu)',",",'$mpid',",$idata);
		$flag = devData::run1Sql($idata);
		//echo "\n\n::(pid-$menu):".basReq::val($menu);
		return 'OK!';
	}

	static function insDeel($fnow=''){
		$_groups = glbConfig::read('groups'); 
		$_muadm = glbConfig::read('muadm'); $_muadm = $_muadm['i'];
		$re = array('abtn'=>array(),'slist'=>'',);
		$icfg = include(DIR_DTMP.'/update/'.$fnow); 
		$idata = include(DIR_DTMP.'/update/'.str_replace('.php','.dbsql',$fnow)); 
		if(!empty($icfg['mods'])){
		foreach ($icfg['mods'] as $mod => $mcfg) {
			$pid = $mcfg['pid'];
			$type = isset($_groups[$mod]) ? 'Update' : 'Install';
			$res = self::ins1Mod($mod,$idata,$type);
			$re['abtn'][$mod] = "$type - $res";
			$re['slist'] .= "<br>\n --- 模型: [$mod]{$mcfg['title']}, 执行结果如右...";
		}}
		if(!empty($icfg['menus'])){
    	$arm = comTypes::getSubs($_muadm,'0','1');
		foreach ($icfg['menus'] as $menu => $mcfg) {
			$pid = $mcfg['pid'];
			$type = isset($_muadm[$menu]) ? 'Update' : 'Install';
			$res = self::ins1Menu($menu,$idata,$type,$pid);
			$re['abtn'][$menu] = "$type - $res";
			@$re['slist'] .= "<br>\n --- 菜单: [$menu]{$mcfg['title']}, 执行结果如右...";
		}}
		return $re;
	} 

	static function insList($fnow='',$mop=0){
		$_groups = glbConfig::read('groups'); 
		$_muadm = glbConfig::read('muadm'); $_muadm = $_muadm['i'];
		$re = array('abtn'=>array(),'slist'=>'',);
		$icfg = include(DIR_DTMP.'/update/'.$fnow); 
		if(!empty($icfg['mods'])){
		foreach ($icfg['mods'] as $mod => $mcfg) {
			$pid = $mcfg['pid'];
			$re['abtn'][$mod] = isset($_groups[$mod]) ? 'Update' : 'Install';
			$re['slist'] .= "<br>\n --- 模型: [$mod]{$mcfg['title']}, 父级: [$pid]{$_groups[$pid]['title']}";
		}}
		if(!empty($icfg['menus'])){
    	$arm = comTypes::getSubs($_muadm,'0','1');
		foreach ($icfg['menus'] as $menu => $mcfg) {
			$pid = $mcfg['pid'];
			$opbar = $mop ? "<select name='$menu'>".basElm::setOption($arm,$pid)."</select>" : '';
			$re['abtn'][$menu] = isset($_muadm[$menu]) ? 'Update' : 'Install';
			@$re['slist'] .= "<br>\n --- 菜单: [$menu]{$mcfg['title']}, 父级: [$pid]{$_muadm[$pid]['title']} @$opbar ";
		}}
		return $re;
	}

	// 导出安装模组 模型/菜单
	static function expGroup($mods,$menus='',$xxx=''){
		$tmp = "$mods$menus";
		if(empty($tmp)) return '';
		$data = $mids = $ares = array(); 
		$_groups = glbConfig::read('groups'); 
		$_muadm = glbConfig::read('muadm'); $_muadm = $_muadm['i'];
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
		$fp = "/dbexp/ins-$mods-$menus";
		$ares = "<?php\nreturn ".var_export($ares,1).";\n?>"; 
		$dres = "<?php\nreturn ".var_export($data,1).";\n?>"; 
		comFiles::put(DIR_DTMP."$fp.php",$ares);
		comFiles::put(DIR_DTMP."$fp.dbsql",$dres);
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
		if($step==5){ comFiles::put(DIR_DTMP.self::$fsetuped,date('Y-m-d H:i:s')); }
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
		$db = glbDBObj::dbObj(); 
		$rcfg['sys_name']   = basReq::val('name');
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
			$db->table('base_paras')->data(array('val'=>$v))->where("kid='$k'")->update();
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
		devData::rstIDPW(basReq::val('uid'),basReq::val('upw'));
		self::updCache();
		$re = array('res'=>'OK','msg'=>'');
		self::ajaxStop($re);
	}
	
	// ajaxStop 
	static function ajaxStop($re){ 
		$act = basReq::val('act');
		$step = basReq::val('step'); 
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
		$db = glbDBObj::dbObj(); 
		$g0 = $db->table('base_model')->where("enable='1'")->order('kid')->select();
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
