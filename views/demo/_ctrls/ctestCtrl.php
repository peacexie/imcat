<?php
namespace imcat\demo;

use imcat\basDebug;
use imcat\basOut;
use imcat\basKeyid;
use imcat\glbHtml;

/*
*/ 
class ctestCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        //echo "__construct<br>\n";
    }

    // 
    function homeAct(){
        $rnd = 's'.basKeyid::kidRand('',5);
        $arr = array('hello','json','jsonp','xml','tplorg',$rnd,);
        $re['vars']['arr'] = $arr; // 变量
        $re['newtpl'] = 'umod/step-vhome'; // 模板
        return $re;
    }

    // 'hello' 
    function helloAct(){
        $name = req('name','Imcat');
        $re['vars']['name'] = $name; // 变量
        $re['newtpl'] = 'umod/step-vhello'; // 模板
        return $re;
    }

    function jsonAct(){
        $vars = array('tmpv1'=>'abc-v1','val2'=>'def-v2');
        $vars['ucfg'] = $this->ucfg;
        die(basOut::outJson($vars));
    }
    function jsonpAct(){
        $vars = array('tmpv1'=>'abc-jsonp1','val2'=>'def-jsonp2');
        die(basOut::outJsonp($vars));
    }
    function xmlAct(){
        $vars = array('tmpv1'=>'abc-xml1','val2'=>'def-xml2');
        die(basOut::outXml($vars));
    }

    // 'tplorg' 
    function tplorgAct(){
        $name = req('name','Imcat');
        $re['vars']['name'] = $name; // 变量
        $re['tplorg'] = 'umod/step-vtplorg'; // 模板
        return $re;
    }

    // _defAct
    function _defAct(){
        echo glbHtml::page('init');
        echo "<p>_defAct(这里不需要模板,这里显示就完了) <br>--- 用了空方法,用['tplnull'=>1]返回</p>\n";
        echo basDebug::runInfo();
        // die()
        return array('tplnull'=>1);
    }
    
    // _detailAct
    function _detailAct(){
        echo '_detailAct';
        // die()
        return array('tplnull'=>1);
    }

}
