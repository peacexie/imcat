<?php
(!defined('RUN_INIT')) && die('No Init');

$mod = req('mod','upvnow'); 
$nava = basLang::ucfg('nava.upd_vers'); 
$mtitle = $nava["admin/upgrade&mod=$mod"];
$step = req('step','init'); // init,set,deel
$kid = req('kid'); 
$act = req('act'); $acg = req('acg'); $acm = req('acm');
$burl = "?mkv=$mkv&mod=$mod&kid=";

if($act&&$acg&&$acm){
    devSetup::ins1Item($act,$acm,$acg,$kid,req('pid'));
    $msg = "[$acm] $act - OK!";
    basMsg::show($msg,'Redir',"$burl$kid");
    die();
}

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

glbHtml::fmt_head('fmlist',"$burl$kid&step=deel",'tblist');

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

}elseif($mod=='extend'){
    echo "\n<tr><th class='tc'>$mtitle </th>\n<th>Extend</th></tr>\n";
    echo "\n<tr><td colspan=2><iframe src='http://txmao.txjia.com/dev/extend.htm' width='100%' height='480' frameBorder=0></iframe></td></tr>\n";
    $out = "<a href='http://txmao.txjia.com/dev/extend.htm' target='_blank' class='center'>More...</a>";
    echo "\n<tr><td class='tl'></td>\n<td class='tc'>$out</td></tr>\n";
}elseif($mod=='install'){

}

if(in_array($mod,array('upvnow','import'))){

    echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_tip1').": </th></tr>\n";
    $text = comFiles::get(DIR_SKIN."/$tpl/d_uplog/upd_readme.txt"); 
    //$text = extMkdown::pdext($text);
    echo "\n<tr><td class='tc w180'>".lang('admin.upg_tip2')."<br>$link</td>\n<td>
        <textarea cols='' rows='18' style='width:100%'>$text</textarea>
    </td></tr>\n";

}

glbHtml::fmt_end(array(""));

