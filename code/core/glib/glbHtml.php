<?php
// Html类
class glbHtml{	

	// 页面结构
	static function page($mod='',$ext=''){
		global $_cbase; 
		$sdir = vopTpls::def(); //可能没有定义
		if($mod=='body'){
			echo "</head><body$ext>\n";
		}elseif($mod=='end'){
			if(empty($_cbase['run']['headed'])){
				self::page('');
			}
			if(strlen($ext)>12) echo "$ext\n";
			echo "</body></html>\n";
		}elseif(in_array($mod,array('robots','viewport','keywords','description'))){
			if($mod=='robots' && empty($ext)) $ext = 'noindex, nofollow';
			if($mod=='viewport' && empty($ext)) $ext = 'width=device-width, initial-scale=1';
			echo "<meta name='$mod' content='$ext'>\n"; 
		}elseif($mod=='h1'){
			echo "<h1>$ext</h1>\n";
		}elseif($mod=='imp'){
			self::impub();
			echo basJscss::imp("/skin/$sdir/b_jscss/comm.css");
		}elseif($mod=='imadm'){  
			self::impub();
			echo basJscss::imp('/plus/ajax/comjs.php?act=jsPlus');
			echo basJscss::imp('/skin/a_jscss/jstyle.css'); 
			echo basJscss::imp("/skin/$sdir/b_jscss/comm.css");
			echo basJscss::imp("/skin/$sdir/b_jscss/comm.js");			
		}elseif($mod=='imvop'){
			self::impub("$ext",1);
			echo basJscss::imp("/skin/$sdir/b_jscss/comm.css");
			echo basJscss::imp("/skin/$sdir/b_jscss/comm.js");
		}elseif($mod=='imin'){
			echo basJscss::imp('/plus/ajax/comjs.php?');
			echo basJscss::imp("/skin/a_jscss/stpub.css");
		}else{ //head
			$_cbase['run']['headed'] = 1;
			$mod || $mod = $_cbase['sys_name'];
			$mod = str_replace('(sys_name)',$_cbase['sys_name'],$mod);
			echo "<!DOCTYPE html><html><head>\n";
			echo "<meta charset='".glbConfig::get('cbase', 'sys.cset')."'>\n";
			echo "<title>$mod</title>\n";
			if($ext) echo "<meta name='robots' content='noindex, nofollow'>\n";
			if(defined('RUN_MOB')){ 
				echo "<meta name='viewport' content='width=device-width, initial-scale=1'>\n";
			}
		}
	}

	// _impub
	static function impub($jsrun='',$nolayer='',$nohick=''){
		echo basJscss::imp("/plus/ajax/comjs.php?$jsrun"); 
		echo basJscss::imp('/plus/ajax/comjs.php?act=autoJQ'); 
		echo basJscss::imp("/skin/a_jscss/stpub.css");
		echo basJscss::imp("/common/stuipub.css",'vui');
		if(!$nolayer) echo basJscss::imp('/layer/layer.js','vui');
		echo "<link rel='shortcut icon' href='".PATH_ROOT."/skin/a_img/favicon.ico' />\n";
		//echo "<!--[if lt IE 9]><script src='".PATH_VENDUI."/jquery/html5shiv.min.js'></script]\n";
		//echo "<script src='".PATH_VENDUI."/jquery/respond.min.js'></script]<![endif]-->";
	}
	
	// header
	static function head($type='js'){
		$cset = glbConfig::get('cbase', 'sys.cset');
		$a = array(
			'js'=>'text/javascript',
			'html'=>'text/html',
			'json'=>'application/json',
			'down'=>'application/octet-stream',
		);
		header("Content-Type:$a[$type]; charset=$cset");
	}
	
	// domain_allow跨域允许
	static function dallow($domain=''){
		$allow = glbConfig::read('dmacc','ex');
		if(empty($domain)){
			@$aurl = parse_url($_SERVER["HTTP_REFERER"]);
			@$domain = $aurl['host'];
		}
		if(in_array($domain, $allow)){ 
			header("Access-Control-Allow-Origin:http://$domain"); // 指定允许其他域名访问  
			header('Access-Control-Allow-Methods:POST'); // 响应类型  
			header('Access-Control-Allow-Headers:x-requested-with,content-type'); // 响应头设置
			/*
			cls_HttpStatus::trace(array('X-Frame-Options' => 'ALLOWALL'));	//ALLOWALL，ALLOW-FROM
			*/
		} 
	}
	
