<?php

// 后台函数; 单独函数可先用new exaFunc();自动加载
class exaFunc{

	static function admenu_m1012(&$s0){
		$s0 .= '
		<ul class="adf_mnu2" id="left_m2test">
		  <li class="adf_dir">演示样例</li>
		  
		  <li id="left_m3demo"><a href="?file=dops/a&amp;mod=demo" target="adf_main">样例文档</a> - <a onClick="admJsClick(\'demo\')">增加</a></li>
		  <li id="left_m3drem"><a href="?file=dops/a&amp;mod=drem" target="adf_main">样例评论</a> - <a href="?file=admin/catalog&amp;mod=demo&amp;frame=1" target="_blank">栏目</a></li>
		  
		  <li class="adf_dir">类别详情</li>
		  <li id="left_m3b3"><a href="?file=admin/types&mod=indep" target="adf_main">部门介绍</a></li>
		  <li id="left_m3b1"><a href="?file=admin/types&mod=brand" target="adf_main">品牌介绍</a></li>
		  <li id="left_m3b2"><a href="?file=admin/types&mod=china" target="adf_main">中国政区</a></li>
		 	  	
		  <li class="adf_dir">自定义菜单</li>
		  <li id="left_m3a1"><a href="?uhome" target="adf_main">设置参考：</a>
		  <br><a href="?uhome" target="adf_main">\code\core\uext\</a>
		  <br><a href="?uhome" target="adf_main">exaFunc.php</a></li>
		  
		</ul>
		'; 
	}
	
}
