<?php
$_inmem = array (
  'kid' => 'inmem',
  'pid' => 'users',
  'title' => '公文会员',
  'enable' => '1',
  'etab' => '0',
  'deep' => '1',
  'f' => 
  array (
    'mname' => 
    array (
      'kid' => 'mname',
      'title' => '会员名称',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => '',
    ),
    'indep' => 
    array (
      'kid' => 'indep',
      'title' => '部门',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => 'indep',
    ),
    'miuid' => 
    array (
      'kid' => 'miuid',
      'title' => '聊天号',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => '',
    ),
    'mtel' => 
    array (
      'kid' => 'mtel',
      'title' => '电话',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => '',
    ),
    'memail' => 
    array (
      'kid' => 'memail',
      'title' => '邮件',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => '',
    ),
    'maddr' => 
    array (
      'kid' => 'maddr',
      'title' => '地址',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => '',
    ),
  ),
  'i' => 
  array (
    'inadm' => 
    array (
      'kid' => 'inadm',
      'title' => '超级会员',
    ),
    'incom' => 
    array (
      'kid' => 'incom',
      'title' => '普通会员',
    ),
    'instop' => 
    array (
      'kid' => 'instop',
      'title' => '过期会员',
    ),
  ),
);
?>