<?php
// 架构相关设置

### 存储格式
$_sy_frame['resfmt'] = array( 
    '0' => array('about'), // mod/yyyy-md-noid
    '1' => array('news'), // mod/yyyy/md-noid 默认
    '2' => array('cargo'), // mod/yyyy-md/noid
    //'3' => array('demo'), // mod/yyyy/md/noid
    '6' => array('demo'), // mod-yy/md-noid 
);    


### 扩展参数(按栏目/等级扩展字段;需要自行添加对应主表字段)
$_sy_frame['expars'] = array( 
    'catid' => array('demo','about'), // ,'cargo'
    'grade' => array('company','govern','organize'),
);    

