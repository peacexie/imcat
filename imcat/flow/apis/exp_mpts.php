<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 
define('RUN_DOPA',1);

$pcfg = $_cbase['part'];
$mod = req('mod','about'); //dopFunc::getDefmod();
//$view = empty($view) ? 'list' : $view;
$_cfg = read($mod); 

$_pid = @$_cfg['pid']; 
$_tmp = array(
    'docs' =>array('dopDocs','did'),
    'coms' =>array('dopComs','cid'),
); 
if(!isset($_tmp[$_pid])) glbHtml::end(lang('flow.dops_parerr').':mod@dop.php');
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@a.php'); 
usrPerm::run('pmod',$mod);

$_cls = '\\imcat\\'.$_tmp[$_pid][0]; 
$dop = new $_cls($_cfg);
$so = $dop->so; 
$cv = $dop->cv;
unset($_cfg,$_pid,$_tmp,$_cls);

//$lpid = req('lpid');
$msg = ''; $tabext = '';

//if($view=='list'){
    if(!empty($bsend)){

        $fs_do = req('fs_do');
        $fs = basReq::arr('fs');
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.dops_setitem');
        $cnt = 0; $msgop = '';
        foreach($fs as $id=>$v){ 
            if(in_array($fs_do,array('show','hidden'))){ 
                $cnt += $dop->opShow($id,$fs_do);
                $msgop = $fs_do=='show' ? lang('flow.dops_checked') : lang('flow.dops_hide');
            }elseif($fs_do=='del'){ 
                $cnt += $dop->opDelete($id);
                $msgop = lang('flow.dops_del');
            }elseif($fs_do=='setd'){ 
                admMpts::setDef($dop, $id); 
                $msgop = "设置_".$pcfg['tab'][$pcfg['def']];
            }elseif($fs_do=='sync'){ 
                admMpts::syncParts($dop, $id); 
                $msgop = "同步_各{$pcfg['name']}";
            }elseif($fs_do=='reset'){ 
                admMpts::resetParts($dop, $id); 
                $msgop = "取消_各{$pcfg['name']}";
            }
            $cnt++;
        }
        $msg = "$cnt ".lang('flow.dops_okn',$msgop);

        #require dopFunc::modAct('list_do',$mod,$dop->type);
    } 

    $dop->sobar($dop->msgBar($msg)); 
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    //basLang::inc('aflow', 'docs_list');
    echo "<th>选</th><th>标题</th><th>".$pcfg['name']."</th><th>栏目</th><th>显示</th>
        <th>添加账号</th><th>添加时间</th>
        <th>修改</th></tr>";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $did = $idend = $r['did'];
          if(empty($idfirst)) $idfirst = $did;
          echo "<tr>\n";
          echo $cv->Select($did);
          echo $cv->Title($r,1,'title',"");
          echo "<td class='tc'>".admMpts::vPart($r['part'])."</td>\n";
          echo $cv->Types($r['catid']);
          echo $cv->Show($r['show']);
          echo $cv->Field($r['auser']);
          echo $cv->Time($r['atime']);
          echo $cv->Url(lang('flow.dops_edit'),1,"?dops-a&mod=$mod&view=form&did=$r[did]&recbk=ref","");
          echo "</tr>"; 
        }
        //$dop->pgbar($idfirst,$idend);
        $pg = $dop->pg->show($idfirst,$idend);
        $acs = "\nsetd|设置_".$pcfg['tab'][$pcfg['def']]."\nsync|同步_各{$pcfg['name']}\nreset|取消_各{$pcfg['name']}";
        $op = "".basElm::setOption(basLang::show('flow.op_op3').$acs,'',basLang::show('flow.op0_bacth'));
        dopFunc::pageBar($pg,$op);
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));

    $jstr = "$('#{$mod}_add').addClass('cCCC').prop('href','#')";
    echo basJscss::jscode($jstr);

    //require dopFunc::modAct('list_show',$mod,$dop->type);

//}elseif($view=='set'){
    ;//utf-8编码
//}
