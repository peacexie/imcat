<?php
(!defined('RUN_INIT')) && die('No Init');
// supml -> setup-multi-language

// ### 演示数据 ------------------------

$_demo_tabs = array(
    'dext_cargo', 'docs_cargo', 
    'dext_keres', 'docs_keres', 
    'dext_faqs', 'dext_faqs', 
    // 
    'dext_demo', 'docs_demo', 
    'dext_news', 'docs_news', 
);

// ### 文件替换 ------------------------

$_files = array();

// ### db-table替换 ------------------------

$_dbtabs = array();

$_updmods = array(); // 'about','info'

// 综合结果 ------------------------
$_scfgs['demo_tabs'] = $_demo_tabs;
$_scfgs['files'] = $_files;
$_scfgs['dbtabs'] = $_dbtabs; 
$_scfgs['updmods'] = $_updmods; 
