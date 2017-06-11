<?php
/*
start_sdict-pub.htm
*/ 
class startCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
    }

    function classAct(){
        $re['vars']['act'] = $act = req('act','root'); 
        if($act=='imps'){
            $root = DIR_CODE.'/adpt';
            $dirs = comFiles::listDir($root); 
            $subs = array_keys($dirs['dir']); 
        }elseif($act=='jslib'){
            $root = DIR_SKIN;
            $subs = array(
                '_pub/jslib',
                '_pub/a_jscss',
                'adm/b_jscss',
                'chn/b_jscss',
                'dev/b_jscss',
                'mob/b_jscss',
                'umc/b_jscss',
            ); 
        }else{
            $root = DIR_CODE.'/core';
            $dirs = comFiles::listDir($root); 
            $subs = array_keys($dirs['dir']);
        }

        if(req('min')){
            $_cfgm =  array(
                'core/blib' => 'bas',
                'core/clib' => 'com',
                'core/glib' => 'adm,glb,saf',
                'core/vops' => 'tag,vop',
                'core/dops' => 'dop,usr',
            );
        }else{
            include DIR_ROOT.'/cfgs/boot/cfg_load.php';
            $_cfgm = $_cfgs['acdir'];
        }
        $re['vars']['root'] = $root;
        $re['vars']['subs'] = $subs; 
        $re['vars']['_cfgm'] = $_cfgm; 
        $re['tplorg'] = $this->ucfg['tplname']; 
        return $re;
    }

    function dbtabAct(){
        $data = comFiles::get(DIR_DTMP."/store/dbdic-cn.cac_htm");
        if(!$data){
            $data = devBase::dbDict(); 
            comFiles::put(DIR_DTMP."/store/dbdic-cn.cac_htm",$data);
        }
        echo $data; 
        $_cbase['tpl']['tplpend'] = '_null_';
        return array('tplnull'=>1);
    }

    /*/ _empty
    function _emptyAct(){
        if(in_array($this->ucfg['key'],array('class','dbtab'))){
            return array('newtpl'=>$this->ucfg['tplname']);
        } // 不用使用-mob后缀
    }*/

}
