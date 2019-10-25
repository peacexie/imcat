<?php
namespace imcat;
#include dirname(dirname(__DIR__)).'/run/_init.php';
include __DIR__.'/_config.php'; 

$act = req('act');
$mod = req('mod');
if($mod){
    $mcfgs = read($mod);
    $itms = isset($mcfgs['i']) ? $mcfgs['i'] : $mcfgs;
    echo out($itms,'jsonp');
    die();
}

$tagname = tagHelp::defTagname();
$typtabs = tagHelp::$typtabs;
$modtabs = admPFunc::modList(array('docs','users','coms','types','advs'),0);
$_tpl = '<li><a href=\'{surl("news.$t_did")}\'>{=$t_title}</a></li>';
$demotpl = basStr::filText("\n$_tpl",0);
$_att = '{tag:dlist=[List][modid,news][limit,5]}';
$democode = basStr::filText("\n$_att\n$_tpl\n{/tag:dlist}",0);
$typkeys = \implode("','",\array_keys($typtabs));

glbHtml::page(lang('tools.tag_helper'));
eimp('initJs','jquery;/tools/exdiy/tagor.js');
eimp('initCss','stpub;/base/assets/cssjs/cinfo.css;/tools/exdiy/style.css');
eimp('/bootstrap/css/font-awesome.min.css','vendui');
glbHtml::page('body');
include __DIR__.'/tagor.htm';
glbHtml::page('end');
