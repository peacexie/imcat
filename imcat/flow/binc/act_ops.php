<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

if(empty($fs_do)) $msg = lang('flow.dops_setop');
if(empty($fs)) $msg = lang('flow.dops_setitem');
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

$cnt && $msg = "$cnt ".lang('flow.dops_delok');
