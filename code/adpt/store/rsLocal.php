<?php
// rsLocal-本地存储
class rsLocal{
    
    public $url = ''; 
    
    // 移动:
    static function moveUres($org,$obj){
        return rename($org,DIR_URES.'/'.$obj);
    }
    // 删除:
    static function delFiles($dir){
        return comFiles::delDir($dir,1);
    }

}

