<?php
(!defined('RUN_INIT')) && die('No Init');
// 架构相关设置


### 顶级域名设置
// 存储格式
$_sy_frame['resfmt'] = array( 
	'0' => array('about'), // yyyy-md-noid
	'1' => array('news'), // yyyy/md-noid默认
	'2' => array('cargo'), // yyyy-md/noid
	'3' => array('demo'), // yyyy/md/noid
);	


### s扩展参数(按栏目/等级)
$_sy_frame['expars'] = array( 
	'catid' => array('demo','about','cargo'), 
	'grade' => array('company','govern','organize'),
);	

