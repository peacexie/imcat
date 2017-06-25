<?php
$_muadm = array (
  'kid' => 'muadm',
  'pid' => 'menus',
  'title' => '后台菜单',
  'enable' => '1',
  'etab' => '1',
  'deep' => '3',
  'i' => 
  array (
    'm1012' => 
    array (
      'kid' => 'm1012',
      'pid' => '0',
      'title' => '演示',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm1main' => 
    array (
      'kid' => 'm1main',
      'pid' => '0',
      'title' => '主营',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2pro' => 
    array (
      'kid' => 'm2pro',
      'pid' => 'm1main',
      'title' => '产品订单',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3order' => 
    array (
      'kid' => 'm3order',
      'pid' => 'm2pro',
      'title' => '产品订单',
      'deep' => '3',
      'cfgs' => '订单管理(!)?mkv=dops-a&mod=corder
评论(!)?mkv=dops-a&mod=crem',
    ),
    'm3pro' => 
    array (
      'kid' => 'm3pro',
      'pid' => 'm2pro',
      'title' => '产品管理',
      'deep' => '3',
      'cfgs' => '产品管理(!)?mkv=dops-a&mod=cargo
增加(!)cargo(!)jsadd',
    ),
    'm3cset' => 
    array (
      'kid' => 'm3cset',
      'pid' => 'm2pro',
      'title' => '分类设置',
      'deep' => '3',
      'cfgs' => '产品分类(!)?mkv=admin-catalog&mod=cargo(!)frame
设置(!)?mkv=apis-exp_order',
    ),
    'm2res' => 
    array (
      'kid' => 'm2res',
      'pid' => 'm1main',
      'title' => '课程资源',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3kres' => 
    array (
      'kid' => 'm3kres',
      'pid' => 'm2res',
      'title' => '课程资源',
      'deep' => '3',
      'cfgs' => '课程资源(!)?mkv=dops-a&mod=keres
增加(!)keres(!)jsadd',
    ),
    'm3keres' => 
    array (
      'kid' => 'm3keres',
      'pid' => 'm2res',
      'title' => '评论设置',
      'deep' => '3',
      'cfgs' => '资源评论(!)?mkv=admin-catalog&mod=keres(!)
栏目(!)?mkv=admin-catalog&mod=keres(!)frame',
    ),
    'm2user' => 
    array (
      'kid' => 'm2user',
      'pid' => 'm1main',
      'title' => '会员管理',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3058' => 
    array (
      'kid' => 'm3058',
      'pid' => 'm2user',
      'title' => '个人会员',
      'deep' => '3',
      'cfgs' => '个人会员(!)?mkv=dops-a&mod=person
添加(!)person(!)jsadd',
    ),
    'm3060' => 
    array (
      'kid' => 'm3060',
      'pid' => 'm2user',
      'title' => '企业会员',
      'deep' => '3',
      'cfgs' => '企业会员(!)?mkv=dops-a&mod=company
添加(!)company(!)jsadd',
    ),
    'm3062' => 
    array (
      'kid' => 'm3062',
      'pid' => 'm2user',
      'title' => '政府机构',
      'deep' => '3',
      'cfgs' => '政府机构(!)?mkv=dops-a&mod=govern
添加(!)govern(!)jsadd',
    ),
    'm3064' => 
    array (
      'kid' => 'm3064',
      'pid' => 'm2user',
      'title' => '非盈利组织',
      'deep' => '3',
      'cfgs' => '非盈利组织(!)?mkv=dops-a&mod=organize
添加(!)organize(!)jsadd',
    ),
    'm1info' => 
    array (
      'kid' => 'm1info',
      'pid' => '0',
      'title' => '资讯',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2info' => 
    array (
      'kid' => 'm2info',
      'pid' => 'm1info',
      'title' => '新闻介绍',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3news' => 
    array (
      'kid' => 'm3news',
      'pid' => 'm2info',
      'title' => '新闻动态',
      'deep' => '3',
      'cfgs' => '新闻动态(!)?mkv=dops-a&mod=news
增加(!)news(!)jsadd',
    ),
    'm3nrem' => 
    array (
      'kid' => 'm3nrem',
      'pid' => 'm2info',
      'title' => '新闻评论',
      'deep' => '3',
      'cfgs' => '新闻评论(!)?mkv=dops-a&mod=nrem(!)
栏目(!)?mkv=admin-catalog&mod=news(!)frame',
    ),
    'm3about' => 
    array (
      'kid' => 'm3about',
      'pid' => 'm2info',
      'title' => '站点介绍',
      'deep' => '3',
      'cfgs' => '站点介绍(!)?mkv=dops-a&mod=about
增加(!)about(!)jsadd',
    ),
    'm2topic' => 
    array (
      'kid' => 'm2topic',
      'pid' => 'm1info',
      'title' => '专题新闻',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3topic' => 
    array (
      'kid' => 'm3topic',
      'pid' => 'm2topic',
      'title' => '专题新闻',
      'deep' => '3',
      'cfgs' => '专题新闻(!)?mkv=dops-a&mod=topic
增加(!)topic(!)jsadd',
    ),
    'm3torem' => 
    array (
      'kid' => 'm3torem',
      'pid' => 'm2topic',
      'title' => '评论栏目',
      'deep' => '3',
      'cfgs' => '新闻评论(!)?mkv=dops-a&mod=trem(!)
栏目(!)?mkv=admin-catalog&mod=topic(!)frame',
    ),
    'm1coms' => 
    array (
      'kid' => 'm1coms',
      'pid' => '0',
      'title' => '互动',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2indoc' => 
    array (
      'kid' => 'm2indoc',
      'pid' => 'm1coms',
      'title' => '内部公文',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3indoc' => 
    array (
      'kid' => 'm3indoc',
      'pid' => 'm2indoc',
      'title' => '公文管理',
      'deep' => '3',
      'cfgs' => '公文管理(!)?mkv=dops-a&mod=indoc
发布(!)indoc(!)jsadd',
    ),
    'm3inread' => 
    array (
      'kid' => 'm3inread',
      'pid' => 'm2indoc',
      'title' => '阅读记录',
      'deep' => '3',
      'cfgs' => '阅读记录(!)?mkv=dops-a&mod=inread
栏目(!)?mkv=admin-catalog&mod=indoc(!)frame	',
    ),
    'm3inrem' => 
    array (
      'kid' => 'm3inrem',
      'pid' => 'm2indoc',
      'title' => '评论设置',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=dops-a&mod=inrem">评论记录</a> 
- <a href="?mkv=apis-exp_indoc">设置</a>
',
    ),
    'm3inmem' => 
    array (
      'kid' => 'm3inmem',
      'pid' => 'm2indoc',
      'title' => '公文会员',
      'deep' => '3',
      'cfgs' => '公文会员(!)?mkv=dops-a&mod=inmem
等级(!)?mkv=admin-grade&mod=inmem',
    ),
    'm2faqs' => 
    array (
      'kid' => 'm2faqs',
      'pid' => 'm1coms',
      'title' => '问答系统',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3qadmin' => 
    array (
      'kid' => 'm3qadmin',
      'pid' => 'm2faqs',
      'title' => '管理发布',
      'deep' => '3',
      'cfgs' => '问答管理(!)?mkv=dops-a&mod=faqs
发布(!)faqs(!)jsadd',
    ),
    'm3qrtag' => 
    array (
      'kid' => 'm3qrtag',
      'pid' => 'm2faqs',
      'title' => '回复标签',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=dops-a&mod=qarep">问答回复</a> 
- <a href="?mkv=dops-a&mod=qatag">标签</a>
',
    ),
    'm3qaset' => 
    array (
      'kid' => 'm3qaset',
      'pid' => 'm2faqs',
      'title' => '统计更新',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=apis-exp_qaset">问答统计</a> 
- <a href="?mkv=apis-exp_qaset&view=uset">更新</a>',
    ),
    'm2vote' => 
    array (
      'kid' => 'm2vote',
      'pid' => 'm1coms',
      'title' => '投票管理',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3vtadm' => 
    array (
      'kid' => 'm3vtadm',
      'pid' => 'm2vote',
      'title' => '管理发布',
      'deep' => '3',
      'cfgs' => '投票管理(!)?mkv=dops-a&mod=votes
发布(!)votes(!)jsadd',
    ),
    'm3voip' => 
    array (
      'kid' => 'm3voip',
      'pid' => 'm2vote',
      'title' => '选项记录',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=dops-a&mod=votei">投票选项</a> 
- <a href="?mkv=dops-a&mod=votep">记录</a>',
    ),
    'm2else' => 
    array (
      'kid' => 'm2else',
      'pid' => 'm1coms',
      'title' => '其他互动',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3gbook' => 
    array (
      'kid' => 'm3gbook',
      'pid' => 'm2else',
      'title' => '网站留言管理',
      'deep' => '3',
      'cfgs' => '?mkv=dops-a&mod=gbook',
    ),
    'm3notea' => 
    array (
      'kid' => 'm3notea',
      'pid' => 'm2else',
      'title' => '站务笔记管理',
      'deep' => '3',
      'cfgs' => '笔记管理(!)?mkv=dops-a&mod=notea
发布(!)notea(!)jsadd',
    ),
    'm1adv' => 
    array (
      'kid' => 'm1adv',
      'pid' => '0',
      'title' => '广告',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm1tool' => 
    array (
      'kid' => 'm1tool',
      'pid' => '0',
      'title' => '工具',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2sys' => 
    array (
      'kid' => 'm2sys',
      'pid' => 'm1tool',
      'title' => '系统工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3catch' => 
    array (
      'kid' => 'm3catch',
      'pid' => 'm2sys',
      'title' => '缓存静态',
      'deep' => '3',
      'cfgs' => '系统缓存(!)?mkv=admin-update
静态(!)?mkv=admin-static',
    ),
    'm3self' => 
    array (
      'kid' => 'm3self',
      'pid' => 'm2sys',
      'title' => '个人资料',
      'deep' => '3',
      'cfgs' => '个人资料(!)?mkv=admin-uinfo
密码(!)?mkv=admin-uinfo&view=passwd',
    ),
    'm3env' => 
    array (
      'kid' => 'm3env',
      'pid' => 'm2sys',
      'title' => '环境检测',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=admin-ediy&part=binfo">基础环境</a> 
- <a href="?mkv=admin-ediy&part=check">检测</a>',
    ),
    'm3ediy' => 
    array (
      'kid' => 'm3ediy',
      'pid' => 'm2sys',
      'title' => '配置搜索',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=admin-ediy&part=exdiy">DIY配置</a> 
- <a href="?mkv=admin-ediy&part=search">搜索</a>',
    ),
    'm2data' => 
    array (
      'kid' => 'm2data',
      'pid' => 'm1tool',
      'title' => '数据工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3plan' => 
    array (
      'kid' => 'm3plan',
      'pid' => 'm2data',
      'title' => '计划任务',
      'deep' => '3',
      'cfgs' => '计划任务(!)?mkv=apis-cron_plan
积分(!)?mkv=apis-jifen_plan',
    ),
    'm3share' => 
    array (
      'kid' => 'm3share',
      'pid' => 'm2data',
      'title' => '分享同步',
      'deep' => '3',
      'cfgs' => '数据分享(!)?mkv=apis-exd_share
同步(!)?mkv=apis-exd_psyn',
    ),
    'm3data' => 
    array (
      'kid' => 'm3data',
      'pid' => 'm2data',
      'title' => '采集导入',
      'deep' => '3',
      'cfgs' => '数据导入(!)?mkv=apis-exd_oimp
采集(!)?mkv=apis-exd_crawl',
    ),
    'm3seo' => 
    array (
      'kid' => 'm3seo',
      'pid' => 'm2data',
      'title' => '优化推送',
      'deep' => '3',
      'cfgs' => 'SEO优化(!)?mkv=apis-seo_push
推送(!)?mkv=apis-seo_push&pid=seo_pset',
    ),
    'm2fav' => 
    array (
      'kid' => 'm2fav',
      'pid' => 'm1tool',
      'title' => '我的收藏',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3favor' => 
    array (
      'kid' => 'm3favor',
      'pid' => 'm2fav',
      'title' => '收藏帮助',
      'deep' => '3',
      'cfgs' => '网址收藏(!)?mkv=dops-a&mod=adfavor&view=vself
帮助(!){$root}/dev.php(!)blank',
    ),
    'm1adm' => 
    array (
      'kid' => 'm1adm',
      'pid' => '0',
      'title' => '架设',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2stru' => 
    array (
      'kid' => 'm2stru',
      'pid' => 'm1adm',
      'title' => '超管架构',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3model' => 
    array (
      'kid' => 'm3model',
      'pid' => 'm2stru',
      'title' => '模块架设',
      'deep' => '3',
      'cfgs' => '模块架设(!)?mkv=admin-groups
安装(!)?mkv=admin-upgrade&mod=install',
    ),
    'm3auser' => 
    array (
      'kid' => 'm3auser',
      'pid' => 'm2stru',
      'title' => '管理员:设置/添加',
      'deep' => '3',
      'cfgs' => '管理员(!)?mkv=dops-a&mod=adminer
添加(!)adminer(!)jsadd',
    ),
    'm3catalog' => 
    array (
      'kid' => 'm3catalog',
      'pid' => 'm2stru',
      'title' => '栏目管理:文档/广告',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=admin-catalog" title="文档栏目">栏目管理</a> 
- 
<a href="?mkv=admin-catalog&mod=adpic" title="广告栏目">广告</a>',
    ),
    'm3relat' => 
    array (
      'kid' => 'm3relat',
      'pid' => 'm2stru',
      'title' => '类别:管理/关联',
      'deep' => '3',
      'cfgs' => '类别管理(!)?mkv=admin-types
关联(!)?mkv=admin-relat',
    ),
    'm3amenu' => 
    array (
      'kid' => 'm3amenu',
      'pid' => 'm2stru',
      'title' => '菜单导航配置',
      'deep' => '3',
      'cfgs' => '?mkv=admin-menus',
    ),
    'm3grade' => 
    array (
      'kid' => 'm3grade',
      'pid' => 'm2stru',
      'title' => '等级权限设置',
      'deep' => '3',
      'cfgs' => '?mkv=admin-grade',
    ),
    'm2api' => 
    array (
      'kid' => 'm2api',
      'pid' => 'm1adm',
      'title' => '插件/接口',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3apisms' => 
    array (
      'kid' => 'm3apisms',
      'pid' => 'm2api',
      'title' => '短信接口',
      'deep' => '3',
      'cfgs' => '短信发送(!)?mkv=apis-sms_send
记录(!)?mkv=apis-sms_logs',
    ),
    'm3apipay' => 
    array (
      'kid' => 'm3apipay',
      'pid' => 'm2api',
      'title' => '支付记录',
      'deep' => '3',
      'cfgs' => '支付记录(!)?mkv=apis-pay_logs
接口(!)?mkv=apis-pay_logs&view=vcfgs',
    ),
    'm3apimail' => 
    array (
      'kid' => 'm3apimail',
      'pid' => 'm2api',
      'title' => '邮件接口',
      'deep' => '3',
      'cfgs' => '邮件记录(!)?mkv=apis-mail_logs
接口(!)?mkv=apis-mail_logs&view=vcfgs',
    ),
    'm3apiwexin' => 
    array (
      'kid' => 'm3apiwexin',
      'pid' => 'm2api',
      'title' => '微信接口',
      'deep' => '3',
      'cfgs' => '<a href="?mkv=awex-wex_apps">微信接口</a> 
- 
<a href="../a3rd/weixin_pay/wedemo.php">演示</a>',
    ),
    'm2adt' => 
    array (
      'kid' => 'm2adt',
      'pid' => 'm1adm',
      'title' => '超管工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3paras' => 
    array (
      'kid' => 'm3paras',
      'pid' => 'm2adt',
      'title' => '参数设置',
      'deep' => '3',
      'cfgs' => '核心参数(!)?mkv=admin-paras
扩展(!)?mkv=admin-parex',
    ),
    'm3dbs' => 
    array (
      'kid' => 'm3dbs',
      'pid' => 'm2adt',
      'title' => '数据库管理',
      'deep' => '3',
      'cfgs' => '数据库(!){root}/root/tools/adbug/dbadm.php(!)blank
管理(!)?mkv=admin-db_act',
    ),
    'm3safes' => 
    array (
      'kid' => 'm3safes',
      'pid' => 'm2adt',
      'title' => '安全日志',
      'deep' => '3',
      'cfgs' => '安全参数(!)?mkv=admin-paras&mod=prsafe
日志(!)?mkv=admin-rlogs&mod=syact',
    ),
    'm3upver' => 
    array (
      'kid' => 'm3upver',
      'pid' => 'm2adt',
      'title' => '更新升级',
      'deep' => '3',
      'cfgs' => '系统升级(!)?mkv=admin-upgrade
导入(!)?mkv=admin-upgrade&mod=import',
    ),
  ),
);
?>