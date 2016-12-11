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
      'pid' => '0',
      'title' => '演示',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm1main' => 
    array (
      'pid' => '0',
      'title' => '主营',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2pro' => 
    array (
      'pid' => 'm1main',
      'title' => '产品订单',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3order' => 
    array (
      'pid' => 'm2pro',
      'title' => '产品订单',
      'deep' => '3',
      'cfgs' => '订单管理(!)?file=dops/a&mod=corder
评论(!)?file=dops/a&mod=crem',
    ),
    'm3pro' => 
    array (
      'pid' => 'm2pro',
      'title' => '产品管理',
      'deep' => '3',
      'cfgs' => '产品管理(!)?file=dops/a&mod=cargo
增加(!)cargo(!)jsadd',
    ),
    'm3cset' => 
    array (
      'pid' => 'm2pro',
      'title' => '分类设置',
      'deep' => '3',
      'cfgs' => '产品分类(!)?file=admin/catalog&mod=cargo(!)frame
设置(!)?file=apis/exp_order',
    ),
    'm2res' => 
    array (
      'pid' => 'm1main',
      'title' => '课程资源',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3kres' => 
    array (
      'pid' => 'm2res',
      'title' => '课程资源',
      'deep' => '3',
      'cfgs' => '课程资源(!)?file=dops/a&mod=keres
增加(!)keres(!)jsadd',
    ),
    'm3keres' => 
    array (
      'pid' => 'm2res',
      'title' => '评论设置',
      'deep' => '3',
      'cfgs' => '资源评论(!)?file=admin/catalog&mod=keres(!)
栏目(!)?file=admin/catalog&mod=keres(!)frame',
    ),
    'm2user' => 
    array (
      'pid' => 'm1main',
      'title' => '会员管理',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3058' => 
    array (
      'pid' => 'm2user',
      'title' => '个人会员',
      'deep' => '3',
      'cfgs' => '个人会员(!)?file=dops/a&mod=person
添加(!)person(!)jsadd',
    ),
    'm3060' => 
    array (
      'pid' => 'm2user',
      'title' => '企业会员',
      'deep' => '3',
      'cfgs' => '企业会员(!)?file=dops/a&mod=company
添加(!)company(!)jsadd',
    ),
    'm3062' => 
    array (
      'pid' => 'm2user',
      'title' => '政府机构',
      'deep' => '3',
      'cfgs' => '政府机构(!)?file=dops/a&mod=govern
添加(!)govern(!)jsadd',
    ),
    'm3064' => 
    array (
      'pid' => 'm2user',
      'title' => '非盈利组织',
      'deep' => '3',
      'cfgs' => '非盈利组织(!)?file=dops/a&mod=organize
添加(!)organize(!)jsadd',
    ),
    'm1info' => 
    array (
      'pid' => '0',
      'title' => '资讯',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2info' => 
    array (
      'pid' => 'm1info',
      'title' => '新闻介绍',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3news' => 
    array (
      'pid' => 'm2info',
      'title' => '新闻动态',
      'deep' => '3',
      'cfgs' => '新闻动态(!)?file=dops/a&mod=news
增加(!)news(!)jsadd',
    ),
    'm3nrem' => 
    array (
      'pid' => 'm2info',
      'title' => '新闻评论',
      'deep' => '3',
      'cfgs' => '新闻评论(!)?file=dops/a&mod=nrem(!)
栏目(!)?file=admin/catalog&mod=news(!)frame',
    ),
    'm3about' => 
    array (
      'pid' => 'm2info',
      'title' => '站点介绍',
      'deep' => '3',
      'cfgs' => '站点介绍(!)?file=dops/a&mod=about
增加(!)about(!)jsadd',
    ),
    'm2topic' => 
    array (
      'pid' => 'm1info',
      'title' => '专题新闻',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3topic' => 
    array (
      'pid' => 'm2topic',
      'title' => '专题新闻',
      'deep' => '3',
      'cfgs' => '专题新闻(!)?file=dops/a&mod=topic
增加(!)topic(!)jsadd',
    ),
    'm3torem' => 
    array (
      'pid' => 'm2topic',
      'title' => '评论栏目',
      'deep' => '3',
      'cfgs' => '新闻评论(!)?file=dops/a&mod=trem(!)
栏目(!)?file=admin/catalog&mod=topic(!)frame',
    ),
    'm1coms' => 
    array (
      'pid' => '0',
      'title' => '互动',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2indoc' => 
    array (
      'pid' => 'm1coms',
      'title' => '内部公文',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3indoc' => 
    array (
      'pid' => 'm2indoc',
      'title' => '公文管理',
      'deep' => '3',
      'cfgs' => '公文管理(!)?file=dops/a&mod=indoc
发布(!)indoc(!)jsadd',
    ),
    'm3inread' => 
    array (
      'pid' => 'm2indoc',
      'title' => '阅读记录',
      'deep' => '3',
      'cfgs' => '阅读记录(!)?file=dops/a&mod=inread
栏目(!)?file=admin/catalog&mod=indoc(!)frame	',
    ),
    'm3inrem' => 
    array (
      'pid' => 'm2indoc',
      'title' => '评论设置',
      'deep' => '3',
      'cfgs' => '<a href="?file=dops/a&mod=inrem">评论记录</a> 
- <a href="?file=apis/exp_indoc">设置</a>
',
    ),
    'm3inmem' => 
    array (
      'pid' => 'm2indoc',
      'title' => '公文会员',
      'deep' => '3',
      'cfgs' => '公文会员(!)?file=dops/a&mod=inmem
等级(!)?file=admin/grade&mod=inmem',
    ),
    'm2faqs' => 
    array (
      'pid' => 'm1coms',
      'title' => '问答系统',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3qadmin' => 
    array (
      'pid' => 'm2faqs',
      'title' => '管理发布',
      'deep' => '3',
      'cfgs' => '问答管理(!)?file=dops/a&mod=faqs
发布(!)faqs(!)jsadd',
    ),
    'm3qrtag' => 
    array (
      'pid' => 'm2faqs',
      'title' => '回复标签',
      'deep' => '3',
      'cfgs' => '<a href="?file=dops/a&mod=qarep">问答回复</a> 
- <a href="?file=dops/a&mod=qatag">标签</a>
',
    ),
    'm3qaset' => 
    array (
      'pid' => 'm2faqs',
      'title' => '统计更新',
      'deep' => '3',
      'cfgs' => '<a href="?file=apis/exp_qaset">问答统计</a> 
- <a href="?file=apis/exp_qaset&view=uset">更新</a>',
    ),
    'm2vote' => 
    array (
      'pid' => 'm1coms',
      'title' => '投票管理',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3vtadm' => 
    array (
      'pid' => 'm2vote',
      'title' => '管理发布',
      'deep' => '3',
      'cfgs' => '投票管理(!)?file=dops/a&mod=votes
发布(!)votes(!)jsadd',
    ),
    'm3voip' => 
    array (
      'pid' => 'm2vote',
      'title' => '选项记录',
      'deep' => '3',
      'cfgs' => '<a href="?file=dops/a&mod=votei">投票选项</a> 
- <a href="?file=dops/a&mod=votep">记录</a>',
    ),
    'm2else' => 
    array (
      'pid' => 'm1coms',
      'title' => '其他互动',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3gbook' => 
    array (
      'pid' => 'm2else',
      'title' => '网站留言管理',
      'deep' => '3',
      'cfgs' => '?file=dops/a&mod=gbook',
    ),
    'm1adv' => 
    array (
      'pid' => '0',
      'title' => '广告',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm1tool' => 
    array (
      'pid' => '0',
      'title' => '工具',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2sys' => 
    array (
      'pid' => 'm1tool',
      'title' => '系统工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3catch' => 
    array (
      'pid' => 'm2sys',
      'title' => '缓存静态',
      'deep' => '3',
      'cfgs' => '系统缓存(!)?file=admin/update
静态(!)?file=admin/static',
    ),
    'm3self' => 
    array (
      'pid' => 'm2sys',
      'title' => '个人资料',
      'deep' => '3',
      'cfgs' => '个人资料(!)?file=admin/uinfo
密码(!)?file=admin/uinfo&view=passwd',
    ),
    'm3env' => 
    array (
      'pid' => 'm2sys',
      'title' => '环境检测',
      'deep' => '3',
      'cfgs' => '<a href="?file=admin/ediy&part=binfo">基础环境</a> 
- <a href="?file=admin/ediy&part=check">检测</a>',
    ),
    'm3ediy' => 
    array (
      'pid' => 'm2sys',
      'title' => '配置搜索',
      'deep' => '3',
      'cfgs' => '<a href="?file=admin/ediy&part=exdiy">DIY配置</a> 
- <a href="?file=admin/ediy&part=search">搜索</a>',
    ),
    'm2data' => 
    array (
      'pid' => 'm1tool',
      'title' => '数据工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3plan' => 
    array (
      'pid' => 'm2data',
      'title' => '计划任务',
      'deep' => '3',
      'cfgs' => '计划任务(!)?file=apis/cron_plan
积分(!)?file=apis/jifen_plan',
    ),
    'm3share' => 
    array (
      'pid' => 'm2data',
      'title' => '分享同步',
      'deep' => '3',
      'cfgs' => '数据分享(!)?file=apis/exd_share
同步(!)?file=apis/exd_psyn',
    ),
    'm3data' => 
    array (
      'pid' => 'm2data',
      'title' => '采集导入',
      'deep' => '3',
      'cfgs' => '数据导入(!)?file=apis/exd_oimp
采集(!)?file=apis/exd_crawl',
    ),
    'm3seo' => 
    array (
      'pid' => 'm2data',
      'title' => '优化推送',
      'deep' => '3',
      'cfgs' => 'SEO优化(!)?file=apis/seo_push
推送(!)?file=apis/seo_push&pid=seo_pset',
    ),
    'm2fav' => 
    array (
      'pid' => 'm1tool',
      'title' => '我的收藏',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3favor' => 
    array (
      'pid' => 'm2fav',
      'title' => '收藏帮助',
      'deep' => '3',
      'cfgs' => '网址收藏(!)?file=dops/a&mod=adfavor&view=vself
帮助(!){$root}/dev.php(!)blank',
    ),
    'm1adm' => 
    array (
      'pid' => '0',
      'title' => '架设',
      'deep' => '1',
      'cfgs' => '',
    ),
    'm2stru' => 
    array (
      'pid' => 'm1adm',
      'title' => '超管架构',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3model' => 
    array (
      'pid' => 'm2stru',
      'title' => '模块架设',
      'deep' => '3',
      'cfgs' => '模块架设(!)?file=admin/groups
安装(!)?file=admin/upgrade&mod=install',
    ),
    'm3auser' => 
    array (
      'pid' => 'm2stru',
      'title' => '管理员:设置/添加',
      'deep' => '3',
      'cfgs' => '管理员(!)?file=dops/a&mod=adminer
添加(!)adminer(!)jsadd',
    ),
    'm3catalog' => 
    array (
      'pid' => 'm2stru',
      'title' => '栏目管理:文档/广告',
      'deep' => '3',
      'cfgs' => '<a href="?file=admin/catalog" title="文档栏目">栏目管理</a> 
- 
<a href="?file=admin/catalog&mod=adpic" title="广告栏目">广告</a>',
    ),
    'm3relat' => 
    array (
      'pid' => 'm2stru',
      'title' => '类别:管理/关联',
      'deep' => '3',
      'cfgs' => '类别管理(!)?file=admin/types
关联(!)?file=admin/relat',
    ),
    'm3amenu' => 
    array (
      'pid' => 'm2stru',
      'title' => '菜单导航配置',
      'deep' => '3',
      'cfgs' => '?file=admin/menus',
    ),
    'm3grade' => 
    array (
      'pid' => 'm2stru',
      'title' => '等级权限设置',
      'deep' => '3',
      'cfgs' => '?file=admin/grade',
    ),
    'm2api' => 
    array (
      'pid' => 'm1adm',
      'title' => '插件/接口',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3apisms' => 
    array (
      'pid' => 'm2api',
      'title' => '短信接口',
      'deep' => '3',
      'cfgs' => '短信发送(!)?file=apis/sms_send
记录(!)?file=apis/sms_logs',
    ),
    'm3apipay' => 
    array (
      'pid' => 'm2api',
      'title' => '支付记录',
      'deep' => '3',
      'cfgs' => '支付记录(!)?file=apis/pay_logs
接口(!)?file=apis/pay_logs&view=vcfgs',
    ),
    'm3apimail' => 
    array (
      'pid' => 'm2api',
      'title' => '邮件接口',
      'deep' => '3',
      'cfgs' => '邮件记录(!)?file=apis/mail_logs
接口(!)?file=apis/mail_logs&view=vcfgs',
    ),
    'm3apiwexin' => 
    array (
      'pid' => 'm2api',
      'title' => '微信接口',
      'deep' => '3',
      'cfgs' => '<a href="?file=awex/wex_apps">微信接口</a> 
- 
<a href="../a3rd/weixin_pay/wedemo.php">演示</a>',
    ),
    'm2adt' => 
    array (
      'pid' => 'm1adm',
      'title' => '超管工具',
      'deep' => '2',
      'cfgs' => '',
    ),
    'm3paras' => 
    array (
      'pid' => 'm2adt',
      'title' => '参数设置',
      'deep' => '3',
      'cfgs' => '核心参数(!)?file=admin/paras
扩展(!)?file=admin/parex',
    ),
    'm3dbs' => 
    array (
      'pid' => 'm2adt',
      'title' => '数据库管理',
      'deep' => '3',
      'cfgs' => '数据库(!){$root}/root/tools/adbug/dbadm.php(!)blank
管理(!)?file=admin/db_act',
    ),
    'm3safes' => 
    array (
      'pid' => 'm2adt',
      'title' => '安全日志',
      'deep' => '3',
      'cfgs' => '安全参数(!)?file=admin/paras&mod=prsafe
日志(!)?file=admin/rlogs&mod=syact',
    ),
    'm3upver' => 
    array (
      'pid' => 'm2adt',
      'title' => '更新升级',
      'deep' => '3',
      'cfgs' => '系统升级(!)?file=admin/upgrade
导入(!)?file=admin/upgrade&mod=import',
    ),
  ),
);
?>