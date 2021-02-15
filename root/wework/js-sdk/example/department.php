<?php
	
	/*
	 * 通讯录管理 － 部门接口测试
	 */

	require_once "../lib/txl_api.php";

	$api = new TXL_API();	

	function testAddDepartment($instance){			
		$info = array();	
		$info["name"]     = "新增部门";  //部门名称  必填
		$info["parentid"] = 1;   //父级部门ID 必填
		$info["order"]    = 10;  //在父部门中的次序值。order值大的排序靠前  非必填
		$info["id"]       = 12;  //部门id，整型。指定时必须大于1，不指定时则自动生成  非必填	
	
		print($instance->createDepartment($info));	
	}

	function testUpdateDepartment($instance){	
		$info = array();	
		$info["name"]     = "更新部门名称";  //部门名称  必填
		$info["parentid"] = 1;   //父级部门ID 非必填
		$info["order"]    = 10;  //在父部门中的次序值。order值大的排序靠前  非必填
		$info["id"]       = 13;  //部门id，整型。指定时必须大于1，不指定时则自动生成  非必填	
		print($instance->updateDepartment($info));	
	}

	function testQueryDepartment($instance){
		$id = isset($_GET["id"]) ? $_GET["id"] : 1;

		print($instance->getDepartmentsById($id));	
	}

	function testDeleteDepartment($instance){	
		$id = isset($_GET["id"]) ? $_GET["id"] : 1;
		
		print($instance->deleteDepartmentById($id));	 //删除操作需谨慎！！！
	}

	//test entry	
	$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "query";

	switch ($cmd) {
		case 'add':
			testAddDepartment($api);
			break;
		case 'update':
			testUpdateDepartment($api);
			break;
		case 'delete':
			testDeleteDepartment($api);
			break;
		case 'query':
			testQueryDepartment($api);
			break;
		default:			
			break;
	}
?>

