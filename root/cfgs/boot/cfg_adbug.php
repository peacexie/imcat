<?php

$out_user = 'ut.<rnd8>'; // 如果有_config.php，则使用_config.php中的设置；
$out_pass = 'up.<rnd12>'; // 请每次使用,都改个新密码；否则安全问题,请后果自负！
$can_upfile = '0'; //是否可上传文件,移除BOM,扫描站点外目录
$can_reset = '0'; //是否可reset 
$show_binfo = array('binfo'); // 'binfo' 或 '_null_', `/?start` 入口显示基本的 `调试/工具`
