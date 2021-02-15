<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$remsg = '';
$emid = req('emid');
if(!empty($emid)){
  $emres = \imcat\umc\texUser::chkGetpw($upass, 1, '', 600);
  $act = 'resetpw';
}elseif($act=='dogetpw'){
  $fm = $_POST['fm'];
  $re2 = safComm::formCAll('fmgetpw');
  if(empty($re2[0])){ 
    if(!empty($fm['uname']) && !empty($fm['memail'])){
      $remsg = \imcat\umc\texUser::sendGetpw($fm['uname'], $fm['memail']);
    }
  }
}
