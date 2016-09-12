<?php

// {root} 表示项目根目录 

$cfgs['faqs'] = array(
	'apis/exp_qaset&view=list' => 'Faq Stat.',
	'apis/exp_qaset&view=uset' => 'Set/Upd',
);

$cfgs['indoc'] = array(
	'apis/exp_indoc&pid=indoc_tpl' => 'Sms Tpl.',
	'apis/exp_indoc&pid=indoc_ext' => 'Ext/Set',
);

$cfgs['exdiys'] = array(
	'admin/ediy&part=exdiy&dkey=tpls' => 'Tpl Set',
	'admin/ediy&part=exdiy&dkey=skin' => 'Tpl Style',
	'admin/ediy&part=exdiy&dkey=cfgs' => 'Sys Parms',
	'admin/ediy&part=exdiy&dkey=runs' => 'Entry Set',
); 

$cfgs['envdiy'] = array(
	'admin/ediy&part=binfo' => 'Base Env.',
	'admin/ediy&part=check' => 'Env Debug',
	'admin/ediy&part=exdiy' => 'DIYSet',
	'admin/ediy&part=reset' => 'Sys.Rest',
	'admin/ediy&part=search' => 'Search',
);   

$cfgs['upd_vers'] = array( 
	'admin/upgrade&mod=upvnow' => 'Update Now System',
	'admin/upgrade&mod=import' => 'Import Old Data',
);

$cfgs['cron_plan'] = array(
	'apis/cron_plan' => 'Plan Task',
	'apis/jifen_plan' => 'Points Set',
);
$cfgs['cron_jifen'] = array(
	'apis/jifen_plan&pid=jifen_grade' => 'Points Grade',
	'apis/jifen_plan&pid=jifen_model' => 'Points Set',
	'apis/jifen_plan&pid=jifen_logs' => 'Points Logs',
);

$cfgs['exd_psyn'] = array(
	'apis/exd_share' => 'Data Share',
	'apis/exd_psyn' => 'Data Syn.',
);
$cfgs['exd_share'] = array(
	'apis/exd_share&view=list' => 'ShareDIY',
	'apis/exd_share&view=set' => 'ShareSet',
);

$cfgs['exd_oimp'] = array(
	'apis/exd_oimp' => 'Data Import',
	'apis/exd_crawl' => 'Data Crawl',
);

$cfgs['seo_push'] = array( 
	'apis/seo_push&pid=seo_sitemap' => 'Sitemap',
	'apis/seo_push&pid=seo_pset' => 'PushSet',
	'admin/rlogs&sfid=act&sfop=eq&sfkw=bpushRun&frame=1' => 'PushLogs',
);

$cfgs['pay'] = array(
	'apis/pay_logs&view=vlist' => 'Pay Logs',
	'apis/pay_logs&view=vcfgs' => 'Pay Set',
);
$cfgs['mail'] = array(
	'apis/mail_logs&view=vlist' => 'Email Logs',
	'apis/mail_logs&view=vcfgs' => 'Email Set',
);

$cfgs['sms'] = array(
	'apis/sms_send' => 'Sms Send',
	'apis/sms_logs&part=slogs' => 'Send Logs',
	'apis/sms_logs&part=charge' => 'Charge Logs',
);

$cfgs['logs'] = array(
	'admin/rlogs&mod=syact' => 'Sys Logs',
	'admin/rlogs&mod=detmp' => 'Debug Logs',
	'admin/rlogs&part=dbsql' => 'SQL Optimizing',
);

$cfgs['dba'] = array( 
	'admin/db_act&view=list' => 'db Table',
	'admin/db_act&view=rem' => 'db Remarks',
	'admin/rlogs&part=dbsql' => 'SQL Optimizing',
);

$cfgs['ordcn'] = array(
	'apis/exp_order&pid=paymode_cn' => '付款方式',
	'apis/exp_order&pid=devmode_cn' => '配送地区',
	'apis/exp_order&pid=timmode_cn' => '送货时间',
	'apis/exp_order&pid=logmode_cn' => '物流方式',
);
$cfgs['orden'] = array(
	'apis/exp_order&pid=paymode_en' => 'Pay Mode',
	'apis/exp_order&pid=devmode_en' => 'Ship Area',
	'apis/exp_order&pid=timmode_en' => 'Receive Time',
	'apis/exp_order&pid=logmode_en' => 'Logistics Mode',
);

$cfgs['ordnav'] = array(
	'dops/a&mod=corder' => 'Order',
	'dops/a&mod=cocar' => 'OrdCar',
	'dops/a&mod=coitem' => 'OrdItem',
);

return $cfgs;
