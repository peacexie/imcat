<?php
namespace imcat;

// rsAlioss-阿里云存储
class rsAlioss{
    
    public static $objs = array();
    public $cfgs        = array();
    public $dir_ures    = '';

    public $oss         = null;
    
    // 移动:从临时文件夹移动(上传)到oss远程
    function moveUres($org, $obj, $fmove=1){
        $obj_dir = $this->dir_ures.'/'.$obj;
        if($fmove){
            $res = $this->oss->fileUpload($org, $obj_dir);
        }
        return $this->cfgs['spre'].$obj.$this->cfgs['sfix'];
    }
    // 删除:
    function delFiles($dir){
        $obj_dir = $this->dir_ures.'/'.$dir;
        $this->oss->delDir($obj_dir);
    }

    // 析构函数 - 设置参数
    function __construct($config=array()){
        $tcfg = read('store.types','ex');
        $this->cfgs = $tcfg['rsAlioss'];
        $this->dir_ures = $tcfg['rsAlioss']['dir_ures'];
        $this->oss = new aliOss();
    }

}

/*

*/
