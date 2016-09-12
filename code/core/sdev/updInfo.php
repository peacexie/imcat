<?php

// ...类
class updInfo{	
	
	static $server_file = '/dset/_upd_server.htm';
	static $client_file = '/dset/_upd_client.htm';
	static $modstat_file = '/dset/_upd_modstat.php'; 
	static $space_file = '/dset/_upd_spaceinfo.php';
	
	static $updtcfg = array(
		'sync'  => '10d', //10d
		'stat'  => '3h', //3h
		'space' => '24h', //24h
	);
	
	//3.0.2015.1225
	static function verComp($new,$old){
		$f = version_compare($old,$new);
		return $f;
	}
	
	// ServerInfo
	static function getServerInfo(){
		global $_cbase;
		$nf = self::getLangFile(self::$server_file);
		$data = self::getCacheData($nf,'sync');
		if(empty($data)){
			// ● [资讯]2015-0501：微信接口基本完成 [2015-05-05] 
			$db = glbDBObj::dbObj();
			$list = $db->table('docs_news')->where("catid='nsystem'")->limit(3)->order('did DESC')->select();
			if($list){foreach($list as $r){
				$url = $_cbase['run']['rsite'].vopUrl::fout("chn:news.$r[did]");
				$a = "<a href='$url' target='_blank'>$r[title]</a>";
				$data .= "<br>● $a [".date('Y-m-d',$r['atime'])."]\n";
			}}
			comFiles::put(DIR_DTMP.$nf,$data);
		}
		return $data;
	}	
	
	// ClientInfo
	static function getClientInfo(){
		global $_cbase;
		$nf = self::getLangFile(self::$client_file);
		$data = self::getCacheData($nf,'sync','data');
		if(empty($data)){
			// ● 当前版本：3.0.2015.1225（官方版本：3.0.2015.1225）
			$surl = $_cbase['server']['txmao']."/root/plus/api/update.php";
			$nver = $_cbase['sys']['ver']; //echo ".$surl.";
			$sver = comHttp::doGet("$surl?act=version",8); 
			$sdata = comHttp::doGet("$surl?act=server",8);
			$linkb = "● ".lang('updinfo_nowver')."V{$nver}"; 
			$slang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
			$surl = $slang=='zh' ? 'dev' : 'doc';
			$linkr = "<a href='".$_cbase['server']['txmao']."/$surl.php?start' target='_blank' title='".lang('updinfo_viewdown')."'>V$sver</a>";
			if(strstr($sdata,'<br>● <a') && strlen($sver)>=3 && strlen($sver)<=18){ 
				$data = "{$linkb}".lang('updinfo_remver',$linkr)."\n$sdata";
			}else{
				$data = "{$linkb}".lang('updinfo_remerr')."\n";
			}
			comFiles::put(DIR_DTMP.$nf,$data);
		}
		return $data;
	}	

	// SysInfo sys
	static function getModStat(){
		global $_cbase;
		$nf = self::getLangFile(self::$modstat_file);
		$data = self::getCacheData($nf,'stat','array');
		if(empty($data)){
			$db = glbDBObj::dbObj();
			$mcfgs = self::getModConfigs(); // ● [订单] 当天:11，3天:44，总计:99
			$tcfgs = self::getTimeConfigs();
			$data = array();
			foreach($mcfgs as $pmod=>$mods){foreach($mods as $mod){foreach($tcfgs as $tk=>$tv){
				$key = "{$mod}_$tk";
				$whr = "atime>='$tv'";
				$data[$key] = $db->table("{$pmod}_$mod")->where($whr)->count();
			}}}
			$dstr = var_export($data,1);
			$dstr = "<?php\n\$data = $dstr\n?>";
			comFiles::put(DIR_DTMP.$nf,$dstr);
		}
		return $data;
	}

