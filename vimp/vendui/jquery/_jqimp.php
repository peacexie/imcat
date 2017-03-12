<?php
if($_GET['light']){
	require(dirname(__FILE__).'/zepto-1.2.imp_js');
}elseif(preg_match("/MSIE [6|7|8].0/", $_SERVER['HTTP_USER_AGENT'])){
	require(dirname(__FILE__).'/jquery-1.x.imp_js'); 
	require(dirname(__FILE__).'/html5.imp_js'); 
}else{
	require(dirname(__FILE__).'/jquery-2.x.imp_js');
}
?>