	// table:(bar): 头
	static function tab_bar($title,$cont,$w1=25,$css2='tc'){	
		echo "<table border='0' class='tbbar1'><tr>\n";
		echo "<th class='tc h150' width='$w1%k'>$title</th>\n";
		echo "<td class='$css2'>$cont</td>\n"; 
		echo "</tr></table>\n";
		
	}
	// form+table:头
	static function fmt_head($fmid,$fmact,$tbcss='',$win='',$tbbrd=1){
		echo "<form id='$fmid' name='$fmid' method='post' action='$fmact' target='$win'>\n";
		$recbk = basReq::val('recbk','');
		$recbk = $recbk==='ref' ? @$_SERVER["HTTP_REFERER"] : $recbk;
		echo "<input name='recbk' type='hidden' value='$recbk' />\n"; 
		echo "<table border='$tbbrd' class='$tbcss'>\n"; // echo "\n";
	}	
	// form+table:(end):结束
	static function fmt_end($data='',$tabend='</table>'){
		if(!$data){ echo "\n$tabend</form>"; return; }
		if(is_array($data)){
			$arr = $data;
		}else{
			$arr[] = $data;	
		}
		$str = '';
		foreach($arr as $v){
			$itm = explode('|',"$v|");
			//if($itm[1]) $itm[1] = $itm[0];
			$str .= "\n<input name='$itm[0]' type='hidden' value='$itm[1]' />";
		}
		echo "$str$tabend</form>";
	}
	
	// form:(增加/修改):一行
	static function fmae_row($title,$msg,$hid=0){
		echo "<tr ".($hid?"style='display:none'":'')."><td class='tc'>$title</td>\n";
		echo "<td class='tl'>$msg</td></tr>\n";
	}
	// form:(增加/修改):头
	static function fmae_top($title,$msg,$width=25){
		echo "<tr><th width='$width%'>$title</th>\n";
		echo "<th class='tr'>$msg</th></tr>\n";
	}
	// form:(增加/修改):提交
	static function fmae_send($fmid,$title,$width=0,$bcls='tc'){
		echo "<tr><td class='tc' ".(!empty($width) ? "width='$width%'" : "").">$title</td>\n";
		echo "<td class='$bcls'><input name='$fmid' type='submit' class='btn' value='$title' />".($bcls=='tr' ? " &nbsp; 　 " : "")."</td></tr>\n";
	}
	
	static function null_cell($str,$char='Y'){
		return empty($str) ? "<span class='cCCC'>---</span>" : ($char=='Y' ? 'Y' : $str);
	}
	
	static function ieLow_js(){
		$s = '<!--[if lt IE 9]><script>';
		$s .= '(function(){';
		$s .= 'if(! /*@cc_on!@*/ 0) return;';
		$s .= 'var e = "abbr,article,aside,audio,canvas,datalist,details,dialog,eventsource,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(",");';
		$s .= 'for(var i=0;i<e.length;i++){document.createElement(e[i]);} ';
		$s .= '})()</script><![endif]-->';
		echo "\n$s\n";
	}
	static function ieLow_html($mie=8,$nie=9,$css='LowIE',$msg=''){
		$msg || $msg = "IE浏览器过低！建议你更换浏览器，如Chrome,Firefox,IE$nie+！";
		$s = "<!--[if lt IE $mie]>\n"; //<!--[if lt IE 8]>
		$s .= "<div class='$css'>$msg</div>\n";
		$s .= "<![endif]-->";
		echo "\n$s\n";
	}
	// 清空缓存 
	static function clearCache(){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); # 让它在过去就“失效”
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); # 永远是改动过的
		header("Cache-Control: no-store, no-cache , must-revalidate"); //# HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); //# HTTP/1.0
		header("Cache-control: max-age=0"); # IE6
	}
	
	// PageEnd()
	static function end($msg='',$end=''){
		global $_cbase; 
		if(empty($_cbase['run']['headed'])){
			self::page('');
		}
		if($end) echo "$end\n";
		if($msg) echo "<h1>$msg</h1>\n";
		echo "</body></html>\n";
		die();
	}
	
	// 发送HTTP状态
	static function httpStatus($code) {
		static $_status = array(
			200 => 'OK', // Success 2xx
			301 => 'Moved Permanently', // Redirection 3xx
			302 => 'Moved Temporarily ',
			400 => 'Bad Request', // Client Error 4xx
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error', // Server Error 5xx
			503 => 'Service Unavailable',
		);
		if(isset($_status[$code])) {
			header('HTTP/1.1 '.$code.' '.$_status[$code]);
			header('Status:'.$code.' '.$_status[$code]); // 确保FastCGI模式下正常
		}
	}

}