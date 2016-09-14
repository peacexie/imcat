<?php

// DBExt
class glbDBExt{	
	
	// 字段 - 添加/删除/修改 
	static function setOneField($mod,$cid,$act='del',$cfg=array()){
		//global $_cbase;
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$tabf = 'base_fields';
		$r = $db->table($tabf)->where("model='$mod' AND kid='$cid'")->find();
		if($r['dbtype']=='nodb') return; 
		$tabid = self::getTable($mod,$r['etab']);
		$cols = $db->fields($tabid); //echo "$mod,$cid,$act";
		if($act=='del'){
			if(isset($cols[$cid])) $db->query("ALTER TABLE $db->pre{$tabid}$db->ext DROP `$cid` ");
			$db->table($tabf)->where("model='$mod' AND kid='$cid'")->delete(); 	
		}else{
			$sql = "ALTER TABLE $db->pre{$tabid}$db->ext";
			if(isset($cols[$cid])) $sql.= " CHANGE `$cid` ";
			else				   $sql.= " ADD ";	 
			if(empty($r) && !empty($cfg)) $r = $cfg;
			$sql.= " `$cid` $r[dbtype]".($r['dbtype']=='varchar' ? "($r[dblen])" : ''); 
			$sql.= (strpos("($r[vreg]",'nul:') ? " NULL " : ' NOT NULL '); 
			$sql.= (empty($r['dbdef']) ? "" : " DEFAULT '$r[dbdef]' "); 
			$after = self::findAfterField($cols,$cid);
			if(!isset($cols[$cid])) $after && $sql.= " AFTER `$after` ";
			$db->query($sql);
		}
	}
	
	// tab下-添加字段，在什么字段后面
	static function findAfterField($tab,$col){
		$_groups = glbConfig::read('groups');
		$a = is_array($tab) ? $tab : $db->fields($tab);
		if(isset($_groups[$col]) && $_groups[$col]['pid']=='types' && isset($a['catid'])){
			$def = 'catid';
		}else{
			$def = ''; $bak = ''; //print_r($a);
			foreach($a as $k=>$v){
				if($k=='aip'){ 
					$def = empty($bak) ? 'aip' : $bak;
					break;
				}
				if(substr("$k)))",0,3)==substr("$col)))",0,3)){
					$def = $k;
				}
				$bak = $k;
			}
		}
		return $def;
	}
	
	static function setfieldDemo($mod,$obj,$org='(drop)'){
		global $_cbase;
		$db = glbDBObj::dbObj();
		if($org=='(drop)'){
			$db->query("DROP TABLE IF EXISTS $db->pre{$obj}$db->ext ");
			$db->table('base_fields')->where("model='$mod'")->delete(); 
		}else{ 
			$_ta = explode('_',$org);
			$obj && $db->query("CREATE TABLE IF NOT EXISTS $db->pre{$obj}$db->ext LIKE $db->pre{$org}$db->ext");
			if(in_array($_ta[0],array('coms','docs','users'))){ //增加默认字段配置'dext',
				$pid = $_ta[1]; 
				$_cfg = glbConfig::read($pid);
				$farr = @$_cfg['f']; 
				$top = 120; 
				if($farr){ foreach($farr as $k=>$v){
					$tabid = 'base_fields';
					if(!$db->table($tabid)->where("kid='$k' AND model='$mod'")->find()){
						$fm = array('kid'=>$k,'model'=>$mod,'top'=>$top,)+$v; 
						$db->table($tabid)->data($fm)->insert();
					}
					$top += 4; 
				} }
			}
		}
	}
	
