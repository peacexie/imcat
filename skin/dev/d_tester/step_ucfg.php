<?php
function autoInc_ys($file=''){
  if(empty($file)) return $file;
  $path = ''; // -- 自动检测路径
  // /vary/html/model/yyyy/md/id/
  for($i=0;$i<6;$i++){
    $path = empty($path) ? dirname(__FILE__) : dirname($path);
    $full = "$path$file";
    if(file_exists($full)){
      return $full; 
      break;
    } 
  }
  return '';
}

$incfile = '/root/run/_init.php';
// require dirname(__FILE__).$incfile; // 可直接注释以下代码用类似此行代码
if(!empty($incfile) && $incpath=autoInc_ys($incfile)){
  include $incpath;
}

// 
$_cbase['tpl']['tpl_dir'] = 'dev';
