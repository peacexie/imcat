<?php
$_prcore = array (
  'kid' => 'prcore',
  'pid' => 'score',
  'title' => '核心设置',
  'enable' => '1',
  'etab' => '0',
  'deep' => '1',
  'f' => 
  array (
    'sys_editor' => 
    array (
      'title' => '系统编辑器',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'kind',
      'vreg' => 'str:2-12',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'sys_editor',
      'cfgs' => 'kind=KindEditor
ue=UEditor
um=UEMini
ck=CKEditor
xh=xhEditor',
    ),
    'sys_open' => 
    array (
      'title' => 'open开窗方式',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '4',
      'vreg' => 'n+i',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'sys_open',
      'cfgs' => '4=4)layer@sentsin.com(推荐)
1=1)Js/window.open',
    ),
    'sys_pop' => 
    array (
      'title' => 'pop开窗方式',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => '3',
      'vreg' => 'n+i',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'sys_pop',
      'cfgs' => '4=4)layer@sentsin.com
3=3)JQ/tipsWindown(推荐)',
    ),
    'msg_timea' => 
    array (
      'title' => '提示停留时间',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'int',
      'dblen' => '255',
      'dbdef' => '1500',
      'vreg' => 'n+i',
      'vtip' => '毫秒',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'msg_timea',
    ),
    'sys_map' => 
    array (
      'title' => '地图API',
      'enable' => '1',
      'etab' => '0',
      'type' => 'select',
      'dbtype' => 'varchar',
      'dblen' => '12',
      'dbdef' => 'baidu',
      'vreg' => 'str:2-12',
      'vtip' => '',
      'vmax' => '12',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'sys_map',
      'cfgs' => 'baidu=百度
google=Google',
    ),
    'sys_name' => 
    array (
      'title' => '站点名称',
      'enable' => '1',
      'etab' => '0',
      'type' => 'input',
      'dbtype' => 'varchar',
      'dblen' => '24',
      'dbdef' => '',
      'vreg' => 'str:2-255',
      'vtip' => '',
      'vmax' => '255',
      'fmsize' => '',
      'fmline' => '1',
      'fmtitle' => '0',
      'key' => 'sys_name',
    ),
  ),
);
?>