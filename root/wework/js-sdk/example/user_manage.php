<?php
	
	/*
	 * 通讯录管理 － 成员接口测试
	 */

	require_once "../lib/txl_api.php";

	$api = new TXL_API();	
	
	function testAddUser($instance){		
		$info = array();
		$info["userid"]       = "UserId";
		$info["name"]         = "18620378423";  
		$info["english_name"] = "UserId";
		$info["mobile"]       = "18620378423"; 
		$info["department"]   = [1]; 
		$info["gender"]       = "1";
		$info["email"]        = "userId@tencent.com";	
		$info["order"]        = [10];
		$info["position"]     = "运营经理";
		$info["isleader"]     = 1;
		//$info["avatar_mediaid"] = "";    //NOTE: 媒体头像的接口需要上传有效的id，否则接口执行会返回失败

		print($instance->createUser($info));	
	}

	function testDeleteUser($instance){			
		$id = isset($_GET["id"]) ? $_GET["id"] : "";	

		print($instance->deleteUserById($id));	
	}

	function testBatchDeleteUser($instance){		
		$info = array();
		$info["useridlist"] = ["UserId1","UserId2"];

		print($instance->batchDeleteUser($info));
	}

	function testUpdateUser($instance){			
		$info = array();	
		$info["userid"]       = "damao";
		$info["name"]         = "张三1";  
		$info["english_name"] = "jack";
		$info["mobile"]       = "13424383529";  
		$info["department"]   = [13];  
		$info["gender"]       = "1";
		$info["email"]        = "jack@test.qq.com";	
		$info["order"]        = [10];
		$info["position"]     = "产品经理";
		$info["isleader"]     = 1;
		//$info["avatar_mediaid"] = "";   //NOTE: 媒体头像的接口需要上传有效的id，否则接口执行会返回失败
		
		print($instance->updateUser($info));	
	}

	function testQueryUser($instance){	
		$id = isset($_GET["id"]) ? $_GET["id"] : "";	
		print($instance->queryUserById($id));	
	}

	function testQueryUserByDepId($instance){
		$id = isset($_GET["id"]) ? $_GET["id"] : 1;	
		$simple = isset($_GET["simple"]) ? $_GET["simple"] : 1;
		$fetch = isset($_GET["fetch"]) ? $_GET["fetch"] : 1;

		print($instance->queryUsersByDepartmentId($id,$fetch,$simple));	
	}

	//test entry	
	$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "query";

	switch ($cmd) {
		case 'add':
			testAddUser($api);
			break;
		case 'update':
			testUpdateUser($api);
			break;
		case 'delete':
			testDeleteUser($api);
			break;
		case 'query':
			testQueryUser($api);
			break;
		case 'batch_delete':
			testBatchDeleteUser($api);
			break;
		case 'dep_query':
			testQueryUserByDepId($api);
			break;
		default:			
			break;
	}
?>

