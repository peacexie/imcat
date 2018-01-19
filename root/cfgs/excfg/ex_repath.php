<?php

// $_pub, 公共替换路径 ------------------------
$_pub = array();

// 自定义(公共)路径
#$_pub['{outSwplayerPath}/'] = 'http://cdn_d/vimp/vendui/swplayer/';  

// $_att, 附件替换路径; ------------------------
$_att = array();
$_dir = array();

// 旧版asp附件路径
/*
if($_SERVER["HTTP_HOST"]=='psw.txjia.com'){
    $_att['{oldUpic}/'] = 'http://127.0.0.1/peace/pswpower/2017/upfile/dtpic/';
    $_dir['{oldUpic}/'] = 'E:/Peace/webs/peace/pswpower/2017/upfile/dtpic/';
}else{
    $_att['{oldUpic}/'] = 'http://www.pswpower.com/upfile/dtpic/';
    $_dir['{oldUpic}/'] = '/upfile/dtpic/';
}*/

// $_tpl, 模板替换路径; ------------------------
$_tpl = array();

// 自定义[local-swplayer]路径
#$_tpl['{mySwplayerPath}/'] = PATH_VENDUI.'/swplayer/'; 

// 综合结果 ------------------------

// 替换路径
$_ex_repath['att'] = $_pub + $_att;
$_ex_repath['tpl'] = $_pub + $_tpl; 
$_ex_repath['dir'] = $_dir; 


/*
src="{stcroot}/media/collect/y_col1.gif" 
{oldAspUpfile} -=> http://old.abc_d.com/upload
*/
