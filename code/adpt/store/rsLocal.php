<?php
namespace imcat;

// rsLocal-本地存储
class rsLocal{
    
    public static $objs = array();
    public $cfgs        = array();
    public $dir_ures    = '';
    
    // 移动:从临时文件夹移动(上传)到ftp远程
    function moveUres($org, $obj, $fmove=1){
        $obj_dir = $this->dir_ures.'/'.$obj;
        if($fmove){
            rename($org, $obj_dir);
        }
        return $this->cfgs['spre'].$obj.$this->cfgs['sfix'];
    }
    // 删除:
    function delFiles($dir){
        # local 目录 comStore 中已删除
        #$obj_dir = $this->dir_ures.'/'.$dir;
        #return comFiles::delDir($obj_dir, 1);
    }

    // 析构函数 - 设置参数
    function __construct($config=array()){
        $tcfg = read('store.types','ex');
        $this->cfgs = $tcfg['rsLocal'];
        $this->dir_ures = $this->cfgs['dir_ures'];
    }

}

