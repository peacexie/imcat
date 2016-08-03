<?php
$_adtext = array (
  'kid' => 'adtext',
  'pid' => 'advs',
  'title' => '文字连接',
  'enable' => '1',
  'etab' => '1',
  'deep' => '2',
  'cfgs' => '<li>{title}</li>',
  'i' => 
  array (
    'athom' => 
    array (
      'pid' => '0',
      'title' => '首页广告',
      'deep' => '1',
      'frame' => '0',
      'char' => 'S',
      'cfgs' => '',
    ),
    'a2012' => 
    array (
      'pid' => 'athom',
      'title' => '环图文字[上]',
      'deep' => '2',
      'frame' => '0',
      'char' => 'H',
      'cfgs' => '',
    ),
    'a2014' => 
    array (
      'pid' => 'athom',
      'title' => '环图文字[左]',
      'deep' => '2',
      'frame' => '0',
      'char' => 'H',
      'cfgs' => '',
    ),
    'a2016' => 
    array (
      'pid' => 'athom',
      'title' => '环图文字[下]',
      'deep' => '2',
      'frame' => '0',
      'char' => 'H',
      'cfgs' => '',
    ),
    'a2018' => 
    array (
      'pid' => 'athom',
      'title' => '环图文字[右]',
      'deep' => '2',
      'frame' => '0',
      'char' => 'H',
      'cfgs' => '',
    ),
    'a2020' => 
    array (
      'pid' => 'athom',
      'title' => '友情链接[下]',
      'deep' => '2',
      'frame' => '0',
      'char' => 'Y',
      'cfgs' => '',
    ),
    'atinn' => 
    array (
      'pid' => '0',
      'title' => '通用内页',
      'deep' => '1',
      'frame' => '0',
      'char' => 'T',
      'cfgs' => '',
    ),
    'a2022' => 
    array (
      'pid' => 'atinn',
      'title' => '测试AB01',
      'deep' => '2',
      'frame' => '0',
      'char' => 'C',
      'cfgs' => '',
    ),
    'a2024' => 
    array (
      'pid' => 'atinn',
      'title' => '测试CD23',
      'deep' => '2',
      'frame' => '0',
      'char' => 'C',
      'cfgs' => '',
    ),
    'atdel' => 
    array (
      'pid' => '0',
      'title' => '[回收站]',
      'deep' => '1',
      'frame' => '0',
      'char' => '[',
      'cfgs' => '',
    ),
  ),
  'f' => 
  array (
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
  ),
);
?>