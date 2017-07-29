<?php

// cache配置
#$_ex_cache['on']     = 0;
$_ex_cache['type']   = 'cacheFile'; // cacheFile,cacheMemd,cacheMemc,cacheSaem
$_ex_cache['prefix'] = 'cac0_'; // prefix/group
$_ex_cache['exp']    = 20; // 600~3600(s)

$_ex_cache['server']  = '127.0.0.1';
$_ex_cache['port']    = '11211';
$_ex_cache['pconn']   = '1';
$_ex_cache['size']    = '15'; // 50~100(M)

$_ex_cache['path']    = '/cache'; // `/mycdir` -=> `{DIR_DTMP}/mycdir`

