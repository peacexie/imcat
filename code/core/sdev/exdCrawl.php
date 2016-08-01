<?php
(!defined('RUN_MODE')) && die('No Init');

// ...类exdCrawl
class exdCrawl{	
	
	// ugetPages
	static function ugetPages($jcfg=array()){
		$oplog = $jcfg['oplog'];
		$opnos = explode('-',$jcfg['opno']);
		$opmin = intval($opnos[0]); if($opmin<1) $opmin=1;
		$opmax = intval($opnos[1]); if($opmax<1) $opmax=1;
		if($opmax<$opmin) $opmax = $opmin;
		$opnow = ''; $opnext = '';
		for($i=$opmax;$i>=$opmin;$i--){
			if(strlen($opnow)==0 && !strstr(",$oplog,",",$i,")){ 
				$opnow = $i;
				continue;
			}
			if(strlen($opnow)>0 && !strstr(",$oplog,",",$i,")){ 
				$opnext = $i;
				break;
			}
		}
		return array($opnow,$opnext);
	}
	
	// ugetLinks
	static function ugetLinks($jcfg=array(),$pno=1){
		$ourl = str_replace('(*)',$pno,$jcfg['ourl']);
		$data = comHttp::doGet($ourl,5); 
		$data = comConvert::autoCSet($data,$jcfg['ocset'],'utf-8'); // echo $data;
		$data = self::orgAll($data,$jcfg);
		// modeAttr(:)href(^)url 规则可要可不要
		if(is_string($data)){ $data = basElm::getAttr($data,'href','url',-1); }
		if(!empty($jcfg['ohas']) && !empty($data)){
			foreach($data as $k=>$url){
				if(!strstr($url,$jcfg['ohas'])) unset($data[$k]);
			}
		}
		//basArray::inStr(array('org','com','net'),'xys@163.com');
		$jcfg['oskip'] = basElm::line2arr($jcfg['oskip']);
		if(!empty($jcfg['oskip']) && !empty($data)){
			foreach($data as $k=>$url){
				if(basArray::inStr($jcfg['oskip'],$url)) unset($data[$k]);
			}
		}
		if(!empty($jcfg['orep']) && !empty($data)){
			$jcfg['orep'] = basElm::text2arr($jcfg['orep']);
			foreach($data as $k=>$url){foreach($jcfg['orep'] as $v1=>$v2){
				$data[$k] = str_replace($v1,$v2,$data[$k]);
			}}
		}
		if(!empty($jcfg['oroot']) && !empty($data)){
			foreach($data as $k=>$url){
				$data[$k] = $jcfg['oroot'].$data[$k];
			}
		} //print_r($data);
		return $data;
	}
	
	// ugetDRow
	static function ugetRow($jcfg,$cfields=array(),$udata=''){
		$farr = array(); 
		if(is_array($udata)){ //oimp
			foreach($cfields as $k=>$v){ //echo "\n$k::::"; print_r($v);
				if(empty($v['orgtg1']) || !isset($udata[$v['orgtg1']])) continue;
				$farr[$k] = self::orgAll($udata[$v['orgtg1']],$v,0);	
			} 
		}else{
			$data = comHttp::doGet($udata,5); 
			$data = comConvert::autoCSet($data,$jcfg['ocset'],'utf-8'); // echo $data;
			foreach($cfields as $k=>$v){ 
				$farr[$k] = self::orgAll($data,$v);	
			} 
		} 
		return $farr;
	}
	
	static function orgAll($data,$cfg=array(),$recomp=1){ 
		//global $_cbase;
		$dold = $data;
		foreach($cfg as $key=>$val){ 
			if(substr($key,0,5)=='orgtg'){
				if(strlen($val)<18) continue; //echo "\n\n($key=$val)\n";
				$data = self::orgPos($data,$val);
			} 
			if(substr($key,0,4)=='deal'){ //echo "\n\n($key=$val)\n";
				if(in_array($key,array('dealfunp'))) continue;
				$extp = $key=='dealfunc' ? $cfg['dealfunp'] : '';
				if(!empty($val)){
					$method = "deal".ucfirst(substr($key,4)); 
					$data = self::$method($data,$val,$extp);
				}
			}
			if($key=='dealval'){ //可以是0
				$data = self::dealDefv($data,$val,$cfg['defover']);
			} //echo "<br>$key=$val=$data";
		}
		return $recomp ? ($data==$dold ? '' : $data) : $dold;
	}
	
