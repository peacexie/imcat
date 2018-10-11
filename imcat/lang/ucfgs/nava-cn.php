<?php

// {root} 表示项目根目录 

$cfgs['faqs'] = array(
    'apis-exp_qaset&view=list' => '问答统计',
    'apis-exp_qaset&view=uset' => '设置更新',
);

$cfgs['indoc'] = array(
    'apis-exp_indoc&pid=indoc_tpl' => '短信模板',
    'apis-exp_indoc&pid=indoc_ext' => '扩展设置',
);

$cfgs['exdiys'] = array(
    //'admin-ediy&part=exdiy&dkey=tpls' => '模板配置',
    'admin-ediy&part=exdiy&dkey=skin' => '模板配置',
    'admin-ediy&part=exdiy&dkey=cfgs' => '系统参数',
    //'admin-ediy&part=exdiy&dkey=dtmp' => 'dtmp参数',
    'admin-ediy&part=exdiy&dkey=runs' => '入口配置',
); 

$cfgs['envdiy'] = array(
    'admin-ediy&part=binfo' => '基础环境',
    'admin-ediy&part=check' => '环境检测',
    'admin-ediy&part=exdiy' => 'DIY配置',
    'admin-ediy&part=reset' => '系统重置',
    'admin-ediy&part=search' => '搜索器',
);   

$cfgs['upd_vers'] = array( 
    'admin-upgrade&mod=upvnow' => '升级当前系统',
    'admin-upgrade&mod=import' => '导入旧版数据',
    //'admin-upgrade&mod=install' => '安装更新模块',
    'admin-upgrade&mod=extend' => '贴心扩展[Extend]',
);

$cfgs['cron_plan'] = array(
    'apis-cron_plan' => '计划任务',
    'apis-hook_plan' => '监听钩子',
    'apis-jifen_plan' => '积分配置',
);
$cfgs['cron_jifen'] = array(
    'apis-jifen_plan&pid=jifen_grade' => '积分等级',
    'apis-jifen_plan&pid=jifen_model' => '积分设置',
    'apis-jifen_plan&pid=jifen_logs' => '积分记录',
);

$cfgs['exd_crsh'] = array(
    'apis-exd_crawl' => '数据采集',
    'apis-exd_share' => '数据分享',
);
$cfgs['exd_share'] = array(
    'apis-exd_share&view=list' => '分享DIY',
    'apis-exd_share&view=set' => '分享配置',
);

$cfgs['seo_push'] = array( 
    'apis-seo_push&pid=seo_sitemap' => 'Sitemap',
    'apis-seo_push&pid=seo_pset' => 'Push设置',
    'admin-rlogs&sfid=act&sfop=eq&sfkw=bpushRun&frame=1' => 'Push记录',
);

$cfgs['pay'] = array(
    'apis-pay_logs&view=vlist' => '支付记录',
    'apis-pay_logs&view=vcfgs' => '支付配置',
);
$cfgs['mail'] = array(
    'apis-mail_logs&view=vlist' => '邮件记录',
    'apis-mail_logs&view=vcfgs' => '邮件配置',
);

$cfgs['sms'] = array(
    'apis-sms_send' => '短信发送',
    'apis-sms_logs&part=slogs' => '发送记录',
    'apis-sms_logs&part=charge' => '充值记录',
);

$cfgs['logs'] = array(
    'admin-rlogs&mod=syact' => '系统日志',
    'admin-rlogs&mod=detmp' => '调试记录',
    //'admin-rlogs&part=dtmp' => '系统文件',
    'admin-rlogs&part=dbsql' => 'SQL优化',
);

$cfgs['dba'] = array( 
    'admin-db_act&view=list' => '数据表操作',
    'admin-db_act&view=rem' => '数据表备注',
    'admin-db_act&view=runsql' => '运行SQL',
    'admin-rlogs&part=dbsql' => 'SQL优化',
);

$cfgs['ordcn'] = array(
    'apis-exp_order&pid=paymode_cn' => '付款方式',
    'apis-exp_order&pid=devmode_cn' => '配送地区',
    'apis-exp_order&pid=timmode_cn' => '送货时间',
    'apis-exp_order&pid=logmode_cn' => '物流方式',
);
$cfgs['orden'] = array(
    'apis-exp_order&pid=paymode_en' => 'Pay Mode',
    'apis-exp_order&pid=devmode_en' => 'Ship Area',
    'apis-exp_order&pid=timmode_en' => 'Receive Time',
    'apis-exp_order&pid=logmode_en' => 'Logistics Mode',
);
$cfgs['downs'] = array(
    'apis-exp_down&pid=down_speci' => '规格',
    'apis-exp_down&pid=wechat_gift' => '礼品',
    'apis-exp_down&pid=user_xxx2' => 'xxx2',
    'apis-exp_down&pid=user_xxx3' => 'xxx3',
);

$cfgs['ordnav'] = array(
    'dops-a&mod=corder' => '订单管理',
    'dops-a&mod=cocar' => '购物车',
    'dops-a&mod=coitem' => '订单项',
);

return $cfgs;
