<?php
require dirname(dirname(__FILE__)).'/a3rd_cfgs.php'; 
define('DIR_PAYRUN', dirname(__FILE__)); 
define('PATH_PAYRUN', PATH_ROOT.'/a3rd/paypal_ec');
define('LIBS_PAYRUN', DIR_VENDOR.'/a3rd/paypal_class');

require LIBS_PAYRUN."/paypal_functions.php"; 
