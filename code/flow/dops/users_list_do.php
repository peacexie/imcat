<?php
(!defined('RUN_MODE')) && die('No Init'); 

$fs_do = basReq::val('fs_do');
$fs = basReq::arr('fs');
if(empty($fs_do)) $msg = "请选择操作项目！";
if(empty($fs)) $msg = "请勾选操作记录！";
$cnt = 0; $msgop = '';
foreach($fs as $id=>$v){ 
	if(in_array($fs_do,array('show','hidden'))){ 
		$cnt += $dop->opShow($id,$fs_do);
		$msgop = $fs_do=='show' ? '审核' : '隐藏';
	}elseif($fs_do=='del'){ 
		$cnt += $dop->opDelete($id);
		$msgop = '删除';
	}elseif($fs_do=='(xxx)'){ 
		;//
	}
}
$msg = "$cnt 条记录 $msgop 成功！";
