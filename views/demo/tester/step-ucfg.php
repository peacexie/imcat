<?php
function autoInc_ys($file=''){
  if(empty($file)) return $file;
  $path = ''; // -- 自动检测路径
  // /xvars/html/model/yyyy/md/id/
  for($i=0;$i<6;$i++){
    $path = empty($path) ? __DIR__ : dirname($path);
    $full = "$path$file";
    if(file_exists($full)){
      return $full; 
      break;
    } 
  }
  return '';
}

$_cbase['tpl']['vdir'] = 'dev';
$incfile = '/root/run/_init.php';
// require __DIR__.$incfile; // 可直接注释以下代码用类似此行代码
if(!empty($incfile) && $incpath=autoInc_ys($incfile)){
  include $incpath;
}

// 

