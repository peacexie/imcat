<?php
$_cbase['run']['outer'] = 1;

include '../run/_init.php';
$_flg = \imcat\safComm::urlQstr7(1);
if($_flg) die($_flg);

$_root = empty(PATH_PROJ) ? '/' : PATH_PROJ;
$_host = $_SERVER["HTTP_HOST"];
$_time = date('Y-m-d H:i:s');
$_qstr = @$_SERVER['QUERY_STRING'];
$uri = str_replace(array('<','>','"',"'","\\","\r","\n"),'',$_SERVER['REQUEST_URI']);

// 发送HTTP状态
function httpStatus($code, $remsg=0) {
    return \imcat\glbHtml::httpStatus($code, $remsg);
}
