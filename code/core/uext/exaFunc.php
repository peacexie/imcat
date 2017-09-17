<?php

// 后台函数; 单独函数可先用new exaFunc();自动加载
class exaFunc{

    static function admenu_m1012(&$s0){
        $s0 .= '
        <ul class="adf_mnu2" id="left_m2test1">
          <li class="adf_dir"><i class="fa fa-folder-o"></i>'.basLang::show('admin.ef_demot').'</li>
          
          <li id="left_m3demo"><a href="?mkv=dops-a&amp;mod=demo" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_demodoc').'</a> - <a onClick="admJsClick(this)">'.basLang::show('admin.ef_add').'</a></li>
          <li id="left_m3drem"><a href="?mkv=dops-a&amp;mod=drem" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_demorem').'</a> - <a href="?mkv=admin-catalog&amp;mod=demo&amp;frame=1" target="_blank">'.basLang::show('admin.ef_catalog').'</a></li>
        
        </ul>
        <ul class="adf_mnu2" id="left_m2test2">  
          <li class="adf_dir"><i class="fa fa-folder-o"></i>'.basLang::show('admin.ef_typedetail').'</li>
          <li id="left_m3b3"><a href="?mkv=admin-types&mod=indep" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_dept').'</a></li>
          <li id="left_m3b1"><a href="?mkv=admin-types&mod=brand" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_brand').'</a></li>
          <li id="left_m3b2"><a href="?mkv=admin-types&mod=china" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_china').'</a></li>
        
        </ul>
        <ul class="adf_mnu2" id="left_m2test3">        
          <li class="adf_dir"><i class="fa fa-folder-o"></i>'.basLang::show('admin.ef_umenu').'</li>
          <li id="left_m3a1"><a href="?uhome" target="adf_main"><i class="fa fa-file-text-o"></i> '.basLang::show('admin.ef_demoset').'</a>
          <br><a href="?uhome" target="adf_main">\code\core\uext\</a>
          <br><a href="?uhome" target="adf_main">exaFunc.php</a></li>
          
        </ul>
        '; 
    }
    
}
