<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)');

$mod = basReq::val('mod','upvnow'); 
$nava = basLang::ucfg('nava.upd_vers'); 
$mtitle = $nava["admin/upgrade&mod=$mod"];
$step = basReq::val('step','init'); // init,set,deel
$fact = basReq::val('fact',''); 
$burl = "?file=$file&mod=$mod&fact=";

$links = admPFunc::fileNav($mod,'upd_vers');
glbHtml::tab_bar(lang('admin.upg_upgrade')."<span class='span ph5'>#</span>$mtitle","$links",50);

$tiprows = "
    \n<tr><td class='tc w180'>".lang('admin.upg_bkdb')."</td>\n<td>
		".lang('admin.upg_tipdb')." 
	</td></tr>\n
    \n<tr><td class='tc w180'>".lang('admin.upg_blfile')."</td>\n<td>
		".lang('admin.upg_tipfile')."
	</td></tr>\n";
$tpl = $_cbase['sys']['lang']=='zh' ? 'dev' : 'doc'; 
$link = "<a href='{$_cbase['server']['txmao']}/$tpl.php?uplog' target='_blank'>".lang('admin.upg_off')."</a>";

glbHtml::fmt_head('fmlist',"$burl$fact&step=deel",'tblist');


if($mod=='upvnow'){
	
	echo "\n<tr><th class='tc'></th>\n<th>$mtitle: </th></tr>\n";
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
		".lang('admin.upg_tipup')."
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_upstart')."</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvnow.php' target='_blank' class='f18 fB'>".lang('admin.upg_upstart')."</a>
	</td></tr>\n";
	
}elseif($mod=='import'){

	echo "\n<tr><th class='tc'></th>\n<th>$mtitle: </th></tr>\n";
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
		".lang('admin.upg_tipimp')."
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_impstart')."</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>".lang('admin.upg_impstart')."</a>
	</td></tr>\n";

}elseif($mod=='install'){

	$list = comFiles::listDir(DIR_DTMP.'/update/','file');
	$msg = '(null)';
	echo "\n<tr><th class='tc'>$mtitle: </th>\n<th>Actions</th></tr>\n";
	if($step=='set'){
		$icfg = devSetup::insList($fact,1); $iu2 = implode('<br>',$icfg['abtn']); 
		$iu2 = str_replace('Update',"<i class='cF0F'>Update</i>",$iu2);  
		echo "\n<tr><td><b>$fact</b>$icfg[slist]</td>\n<td class='tc'>Will...<br>$iu2</td></tr>\n";
	    #echo $btnrows;
	    $deel = "<input name='bsend' class='btn' type='submit' value='Install/Update' /></a>";
		echo "\n<tr><td class='tc'>$deel</td>\n<td class='tc'>
			<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>$link</a>
		</td></tr>\n";
	}elseif($step=='deel'){
		$icfg = devSetup::insDeel($fact,1); $iu2 = implode('<br>',$icfg['abtn']); 
		$iu2 = str_replace('Update',"<i class='cF0F'>Update</i>",$iu2);  
		echo "\n<tr><td><b>$fact</b>$icfg[slist]</td>\n<td class='tc'>Done...<br>$iu2</td></tr>\n";
		# next ... (后续...)
	    #echo $btnrows;
		$bak = "<a href='$burl'>GoBack</a>";
		$del = "<a href='$burl".str_replace('.php','',$fact)."&step=del' class='cF00'>Delete</a>";
		echo "\n<tr><td class='tc'>$bak # $del</td>\n<td class='tc'>
			<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>$link</a>
		</td></tr>\n";
	}else{ //init
		foreach ($list as $fnow => $v) {
			if($step=='del'&&strstr($fnow,"$fact.")){
				unlink(DIR_DTMP.'/update/'.$fnow);
				$msg = 'Delete OK!';
				continue;
			}
			if(substr($fnow,0,4)!='ins-' || strpos($fnow,'.php')<=0) continue;
			$icfg = devSetup::insList($fnow); $iu2 = implode('<br>',$icfg['abtn']); 
			$iu2 = str_replace('Update',"<i class='cF0F'>Update</i>",$iu2);  
			$upd = "<a href='$burl$fnow&step=set'>Ins/Upd</a>";
			$del = "<a href='$burl".str_replace('.php','',$fnow)."&step=del' class='cF00'>Delete</a>";
			echo "\n<tr><td>[".date('Y-m-d H:i',$v[0])."] <b>$fnow</b>$icfg[slist]</td>\n<td class='tc'>$upd # $del<br>$iu2</td></tr>\n";
		} //dump($v);
	    #echo $tiprows;
		echo "\n<tr><td class='tc'>Message: $msg</td>\n<td class='tc'>
			<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>$link</a>
		</td></tr>\n";
	}
		
}

if(in_array($mod,array('upvnow','import'))){

	echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_tip1').": </th></tr>\n";
	$text = comFiles::get(DIR_CODE."/tpls/$tpl/d_uplog/upd_readme.txt"); 
	//$text = extMkdown::pdext($text);
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_tip2')."<br>$link</td>\n<td>
		<textarea cols='' rows='18' style='width:100%'>$text</textarea>
	</td></tr>\n";

}

glbHtml::fmt_end(array(""));


?>
