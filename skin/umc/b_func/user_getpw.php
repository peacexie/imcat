<?php
(!defined('RUN_INIT')) && die('No Init');

$remsg = '';
$emid = req('emid');
if(!empty($emid)){
  $emres = tex_user::chkGetpw($upass, 1, '', 600);
  $act = 'resetpw';
}elseif($act=='dogetpw'){
  $fm = $_POST['fm'];
  $re2 = safComm::formCAll('fmgetpw');
  if(empty($re2[0])){ 
    if(!empty($fm['uname']) && !empty($fm['memail'])){
      $remsg = tex_user::sendGetpw($fm['uname'], $fm['memail']);
    }
  }
}