	static function orgPos($data,$pcfg){ // (:)  (^)  (*)
		if(empty($pcfg)) return $data; //echo "\n\n:="; print_r($pcfg);
		$pcfg = explode('(:)',"$pcfg(:)(:)");
		$pmode = $pcfg[0]; $paras = @explode('(^)',$pcfg[1]."(^)(^)(^)"); $pext = $pcfg[2]; 
		if($pmode=='modeVal'){ // ($data,'<div class="content">(*)</div>') 或 ($data,'class="content"(*)</div>','>')
			if(!empty($paras[0]) && strpos($paras[0],'(*)')){ 
				$data = basElm::getVal($data,$paras[0],$paras[1]);
			}
		}elseif($pmode=='modePos'){ // ($data,'<div class="content">(*)id="link"')
			if(!empty($paras[0]) && strpos($paras[0],'(*)')){
				$data = basElm::getPos($data,$paras[0]);
			} 
		}elseif($pmode=='modeArr'){ // ($data,'<li class="cls22">(*)</li>') 或 ($data,'<li class(*)</li>')
			if(!empty($paras[0]) && strpos($paras[0],'(*)')){
				$paras[1] = is_numeric($paras[1]) ? $paras[1] : -1; 
				$data = basElm::getArr($data,$paras[0],$paras[1]);
			}
		}elseif($pmode=='modePreg'){ // getArr($data,"<td class="tc1">(*)</td>","[^rn]{1,1200}")
			if(!empty($paras[0]) && strpos($paras[0],'(*)') && !empty($paras[1])){
				$paras[1] = str_replace(array("rn"),array("\n|\r"),$paras[1]);
				$paras[2] = is_numeric($paras[2]) ? $paras[2] : -1; 
				$data = basElm::getPreg($data,$paras[0],$paras[1],$paras[2]);
			}			
		}elseif($pmode=='modeAttr'){ // getAttr($data,'witdh','val',1);
			if(!empty($paras[0]) && !empty($paras[1])){
				$paras[2] = isset($paras[2]) ? $paras[2] : -1; 
				$data = basElm::getAttr($data,$paras[0],$paras[1],$paras[2]);
			}
		}else{
			//$data = '';
		}
		if($pext=='url:fatch'){
			self::orgUrl($data);
		}elseif($pext=='save:image'){
			self::orgImg($data);	
		}
		return $data;
	}
	
	static function orgUrl($url){
		if(strpos($url,'>') || strpos($url,'<')){
			$url = basElm::getAttr($url,'href','url',0);	
		}
		$data = comHttp::doGet($url,3);
		return $data;
	}
	
	static function orgImg($url){ 
		$config = array( //上传配置
			"maxSize" => 500, //单位KB
			"allowFiles" => array(".gif", ".png",'.jpg')
		);
		$up = new comUpload($url, $config, 'remote');
		$info = $up->getFileInfo(); 
		if($info['state']=='SUCCESS'){
			$mod = basReq::val('mod'); $mod || $mod='crawl';
			$ure = comFiles::moveTmpDir($info['url'],$mod,basKeyid::kidTemp('hms'),0);
		}else{
			$ure = '';	
		} //print_r($ure);
		return $ure;
	}
	
	// dealfmts	varchar(255) []	 note,html,blank,strtotime,
	static function dealFmts($data,$fmts){
		global $_cbase;
		if(strstr($fmts,'note')) $data = basStr::filNotes($data);
		if(strstr($fmts,'html')) $data = basStr::filHText($data);
		if(strstr($fmts,'blank')){ 
			$data = preg_replace("/\s(?=\s)/","\\1",$data); 
		}
		$data = trim($data);
		$_cbase['crawl']['strtotime'] = $data; 
		if(strstr($fmts,'strtotime')) $data = strtotime($data);
		//$str = preg_replace("/<sty.*?\\/style>|<scr.*?\\/script>|<!--.*?-->/is", '', $str);
		return $data;
	}
	// dealtabs	varchar(255)	 替换来源内容=空
	static function dealTabs($data,$fmts){ 
		$a = basElm::line2arr($fmts); $re = array();
		foreach($a as $val){
			if(strlen($val)==0) continue;
			$re[] = $val;
		} 
		if(!empty($re)){ 
			$data = str_replace($re,'',$data); 
		}
		return $data;
	}
	// dealconv	varchar(24) []	 a=b
	static function dealConv($data,$fmts){
		$fmts = basElm::text2arr($fmts);
		$rek = $rev = array();
		foreach($fmts as $key=>$val){
			$rek[] = $key;
			$rev[] = $val=='(null)' ? '' : $val;
		} //print_r($rek);
		if(!empty($rek)) $data = str_replace($rek,$rev,$data);
		return $data;
	}
	// dealfunc	varchar(48) []	 结果处理函数 
	static function dealFunc($data,$func,$funp=''){
		global $_cbase;
		$_cbase['crawl']['dealfunc'] = $data; 
		if(method_exists('exaCrawl',$func)){ 
			$paras = explode(',',$funp.",,");
			return exaCrawl::$func($data,$paras[0],$paras[1],$paras[2]); 
		}elseif(function_exists($func)){
			$paras = explode(',',$funp.",,");
			return $func($data,$paras[0],$paras[1],$paras[2]);	
		}
	}
	// defval,defover
	static function dealDefv($data,$defval='',$over=''){
		$val = $defval=='(null)' ? '' : $defval;
		$data = strlen($data)==0 ? $val : $data;
		return $data;
	}

}

/*
 
*/
