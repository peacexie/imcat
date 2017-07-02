<?php
if($_GET['light']){
	require(dirname(__FILE__).'/zepto-1x.js');
}elseif(preg_match("/MSIE [6|7|8].0/", $_SERVER['HTTP_USER_AGENT'])){
	require(dirname(__FILE__).'/jquery-1.x.js'); 
	require(dirname(__FILE__).'/html5.js'); 
}else{
	require(dirname(__FILE__).'/jquery-2.x.js');
}
?>