<?php
namespace imcat\adm;

use imcat\basDebug;
use imcat\basEnv;
use imcat\basReq;
use imcat\basJscss;
use imcat\basLang;
use imcat\dopBase;
use imcat\glbConfig;
use imcat\glbDBObj;
use imcat\glbHtml;
use imcat\usrBase;
use imcat\usrPerm;

class _defCtrl{
    
    public $ucfg = array();
    public $vars = array();

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->_init();
    }

    function _init(){
        $user = usrBase::userObj('Admin');
        if($user->userFlag=='Guest') header('Location:'."?login");
    }

    function homeAct(){
        dump("Error:(mhome)!");
        die();
    }

    // _defAct
    function _defAct(){
        global $_cbase; 
        usrPerm::run();
        // init
        extract(basReq::sysVars()); 
        $mkv = $this->ucfg['mkv'];
        $_groups = glbConfig::read('groups'); 
        $db = glbDBObj::dbObj();
        $user = usrBase::userObj('Admin');
        $aurl = basReq::getUri(-2); 
        // body
        $this->head($_cbase);
        if($mod && isset($_groups[$mod])){
            $dop = new dopBase($_groups[$mod],'base_model');
            $cv = $dop->cv; 
        }
        $full = $this->flowFile();
        require $full;
        $this->foot($_cbase);
        die(); 
    }

    function flowFile(){
        include(DIR_VIEWS."/adm/frame/_inc-navbar.htm");
        $fp = str_replace('-','/',$this->ucfg['mkv']).'.php';
        $fext = "/extra/$fp";
        $file = "/flow/$fp";
        if(file_exists(DIR_ROOT.$fext)){
            echo "\n<!--inc:{root}$fext-->\n";
            return DIR_ROOT.$fext;
        }elseif(file_exists(DIR_IMCAT.$file)){
            echo "\n<!--inc:{imcat}$file-->\n";
            return DIR_IMCAT.$file;
        }else{
           die("<h3>File Not Found: <br>{root}$fext or<br>{imcat}$file</h3>"); 
        }
    }

    function head(){
        glbHtml::page(basLang::show('admin.adm_center').'-(sys_name)',1);
        eimp('initJs','jquery,jspop;comm;comm(-lang)');
        eimp('initCss','bootstrap,stpub,jstyle;comm');
        glbHtml::page('aumeta');
        echo '</head><body class="'.(basEnv::isMobile()?'mobbody':'pcbody').'">';
    }

    function foot($_cbase){
        echo "\n<!--inc:end-->\n"; 
        eimp('/~base/jslib/jq_base.js');
        eimp('/layer/layer.js','vendui');
        eimp('/bootstrap/js/bootstrap.min.js','vendui');
        if($_cbase['debug']['err_mode']){
            echo "<p class='tc pv20'>".basDebug::runInfo()."</p>\n";
        }
        echo "</body></html>\n";
        echo basJscss::jscode("$(function(){ admNavd(); setTimeout('jcronRun()',4700) });")."\n";
    }

}
