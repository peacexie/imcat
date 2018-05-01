<?php

// 项目(project): 访问相对根路径：注意：在根目录设置是空字符串,而不是/，非根目录前面以/开头,后面不要/
define('PATH_PROJ', ''); 

//dirs&path : DIR_*实体路径; PATH_*访问路径
define('DIR_ROOT', DIR_PROJ.'/root'); define('PATH_ROOT', PATH_PROJ.'/root'); //web_root入口文件根目录,访问相对路径
define('DIR_CODE', DIR_PROJ.'/code'); define('PATH_CODE', PATH_PROJ.'/code'); //web_code主要流程代码目录
define('DIR_SKIN', DIR_PROJ.'/skin'); define('PATH_SKIN', PATH_PROJ.'/skin'); //模板资源目录，一般不需要修改

define('DIR_VARS', DIR_PROJ.DS.'vary'); define('PATH_VARS', PATH_PROJ.'/vary'); //variable可变目录 
define('DIR_CTPL', DIR_VARS.'/ctpl'); define('PATH_CTPL', PATH_VARS.'/ctpl'); //模板缓存
define('DIR_DTMP', DIR_VARS.'/dtmp'); define('PATH_DTMP', PATH_VARS.'/dtmp'); //dynamic_temp动态临时目录,访问相对路径
define('DIR_HTML', DIR_PROJ.'/html'); define('PATH_HTML', PATH_PROJ.'/html'); //html_doc静态html文档目录,访问相对路径
define('DIR_URES', DIR_PROJ.'/ures'); define('PATH_URES', PATH_PROJ.'/ures'); //upload_resource上传资源文件目录,访问相对路径
#define('DIR_URES', DIR_PROJ.'/ures'); define('PATH_URES', 'http://img.domain.com/imcat'); // ftp附件
 
define('DIR_IMPS', DIR_PROJ.DS.'vimp'); define('PATH_IMPS', PATH_PROJ.'/vimp'); //import_root导入目录
define('DIR_VENDOR', DIR_IMPS.'/vendor'); define('PATH_VENDOR', PATH_IMPS.'/vendor'); //vendor_package第三方组件目录,访问相对路径
define('DIR_VENDUI', DIR_IMPS.'/vendui'); define('PATH_VENDUI', PATH_IMPS.'/vendui'); //vendor_ui第三方UI目录,访问相对路径
define('DIR_STATIC', DIR_IMPS.'/static'); define('PATH_STATIC', PATH_IMPS.'/static'); //static_files静态文件目录,访问相对路径