	// SpaceInfo
	static function getSpaceInfo(){
		global $_cbase;
		$nf = self::getLangFile(self::$space_file);
		$data = self::getCacheData($nf,'space','array');
		if(empty($data)){
			$db = glbDBObj::dbObj();
			$sum = 0;
			$data = array('db'=>array('data'=>0,'index'=>0,'free'=>0));
			$tabinfo = $db->tables(1); 
			foreach($tabinfo as $r){ 
				$data['db']['data'] += $r['Data_length'];
				$data['db']['index'] += $r['Index_length'];
				$data['db']['free'] += $r['Data_free'];
			}
			$sum = $data['db']['data'];
			$cfgs = glbConfig::read('pubcfg','sy');
			$data['dir']['main'] = 0;
			foreach($cfgs['dirs'] as $key=>$dir){
				$idir = comFiles::statDir($dir);
				$data['dir'][$key] = $idir['nsize'];
				$sum += $idir['nsize'];
			}
			$data['dir']['main'] = $data['dir']['root'] + $data['dir']['code'];
			unset($data['dir']['root'],$data['dir']['code']);
			foreach(array('db','dir') as $key){foreach($data[$key] as $k=>$v){
				//$space = 
				$data[$key][$k] = basStr::showNumber($v,'Byte');
			}}
			$data['total'] = $_cbase['ucfg']['space'];
			$data['sum'] = basStr::showNumber($sum,'Byte');
			$dstr = var_export($data,1);
			$dstr = "<?php\n\$data = $dstr\n?>";
			comFiles::put(DIR_DTMP.$nf,$dstr);	
		}
		return $data;
	}

	// showSpaceInfo
	static function showSpaceInfo(){
		$data = self::getSpaceInfo();
		$s1 = $s2 = ''; 
		$str = "\n<tr><td>".lang('updinfo_allspace')."</td><td colspan=6 class='tc'>{$data['total']}M ".lang('updinfo_uesspace',$data['sum'])."</td>";
		$str .= "<td><a href='?mkv=uhome&act=uspace'>".lang('updinfo_upd')."</a></td></tr>\n";
		foreach($data['dir'] as $key=>$val){
			$s1 .= "<td>$key</td>";
			$s2 .= "<td>$val</td>";
		}
		$str .= "<tr><td rowspan=2>".lang('updinfo_dir')."</td>$s1</tr>\n<tr>$s2</tr>\n";
		$str .= "<tr><td>".lang('updinfo_dbinfo')."</td><td></td>";
		foreach($data['db'] as $key=>$val){
			$str .= "<td colspan=2>$key=$val</td>";
		}
		$str .= "</tr>";
		echo $str;
	}

	// showSysInfo
	static function showModStat($key){
		$_groups = glbConfig::read('groups');
		$data = self::getModStat(); 
		$mcfgs = self::getModConfigs();
		$tcfgs = self::getTimeConfigs();
		foreach($mcfgs[$key] as $mod){ 
			$link = "<a href='?file=dops/a&mod=$mod'>{$_groups[$mod]['title']}</a>";
			$v = array();
			foreach($tcfgs as $tk=>$tv){
				$v[$tk] = empty($data[$mod."_$tk"]) ? 0 : $data[$mod."_$tk"];
			}
			echo "● [$link] ".lang('updinfo_st1day').":$v[d1], ".lang('updinfo_st3day').":$v[d3], ".lang('updinfo_st7day').":$v[d7], ".lang('updinfo_stall').":$v[all]\n<br>"; 
		}
	}

	// getTimeConfigs
	static function getTimeConfigs(){
		$hour0 = strtotime(date('Y-m-d')); 
		$tcfgs = array('d1'=>$hour0,'d3'=>$hour0-2*86400,'d7'=>$hour0-6*86400,'all'=>0);
		return $tcfgs;
	}

	// getModConfigs
	static function getModConfigs(){
		$mcfgs = glbConfig::read('modstat','sy');
		return $mcfgs;
	}
	// getCacheData
	static function getCacheData($file,$updkey,$type='data'){
		global $_cbase;
		$file = DIR_DTMP.$file;
		$updtime = self::$updtcfg[$updkey];
		$upath = tagCache::chkUpd($file,$updtime,0);
		if($upath && $type=='data'){
			$data = comFiles::get($file);	
		}elseif($upath){
			include($file);
		}else{
			$data = array();	
		}
		$res = empty($data) ? ($type=='data' ? '' : array()) : $data;
		return $res;
	}
	// getLangFile
	static function getLangFile($file){
		global $_cbase;
		$lang = $_cbase['sys']['lang'];
		$file = str_replace(array(".htm",".php"),array("-$lang.htm","-$lang.php"),$file);
		return $file;
	}
	

}

