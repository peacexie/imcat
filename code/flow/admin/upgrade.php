<?php
(!defined('RUN_MODE')) && die('No Init');
usrPerm::run('pfile','(auto)');

$mod = basReq::val('mod','upvnow'); 
$marr = array( 
	'upvnow' => lang('admin.upg_upnow'),
	'import' => lang('admin.upg_impold'),
);
$mtitle = @$marr[$mod];
$links = admPFunc::fileNav($mod,'upd_vers');
glbHtml::tab_bar(lang('admin.upg_upgrade')."<span class='span ph5'>#</span>$mtitle","$links",50);

$tiprows = "
    \n<tr><td class='tc w180'>".lang('admin.upg_bkdb')."</td>\n<td>
		".lang('admin.upg_tipdb')." 
	</td></tr>\n
    \n<tr><td class='tc w180'>".lang('admin.upg_blfile')."</td>\n<td>
		".lang('admin.upg_tipfile')."
	</td></tr>\n";

glbHtml::fmt_head('fmlist',"?",'tblist');

if($mod=='upvnow'){
	
	echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_upnow').": </th></tr>\n";
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
		".lang('admin.upg_tipup')."
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_upstart')."</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvnow.php' target='_blank' class='f18 fB'>".lang('admin.upg_upstart')."</a>
	</td></tr>\n";
	

}elseif($mod=='import'){

	echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_impold').": </th></tr>\n";
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
		".lang('admin.upg_tipimp')."
	</td></tr>\n";
    echo $tiprows;
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_impstart')."</td>\n<td class='tc'>
		<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>".lang('admin.upg_impstart')."</a>
	</td></tr>\n";
		
}

	echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_tip1').": </th></tr>\n";
	$tpl = $_cbase['sys']['lang']=='zh' ? 'dev' : 'doc'; 
	$text = comFiles::get(DIR_CODE."/tpls/$tpl/d_uplog/upd_readme.txt"); 
	//$text = extMkdown::pdext($text);
	$link = "<a href='{$_cbase['server']['txmao']}/$tpl.php?uplog' target='_blank'>".lang('admin.upg_off')."</a>";
	echo "\n<tr><td class='tc w180'>".lang('admin.upg_tip2')."<br>$link</td>\n<td>
		<textarea cols='' rows='18' style='width:100%'>$text</textarea>
	</td></tr>\n";
	
glbHtml::fmt_end(array("mod|$mod"));


?>
