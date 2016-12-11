<?php

// Store存储类
class comStore{	

	/**
	 * 上传到的临时目录，后续再移动到正式目录
	 * @return string
	 */
	static function getTmpDir($isfull=1){
		$user = user();
		$sid = empty($user->sinit['sid']) ? usrPerm::getUniqueid('Cook','sip') : $user->sinit['sid'];
		$path = "@udoc/$sid"; //$modFix-
		comFiles::chkDirs($path,'dtmp',0);
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
	static function getResDir($mod,$kid,$isfull=1,$chkdir=0){
		$user = user();
		if(empty($kid)){
			die(__FUNCTION__);
		}
		$kpath = $kid; 
		$fmts = read('frame.resfmt','sy'); 
		$fmt = 1; //yyyy/md-noid默认
		foreach($fmts as $k=>$v){
			if(in_array($mod,$v)){ $fmt = $k; }
		}
		if(strpos($kid,'-')){
			$ka = explode('-',$kid);
			if($fmt==1) $kpath = $ka[0].'/'.$ka[1].'-'.$ka[2];
			if($fmt==2) $kpath = $ka[0].'-'.$ka[1].'/'.$ka[2];
			if($fmt==3) $kpath = $ka[0].'/'.$ka[1].'/'.$ka[2];	
		}else{ // 加了一层目录,有些人不喜欢...
			$groups = read('groups');
			if(isset($groups[$mod]) && in_array($groups[$mod]['pid'],array('docs','users'))){
				$kpath = (empty($fmt) ? '' : "home/")."$kid";
			}elseif(isset($groups[$mod]) && in_array($groups[$mod]['pid'],array('types'))){
				$kpath = '';
			}else{ // coms 
				$kpath = $kid;
			}
		}
		$repath = empty($kpath) ? $mod : "$mod/$kpath";
		$chkdir && comFiles::chkDirs($repath,'ures',0);
		return ($isfull ? DIR_URES.'/' : '').$repath;
	}
	
	//移动临时文件夹中的文件
	static function moveTmpDir($str,$mod,$kid,$ishtml=0){
		$ar2 = self::moveTmpFmt($str,$ishtml);
		if(empty($ar2)) return $str;
		foreach($ar2 as $v){
			if(self::moveTmpOne($str,$v,$mod,$kid)) continue;
			$cfg = array(
				array('dtmp','/@udoc/'), 
				array('ures',"/$mod/"), 
				array('html',"/$mod/"),
				array('static',"/"), 
				array('vendui',"/"), 
				array('vendor',"/"), 
				array('root',"/"),
			);
			foreach($cfg as $cv){
				$str = self::moveRepRoot($str,$v,$cv[0],$cv[1]);
			}
		} //die();
		return $str;
	}
	// deel:@udoc
	static function moveTmpOne(&$str,$v,$mod,$kid){
		global $_cbase;
		$fix = PATH_DTMP."/@udoc/";
		$flag = 0;
		if($org=strstr($v,$fix)){
			$orgfile = DIR_DTMP.substr($org,strlen(PATH_DTMP));
			$obj = self::getResDir($mod,$kid,0,1)."/".basename($org);
			if(in_array($org,$_cbase['run']['tmpFile'])){
				$str = str_replace($v,'{uresroot}/'.$obj,$str);
				$flag = 1; 
			}elseif(is_file($orgfile)){ 
				if($re=rename($orgfile,DIR_URES.'/'.$obj)){
					$str = str_replace($v,'{uresroot}/'.$obj,$str);
					$_cbase['run']['tmpFile'][] = $org;
					$flag = 1; 
				}
			}
		}
		return $flag;
	}
	// str2arr
	static function moveTmpFmt($str,$ishtml=0){
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
		return $ar2;
	}
	
	//替换root路径
	static function moveRepRoot($str,$v,$key,$fix=''){
		$rsite = cfg('run.rsite');
		$cfg = self::cfgDirPath($key,'arr');
		$res = $v;
		if(strpos($res,'://')>0){ //完整路径
			if(strpos($res,$rsite)===0){ //本地
				$res = str_replace($rsite,"",$res); 
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
			'root'=>array(DIR_ROOT,	PATH_ROOT),
			'code'=>array(DIR_CODE,	PATH_CODE),
			'skin'=>array(DIR_SKIN,	PATH_SKIN),
			'ctpl'=>array(DIR_CTPL,''),
			'dtmp'=>array(DIR_DTMP,	PATH_DTMP),
			'ures'=>array(DIR_URES,	PATH_URES),
			'html'=>array(DIR_HTML,	PATH_HTML),
			'vendor'=>array(DIR_VENDOR,  PATH_VENDOR),
			'vendui'=>array(DIR_VENDUI,  PATH_VENDUI),
			'static'=>array(DIR_STATIC,  PATH_STATIC),
			'tpl'=>array(vopTpls::path('tpl'),''), //可能没有定义
			'tpc'=>array(vopTpls::path('tpc'),''),
			#'uftp'=>array(),
		);
		$re = isset($cfg[$key]) ? $cfg[$key] : $cfg;
		if($part=='arr') return $re;
		$id = $part=='dir' ? 0 : 1;
		return empty($re[$id]) ? $key : $re[$id];
	}
	
	//还原保存的文件夹
	static function revSaveDir($str,$part=''){
		$paths = self::cfgDirPath(0,'arr');
		foreach($paths as $ck=>$itm){
			if(in_array($ck,array('tpl','tpc','ctpl','code'))) continue;
			$path = $part=='dir' ? $itm[0] : $itm[1];
			$str = str_replace(array('{'.$ck.'root}','{$'.$ck.'root}'),$path,$str); 
		}
		return $str;
	}
	
}
