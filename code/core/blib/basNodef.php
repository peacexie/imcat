<?php

// basNodef
class basNodef{	
	
	static $_CACHES_LG = array();//将读取过的缓存暂存可重用
	
	// php5.5弃用了mysql扩展后,此函数也不可用了
	static function mysql_real_escape_string($str,$no=1){
		if (function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($str);
		}
		$a[0] = array("\x00","\n","\r","\\","'",'"',"\x1a",);
		$a[1] = array("","\\n","\\r","\\\\","\\'",'\\"',"",);
		return str_replace($a[0],$a[$no],$str);
	}

	// 低版本用mime_content_type, 较高版本用finfo扩展, 度不能用则用table对照...
	static function mime_content_type($filename) {
		if (function_exists('mime_content_type')) {
			return mime_content_type($filename);
		}
		if(function_exists('finfo_open')){
			$finfo = finfo_open(FILEINFO_MIME);
			$mtype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mtype;			
		}
		require_once DIR_STATIC.'/ilibs/mime_types.imp_php';
		static $contentType;
		$contentType = empty($contentType) ? $swift_mime_types : $contentType;
		$type = strtolower(substr(strrchr($filename, '.'),1));
		if(isset($contentType[$type])) {
			$mime = $contentType[$type];
		}else {
			$mime = 'application/octet-stream';
		}
		return $mime;
	}

}

