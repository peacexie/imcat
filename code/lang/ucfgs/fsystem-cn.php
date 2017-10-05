<?php

return array(

    'kid'=>'标识ID','did'=>'文档ID','uid'=>'会员ID','cid'=>'交互ID','aid'=>'广告ID',
    'pid'=>'父ID','dno'=>'序列ID','uno'=>'序列ID','cno'=>'序列ID', 'ano'=>'序列ID','model'=>'模块ID',
    'kno'=>'序列ID', 'rel_user'=>'相关会员', 
    
    'title' =>'标题','catid'=>'栏目','grade' =>'会员等级', 'static'=>'静态目录',
    'enable'=>'状态','show'=>'显示', 
    'appid' =>'appid', 'appsecret' =>'appsecret', 'token' =>'token', 
    'type' =>'类型', 'openid' =>'openid', 'ticket' =>'ticket',
    
    'top' =>'显示顺序', 'enable'=>'状态', 'etab'=>'扩展表', 
    'char'=>'头字母', 'deep'=>'最大级别数', 'frame'=>'结构性', 
    'icon'=>'图标', 'cfgs'=>'配置数组', 'note'=>'备注', 
    
    'aip'  =>'添加IP','atime'=>'添加时间','auser'=>'添加者',
    'eip'  =>'修改IP','etime'=>'修改时间','euser'=>'修改者',
    
    'sid'  =>'会话ID','sip'  =>'会话IP','stime'=>'会话时间',
    'aua'  =>'UserAgent','sua'=>'UserAgent',
    
    'pmod' =>'父模型ID', 'uname'=>'用户名', // 'upass'=>'用户密码', 'umods'=>'用户模型', //''=>'', 
    'errno'=>'Errno', 'url'=>'Url', 'page'=>'Page', 'act'=>'Action', 
    
    'ufrom'=>'From', 'uto'=>'To', 
    'api'=>'API', 'stat'=>'状态', 
    'amount'=>'金额', 'spid'=>'SphinxID', 'zzz'=>'zzz', 
    
    'dbdef'=>'db默认值', 'dblen'=>'db长度', 'dbtype'=>'db类型', 
    'fmexstr'=>'表单插件参数', 'fmextra'=>'表单元素插件', 
    'fmline'=>'表单元素是否独立行显示', 'fmsize'=>'表单元素大小', 'fmtitle'=>'表单元素是否显示标题(fmline=0有效)', 
    'vmax'=>'认证最大值', 'vreg'=>'认证规则', 'vtip'=>'认证提示', 
    
    'exp_s01'=>'扩展参数-select-1', 'exp_s02'=>'扩展参数-select-2', 'exp_s03'=>'扩展参数-select-3',  'exp_s04'=>'扩展参数-select-4',  'exp_s05'=>'扩展参数-select-5', 
    'exp_i01'=>'扩展参数-input-1', 'exp_i02'=>'扩展参数-input-2', 'exp_i03'=>'扩展参数-input-3', 'exp_i04'=>'扩展参数-input-4', 
    'exp_m01'=>'扩展参数-checkbox-1', 'exp_m02'=>'扩展参数-checkbox-2', 'exp_m03'=>'扩展参数-checkbox-3', 
    'exp_t01'=>'扩展参数-text-1', 'exp_t02'=>'扩展参数-text-2',
    
    'orgtg1'=>'采集标记', 'orgtg2'=>'采集标记', 'orgtg3'=>'采集标记', 'orgtg4'=>'采集标记', 
    'orgtg5'=>'采集标记', 'orgtg6'=>'采集标记', 'orgtg7'=>'采集标记', 
    
    '_stabs' => array(
        'base_catalog'=>'(系统)栏目表', 'base_fields'=>'(系统)字段配置表', 'base_grade'=>'(系统)用户等级表', 
        'base_menu'=>'(系统)菜单表', 'base_model'=>'(系统)[群组/模块]表', 'base_paras'=>'(系统)参数表', 
        'bext_dbdict'=>'(扩展)数据库词典表', 'bext_relat'=>'(扩展)类别关联表', 'bext_fields'=>'(扩展)类别字段表', 
        'bext_paras'=>'(扩展)参数表', 'bext_cron'=>'(扩展)计划任务表',  
        
        'exd_crawl'=>'(数据)采集表', 'exd_crlog'=>'(数据)采集记录', 
        'exd_oilog'=>'(数据)导入表', 'exd_oimp'=>'(数据)导入记录', 
        'exd_pslog'=>'(数据)同步表', 'exd_psyn'=>'(数据)同步记录', 
        'exd_sfield'=>'(数据)字段配置',  
        
        'init_advs'=>'(初始)[广告]表', 'init_coms'=>'(初始)[互动]表', 'init_dext'=>'(初始)[文档]扩展表', 
        'init_docs'=>'(初始)[文档]主表', 'init_types'=>'(初始)[分类]表', 'init_users'=>'(初始)[会员]表', 
        'types_common'=>'(常规)分类表', 'users_uacc'=>'(会员)账户表', 'users_uppt'=>'(会员)通行证表',
        'active_admin'=>'(管理员)会话表', 'active_online'=>'(会员)会话表', 'active_session'=>'(Session)会话表', 
        
        'logs_dbsql'=>'(sql)调试记录表', 'logs_detmp'=>'(用户)调试记录表', 
        'logs_syact'=>'(系统)调试记录表', 'logs_jifen'=>'(积分)操作记录', 
        'plus_emsend'=>'(邮件)发送记录', 'plus_paylog'=>'(支付)历史记录', 'plus_sphinx'=>'(Sphinx)MaxID', 
        'plus_smcharge'=>'(短信)充值记录', 'plus_smsend'=>'(短信)发送记录', 

        'token_limit'=>'Token频率限制', 'token_rest'=>'Token账户', 
        'token_store'=>'Token存储(外部)', 'token_turl'=>'Token短链接', 
        
        'wex_apps'=>'(微信)配置表', 'wex_menu'=>'(微信)菜单表', 'wex_keyword'=>'(微信)关键字表', 'wex_qrcode'=>'(微信)二维码表', 
        'wex_locate'=>'(微信)地理位置表', 'wex_msgget'=>'(微信)接收信息', 'wex_msgsend'=>'(微信)发送信息',
    ),
    
);
