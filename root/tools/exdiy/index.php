<?php
namespace imcat;
include __DIR__.'/_config.php'; 

// for your dir debug ... 
$list = comFiles::listDir(dirname(DIR_PROJ).'/project/tester'); 
$svlink = '';
if(!empty($list['dir'])){
  foreach ($list['dir'] as $dir=>$ctime) {
    $svlink .= "<a href='?dir=$dir&part=tester'>$dir</a> #\n";
  }
}
glbHtml::page('DIY-Tools');
eimp('initJs','jquery;/tools/exdiy/rplan.js');
eimp('initCss','stpub;/tools/exdiy/style.css');
//echo glbHtml::wpscale(480, 1);
echo '</head><body class="divOuter">';

echo "<div>";

echo "<p>
  <a href='?dir=exdiy'>exdiy:扩展功能</a>
  # <a href='?dir=08data&part=08data'>08data:导数据</a> 
  # <a href='?dir=fzg360&part=fzg360'>fzg360:采集分析</a> 
  # <a href='?dir=fzgcaiji&part=fzgcaiji'>fzgcaiji:fang采集</a> 
  # <a href='?dir=fzg-works&part=fzg-works'>fzg-works</a> 
  </p>
  <dl class=tc>$svlink</dl><hr>
\n";

$part = req('part','(root)');
$dir = req('dir','utest');
$pcfg = array(
  '(root)' => array(dirname(__DIR__), '..'),
  'tester' => array(dirname(DIR_PROJ)."/project/tester", '../../../../project/tester'),
  '08data' => array(dirname(DIR_PROJ)."/project", '../../../../project'),
  'fzg360' => array(dirname(DIR_PROJ)."/project", '../../../../project'),
  'fzgcaiji' => array(dirname(DIR_PROJ)."/project", '../../../../project'),
  'fzg-works' => array(dirname(DIR_PROJ)."/project", '../../../../project'),
);
$dbase = $pcfg[$part][0]; 

function listDir($dbase,$dir='',$part=''){
  global $pcfg;
  $list = comFiles::listDir("$dbase/$dir"); 
  if(empty($list['file'])) die('(null)');
  foreach ($list['file'] as $fp => $val) {
    if(strpos($fp,'.php') || strpos($fp,'.htm')){
      $b2 = $pcfg[$part][1]; 
      echo " --- <a href='{$b2}/$dir/$fp' target='_blank'>$fp</a><br>\n";
    }
  }
}

echo "\n<ul>";
if(!strstr($dir,'./')) listDir($dbase,$dir,$part);
echo "</ul>\n";

echo "</div>";
