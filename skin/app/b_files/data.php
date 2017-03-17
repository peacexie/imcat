<?php
(!defined('RUN_INIT')) && die('No Init');

$dm = req('dm','');
if(empty($dm) || empty($_groups[$dm])){ 
    $this->vars = $this->error("Error-mod(data):{$dm}");
    $this->view('~'); 
}
$pid = $_groups[$dm]['pid']; // docs,users,coms
$_cfg = read($dm); 
$_tmp = array(
    'docs' => 'dopDocs',
    'users'=> 'dopUser',
    'coms' => 'dopComs',
); 

$_cls = $_tmp[$pid]; 
$dop = new $_cls($_cfg); 
unset($_cfg,$_tmp,$_cls);

$kid = req($dop->_kid,'');
$isadd = $kid ? 0 : 1;
if(!$isadd) $_POST['fm'][$dop->_kid] = $kid;

$dop->svPrep();
$fp = vopTpls::pinc("c_mod/data-$dm"); // 扩展:c_mod/data-demo

if(file_exists($fp)){
    include($fp); 
}elseif($pid=='docs'){

    if(!empty($isadd)){ 
        $dop->svAKey(); $did = $dop->fmu['did'] = $dop->fmv['did'];
        $db->table($dop->tbid)->data($dop->fmv)->insert(); 
        $db->table($dop->tbext)->data($dop->fmu)->insert(0); 
    }else{ 
        $did = $dop->svEKey();
        $db->table($dop->tbid)->data($dop->fmv)->where("did='$did'")->update();
        $dop->fmu['did'] = $did;
        $db->table($dop->tbext)->data($dop->fmu)->replace(0);
    }

}elseif($pid=='users'){

    if(!empty($isadd)){ 
        $dop->svAKey();
        $dop->svAccount('add'); 
        $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    }else{ 
        $uid = $dop->svEKey();
        $dop->svAccount('edit');
        $db->table($dop->tbid)->data($dop->fmv)->where("uid='$uid'")->update();
    }

}elseif($pid=='coms'){

    if(!empty($isadd)){ 
        $dop->svAKey();
        $dop->svPKey('add');
        $db->table($dop->tbid)->data($dop->fmv)->insert(); 
    }else{ 
        $cid = $dop->svEKey();
        $dop->svPKey('edit');
        $db->table($dop->tbid)->data($dop->fmv)->where("cid='$cid'")->update();
    }

}

$dop->svEnd($uid); 
//basMsg::show("$actm".lang('flow.dops_ok')); 

$kid = $dop->_kid;
$vars = array(
    'errno' => 0,
    'state' => '',
    'msg' => "$dm-",
    'res' => array(
        '_kid' => $kid,
        '_kval' => $$kid,
    ),
);

/*
* 使用场合：
 - 用于与其他系统整合，如：
 - 其他系统（App,Web）等增加/修改一条资料，同时同步此资料到本系统；
 - 其他系统处理好数据后，POST一条资料到此接口(如：curl)；
* 规范：
 - 资料格式：POST一个fm数组，$fm['本系统字段名']=其他系统对应值，如：$fm['title']=$subject;
 - 如是单选，多选等项目，则要把对方系统的值转化为本系统的值，
 - 把对方系统的值转化为本系统的值，也可通过本系统扩展完成，那对方系统不用处理
 - 如：对方系统`6`表示推荐，本系统`hot`表示推荐，则要转化为本系统的`hot`
* 关键参数：
 - 参数dm表示本系统模块ID，
 - 带了如did,uid,cid等参数表示更新数据
 - 本系统每次处理完毕，会返回: res._kid, res._kval等参数，如：res._kid='did', res._kval='2015-69-ac7n'
 - 必要的化，请第三方程序自行记录，用于后续修改资料，同步更新到本系统资料；
 - 下次如第三方修改资料，如上述，除了POSTfm数组，另请附加`did=2015-69-ac7n`资料到地址栏，实现本系统资料更新。
* 扩展：
 - 如扩展脚本处理，则添加文件，c_mod/data-$dm.php，参考上面代码改写
 - 如扩展demo模块，则添加 c_mod/data-demo.php 文件
*/
