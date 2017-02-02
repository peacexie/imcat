<?php
$_inrem = array (
  'kid' => 'inrem',
  'pid' => 'coms',
  'title' => '公文评论',
  'enable' => '1',
  'etab' => '0',
  'deep' => '1',
  'cfgs' => 'ippub=5
repub=3
daypub=10
allpub=100
r_cvip=5',
  'pmod' => 'indoc',
  'cradd' => '10',
  'crdel' => '40',
  'f' => 
  array (
    'title' => 
    array (
      'kid' => 'title',
      'title' => '标题',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => NULL,
    ),
    'detail' => 
    array (
      'kid' => 'detail',
      'title' => '内容',
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
      'fmextra' => '',
      'fmexstr' => '',
      'cfgs' => NULL,
    ),
  ),
);
?>