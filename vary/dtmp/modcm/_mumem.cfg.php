<?php
$_mumem = array (
  'kid' => 'mumem',
  'pid' => 'menus',
  'title' => '会员菜单',
  'enable' => '1',
  'etab' => '1',
  'deep' => '2',
  'i' => 
  array (
    'user-m' => 
    array (
      'kid' => 'user-m',
      'pid' => '0',
      'title' => '用户资料',
      'deep' => '1',
      'cfgs' => '.guest',
    ),
    'user-uedpw' => 
    array (
      'kid' => 'user-uedpw',
      'pid' => 'user-m',
      'title' => '修改密码',
      'deep' => '2',
      'cfgs' => '',
    ),
    'user-uedit' => 
    array (
      'kid' => 'user-uedit',
      'pid' => 'user-m',
      'title' => '修改资料',
      'deep' => '2',
      'cfgs' => '',
    ),
    'user-tips' => 
    array (
      'kid' => 'user-tips',
      'pid' => 'user-m',
      'title' => '提示页',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'user-testguset' => 
    array (
      'kid' => 'user-testguset',
      'pid' => 'user-m',
      'title' => '测试-游客权限',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'user-testlogin' => 
    array (
      'kid' => 'user-testlogin',
      'pid' => 'user-m',
      'title' => '测试-登录权限',
      'deep' => '2',
      'cfgs' => '',
    ),
    'user-testset' => 
    array (
      'kid' => 'user-testset',
      'pid' => 'user-m',
      'title' => '测试-按设置的权限',
      'deep' => '2',
      'cfgs' => '',
    ),
    'user-mbind' => 
    array (
      'kid' => 'user-mbind',
      'pid' => 'user-m',
      'title' => '',
      'deep' => '2',
      'cfgs' => '',
    ),
    'order-m' => 
    array (
      'kid' => 'order-m',
      'pid' => '0',
      'title' => '产品订单',
      'deep' => '1',
      'cfgs' => '',
    ),
    'order-inquiry' => 
    array (
      'kid' => 'order-inquiry',
      'pid' => 'order-m',
      'title' => '订单查询',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'order-nodone' => 
    array (
      'kid' => 'order-nodone',
      'pid' => 'order-m',
      'title' => '未完成',
      'deep' => '2',
      'cfgs' => '',
    ),
    'order-isdone' => 
    array (
      'kid' => 'order-isdone',
      'pid' => 'order-m',
      'title' => '已完成',
      'deep' => '2',
      'cfgs' => '',
    ),
    'order-d' => 
    array (
      'kid' => 'order-d',
      'pid' => 'order-m',
      'title' => '订单详情',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'indoc-m' => 
    array (
      'kid' => 'indoc-m',
      'pid' => '0',
      'title' => '内部公文',
      'deep' => '1',
      'cfgs' => '',
    ),
    'indoc-d' => 
    array (
      'kid' => 'indoc-d',
      'pid' => 'indoc-m',
      'title' => '公文详情',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'indoc-iget' => 
    array (
      'kid' => 'indoc-iget',
      'pid' => 'indoc-m',
      'title' => '接收公文',
      'deep' => '2',
      'cfgs' => '.guest',
    ),
    'indoc-iedit' => 
    array (
      'kid' => 'indoc-iedit',
      'pid' => 'indoc-m',
      'title' => '公文发布',
      'deep' => '2',
      'cfgs' => '1',
    ),
    'indoc-iadm' => 
    array (
      'kid' => 'indoc-iadm',
      'pid' => 'indoc-m',
      'title' => '公文管理',
      'deep' => '2',
      'cfgs' => '1',
    ),
    'faqs-m' => 
    array (
      'kid' => 'faqs-m',
      'pid' => '0',
      'title' => '问答系统',
      'deep' => '1',
      'cfgs' => '',
    ),
    'faqs-d' => 
    array (
      'kid' => 'faqs-d',
      'pid' => 'faqs-m',
      'title' => '详情',
      'deep' => '2',
      'cfgs' => '',
    ),
    'faqs-t' => 
    array (
      'kid' => 'faqs-t',
      'pid' => 'faqs-m',
      'title' => '类别',
      'deep' => '2',
      'cfgs' => '',
    ),
    'faqs-new' => 
    array (
      'kid' => 'faqs-new',
      'pid' => 'faqs-m',
      'title' => '最新',
      'deep' => '2',
      'cfgs' => '',
    ),
    'faqs-tip' => 
    array (
      'kid' => 'faqs-tip',
      'pid' => 'faqs-m',
      'title' => '精华',
      'deep' => '2',
      'cfgs' => '',
    ),
    'faqs-hot' => 
    array (
      'kid' => 'faqs-hot',
      'pid' => 'faqs-m',
      'title' => '热门',
      'deep' => '2',
      'cfgs' => '',
    ),
    'faqs-tag' => 
    array (
      'kid' => 'faqs-tag',
      'pid' => 'faqs-m',
      'title' => '标签',
      'deep' => '2',
      'cfgs' => '',
    ),
    'help-m' => 
    array (
      'kid' => 'help-m',
      'pid' => '0',
      'title' => '',
      'deep' => '1',
      'cfgs' => '.guest',
    ),
    'help-getpw' => 
    array (
      'kid' => 'help-getpw',
      'pid' => 'help-m',
      'title' => '',
      'deep' => '2',
      'cfgs' => '',
    ),
    'help-emact' => 
    array (
      'kid' => 'help-emact',
      'pid' => 'help-m',
      'title' => '',
      'deep' => '2',
      'cfgs' => '',
    ),
  ),
);
?>