	/* *****************************************************************************
	  *** 数据库相关函数 
	- db前缀
	- by Peace(XieYS) 2012-07-23
	落伍 - 今日: 20,375 |昨日: 60,851 |帖子: 44,262,754 |会员: 892,782
	4(0001~9999)   + (A001~YYYY)   + (00000~YYYYY)   =  10K + 700K + 32-1)M
	5(00001~99999) + (A0001~YYYYY) + (000000~YYYYYY) = 100K +  22M + 1000)M
	***************************************************************************** */
	// db, tab, n; $tmp(4,6,3.4,5.6)
	// y,m,d: 2013-md01 / 2013m-dh001 / 2013-md-h001
	static function dbAutID($tab='utest_keyid',$fix='yyyy-md-',$tmp='6',$key='',$n=0){
		$db = glbDBObj::dbObj();
		$kno = 0;
		$tfix = substr($tab,0,5); 
		if(in_array($tfix,array('docs_','coms_','advs_','users'))){
			$tkey = substr($tfix,0,1).'id';
			$tno = substr($tfix,0,1).'no';
		}else{
			$tkey = 'kid'; 
			$tno = 'kno';
		}
		$cid = $tno; 
		$tmpbak = $tmp;
		if(strpos('(,6,7,3.4,4.5,5.6,)',",$tmp,")){ 
			$kid = basKeyid::kidTemp($tmp);
		}elseif($key){ //echo "c.$key:";
			$len = $n>10 ? strlen($n)-1+2 : 2;
			$kno = substr($key,8,3).basKeyid::kidRand('',$len);  
			$kid = substr($key,0,8).$kno; 
		}else{ //4,5,22,23,32,12,13,14,
			if(strpos('(,22,23,31,32,)',",$tmp,")){
				$ktmp = in_array($tmp,array('31','32')) ? basKeyid::kidTemp('hms') : basKeyid::kidTemp('hm');
				$tmp = substr($tmp,1,1);
			}elseif(strpos('(,13,14,)',",$tmp,")){
				$ktmp = basKeyid::kidTemp('h');
				$tmp = substr($tmp,1,1);
			}else{ // 2013-md
				$ktmp = basKeyid::kidTemp('0').'-';
			}
			$tabf = $db->pre.$tab.$db->ext;
			$mdb = $db->query("SELECT max($tno) as $tno FROM $tabf WHERE $tkey like '$ktmp%'"); 
			$min = str_repeat('0',$tmp-1).'1'; 
			if(empty($mdb[0][$tno])){ 
				$max = $min; 
			}else{
				$max = $mdb[0][$tno]; 
				$max = basKeyid::kidNext('',$max,$min,1,8);
			}
			$kid = $ktmp.$max; 
			$kno = $max; 
		}
		$rec = $db->table($tab)->where("$tkey LIKE '$kid%'")->find(); 
		if($rec) return self::dbAutID($tab,$fix,$tmpbak,$kid,$n+1);	
		else return array($kid,$kno);
	}

	static function dbNxtID($tab,$mod,$pid=0){
		$_groups = glbConfig::read('groups');
		$db = glbDBObj::dbObj();
		$fix = substr($mod,0,1);
		if($pid){
			$cfg = glbConfig::read($mod); 
			$fix .= ($cfg['i'][$pid]['deep']+1);
		}else{ $fix .= "1"; }
		$sqlm = $tab=='bext_relat' ? '' : ($tab=='base_model' ? 'pid' : 'model')."='$mod' AND ";
		$re = $db->table($tab)->where("$sqlm kid REGEXP ('^{$fix}[0-9]{3}$')")->order('kid DESC')->find();
		if($re){
			$nid = substr($re['kid'],2);
			$nid = basKeyid::kidNext('',$nid,'012',2,3);
		}else{
			$nid = '012';
		}
		return $fix.$nid;
	}
	
	static function getTable($mod,$ext=0){ 
		global $_cbase; 
		$_groups = glbConfig::read('groups');
		if(!isset($_groups[$mod])) return '';
		if($_groups[$mod]['pid']=='docs'){
			$tabid = $ext ? 'dext_'.$mod : 'docs_'.$mod;
		}elseif($_groups[$mod]['pid']=='users'){
			$tabid = 'users_'.$mod;
		}elseif($_groups[$mod]['pid']=='advs'){
			$tabid = 'advs_'.$mod;
		}elseif($_groups[$mod]['pid']=='coms'){	
			$tabid = 'coms_'.$mod;
		}elseif($_groups[$mod]['pid']=='types'){	
			$tabid = empty($_groups[$mod]['etab']) ? 'types_common' : 'types_'.$mod;
		}else{
			$tabid = '';	
		}
		return $tabid;
	}
	
	static function getKeyid($mod){ 
		global $_cbase;
		$_groups = glbConfig::read('groups');
		if($_groups[$mod]['pid']=='docs'){
			$keyid = 'did';
		}elseif($_groups[$mod]['pid']=='users'){
			$keyid = 'uid';
		}elseif($_groups[$mod]['pid']=='advs'){
			$keyid = 'aid';
		}elseif($_groups[$mod]['pid']=='coms'){	
			$keyid = 'cid';
		}elseif($_groups[$mod]['pid']=='types'){	
			$keyid = 'kid';
		}
		return $keyid;
	}
	
