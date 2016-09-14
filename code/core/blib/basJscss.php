<?php

// basJscss类
class basJscss{	

	// imp css/js
	static function imp($path,$base='',$mod='auto'){
		global $_cbase; 
		if(!strpos($_cbase['run']['jsimp'],$path)){
			$_cbase['run']['jsimp'] .= "$path,";
		}else{
			return;	
		}
		if(strpos($path,'://')||strpos($path,'../')){
			$base = '';
		}elseif(in_array(substr($path,0,5),array('/plus','/skin'))){
			$base = PATH_ROOT;
			if(strpos($path,'/comjs.php')) $mod = 'js';
		}elseif($pcfg = comFiles::cfgDirPath($base,'path')){
			$base = $pcfg;	
		}else{
			$base = $base=='null' ? '' : (empty($base) ? PATH_ROOT : $base);	
		}
		$path = "$base$path";
		if(empty($mod) || $mod=='auto') $mod = strpos($path,'.css') ? 'css' : 'js'; 
		if(!empty($_cbase['tpl']['tpc_on'])){
			$_r = '_r='.time();
			$path .= strpos($path,'?') ? "&$_r" : "?$_r";
		}
		if($mod=='js') return self::jscode('',$path)."\n";
		else return "<link rel='stylesheet' type='text/css' href='$path'/>\n";
	}
	
	/*《"&<>》HTML
	《:/?=&#%》URL 
	《\/*?"<>|》FILE
	《'$[]{}》SQL,PHP,下标,变量
	《!()+,-.;@^_`~》安全13个 
	《*+-./@_》7个js-escape安全escape*/
	/* *****************************************************************************
	  *** js字符处理
	- js前缀
	- by Peace(XieYS) 2012-02-24
	***************************************************************************** */
	
	// keyid, subject('"<>\n\r), js
	static function Alert($xMsg,$xAct='prClose',$xAddr='',$head=0){
	  global $_cbase;
	  if($head && empty($_cbase['run']['headed'])) glbHtml::page();
	  if(empty($xAddr)) $xAddr = @$_SERVER["HTTP_REFERER"];
	  $s = "\n<script language='javascript'>\n";
	  $s .= "alert('$xMsg');\n";
	  switch ($xAct) { 
	  case "Back" : 
		$s .= "history.go($xAddr);\n";
		break; 
	  case "Close" : 
		$s .= "window.close();\n";
		break; 
	  case "prClose" : 
		if(@$_cbase['sys_open']==='4'){ 
			$s .= "parent.location.reload();\n";
		}else if(@$_cbase['sys_open']==='1'){ 
			$s .= "window.opener.location.reload();\n";
			$s .= "window.close();\n";
		}else{ 
			$s .= "window.close();\n";
		}
		break; 
	  case "Open" : 
		$s .= "window.open('$xAddr');\n";
		break; 
	  case "Redir" : 
		$s .= "location.href='$xAddr';\n";
		break;
	  default: 
		break; 
	  }
	  $s .= "</script>\n";
	  return $s;
	}
	// $enchf=1,编码html标记：尖括号，
	static function jsShow($xStr, $enchf=1){
	   $Tmp = $xStr;
	   $enchf && $Tmp = str_replace(array('<','>'),array('&lt;','&gt;'),$Tmp);
	   $Tmp = str_replace(array("\\","'",'"'),array("\\\\","\\'",'\\"'),$Tmp); 
	   $Tmp = str_replace(array("\r\n","\r","\n"),array("\\r\\n","\\r","\\n"),$Tmp);
	   return $Tmp;
	}
	static function jsKey($xStr) {
	   $xStr = str_replace(array('[',']',' '),array('_','_',''),$xStr);
	   $xStr = str_replace(array('/','-','.'),array('_','_','_'),$xStr);
	   return $xStr;	
	}
	// document.write
	static function write($xStr){ // ,array(),array()
		return "document.write('".self::jsShow($xStr, 0)."');";
	}
	static function jscode($code,$url=''){ 
		if($url){
			return "<script src='$url'></script>"; 
		}else{
			return "<script>\n$code</script>";
		}
	}
}