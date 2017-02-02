<?php
(!defined('RUN_INIT')) && die('No Init');
usrPerm::run('pfile','(auto)');

$mod = req('mod','upvnow'); 
$nava = basLang::ucfg('nava.upd_vers'); 
$mtitle = $nava["admin/upgrade&mod=$mod"];
$step = req('step','init'); // init,set,deel
$kid = req('kid'); 
$act = req('act'); $acg = req('acg'); $acm = req('acm');
$burl = "?file=$file&mod=$mod&kid=";

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

}elseif($mod=='install'){

    $list = comFiles::listDir(DIR_DTMP.'/update/','file');
    $msg = '(null)'; 
    $oflink = "<a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>$link</a>";

    if($step=='set'){
        echo "\n<tr><th class='tc'>$mtitle: </th>\n<th>Actions</th></tr>\n";
        $icfg = updInfo::minsList($kid); 
        $iu2 = implode('<br>',$icfg['abtn']); 
        $notes = strlen($icfg['notes'])>12 ? $icfg['notes'] : '-';
        echo "\n<tr><td><b>ins~$kid.php</b>$icfg[slist]</td>\n<td class='tc'>Will...<br>$iu2</td></tr>\n";
        $bak = "$notes <a href='$burl' class='right'>GoBack</a>";
        echo "\n<tr><td class='tl'>$bak</td>\n<td class='tc'>$oflink</td></tr>\n";
    }else{ //init
        $list = updInfo::minsTable();
        echo "\n<tr class='tc'><th>ID</th>";
        echo "<th>Title</th>";
        echo "<th>Files</th>"; 
        echo "<th>Api</th>"; 
        echo "<th>Action</th>";
        foreach ($list as $kid => $v) {
            if(file_exists(DIR_DTMP."/update/ins~$kid.php")){
                $ins = "<a href='$burl$kid&step=set'>Install/Update</a>";
            }else{
                $url = PATH_ROOT."/plus/api/update.php?act=fatch&kid=$kid";
                $ins = "<a class='cF0F' href='$url' ".vopCell::vOpen(0).">Fatch Files...</a>";
            }
            $files = updInfo::minsDUrls($v['api'],$kid,$v['files']);
            $itmes = updInfo::minsSMods($v['mods']);
            $ires = '';
            foreach ($itmes as $k=>$itme) {
                $ires .= "[$k] ".implode('; ',$itme)."<br>\n";
            }
            echo "\n<tr class='tc'><td rowspan=2>$kid</td>";
            echo "<td>$v[title]</td>";
            echo "<td>$files</td>"; 
            echo "<td>$v[api]</td>"; 
            echo "<td>$ins</td>";
            echo "</tr>\n<tr><td colspan=4 class='h100' style='line-height:120%;background:#EEE;'>$ires</td></tr>";
        } 
        $init = "<a href='".PATH_ROOT."/plus/api/update.php?act=fatch' ".vopCell::vOpen().">Update-Init</a>";
        echo "\n<tr class='tc'><th>Message</th><th colspan=2>$msg</th><th>$init</th><th>$oflink</th></tr>\n";
    }
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

