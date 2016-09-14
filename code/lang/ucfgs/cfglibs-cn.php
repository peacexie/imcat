<?php

// 语言包:cn

$cfgs['grset_types'] = array(
	'pmadm'=>'菜单',
	'pmusr'=>'会员',
	'pfile'=>'脚本',
	'pextra'=>'附加',
);

// 字段类型
$cfgs['fcfg_type'] = array(
	'^group^data'  => '-数据字段-',
	'input'  => '输入框',
	'select' => '下拉选择',
	'cbox'   => '多选框',
	'radio'  => '单选按钮',
	'text'  => '文本框',
	'file'  => '文件域',
	'passwd' => '密码域',
	'hidden' => '隐藏域',
	'^group^control'  => '-控制字段-',
	'parts'  => '表单分段',
	'repeat' => '重复检查',
);	
// 字段插件
$cfgs['fcfg_plug'] = array(
	'editor' => 'Html编辑器',
	'pics'   => 'Pics文件组', //图集等
	'pick'   => 'Pick资料选取',
	'winpop' => '弹窗选择',
	'datetm' => '日期选择',
	'color'  => '颜色设置',
	'map'	=> '地图标点',
);		
// 数据类型
$cfgs['fcfg_dbtype'] = array(
	'varchar' => 'varchar.字符',
	'int'	 => 'int.整型',
	'float'   => 'float.浮点',
	'text'	=> 'text.64K长文本',
	'mediumtext' => 'mtext.16M长文本',
	'nodb'	=> 'nodb.不保存',	
);		
// 字段认证
$cfgs['fcfg_vreg'] = array(
	'fix:tel'   => '电话号码',
	'fix:email' => '邮件地址',
	'fix:uri'   => 'Url地址',
	'fix:file'  => '文件',
	'fix:image' => '图片',
	'key:'	  => 'Key(变量形式)',
	'fix:xid'   => 'Key(9999-md)',
	'tit:'	  => '标题',
	'n+i'	   => '正整数',
	'n-i'	   => '整数(含负)',
	'n+d'	   => '小数',
	'n-d'	   => '小数(含负)',
	'str:'	  => '普通文本',
	'vimg:'	 => '认证码',		
);

$cfgs['fedit_numtype'] = array(
	'n+i'=>'整数', 
	'n-i'=>'整数(含负)',
	'n+d'=>'小数', 
	'n-d'=>'小数(含负)',
);
$cfgs['fedit_vline'] = array(
	'1'=>'独立行显示',
	'0'=>'同行显示',
);
$cfgs['fedit_vltitle'] = array(
	'0'=>'隐藏提示',
	'1'=>'显示提示',
);

$cfgs['model_deep'] = array(
	'docs'=>'栏目级数',
	'types'=>'类别级数',
	'menus'=>'菜单深度',
);

$cfgs['advs_type'] = array(
	1=>'文字连接',
	2=>'图片连接',
	3=>'信息区块',
	4=>'网址收藏',
);

$cfgs['exdbase_mode'] = array(
	'modeVal'=>'Html标签模式',
	'modePos'=>'Html定点模式',
	'modeArr'=>'tml标签数组模式',
	'modePreg'=>'正则取数组模式',
	'modeAttr'=>'提取Html属性模式',
);
$cfgs['exdbase_ext'] = array(
	'url:fatch'=>'提取远程Url数据',
	'save:image'=>'提取远程图片到本地',
);

$cfgs['usrbase'] = array(
	'Null' => '登录失败，账号密码为空！',
	'Forbid' => '登录失败，锁定状态禁止登录！',
	'noChecked' => '登录失败，账号密码错误或未审核！',
	'isLogin' => '已经登录，不能重复登录！',
	'OK' => '登录成功！',
);

$cfgs['upload'] = array(
	"SUCCESS", //上传成功标记
	"文件大小超出 upload_max_filesize 限制",
	"文件大小超出 MAX_FILE_SIZE 限制",
	"文件未被完整上传",
	"没有文件被上传",
	"上传文件为空",
	"ERROR_TMP_FILE" => "临时文件错误",
	"ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
	"ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
	"ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
	"ERROR_CREATE_DIR" => "目录创建失败",
	"ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
	"ERROR_FILE_MOVE" => "文件保存时出错",
	"ERROR_FILE_NOT_FOUND" => "找不到上传文件",
	"ERROR_WRITE_CONTENT" => "写入文件内容错误",
	"ERROR_UNKNOWN" => "未知错误",
	"ERROR_DEAD_LINK" => "链接不可用",
	"ERROR_HTTP_LINK" => "链接不是http链接",
	"ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确"
);

$cfgs['dbext'] = array(
	'advs' => '广告',
	'coms' => '互动',
	'docs' => '主',
	'types' => '分类',
	'users' => '会员',
);

$cfgs['dopmedia'] = array(
	'iframe' => 'iframe(内框架)',
	'map' => 'map(地图)',
	'swf' => 'swf(Flash媒体文件)',
	'audio'	=> 'audio(音频媒体文件)',
	'ckvdo'	=> 'video(视频媒体文件)',
);
//'flv'	=> 'flv(Flash媒体文件)', // (合并到ckvdo)
//'bgsnd'  => 'bgsnd(背景音乐)', // 需要的化，专门添加字段

return $cfgs;

