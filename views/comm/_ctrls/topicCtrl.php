<?php
namespace imcat\comm;

// 专题
class topicCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    // _detailAct
    function _detailAct(){
        global $_cbase;
        $m = $this->ucfg['mod'];
        $k = $this->ucfg['key'];
        $v = $this->ucfg['view'];
        if($v=='cfgs' && $this->ucfg['type']=='detail'){
            $tpl = 'topic/book/all~cfgs'; // book/all~cfgs
        }elseif(empty($this->vars['tplname'])){
            $tpl = $this->ucfg['tplname'];
        }else{
            $dir = "/{$_cbase['tpl']['vdir']}/topic";
            $tpl = $this->vars['tplname'];
            if($v){
                if(file_exists(DIR_VIEWS."$dir/$tpl~$v.htm")){
                    $tpl = "$tpl~$v"; // book/
                }else{
                    $tpl = "$tpl~detail";
                }
            } // ?topic.2015-9c-p481.vtechs/7awse21
            $tpl = 'topic/'.$tpl; 
        } //echo "(($tpl))";  
        $re['newtpl'] = $tpl; // 模板
        return $re;
    }

    // _defAct
    function xxx_defAct(){
        $re['newtpl'] = 'topic/_index/stype'; // 模板
        return $re;
    }
    
}
