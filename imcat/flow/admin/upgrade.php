<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$mod = req('mod','insupmod'); // insupmod
$nava = basLang::ucfg('nava.upd_vers');
$nava['admin-upgrade&mod=insupmod'] = '安装更新模块';
$mtitle = $nava["admin-upgrade&mod=$mod"];
$step = req('step','init'); // init,set,deel
$kid = req('kid'); 
$act = req('act'); $acg = req('acg'); $acm = req('acm');
$burl = "?$mkv&mod=$mod&kid=";

if($act&&$acg&&$acm){
    devSetup::ins1Item($act,$acm,$acg,$kid,req('pid'));
    $msg = "[$acm] $act - OK!";
    basMsg::show($msg,'Redir',"$burl$kid");
    die();
}


//echo urldecode('weixin%3A%2F%2Fwxpay%2Fbizpayurl%3Fpr%3Dlk0QhAF00');
//echo 'xxx';


$links = admPFunc::fileNav($mod,$nava);
glbHtml::tab_bar(lang('admin.upg_upgrade')."<span class='span ph5'>#</span>$mtitle","$links",50);

$_ktip = $mod=='upvnow' ? 'upg_tipup' : 'upg_tipimp';
$tiprows = "
    \n<tr><th class='tc'></th>\n<th>$mtitle: </th></tr>\n
    \n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
        ".lang('admin.'.$_ktip)."
    </td></tr>\n
    \n<tr><td class='tc w180'>".lang('admin.upg_bkdb')."</td>\n<td>
        ".lang('admin.upg_tipdb')." 
    </td></tr>\n
    \n<tr><td class='tc w180'>".lang('admin.upg_blfile')."</td>\n<td>
        ".lang('admin.upg_tipfile')."
    </td></tr>\n";
$tpl = $_cbase['sys']['lang']=='cn' ? 'dev' : 'doc'; 
$link = "<a href='{$_cbase['server']['txmao']}/$tpl.php?uplog' target='_blank'>".lang('admin.upg_off')."</a>";

glbHtml::fmt_head('fmlist',"$burl$kid&step=deel",'tblist');

