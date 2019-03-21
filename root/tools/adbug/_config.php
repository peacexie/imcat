<?php
namespace imcat;
//    辅助调试工具，请用于合法用途，使用后请删除本文件或移动到网站目录之外！
//$_cbase['tpl']['vdir'] = 'adm';    
//$_cbase['run']['outer'] = 1;
//$_cbase['skip']['_sess_'] = true;
if(!session_id()) @session_start();
$_cbase['ucfg']['lang'] = '(auto)';    
include dirname(dirname(__DIR__)).'/run/_init.php';
include DIR_ROOT.'/cfgs/boot/cfg_adbug.php';
$sess_id = \imcat\usrPerm::getSessid(); 

$qstr = @$_SERVER['QUERY_STRING'];
$qstr || $qstr = 'binfo';    
$_selfname = $_SERVER['SCRIPT_NAME'];    
$allowb = array('binfo','login','dologin','iframe','frame','fset'); // 'phpinfo1','cookie',
$allowc = array('_null_');

if(strstr($_selfname,'start.php')){    
    ;//
}elseif(strstr($_selfname,'binfo.php') && in_array($qstr,$allowb)){    
    ;//
}elseif(strstr($_selfname,'check.php') && in_array($qstr,$allowc)){        
    ;//
}else{
    bootPerm_ys('pstools','','<p><a href="binfo.php?login">login</a></p>');
    //else {  $_isOut = 1;    @include __DIR__.'/devRun.php';    }
}
/*if(!basEnv::isLocal()){
    //
}*/

function tadbugNave($path=''){
    if(empty($path)){
    echo "<tr class='tc'>
        <td class='tip'><a href='start.php'>&lt;&lt;".lang('tools.adcfg_start')."</a></td>
        <th colspan='2'>".lang('tools.bug_tools')."</th>
        <td class='tip'><a href='../setup/'>".lang('tools.adcfg_setup')."&gt;&gt;</a></td>";
    }
    echo "</tr><tr class='tc'>
        <td width='25%'><a href='binfo.php'>".lang('tools.adcfg_binfo')."</a></td>
        <td width='25%'><a href='check.php'>".lang('tools.adcfg_chkenv')."</a></td>
        <td width='25%'><a href='cscan.php'>Scan</a></td>
        <td width='25%'><a href='reset.php'>".lang('tools.adcfg_reset')."</a></td>
        </tr>";
}

function fchkFuncs($name) { return function_exists($name)?FLAGYES.' - Support('.lang('tools.adcfg_yes').') ':FLAGNO.' --- (X) ';}

function dfmtRemote($str,$method=''){
    $rem_cset = empty($_GET['rem_cset']) ? '' : $_GET['rem_cset'];
    if($rem_cset) $str = iconv($rem_cset,'utf-8',$str);
    $rem_show = empty($_GET['rem_show']) ? 'script,style' : $_GET['rem_show'];
    if(strstr($rem_show,'script')) $str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str);
    if(strstr($rem_show,'style')) $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str);
    if(strstr($rem_show,'tags')) $str = strip_tags($str);
    if(strstr($rem_show,'_null_'))    {
        $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str);
    }else{
        $str = nl2br($str);
        $str = str_replace(array('<','>'), array('&lt;','&gt;'), $str);    
        $str = str_replace(array('&lt;br /&gt;','&amp;nbsp;'),array('<br />',' '),$str);    
    }
    return "<h1>$method</h1>\n$str";
}

