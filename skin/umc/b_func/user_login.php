<?php
(!defined('RUN_INIT')) && die('No Init');

if($act=='doout'){
  $user->logout();
}elseif($act=='dologin'){
  $fm = $_POST['fm'];
  $re2 = safComm::formCAll('fmlogin');
  if(empty($re2[0])){ 
    $res = $user->login($fm['uname'],$fm['upass']);
    $remsg = $res[0]=='OK' ? '' : $res[1];
    $recbk = req('recbk');
    if($res[0]=='OK' && $recbk){
      $remsg = lang('user.lon_jump').": <br>$recbk";
    }
  }
}elseif($user->userFlag=='Login'){
  header('Location:'."?");
}if($act=='doout'){
  $user->logout();
}elseif($act=='dologin'){
  $fm = $_POST['fm'];
  $re2 = safComm::formCAll('fmlogin');
  if(empty($re2[0])){ 
    $res = $user->login($fm['uname'],$fm['upass']);
    $remsg = $res[0]=='OK' ? '' : $res[1];
    $recbk = req('recbk');
    if($res[0]=='OK' && $recbk){
      $remsg = lang('user.lon_jump').": <br>$recbk";
    }
  }
}elseif($user->userFlag=='Login'){
  header('Location:'."?");
}
