<?php
/*
 * 默认首页模板和通用配置
*/
$_sy_home = array(

    //config配置
    'c' => array(
        'vmode' => 'dynamic', //dynamic,static,close(关闭)
        'stext' => '.html', 
        'stexp' => '2h', //30,60,3h,6h,12h,24h,7d
        //'tmfix' => '-mob', // 移动适配-模板后缀(正式使用请丰富模板,或屏蔽这里)
        'imcfg' => array( // import导入配置的模块
            //'gbook' => 'nrem', // gbook按nrem方式显示
        ),
        'extra' => array(), // 扩展模块: 'hello','umod',
    ),
    
    //mod.home模块首页模板
    'm' => 'home/mhome',
  
);