	static function getKids($mod,$kid='',$whr='',$ret='sub'){
		$db = glbDBObj::dbObj(); 
		$tabid = self::getTable($mod); 
		$kid = $kid ? basStr::filKey($kid,'_-.') : self::getKeyid($mod); 
		$list = $db->table($tabid)->field($kid)->where($whr)->select();
		$re = array();
		if($list){
		foreach($list as $r){
			$re[] = $r[$kid];
		} }
		if(empty($re)){
			$re[] = '(null)';	
		}
		if($ret=='sub'){
			$re = "'".implode("','",$re)."'";
		}
		return $re;
	}
	
	static function dbComment($tabid='~return~'){ 
		static $dbdict,$fmod,$fdemo;
		$db = glbDBObj::dbObj();
		$fsystem = basLang::ucfg('fsystem');
		if(empty($dbdict)){
			$dict = $db->table('bext_dbdict')->field("kid,tabid,title")->select();
			foreach($dict as $v){
				$dbdict[$v['tabid']][$v['kid']] = $v['title'];
			}
		}
		if(empty($fmod)){
			$dict = $db->table('base_fields')->field("kid,model,title")->select();
			foreach($dict as $v){
				$fmod[$v['model']][$v['kid']] = $v['title'];
			}
		}	
		if(empty($fdemo)){
			$fdemo = array();
			$demo = glbConfig::read('fdemo','sy');
			foreach(array('init_users','init_coms','init_dext','init_docs',) as $part){
				$fpart = $demo[$part];
				foreach($fpart as $f=>$v){
					$fdemo[$f] = $v['title'];
			} }
		}
		if($tabid=='~return~') return array('fsystem'=>$fsystem,'fdemo'=>$fdemo,);
		$fields = $db->fields($tabid);
		$moda = explode('_',$tabid); 
		$modid = $moda[1];
		foreach($fields as $f=>$v){
			$flag = 'def'; $rem = '';
			if(isset($fmod[$modid][$f])){ //模型设置
				$flag = 'mod';
				$rem = $fmod[$modid][$f];
			}elseif(empty($dbdict[$tabid][$f])){ //dbdict为空
				if(isset($fdemo[$f])){
					$flag = 'demo';
					$rem = $fdemo[$f];
				}
				if(isset($fsystem[$f])){
					$flag = 'sys';
					$rem = $fsystem[$f];
				}
			}else{ //dbdict设置
				$rem = $dbdict[$tabid][$f];	
				if(isset($fsystem[$f]) || in_array($moda[0],array('active','advs','base','bext','logs'))){
					$flag = 'sys';
				}
			}
			$fields[$f]['_flag'] = $flag;
			$fields[$f]['_rem'] = basStr::filSafe4($rem);
		}
		$_groups = glbConfig::read('groups');
		if(isset($_groups[$modid]) && $_groups[$modid]['pid']==$moda[0]){
			$fields[0]['_flag'] = 'sys'; 
			$cfg = basLang::ucfg('cfglibs.dbext');
			$fields[0]['_rem'] = $_groups[$modid]['title'].('['.$cfg[$moda[0]].']').lang('dbdict_tab');
		}
		if(isset($_groups[$modid]) && $moda[0]=='dext'){
			$fields[0]['_flag'] = 'sys';
			$fields[0]['_rem'] = $_groups[$modid]['title'].lang('dbdict_extab');
		}
		if(isset($fsystem['_stabs'][$tabid])){
			$fields[0]['_flag'] = 'sys';
			$fields[0]['_rem'] = $fsystem['_stabs'][$tabid];
		}
		if(empty($fields[0]['_rem'])&& !empty($dbdict[$tabid][0])){
			$fields[0]['_flag'] = '';
			$fields[0]['_rem'] = $dbdict[$tabid][0];
		}
		return $fields;
	}
	

	static function getExtp($type){ 
		$db = glbDBObj::dbObj();
		$data = array();
		$whr = strpos($type,'%') ? " LIKE '$type'" : "='$type'";
		$list = $db->table('bext_paras')->where("pid$whr AND enable=1")->order('top')->limit(99)->select();
		if($list){ foreach($list as $i=>$r){ 
			$r['i'] = $i+1;
			foreach(array('aip','atime','auser','eip','etime','euser','cfgs','note','enable') as $k2){
				unset($r[$k2]);
			}
			$data[$r['kid']] = $r;
		} } 
		return $data;
	}

}
