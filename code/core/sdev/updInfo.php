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
		$data = self::getCacheData(self::$server_file,'sync');
		if(empty($data)){
			// ● [资讯]2015-0501：微信接口基本完成 [2015-05-05] 
			$db = glbDBObj::dbObj();
			$list = $db->table('docs_news')->where("catid='nsystem'")->limit(3)->order('did DESC')->select();
			if($list){foreach($list as $r){
				$url = $_cbase['run']['rsite'].vopUrl::fout("chn:news.$r[did]");
				$a = "<a href='$url' target='_blank'>$r[title]</a>";
				$data .= "<br>● $a [".date('Y-m-d',$r['atime'])."]\n";
			}}
			comFiles::put(DIR_DTMP.self::$server_file,$data);
		}
		return $data;
	}	
	
	// ClientInfo
	static function getClientInfo(){
		global $_cbase;
		$data = self::getCacheData(self::$client_file,'sync','data');
		if(empty($data)){
			// ● 当前版本：3.0.2015.1225（官方版本：3.0.2015.1225）
			$surl = $_cbase['server']['txmao']."/root/plus/api/update.php";
			$nver = $_cbase['sys']['ver']; //echo ".$surl.";
			$sver = comHttp::doGet("$surl?act=version",8); 
			$sdata = comHttp::doGet("$surl?act=server",8);
			$linkb = "● 当前版本：V{$nver}"; 
			$linkr = "<a href='".$_cbase['server']['txmao']."/dev.php?start' target='_blank' title='查看官方下载'>V$sver</a>";
			if(strstr($sdata,'<br>● <a') && strlen($sver)>=3 && strlen($sver)<=18){ 
				$data = "{$linkb}（官方版本：{$linkr}）\n$sdata";
			}else{
				$data = "{$linkb}（官方版本：[获取数据错误]）\n";
			}
			comFiles::put(DIR_DTMP.self::$client_file,$data);
		}
		return $data;
	}	
	
	// SysInfo sys
	static function getModStat(){
		//global $_cbase;
		$data = self::getCacheData(self::$modstat_file,'stat','array');
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
			comFiles::put(DIR_DTMP.self::$modstat_file,$dstr);
		}
		return $data;
	}

	// SpaceInfo
	static function getSpaceInfo(){
		global $_cbase;
		$data = self::getCacheData(self::$space_file,'space','array');
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
			comFiles::put(DIR_DTMP.self::$space_file,$dstr);	
		}
		return $data;
	}

	// showSpaceInfo
	static function showSpaceInfo(){
		$data = self::getSpaceInfo();
		$s1 = $s2 = ''; 
		$str = "\n<tr><td>总空间</td><td colspan=6 class='tc'>{$data['total']}M [使用:{$data['sum']}含文件和数据]</td>";
		$str .= "<td><a href='?mkv=uhome&act=uspace'>更新</a></td></tr>\n";
		foreach($data['dir'] as $key=>$val){
			$s1 .= "<td>$key</td>";
			$s2 .= "<td>$val</td>";
		}
		$str .= "<tr><td rowspan=2>目录</td>$s1</tr>\n<tr>$s2</tr>\n";
		$str .= "<tr><td>数据库</td><td></td>";
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
			echo "● [$link] 当天:$v[d1]，3天:$v[d3]，7天:$v[d7]，总计:$v[all]\n<br>"; 
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

}

