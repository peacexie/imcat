<?php

// String类
class basMsg{	
	
	// init
	static function init($xMsg,$clear=0){ //
		global $_cbase;
		if($clear){
			//ob_end_clean(); //清除了不好找...
			$_cbase['run']['jsimp'] = '';
		}
		if(strlen($_cbase['run']['jsimp'])<6){
			glbHtml::page(strip_tags($xMsg),1);
			 //glbHtml::page('imp'); 
			glbHtml::page('body');
		}
	}
	
	// js跳转
	static function dir($url=''){
		return "\n".basJscss::jscode("window.location.href='$url';")."\n";
	}

	// show
	static function show($xMsg,$xAct='Redir',$xAddr=array(),$head=0){
		global $_cbase; 
		$dialog = basReq::val('dialog','','');
		$recbk = basReq::val('recbk','','Html'); 
		if(empty($xAddr) && $recbk) $xAddr = $recbk; 
		if($xAct=='die'){
			@header("HTTP/1.1 404 Not Found");
			self::init($xMsg);
			echo "<p class='tc bold pa10'> $xMsg </p>";
			die(glbHtml::page('end'));
		}elseif($dialog){
			echo self::msgbox($xMsg,$xAct,$xAddr);
		}else{ 
			echo basJscss::Alert($xMsg,$xAct,$xAddr,$head);	  
		}
	}
	// xAddr : array(array('地址1','http://txjia.com/'),array('地址2','http://domain.com/'))
	static function msgbox($xMsg,$xAct='prClose',$xAddr=array()){
		global $_cbase; 
		$css = empty($xAddr) ? 'msg_1info' : 'msg_golist';
		$str = "<div id='msg_box' class='$css'>\n<table border='1' class='tblist'>";
		$str .= "\n<tr><th colspan='2' class='msg_th'>{$xMsg}！</th></tr>";
		if(!empty($xAddr) && is_array($xAddr)){ $i=0; 
			foreach($xAddr as $ar){ $i++;
			  $link = "<a href='$ar[1]'>$ar[0]</a>";
			  $str .= "\n<tr><td class='tc'>".($i>1 ? lang('core.msg_goto') : lang('core.msg_or'))."</td><td class='tl'>$link</td></tr>";
			  if($i==1){ $lnks = $link; $lnka = $ar; }
			}
			$str .= "\n<tr><td width='20%' class='tc'>".lang('core.msg_jumpto')."</td><td class='tc'>{$lnks}</td></tr>";
		} 
		$tm = empty($_cbase['msg_timea']) ? 1503 : $_cbase['msg_timea']; 
		$pwin = $_cbase['sys_open']=='1' ? 'window.opener' : 'parent'; //window.opener,parent
		$act = (!empty($xAddr) && !empty($lnka[1])) ? "$pwin.location.href='$lnka[1]'" : "$pwin.location.reload()";
		$actext = ($_cbase['sys_open']=='1') ? "setTimeout('window.close()',300);" : ''; 
		$act = "jeCenter('msg_box',50);setTimeout(\"$act;$actext;\",$tm);";
		$str .= "\n</table>\n</div>\n".basJscss::jscode($act)."\n";
		return $str;
	}	
}

