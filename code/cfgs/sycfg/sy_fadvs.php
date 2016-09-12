<?php
(!defined('RUN_MODE')) && die('No Init');

$_sy_fadvs = array (
    'title' => 
    array (
      'title' => '标题',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'tit:2-60',
      'vtip' => '标题2-60字符',
      'vmax' => '60',
      'fmsize' => '360',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'color' => 
    array (
      'title' => '标题颜色',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '',
      'vreg' => 'nul:str:4-7',
      'vtip' => '如:#FF00FF',
      'vmax' => '8',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '0',
      'fmextra' => 'color',
      'fmexstr' => 'title',
    ),
    'ndb_repeat' => 
    array (
      'title' => '检查重复',
      'enable' => '1',
      'etab' => '0',
      'type' => 'hidden',
      'dbtype' => 'nodb',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '0',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '0',
      'fmextra' => 'repeat',
    ),
    'detail' => 
    array (
      'title' => '内容',
      'enable' => '1',
      'etab' => '0',
      'type' => 'text',
      'dbtype' => 'text',
      'dblen' => '0',
      'dbdef' => '',
      'vreg' => '',
      'vtip' => '',
      'vmax' => '24012',
      'fmsize' => '480x8',
      'fmline' => '1',
      'fmtitle' => '1',
      'fmextra' => 'text',
      'fmexstr' => '',
    ),

    'mpic' => 
    array (
      'title' => '缩略图',
      'enable' => '1',
      'etab' => '0',
      'type' => 'file',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'nul:fix:image',
      'vtip' => 'gif/jpg/jpeg/png格式.',
      'vmax' => '255',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'url' => 
    array (
      'title' => 'Url地址',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'nul:fix:uri',
      'vtip' => 'http://开头',
      'vmax' => '255',
      'fmsize' => '480',
      'fmline' => '1',
      'fmtitle' => '1',
    ),

    'click' => 
    array (
      'title' => '点击次数',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'int',
      'dblen' => '10',
      'dbdef' => '0',
      'vreg' => 'n+i',
      'vtip' => '如:888',
      'vmax' => '8',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '1',
    ),

);

// 实现多语言
$__ucfg = basLang::ucfg('fadvs');
foreach ($_sy_fadvs as $__key => $__val) {
  empty($__ucfg[$__key]) || $_sy_fadvs[$__key]['title'] = $__ucfg[$__key];
}
unset($__ucfg,$__pval,$__val);

