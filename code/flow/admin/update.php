<?php
(!defined('RUN_MODE')) && die('No Init'); 
usrPerm::run('pfile','(auto)');

$parts = empty($parts) ? 'cache' : $parts;
$cfg = basLang::ucfg('cfgbase.admupd'); 

$gbar = ''; $ggap = ''; // class='cur,
foreach($cfg as $k=>$v){ 
	$gbar .= "$ggap<input type='checkbox' class='rdcb' value='cache' onClick=\"clr_group(this,'$k');\"><a href='#' id='navid_$k'>$v</a>";	
	$ggap = ' | ';
}

$g0 = $db->table('base_model')->where("enable='1'")->order('pid,top,kid')->select();
$g1 = array();
foreach($g0 as $k=>$v){
	$g1[$v['kid']] = $v;
}

if(empty($bsend)){
	$str1 = ''; $ti = 0;
	foreach($g1 as $k=>$v){
		if($v['pid']=='types'){
			if($ti && $ti%6==0) $str1 .= "<br>";
            $str1 .= "<label><input type='checkbox' class='rdcb cbg_types' name='clr[]' id='clr_$k' value='type_$k'>$v[title]</label>";
            $ti++;
		}
	}
	$stra = "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_cache' value='cache'>{$cfg['cache']}</label>";
	$stra .= "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_relat' value='relat'>".lang('admin.ud_reltype')."</label>";	
	$stra .= "<label><input type='checkbox' class='rdcb cbg_cache' name='clr[]' id='clr_gperm' value='gperm'>".lang('admin.ud_gperm')."</label>";

	$strb = "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_data' value='data'>".lang('admin.ud_exdata')."</label>";
	$strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_file' value='file'>".lang('admin.ud_exfile')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_ctpl' value='ctpl'>".lang('admin.ud_tplcache')."</label>";
    $strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_ctag' value='ctag'>".lang('admin.ud_tagcache')."</label>";
	$strb .= "<label><input type='checkbox' class='rdcb cbg_data' name='clr[]' id='clr_cadv' value='cadv'>".lang('admin.ud_advcache')."</label>";	
    
    $strm = "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_menua' value='menua'>".lang('admin.ud_amenu')."</label>";
    $strm .= "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_menum' value='muadm'>".lang('admin.ud_umenu')."</label>";	
	$strm .= "<label><input type='checkbox' class='rdcb cbg_menux' name='clr[]' id='clr_madvs' value='madvs'>".lang('admin.ud_adlink')."</label>";	
	
	glbHtml::tab_bar($cfg['cache']." : ".lang('admin.ud_updclr'),lang('admin.ud_quick')." : $gbar",35);
	glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
	glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['cache'],$stra);
	glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['types'],$str1);
	glbHtml::fmae_row(lang('flow.op_upd').':'.$cfg['menux'],$strm);
	glbHtml::fmae_row(lang('flow.dops_clear').':'.$cfg['data'],$strb);
	glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
	glbHtml::fmt_end(array("kid|".(empty($kid) ? '_isadd_' : $kid))); 
	echo basJscss::jscode("\nfunction clr_group(e,part){fmSelGroup(e,part);$('#navid_'+part).toggleClass('cur');}$('input:first').trigger('click');");

}elseif(!empty($bsend)){

	$clr = basReq::arr('clr'); 
	if(empty($clr)) basMsg::show(lang('admin.ud_pick'),'Redir',"?file=admin/update&parts=$parts");
	
	if(in_array('cache',$clr)){
		glbCUpd::upd_groups();
		foreach($g1 as $k=>$v){
			if(in_array($k,array('score','sadm','smem','suser',))){ 
				glbCUpd::upd_paras($k);
			}
			if($v['pid']=='groups') continue;
			if($v['pid']=='types' && !empty($v['etab'])) continue; 
			if(in_array($v['pid'],array('score','sadm','smem','suser',))) continue;
			glbCUpd::upd_model($k); 
		}
	}
	if(in_array('relat',$clr)){
		$re = glbCUpd::upd_relat();
		print_r($re);	
	}
	foreach($clr as $k){
		if(substr($k,0,5)=='type_'){
			$key = substr($k,5);
			if(isset($g1[$key])){ 
				echo "<br>$key : OK! ";
				glbCUpd::upd_model($key); 
			}		
		}
	}
	if(in_array('menua',$clr)){
		foreach(array('muadm') as $mod){
			echo glbCUpd::upd_menus($mod);
		}
	} // upd_grade放在upd_menus后面
    if(in_array('menum',$clr)){
		 admAFunc::umcVInit();
	}
	if(in_array('gperm',$clr)){
		glbCUpd::upd_grade(); 
	}
	if(in_array('data',$clr)){
		devData::clrLogs();
	}
	if(in_array('file',$clr)){
		devData::clrTmps();
		$p0 = DIR_DTMP.'/modcm/';
		$a0 = comFiles::listDir($p0);
		$af = $a0['file'];
		foreach($af as $k=>$v){
			if(strstr($k,'.cfg.php')){
				$k2 = substr(str_replace(".cfg.php","",$k),1);
				if($k2=='groups') continue;
				if(!isset($g1[$k2])){
					unlink("{$p0}_{$k2}.cfg.php");	
				}
			}
		}
	}
	
	// 
	$arr = array('ctpl'=>'','ctag'=>'tagc','cadv'=>'advs',);
	foreach($arr as $k=>$v){
		if(in_array($k,$clr)){
			devData::clrCTpl($v);	
		}
	}

	if(in_array('madvs',$clr)){
		foreach($g1 as $k=>$v){
			if(in_array($v['pid'],array('advs',))){ 
				vopStatic::advMod($k,"(all)");
			}
		}
	}
	
	echo '<br>'.basDebug::runInfo().'<hr>';
	//print_r($clr);
	basMsg::show(lang('admin.ud_end'),'Redir',"?file=admin/update&parts=$parts");

}elseif($view=='set'){



}

?>
