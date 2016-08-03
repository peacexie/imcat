<?php
$_nrem = array (
  'kid' => 'nrem',
  'pid' => 'coms',
  'title' => '新闻评论',
  'enable' => '1',
  'etab' => '0',
  'deep' => '1',
  'cfgs' => 'showdef=1
login=1
ap_cvip=500
allpub=100
ippub=5
iprep=60',
  'pmod' => 'news',
  'cradd' => '10',
  'crdel' => '40',
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
      'vtip' => '内容10字符以上',
      'vmax' => '255',
      'fmsize' => '360x5',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'mname' => 
    array (
      'title' => '会员名称',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '24',
      'dbdef' => '',
      'vreg' => 'str:2-24',
      'vtip' => '2-24字符',
      'vmax' => '24',
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
      'vtip' => '2-24字符',
      'vmax' => '24',
      'fmsize' => '',
      'fmline' => '0',
      'fmtitle' => '1',
    ),
    'memail' => 
    array (
      'title' => '邮件地址',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'nul:fix:email',
      'vtip' => '如:peace@domain.com',
      'vmax' => '255',
      'fmsize' => '360',
      'fmline' => '1',
      'fmtitle' => '1',
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
      'fmsize' => '240',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
    'mweb' => 
    array (
      'title' => '网址',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '255',
      'dbdef' => '',
      'vreg' => 'nul:fix:uri',
      'vtip' => 'http://开头',
      'vmax' => '255',
      'fmsize' => '360',
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
      'fmsize' => '360',
      'fmline' => '1',
      'fmtitle' => '1',
    ),
  ),
);
?>