<?php
namespace imcat\rest;
include dirname(__FILE__).'/_defCtrl.php';
/*
    // 扩展类样例,
    // `_exdemo`替换为对应的模型id,文件名和类名一起更改
*/ 
class _exdemoCtrl extends _defCtrl{

    function __construct($ucfg=array(),$vars=array()){ 
        parent::__construct($ucfg,$vars);
        //
    }

    // function addAct(){} // 重写add方法

    // function editBefore(){} // 编辑前的扩展方法
    
    // function delAfter($id){} // 删除的扩展方法
  
}
