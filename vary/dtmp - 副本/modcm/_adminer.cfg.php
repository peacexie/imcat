<?php
$_adminer = array (
  'kid' => 'adminer',
  'pid' => 'users',
  'title' => '管理员',
  'enable' => '1',
  'etab' => '0',
  'deep' => '1',
  'f' => 
  array (
    'mname' => 
    array (
      'title' => '会员名称',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '96',
      'dbdef' => '',
      'vreg' => 'str:2-24',
      'vtip' => '2-24字符',
      'vmax' => '24',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'indep' => 
    array (
      'title' => '部门',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '',
      'vreg' => 'str:2-12',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '1',
      'cfgs' => 'indep',
    ),
    'miuid' => 
    array (
      'title' => '聊天号',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '120',
      'dbdef' => '',
      'vreg' => 'nul:str:5-120',
      'vtip' => '聊天号:QQ,MSN等',
      'vmax' => '120',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'mtel' => 
    array (
      'title' => '电话',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '24',
      'dbdef' => '',
      'vreg' => 'fix:tel',
      'vtip' => '',
      'vmax' => '24',
      'fmsize' => '150',
      'fmline' => '0',
      'fmtitle' => '1',
    ),
    'memail' => 
    array (
      'title' => '邮件',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'nul:fix:email',
      'vtip' => '如:peace@domain.com',
      'vmax' => '255',
      'fmsize' => '320',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'maddr' => 
    array (
      'title' => '地址',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '120',
      'dbdef' => '',
      'vreg' => 'nul:str:5-120',
      'vtip' => '详细地址',
      'vmax' => '120',
      'fmsize' => '320',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
  ),
  'i' => 
  array (
    'supper' => 
    array (
      'title' => '超级管理员',
    ),
    'ainfo' => 
    array (
      'title' => '信息管理员',
    ),
    'auser' => 
    array (
      'title' => '会员管理员',
    ),
    'advers' => 
    array (
      'title' => '广告管理员',
    ),
    'aframe' => 
    array (
      'title' => '系统架构员',
    ),
    'aperm' => 
    array (
      'title' => '权限测试员',
    ),
    'astop' => 
    array (
      'title' => '过期管理员',
    ),
  ),
);
?>