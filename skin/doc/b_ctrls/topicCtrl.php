<?php
/*
2018-04:处理跳转,6个月后可删除
*/ 
class topicCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    function homeAct(){
        $this->_defAct();
    }

    // _detailAct
    function _detailAct(){
        $mkv = 'topic.'.$this->ucfg['key'];
        $url = surl("chn:$mkv");
        header("Location: $url");
        die($url);
    }

    // _defAct
    function _defAct(){
        $url = surl("chn:topic");
        header("Location: $url");
        die($url);
    }
    
}
