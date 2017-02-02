<?php

// 语言包:cn

$cfgs['grset_types'] = array(
    'pmadm'=>'Menu',
    'pmusr'=>'Member',
    'pfile'=>'Script',
    'pextra'=>'Extra',
);

// 字段类型
$cfgs['fcfg_type'] = array(
    '^group^data'  => '-Data Fields-',
    'input'  => 'Input',
    'select' => 'Select',
    'cbox'   => 'Checkbox',
    'radio'  => 'Radio',
    'text'  => 'Text',
    'file'  => 'File',
    'passwd' => 'Password',
    'hidden' => 'Hidden',
    '^group^control'  => '-Contral Fields-',
    'parts'  => 'Cnrl-Parts',
    'repeat' => 'Check Repeat',
);    
// 字段插件
$cfgs['fcfg_plug'] = array(
    'editor' => 'Html-Editor',
    'pics'   => 'Multi-Pics', //图集等
    'pick'   => 'Pick Info',
    'winpop' => 'Win-POP',
    'datetm' => 'Pick Date',
    'color'  => 'Color-Set',
    'map'    => 'Map-Piont',
);        
// 数据类型
$cfgs['fcfg_dbtype'] = array(
    'varchar' => 'varchar.Characters',
    'int'     => 'int.Integer',
    'float'   => 'float.Decimal',
    'text'    => 'text.64K Text',
    'mediumtext' => 'mtext.16M LongText',
    'nodb'    => 'nodb.NO-Save',    
);        
// 字段认证
$cfgs['fcfg_vreg'] = array(
    'fix:tel'   => 'Tel',
    'fix:email' => 'E-mail',
    'fix:uri'   => 'Url',
    'fix:file'  => 'File',
    'fix:image' => 'Pic',
    'key:'        => 'Key(var-fmt)',
    'fix:xid'   => 'Key(9999-md)',
    'tit:'       => 'Title',
    'n+i'       => 'Integer',
    'n-i'       => 'Integer(Negative)',
    'n+d'       => 'Decimal',
    'n-d'       => 'Decimal(Negative)',
    'str:'       => 'CommStr',
    'vimg:'       => 'AuthCode',        
);

$cfgs['fedit_numtype'] = array(
    'n+i'=>'Int', 
    'n-i'=>'Int(Negative)',
    'n+d'=>'Decimal', 
    'n-d'=>'Decimal(Negative)',
);
$cfgs['fedit_vline'] = array(
    '1'=>'Show One Line',
    '0'=>'Show same Line',
);
$cfgs['fedit_vltitle'] = array(
    '0'=>'Hide Title',
    '1'=>'Show Title',
);

$cfgs['model_deep'] = array(
    'docs'=>'Catalog Level',
    'types'=>'Types Level',
    'menus'=>'Menu Level',
);

$cfgs['advs_type'] = array(
    1=>'Text Links',
    2=>'Pics Links',
    3=>'Info.Block',
    4=>'Url Favor',
);

$cfgs['exdbase_mode'] = array(
    'modeVal'=>'Html Tag Mode',
    'modePos'=>'Html Point Mode',
    'modeArr'=>'tml TagArray Mode',
    'modePreg'=>'Regular Mode',
    'modeAttr'=>'Get Html-att Mode',
);
$cfgs['exdbase_ext'] = array(
    'url:fatch'=>'Get Remote Url Data',
    'save:image'=>'Save Remote Pic',
);

$cfgs['usrbase'] = array(
    'Null' => 'Login Fail, Null ID/PW!',
    'Forbid' => 'Login Fail, It is Locked User!',
    'noChecked' => 'Login Fail, ID/PW Error or NOT Checked!',
    'isLogin' => 'Already logged, Can NOT Loinin Repeat!',
    'OK' => 'Login OK!',
);

$cfgs['upload'] = array(
    "SUCCESS", //上传成功标记
    "File Size over upload_max_filesize Limit",
    "File Size over MAX_FILE_SIZE Limit",
    "File not fully Uploaded",
    "NO File Data",
    "Null File",
    "ERROR_TMP_FILE" => "Temp File Error",
    "ERROR_TMP_FILE_NOT_FOUND" => "Temp File NOT Found",
    "ERROR_SIZE_EXCEED" => "File Size over then Site set",
    "ERROR_TYPE_NOT_ALLOWED" => "File Type Disallow",
    "ERROR_CREATE_DIR" => "Creat Dir Error",
    "ERROR_DIR_NOT_WRITEABLE" => "Dir Cant Write",
    "ERROR_FILE_MOVE" => "File Save Error",
    "ERROR_FILE_NOT_FOUND" => "Upload File NOT Found",
    "ERROR_WRITE_CONTENT" => "File Write Error",
    "ERROR_UNKNOWN" => "Unknown Error",
    "ERROR_DEAD_LINK" => "Link is NOT Available",
    "ERROR_HTTP_LINK" => "NOT http Link",
    "ERROR_HTTP_CONTENTTYPE" => "Error contentType"
);

$cfgs['dbext'] = array(
    'advs' => 'Advs',
    'coms' => 'Coms',
    'docs' => 'Main',
    'types' => 'Types',
    'users' => 'Users',
);

$cfgs['dopmedia'] = array(
    'iframe' => 'iframe(InnerFrame)',
    'map' => 'map(MapPiont)',
    'swf' => 'swf(FlashMedia)',
    'audio'    => 'audio(AudioMedia)',
    'ckvdo'    => 'video(VideoMedia)',
    'vdojj'    => 'videojj(VideoMedia)',
);
//'flv'    => 'flv(Flash媒体文件)', // (合并到ckvdo)
//'bgsnd'  => 'bgsnd(背景音乐)', // 需要的化，专门添加字段

return $cfgs;

