<?php
(!defined('RUN_INIT')) && die('No Init');

$_sy_fdemo = array(
	  'init_docs' => array(
		  'title'=>array('title'=>'标题','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'60',
			   'vreg'=>'tit:2-60','vtip'=>'标题2-60字符','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'mpic'=>array('title'=>'缩略图','etab'=>'0','type'=>'file','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:image','vtip'=>'gif/jpg/jpeg/png格式.','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'jump'=>array('title'=>'跳转地址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:uri','vtip'=>'http://开头','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'static'=>array('title'=>'静态格式','etab'=>'0','type'=>'input','enable'=>'0','vmax'=>'255',
			   'vreg'=>'nul:str:3-24','vtip'=>'yyyy-md-(no)','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'show'=>array('title'=>'是否显示','etab'=>'0','type'=>'select','enable'=>'1','vmax'=>'1',
			   'vreg'=>'str:1-1','vtip'=>'一定要选','dbtype'=>'int','dblen'=>'1','dbdef'=>'1',),
		  'top'=>array('title'=>'显示顺序','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'6',
			   'vreg'=>'n-i','vtip'=>'如:888','dbtype'=>'int','dblen'=>'10','dbdef'=>'888',),
		  'color'=>array('title'=>'标题颜色','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'8',
			   'vreg'=>'nul:str:4-7','vtip'=>'如:#FF00FF','dbtype'=>'varchar','dblen'=>'12','dbdef'=>'',),
		  'click'=>array('title'=>'点击次数','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'8',
			   'vreg'=>'n+i','vtip'=>'如:888','dbtype'=>'int','dblen'=>'10','dbdef'=>'0',),
	  ),
	  'init_dext' => array(
		  'detail'=>array('title'=>'内容','etab'=>'1','type'=>'text','enable'=>'1','vmax'=>'0',
			   'vreg'=>'str:10+','vtip'=>'内容10字符以上','dbtype'=>'mediumtext','dblen'=>'0','dbdef'=>NULL,),
		  'author'=>array('title'=>'作者','etab'=>'1','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'source'=>array('title'=>'来源','etab'=>'1','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'seo_key'=>array('title'=>'关键字','etab'=>'1','type'=>'input','enable'=>'0','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'seo_des'=>array('title'=>'描述','etab'=>'1','type'=>'input','enable'=>'0','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'seo_tag'=>array('title'=>'标签','etab'=>'1','type'=>'input','enable'=>'0','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,), 
	  ),
	  'init_coms' => array(
		  'title'=>array('title'=>'标题','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'60',
			   'vreg'=>'tit:2-60','vtip'=>'标题2-60字符','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'detail'=>array('title'=>'内容','etab'=>'0','type'=>'text','enable'=>'1','vmax'=>'0',
			   'vreg'=>'str:10+','vtip'=>'内容10字符以上','dbtype'=>'text','dblen'=>'0','dbdef'=>NULL,),
		  'mname'=>array('title'=>'会员名称','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'str:2-24','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  'mtel'=>array('title'=>'电话','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'fix:tel','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  'memail'=>array('title'=>'邮件地址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:email','vtip'=>'如:peace@domain.com','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'miuid'=>array('title'=>'聊天号','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'120',
			   'vreg'=>'nul:str:5-120','vtip'=>'聊天号:QQ,MSN等','dbtype'=>'varchar','dblen'=>'120','dbdef'=>NULL,),
		  'mweb'=>array('title'=>'网址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:uri','vtip'=>'http://开头','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'maddr'=>array('title'=>'地址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'120',
			   'vreg'=>'nul:str:5-120','vtip'=>'详细地址','dbtype'=>'varchar','dblen'=>'120','dbdef'=>NULL,),
	  ),
  
	  'init_users' => array(
		  'company'=>array('title'=>'公司名称','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'str:2-48','vtip'=>'2-48字符','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
		  'mname'=>array('title'=>'会员名称','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'str:2-24','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
		  'mfrom'=>array('title'=>'籍贯','etab'=>'1','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
		  'ename'=>array('title'=>'英文名','etab'=>'0','type'=>'input','enable'=>'0','vmax'=>'96',
			   'vreg'=>'str:2-60','vtip'=>'标题2-60字符','dbtype'=>'varchar','dblen'=>'96','dbdef'=>NULL,),
		  'detail'=>array('title'=>'内容','etab'=>'1','type'=>'html','enable'=>'0','vmax'=>'0',
			   'vreg'=>'str:10+','vtip'=>'内容10字符以上','dbtype'=>'varchar','dblen'=>'0','dbdef'=>NULL,),
		  'mpic'=>array('title'=>'缩略图','etab'=>'0','type'=>'file','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:image','vtip'=>'gif/jpg/jpeg/png格式.','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'map'=>array('title'=>'地图','etab'=>'1','type'=>'input','enable'=>'0','vmax'=>'48',
			   'vreg'=>'','vtip'=>'','dbtype'=>'varchar','dblen'=>'48','dbdef'=>NULL,),
		  't400'=>array('title'=>'400电话','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'fix:tel','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  't800'=>array('title'=>'800电话','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'fix:tel','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  'mtitle'=>array('title'=>'联系人','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'str:2-24','vtip'=>'2-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  'mtel'=>array('title'=>'电话','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'24',
			   'vreg'=>'fix:tel','vtip'=>'7-24字符','dbtype'=>'varchar','dblen'=>'24','dbdef'=>NULL,),
		  'memail'=>array('title'=>'邮件地址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:email','vtip'=>'如:peace@domain.com','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'miuid'=>array('title'=>'聊天号','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'120',
			   'vreg'=>'nul:str:5-120','vtip'=>'聊天号:QQ,MSN等','dbtype'=>'varchar','dblen'=>'120','dbdef'=>NULL,),
		  'mweb'=>array('title'=>'网址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'255',
			   'vreg'=>'nul:fix:uri','vtip'=>'http://开头','dbtype'=>'varchar','dblen'=>'255','dbdef'=>NULL,),
		  'maddr'=>array('title'=>'地址','etab'=>'0','type'=>'input','enable'=>'1','vmax'=>'120',
			   'vreg'=>'nul:str:5-120','vtip'=>'详细地址','dbtype'=>'varchar','dblen'=>'120','dbdef'=>NULL,),
			   
	  ),

);

// 实现多语言
$__ucfg = basLang::ucfg('fdemo');
foreach ($_sy_fdemo as $__pk => $__pval) {
	foreach ($__pval as $__key => $__val) {
	  empty($__ucfg[$__key]) || $_sy_fdemo[$__pk][$__key]['title'] = $__ucfg[$__key];
	}
}
unset($__ucfg,$__pval,$__val);

