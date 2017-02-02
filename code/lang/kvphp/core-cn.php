<?php

// 语言包:cn

return array(

    // sdev/exdBase.php
    'exdb_mode' => '-提取模式-',
    'exdb_exop' => '-扩展操作-',
    'exdb_rnote' => '规则说明',
    'exdb_orgtag' => '原始内容标记 ',
    'exdb_nres' => '当前批次执行结果：',
    'exdb_okn' => '成功执行[{val}]条',
    'exdb_next' => '{val}秒后执行下一批次…',
    'exdb_rok' => '执行完毕。',
    'exdb_res' => '执行结果：',
    'exdb_bugres' => '调试结果：',
    'exdb_orgdata' => '原始内容',

    // dops/dopUser.php
    'du_gradeo' => '-会员等级-',
    'du_userid' => '登陆名',
    'du_upass' => '密码',
    'du_gradet' => '等级',
    'du_empty' => '为空则不修改密码',
    'du_editng' => '修改失败：两次密码不一致！',
    'du_editok' => '修改成功',
    'du_erold' => '修改失败：旧密码错误',

    // dops/usrBase.php
    'usrb_ertimes' => '账号密码错误，错误次数：{val}次！',
    'usrb_erunknow' => '未知错误',

    // dops/usrMember.php
    'usrm_emsubj' => '邮件找回密码',
    'usrm_emtip' => '请登录邮件，根据提示找回密码。',
    'usrm_emeror' => '发邮件错误！请稍等再试，或联系管理员。',
    'usrm_eremail' => '账号-邮箱:参数错误！',

    // sdev/devApp.php
    'devapp_dferr' => '目录或文件名不规范',
    'devapp_dfnum' => '目录或文件名不能全为数字',
    'devapp_dfues' => '目录或文件名已经使用',
    'devapp_dfext' => '目录或配置已经存在',
    'devapp_dataerr' => '数据模型不规范',

    // sms 
    'sms_fail' => '失败!',
    'sms_errtel' => '号码不正确',
    'sms_errmsg' => '信息内容为空',
    'sms_charge0' => '系统余额为0,请联系管理员',
    'sms_charged' => '系统余额不足,请联系管理员',
    'sms_msenderr' => '群发失败',

    //sdev/updBase.php
    'updbase_lock1' => '已经锁定！重新升级 请找到文件：',
    'updbase_tip1' => '1. 先删除，并重新配置<升级包路径>；',
    'updbase_tip2' => '2. 或：修改[done=update]，重新升级',
    'updbase_back' => '[返回]',
    'updbase_setdirerr' => '设置目录[{val}]错误',
    'updbase_verbig' => '当前版本 高于或等于 升级包版本',
    'updbase_notwrite' => '可能目录[{val}]不可写！',
    'updbase_compare' => '[对比]',

    // sdev/devRun.php
    'devrun_tipr1' => '注意：根目录设置是空字符串,而不是/',
    'devrun_tipr2' => '注意：前面以/开头,后面不要/',
    'devrun_upd' => '刷新',
    'devrun_fixpararm' => '注意：刚才自动修正了参数：文件：',
    'devrun_file' => '文件：',
    'devrun_setpath' => '请设置路径：',
    'devrun_upding' => '点刷新继续操作……',
    'devrun_needenv' => '需要服务器环境',

    'devrun_my3a' => '需要开启[{val}]扩展，',
    'devrun_my3b' => '或修改文件：',
    'devrun_my3c' => '设置：',
    'devrun_my3d' => '选一个,且开启相应扩展',

    'devrun_gd2' => '需要开启GD2扩展，请设置php.ini：',
    'devrun_phpvbest' => '建议',
    'devrun_phpver' => 'PHP版本',
    'devrun_notwrite' => '不可写',
    'devrun_extendset' => ' - 不支持{val}扩展:请检查php.ini - ',
    'devrun_linkmysqlerr' => ' - 未连接服务器 - ',
    'devrun_tmfiles' => '文件太多,请设置目录缩小范围.',

    // sdev/devSetup.php
    'devsetup_deltip' => '已经安装！重新安装请先删除：',
    'devsetup_dt1' => '下',
    'devsetup_dt2' => '和',
    'devsetup_dt3' => '文件',
    'devsetup_donen' => '-已安装完第[{val}]步-',
    'devsetup_nosetup' => '（还未安装）',
    'devsetup_chkenv' => '请检查环境',
    'devsetup_chkdir' => '请检查目录',
    'devsetup_chkmysql' => '请检查Mysql数据库服务器',
    'devsetup_noframe' => '无表结构导入',
    'devsetup_runasright' => '执行结果如右...',

    // sdev/updInfo.php
    'updinfo_nowver' => '当前版本：',
    'updinfo_viewdown' => '查看官方下载',
    'updinfo_remver' => '（官方版本：{val}）',
    'updinfo_remerr' => '（官方版本：[获取数据错误]）',
    'updinfo_allspace' => '总空间',
    'updinfo_uesspace' => '[使用:{val}含文件和数据]',
    'updinfo_upd' => '更新',
    'updinfo_dir' => '目录',
    'updinfo_dbinfo' => '数据库',
    'updinfo_st1day' => '当天',
    'updinfo_st3day' => '3天',
    'updinfo_st7day' => '7天',
    'updinfo_stall' => '总计',

    'safcomm_vcode' => '点击输入框后见图片,<br>不分大小写',
    'rule_uname' => '字母开头,允许3-16字节<br>允许字母数字下划线',
    'rule_upass' => '允许6,15字节',
    'ie_low' => 'IE浏览器过低！建议你更换浏览器，如Chrome,Firefox,IE{val}}+！',
    'cfg_close' => '网站升级中，临时关闭!',
    'cfg_dggdcn' => '感谢dg.gd.cn提供主机空间',

    'opt_first' => '-请选择-',
    'nul_orgdata' => '无原始数据',
    'pub_title' => '发布',
    'no_rank' => '-无头衔-',
    'upd_comp' => '对比',
    'safcomm_vcoderr' => '认证码错误！',
    'devbase_clsrepeat' => '重复类名',

    'kid_minlen' => '请输入{val}+个字符！',
    'kid_keeped' => '[{val}]已被系统保留！',
    'kid_preused' => '前缀[{val}]已被系统保留！',
    'kid_ismodel' => '[{val}]为系统模型（已占用）！',

    'bcv_pic' => '[图]',
    'bcv_file' => '[附]',
    'bcv_set' => '设置',

    'msg_goto' => '我要去',
    'msg_jumpto' => '将自动到…',
    'msg_or' => '或',
    'msg_add' => '增加',
    'msg_edit' => '修改',
    'msg_eg' => '如:',

    'cupd_reprule' => '替换规则',
    'opay_order' => '订单',

    'vshow_uncheck' => '信息不存在或未被审核!',
    'vshow_1pagetag' => '每页只允许一个分页标签: 请检查如下模版,标签:',

    'seal_defstr' => '印章测试字符串',
    'seal_move' => '点击移开位置',
    'seal_mark' => '专用章',

    'push_ok' => '成功推送:',
    'push_ng' => '推送失败:',

    'vop_parerr' => '参数错误!',
    'vop_closemod' => '本模块关闭!',
    'vop_closecat' => '本栏目关闭!',
    'vop_st301dir' => '301跳转未生成静态',

    'vops_batres' => '当前批次执行结果：',
    'vops_dores' => '成功执行{val}条',
    'vops_3secnext' => '3秒后执行下一批次…',
    'vops_end' => '执行完毕。',
    'vops_res' => '执行结果：',

    'dbdict_refresh' => '刷新',
    'dbdict_title' => '数据库词典',
    'dbdict_table' => '数据表',
    'dbdict_field' => '字段',
    'dbdict_tab' => '表',
    'dbdict_extab' => '[扩展]表',

    // tester
    'test_name' => '中文名',
    'test_end' => '测试结束',
    'start_title' => '起始页',
    'view_times' => '浏览{val}次',

    'home' => '首页',

    'uname' => '用户名',
    'upass' => '密　码',
    'vcode' => '认证码',
    'submit' => '提交',

    // pager
    'page_First' => '首页',
    'page_Prev' => '上页',
    'page_Next' => '下页',
    'page_Last' => '尾页',
    
);