if($mod=='insupmod'){

    echo "\n<tr><th class='tc'></th>\n<th>$mtitle: </th></tr>\n";
    echo "\n<tr><td class='tc w180'>".lang('admin.upg_use')."</td>\n<td>
        安装或更新：导出的模块、菜单等。
    </td></tr>\n";
    /*echo $tiprows;*/
    $files = glob(DIR_VARS.'/dtmp/update/*.php'); //dump($files);
    echo "\n<tr><td class='tc w180'>文件位置</td>\n<td>
        {xvars}/dtmp/update/* (".count($files).")个文件，(提示：把导出的文件放入此目录)<br>";
    foreach($files as $file) {
        $bfile = basename($file);
        echo "\n<a href='?$mkv&mod=$mod&fp=$bfile'>$bfile</a>; &nbsp; ";
    }
    echo "</td></tr>\n";

    $fp = req("fp"); // 显示文件数据
    if($fp){
        $fpr = DIR_VARS."/dtmp/update/$fp";
        if(!file_exists($fpr) || strstr($fp,'./')){ die('</table>Error!');}
        $mcfgs = include ($fpr); //dump($mcfgs);
        echo "\n<tr><td class='tc w180'>文件内容</td>\n<td>";
        echo "\n<a href='?$mkv&mod=$mod&fp=$fp&dbv=1'>展开详情</a>";
        if(!empty($mcfgs['menus'])){
            foreach($mcfgs['menus'] as $mk=>$mr) {
                echo "; &nbsp; \n<span>($mk)$mr[title](菜单)</span>";
            }
        }
        if(!empty($mcfgs['mods'])){
            foreach($mcfgs['mods'] as $mk=>$mr) {
                echo "; &nbsp; \n<span>($mk)$mr[title](模型)</span>";
            }
        }
        echo "</td></tr>\n";        
    }

    function keyRun1($run, $key, $sql, $idx=0){ // 执行一条sql
        $sqli = empty($sql) ? '-- nosql' : $sql;
        echo "<div><textarea rows=5 class='wp100'>$sqli</textarea></div>";
        if(!$run || !$sql) return true;
        if($idx){ 
            echo "<div> --- <b>执行结果:</b>人工检查索引更新!</div><br><br>";
            return; 
        }
        $db = glbDBObj::dbObj(); 
        try {
            $cnt = $db->query($sql,'run'); 
            //dump($sql);
            $msg = $cnt || $db->db->affRows;
        }catch (Exception $e){ 
            $msg = $e->getMessage();
        }
        echo "<div> --- <b>执行结果:</b> 更新了[".$msg."]行数据</div><br><br>";
    }

    $dbv = req("dbv"); // 显示导入结果
    $run = req("run"); 
    if($fp && ($dbv||$run)){
        $dbstr = devData::struExp(0); 
        $dbarr = updBase::listTab($dbstr);
        $mdata = file_get_contents(DIR_VARS."/dtmp/update/".str_replace(".php",".dbsql",$fp)); //dump($mcfgs);
        echo "\n<tr><td class='tc w180'>导入结果</td>\n<td>";
        if(!empty($mcfgs['keys'])){
            $keya = explode(',',$mcfgs['keys']);
            foreach($keya as $ik) {
                if($run && $ik!==$run){
                    continue;
                }
                $sqls = devData::keySqls($mdata, $ik);
                echo "\n<br><b>$ik</b>:\n<a href='?$mkv&mod=$mod&fp=$fp&run=$ik' target='_blank'>执行</a>";
                foreach($sqls as $sk=>$sqli) {
                    if(empty($sqli) || strstr($sqli,'DROP TABLE')){
                        continue;
                    }
                    if(strstr($sqli,'CREATE TABLE')){
                        $tbk = str_replace('stru_','',$ik); //stru_docs_cargo
                        $shead = "ALTER TABLE `{$db->pre}$tbk{$db->ext}` ";
                        if(isset($dbarr[$tbk])){
                            $fnew = updBase::dbFields($sqli, 1, 1);
                            $rcmp = updBase::dbTab1($fnew, $dbarr[$tbk]); //dump($fnew); dump($dbarr[$tbk]);
                            //$tmp1 = updBase::dbTable([$ik=>$fnew], [$ik=>$dbarr[$tbk]], []); dump($tmp1); echo "###<hr>";
                            if(!empty($rcmp['edit']) || !empty($rcmp['add']) || !empty($rcmp['idx'])){
                                foreach(['edit','add','idx'] as $sqlc) {
                                    if(empty($rcmp[$sqlc])){ continue; }
                                    keyRun1($run, $ik, "$shead{$rcmp[$sqlc]}", $sqlc=='idx'?1:0); 
                                }
                            }else{ // Null
                                keyRun1($run, $ik, ''); 
                            }
                        }else{ // CREATE
                            keyRun1($run, $ik, $sqli);
                        }
                    }else{ // INSERT
                        if(strstr($ik,'menu_')){ 
                            $ikk = str_replace('menu_','',$ik);
                            $sqli = str_replace("(pid-$ikk)", $mcfgs['menus'][$ikk]['pid'], $sqli); 
                        }
                        keyRun1($run, $ik, $sqli);
                    }
                }
            }
        }
        echo "</td></tr>\n";        
    }

    echo "\n<tr><td class='tc w180'>Tips</td>\n<td class='tc'>
        点击文件 > 展开详情 > 点击[执行] > 即执行对应的sql
    </td></tr>\n";
    #devSetup::run1Sql($sql,$rep='');

}elseif($mod=='upvnow'){
    
    echo $tiprows;
    echo "\n<tr><td class='tc w180'>".lang('admin.upg_upstart')."</td>\n<td class='tc'>
        <a href='".PATH_ROOT."/tools/setup/upvnow.php' target='_blank' class='f18 fB'>".lang('admin.upg_upstart')."</a>
    </td></tr>\n";
    
}elseif($mod=='import'){

    echo $tiprows;
    echo "\n<tr><td class='tc w180'>".lang('admin.upg_impstart')."</td>\n<td class='tc'>
        <a href='".PATH_ROOT."/tools/setup/upvimp.php' target='_blank' class='f18 fB'>".lang('admin.upg_impstart')."</a>
    </td></tr>\n";

}elseif($mod=='extend'){

    echo "\n<tr><th class='tc'>$mtitle </th>\n<th>Extend</th></tr>\n";
    echo "\n<tr><td colspan=2><iframe src='http://imcat.txjia.com/dev.php?extend' width='100%' height='480' frameBorder=0></iframe></td></tr>\n";
    $out = "<a href='http://imcat.txjia.com/dev.php?extend' target='_blank' class='center'>More...</a>";
    echo "\n<tr><td class='tl'></td>\n<td class='tc'>$out</td></tr>\n";

}

if(in_array($mod,array('upvnow','import'))){

    echo "\n<tr><th class='tc'></th>\n<th>".lang('admin.upg_tip1').": </th></tr>\n";
    $text = comFiles::get(DIR_VIEWS."/$tpl/d_uplog/readme.txt"); 
    //$text = extMkdown::pdext($text);
    echo "\n<tr><td class='tc w180'>".lang('admin.upg_tip2')."<br>$link</td>\n<td>
        <textarea cols='' rows='18' style='width:100%'>$text</textarea>
    </td></tr>\n";

}

glbHtml::fmt_end(array(""));

