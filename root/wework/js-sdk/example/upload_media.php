<?php
	
	/**
	 * 媒体文件上传测试
	 *
	 * NOTE：其他类型的媒体文件上传逻辑类似，文件下载的逻辑可以参考：devtool目录下的devhandler.php文件
	 * 
	 */

	require_once "../lib/media_api.php";	

	$api = new MEDIA_API(1000002);
	
	//test entry	
	$info = array();
	$info["media"] = '@'.dirname($_SERVER["DOCUMENT_ROOT"].$_SERVER["REQUEST_URI"]).'/assets/test.png';
		
	var_dump($api->uploadMedia($info,"image"));
?>

