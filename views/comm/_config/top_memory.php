<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$cover = db()->table('topic_items')->where("did='$did' AND dno='cover'")->find();
$flower = db()->table('topic_items')->where("did='$did' AND dno='flower'")->find();
$fclick = empty($flower['click']) ? '0' : $flower['click'];
$ftitle = empty($flower['title']) ? '送花' : $flower['title'];
$words = db()->table('topic_form')->where("did='$did' AND `show`=1")->order("kid desc")->limit(50)->select(); 

if(empty($this->view)){
    $arcs = db()->table('topic_items')->where("did='$did' AND part='arcs'")->order('top')->select();

    $fcfg = devTopic::cfg2arr($cpics);
    $pics = array();
    foreach ($fcfg as $k2=>$ktitle) {
        $pics[$k2] = devTopic::fmtRow($did,$k2,1,1);
    } //dump($pics);
}else{
    $detail = db()->table('topic_items')->where("did='$did' AND dno='$this->view'")->find();
    if(empty($detail)){
        die("Error [$this->view]");
    }
    if(is_numeric($this->view)){
        $text = extMkdown::pdext($detail['detail'],0);
    }else{
        $detail = comStore::revSaveDir($detail);
        $upics = basElm::line2arr($detail['detail'],0,";"); 
        //dump($detail); dump($upics);
    }
}

/*

*/