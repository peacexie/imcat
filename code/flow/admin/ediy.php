<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)'); 

$_sy_nava['exdiys'] = array(
	'tpls' => '/code/tpls',
	'skin' => '/root/skin',
	'cfgs' => '/code/cfgs',
	'runs' => 'vopfmt',
); 

$part = basReq::val('part','binfo'); 
$dkey = basReq::val('dkey','tpls'); 
$dsub = basReq::val('dsub',''); 

$links = admPFunc::fileNav($part,'envdiy');
if(!in_array($part,array('edit','restore'))) glbHtml::tab_bar("附加工具[$part]","$links",50); 
$msg = ''; //print_r($msg);

$view = basReq::val('view');
$efile = basReq::val('efile','');

if(in_array($part,array('edit','restore'))){

	$edir = $_sy_nava['exdiys'][$dkey];
	$edir = $edir=='vopfmt' ? '' : (empty($dsub) ? $edir : "$edir/$dsub");
	$nfile = str_replace('//','/',"/$edir/$efile");
	$fp = DIR_PROJ.$nfile;
	
	if($part=='restore'){
		unlink($fp); copy("$fp.maobak",$fp);
		basMsg::show('还原成功！');
	}elseif(!empty($bsend)){
		$ndata = $_POST['ndata']; //basReq::val('ndata','','Html',102400);
		@unlink("$fp.maobak"); copy($fp,"$fp.maobak");
		comFiles::put($fp,$ndata);
		basMsg::show('修改成功！');
	}else{
		$ndata = basStr::filForm(comFiles::get($fp)); //str_replace(array('<','>'),array('&lt;','&gt;')
		glbHtml::fmt_head('fmlist',"?file=$file&part=edit&dkey=$dkey&dsub=$dsub&efile=$efile",'tblist');
		echo "\n<tr>\n<th colspan=2>正在编辑：$nfile</th></tr>\n"; 
		echo "\n<tr>\n<td style='width:50%'>大小：".basStr::showNumber(filesize($fp),'Byte')."</td>
		        <td class='tr'>修改时间：".date("Y-m-d H:i:s",filemtime($fp))."</td></tr>\n";
		echo "\n<tr>\n<td colspan=2><textarea name='ndata' rows='18' wrap='off' style='width:100%'>$ndata</textarea></td></tr>\n";
		echo "\n<tr>\n<td style='width:50%'>保留备份.bak</td><th><input name='bsend' type='submit' value='保存修改' /></th></tr>\n";
		glbHtml::fmt_end(array("nmod|nmod","ntpl|ntpl"));		
	}

}elseif($part!='exdiy'){
	
	glbHtml::fmt_head('fmlist',"?file=$file&part=$part&dkey=$dkey",'tblist');
	echo "\n<tr><td><iframe src='".PATH_ROOT."/tools/adbug/$part.php' width='100%' height='560'></iframe></td></tr>";
	glbHtml::fmt_end(array("mod|$mod"));

}else{
	
	$lnkdk = admPFunc::fileNav($dkey,'exdiys');
	
	if($dkey=='runs'){
		$lnkds = " -- 无下层目录 -- ";
		$listu = comFiles::listDir(DIR_PROJ); $listu = $listu['file'];
		$lists = comFiles::listDir(DIR_PROJ."/root/run");  
		$edir = '';
		foreach($lists['file'] as $ifile=>$fv){
			$listu["root/run/$ifile"] = $fv; 
		}
	}elseif($dkey=='cfgs'){
		$lnkds = " -- 无下层目录 -- ";
		$edir = $_sy_nava['exdiys'][$dkey];
		$listu = comFiles::listScan(DIR_PROJ.$edir);
	}else{ //tpls,skin
		$edir = $_sy_nava['exdiys'][$dkey];
		$lists = comFiles::listDir(DIR_PROJ.$edir);
		$lnkds = ""; $dsub || $dsub = $_cbase['tpl']['def_static']; 
		foreach($lists['dir'] as $sdir=>$etime){
			if(in_array($sdir,array('a_img','b_img','logo'))) continue;
			$ititle = $sdir==$dsub ? "<span class='cF0F'>$sdir<span>" : $sdir;
			$lnkds .= (empty($lnkds)?'':' # ')."<a href='?file=$file&part=$part&dkey=$dkey&dsub=$sdir'>$ititle</a>";
			$listu = comFiles::listScan(DIR_PROJ.$edir."/$dsub");
		}
		$edir = $edir."/$dsub";
	} 
	glbHtml::tab_bar("$lnkdk",$lnkds,50); 
	
	//echo $edir; echo "<pre>"; print_r($listu);
	glbHtml::fmt_head('fmlist',"?",'tblist');
	
	echo "<tr><th>".(empty($edir)?'[/]':$edir)."</th><th>文件</th><th>大小</th>"; 
	echo "<th>更新时间</th><th>创建时间</th><th colspan=2>操作</th></tr>\n"; //<th>创建</th>
	$idir = $odir = '|';
	foreach($listu as $ifile=>$fv){ 
	  $ext = strtolower(strrchr($ifile,".")); 
	  if(!in_array($ext,array('.php','.htm','.html','.css','.js'))) { continue; } //echo "$ifile, "; 
	  $tmp = explode('/',$ifile); $bkfile = $ifile;
	  if(count($tmp)==1){
		  $idir = '[/]';
		  $ifile = $ifile;
	  }elseif(count($tmp)>2){
		  $idir = $tmp[0];
		  $ifile = substr($ifile,strpos($ifile,'/'));
	  }else{
		  $idir = $tmp[0];
		  $ifile = $tmp[1]; 
	  }
	  $ndir = $idir==$odir ? '' : $idir;
	  $odir = $idir;
	  $atime = fileatime(DIR_PROJ.$edir."/$bkfile");
	  $atstr = date("Y-m-d H:i",$atime);
	  $atstr = $atime==$fv[0] ? "<span class='cCCC'>$atstr</span>" : "<span class='cF0F'>$atstr</span>";
	  echo "<td class='tr'>$ndir</td>\n";
	  echo "<td class='tl'>$ifile</td>\n"; 
	  echo "<td class='tr'>".basStr::showNumber($fv[1],'Byte')."</td>\n";
	  echo "<td class='tc'>".date("Y-m-d H:i",$fv[0])."</td>\n"; //$title,$td=1,$url,$twin='',$w=780,$h=560
	  echo "<td class='tc'>$atstr</td>\n";
	  if(file_exists(DIR_PROJ.$edir."/$bkfile.maobak")){
		  echo $cv->Url('还原','1',"?file=$file&part=restore&dkey=$dkey&dsub=$dsub&efile=$bkfile");
	  }else{
		  echo "<td class='tc cCCC'>还原</td>\n";
	  }
	  echo $cv->Url('修改','1',"?file=$file&part=edit&dkey=$dkey&dsub=$dsub&efile=$bkfile","DIY修改:{$bkfile}");
	  echo "</tr>"; 
	}
	
	glbHtml::fmt_end(array("nmod|nmod","ntpl|ntpl"));
    
} //echo $part;

	/*
    echo "<pre>"; 
    #$res = vopStatic::updKid('news','2015-9g-mvp1','upd'); print_r($res);
    print_r($mods);
	//*/

?>
