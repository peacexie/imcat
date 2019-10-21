<?php
namespace imcat\base;

//use imcat\basReq;

class homeCtrl{

    public $ucfg = array();
    public $vars = array();
    public $start = '/root/tools/adbug/start.php';
    public $setup = '/root/tools/setup/index.php';

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->check();
    }

    // home检查
    function check(){
        // 检查路径
        if(empty($_SERVER['PATH_INFO']) && empty($_SERVER['QUERY_STRING'])){
            $npath = \imcat\devRun::prootGet();
            if($npath!=PATH_PROJ){
                header('Location:'.$npath.$this->start.'?FixProot');
                die('<!--FixProot-->');
            }
            if(!\imcat\devSetup::isSetuped()){
                header('Location:'.$npath.$this->setup.'?FixSetup');
                die('<!--FixSetup-->');
            }            
        }
        // 检查关闭(兼容?)
        $hclose = empty($_cbase['close_home']) ? 'index' : $_cbase['close_home'];
        if($hclose=='close'){
            \imcat\vopTpls::cinc("stpl/close_info",1);
            die();
        }elseif(substr($hclose,0,4)=='dir-'){
            $tpl = substr($hclose,4);
            $cfg = read('vopcfg.tpl','sy'); 
            header('Location:'.PATH_PROJ.$cfg[$tpl][1]); 
            die('<!--close/dir-->');
        }
    }

    function homeAct(){
        // header('Location:?user');
        $lang = \imcat\comCookie::oget('lang');
        $key = in_array($lang,array('en','cn')) ? $lang : 'cn';
        $vars['lang'] = $lang;
        return array('newtpl'=>"home/$key",'vars'=>$vars);
    }

    function startAct(){
        header('Location:'.PATH_PROJ.$this->start);
        die('<!--startAct-->');
    }

}
