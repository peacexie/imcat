<?php
(!defined('RUN_MODE')) && die('No Init');

if(empty($fs_do)) $msg = "请选择操作项目！";
if(empty($fs)) $msg = "请勾选操作记录！";
$cnt = 0; 
if(empty($msg)){
  foreach($fs as $id=>$v){ 
	  if($fs_do=='dele'){ 
		  $cnt += $dop->opDelete($id);
	  }elseif($fs_do=='dnow'){ 
		  //$cnt += $dop->opDelnow();
	  }
  } 
}

$cnt && $msg = "$cnt 条记录 删除成功！";
