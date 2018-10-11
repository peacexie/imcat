<?php
/* Demo: 
  $_cbase['skip']['_all_'] = true;
  require __DIR__.'/run/_init.php'; 
  // $_cbase['skip']['robot'] = true; //单独用
  // $_cbase['skip']['db'] = true; //单独用
  // $_cbase['skip']['uchr7'] = true; //单独用
  // $_cbase['handler']['shutdown'] = true; //单独用 
  // Access-Control-Allow-Origin ??
  // X-Frame-Options ??
*/

// _all_方案:所有
if(isset($_cbase['skip']['_all_'])){
    $_cbase['skip']['error'] = true;
    $_cbase['skip']['session'] = true;
}

// _session_方案:除[session]所有
if(isset($_cbase['skip']['_sess_'])){
    $_cbase['skip']['error'] = true;
    //$_cbase['skip']['session'] = true;
}
