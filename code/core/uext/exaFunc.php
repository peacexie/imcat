<?php

// 后台函数; 单独函数可先用new exaFunc();自动加载
class exaFunc{

	static function admenu_m1012(&$s0){
		$s0 .= '
		<ul class="adf_mnu2" id="left_m2test">
		  <li class="adf_dir">'.lang('admin.ef_demot').'</li>
		  
		  <li id="left_m3demo"><a href="?file=dops/a&amp;mod=demo" target="adf_main">'.lang('admin.ef_demodoc').'</a> - <a onClick="admJsClick(\'demo\')">'.lang('admin.ef_add').'</a></li>
		  <li id="left_m3drem"><a href="?file=dops/a&amp;mod=drem" target="adf_main">'.lang('admin.ef_demorem').'</a> - <a href="?file=admin/catalog&amp;mod=demo&amp;frame=1" target="_blank">'.lang('admin.ef_catalog').'</a></li>
		  
		  <li class="adf_dir">'.lang('admin.ef_typedetail').'</li>
		  <li id="left_m3b3"><a href="?file=admin/types&mod=indep" target="adf_main">'.lang('admin.ef_dept').'</a></li>
		  <li id="left_m3b1"><a href="?file=admin/types&mod=brand" target="adf_main">'.lang('admin.ef_brand').'</a></li>
		  <li id="left_m3b2"><a href="?file=admin/types&mod=china" target="adf_main">'.lang('admin.ef_china').'</a></li>
		 	  	
		  <li class="adf_dir">'.lang('admin.ef_umenu').'</li>
		  <li id="left_m3a1"><a href="?uhome" target="adf_main">'.lang('admin.ef_demoset').'</a>
		  <br><a href="?uhome" target="adf_main">\code\core\uext\</a>
		  <br><a href="?uhome" target="adf_main">exaFunc.php</a></li>
		  
		</ul>
		'; 
	}
	
}
