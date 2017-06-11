<?php
require dirname(dirname(dirname(__FILE__)))."/run/_init.php";
require_once(DIR_VENDOR.'/a3rd/qqcAPI/qqConnectAPI.php');

$recbk = req('recbk');
$recbk && $_SESSION['recbk'] = $recbk;
$qc = new QC();
$qc->qq_login();
