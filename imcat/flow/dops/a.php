<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
define('RUN_DOPA',1);

$mod = dopFunc::getDefmod();
$view = empty($view) ? 'list' : $view;
$_cfg = read($mod); 

$_pid = @$_cfg['pid']; 
$_tmp = array(
    'docs' =>array('dopDocs','did'),
    'users'=>array('dopUser','uid'),
    'coms' =>array('dopComs','cid'),
    'advs' =>array('dopAdvs','aid'),
); 
if(!isset($_tmp[$_pid])) glbHtml::end(lang('flow.dops_parerr').':mod@dop.php');
if(!($gname = @$_groups[$mod]['title'])) glbHtml::end(lang('flow.dops_parerr').':mod@a.php'); 
usrPerm::run('pmod',$mod);

$_cls = '\\imcat\\'.$_tmp[$_pid][0]; 
$dop = new $_cls($_cfg);
$so = $dop->so; 
$cv = $dop->cv;
unset($_cfg,$_pid,$_tmp,$_cls);

#$_scdir = dirname(__DIR__); // 脚本: 
if($_fex=dopFunc::modFile($mod,$dop->type)){
    require $_fex;
}else{
    $msg = ''; $tabext = '';
    if($view=='list'){
        if(!empty($bsend)){
            require dopFunc::modAct('list_do',$mod,$dop->type);
        } //$dop->whrstr = " AND "; $_mpid,
        require dopFunc::modAct('list_show',$mod,$dop->type);
    }elseif($view=='form'){
        if(!empty($bsend)){
            require dopFunc::modAct('form_do',$mod,$dop->type);
        }else{
            require dopFunc::modAct('form_show',$mod,$dop->type);
        }
    }
}

