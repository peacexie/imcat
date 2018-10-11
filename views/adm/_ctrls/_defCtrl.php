<?php
namespace imcat\adm;

use imcat\basDebug;
use imcat\basEnv;
use imcat\basReq;
use imcat\basJscss;
use imcat\basLang;
use imcat\dopBase;
use imcat\comParse;
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
        usrPerm::run();
        // init
        global $_cbase;
        extract(basReq::sysVars()); 
        $_groups = glbConfig::read('groups'); 
        $db = glbDBObj::dbObj();
        $user = usrBase::userObj('Admin');
        $aurl = basReq::getUri(-2); 
        if(strpos($aurl[1],'&frame=1')){
            $reurl = str_replace('&frame=1','',$aurl[1]); 
            $reurl = comParse::urlBase64($reurl,0); 
            header('Location:'."?mke=$reurl");
        }
        $file = str_replace('-','/',$this->ucfg['mkv']);
        // head
        glbHtml::page(basLang::show('admin.adm_center').'-(sys_name)',1);
        eimp('initJs','jquery,jspop;comm;comm(-lang)');
        eimp('initCss','bootstrap,stpub,jstyle;comm');
        echo '</head><body class="'.(basEnv::isMobile()?'mobbody':'pcbody').'">';
        // inc
        echo "\n<!--inc:/$file.php-->\n"; 
        $full = DIR_IMCAT."/flow/$file.php";
        if(!file_exists($full)){
            die("<h1>File Not Found: $file.php</h1>");
        }else{
            include(DIR_VIEWS."/adm/frame/_inc-navbar.htm");
            $dop = new dopBase(@$_groups[$mod],'base_model');
            $cv = $dop->cv;
            require $full;
        }
        echo "\n<!--inc:end-->\n"; 
        eimp('/base/jslib/jq_base.js');
        eimp('/layer/layer.js','vendui');
        eimp('/bootstrap/js/bootstrap.min.js','vendui');
        // footer
        if($_cbase['debug']['err_mode']){
            echo "<p class='tc pv20'>".basDebug::runInfo()."</p>\n";
        }
        echo "</body></html>\n";
        echo basJscss::jscode("$(function(){ admNavd(); setTimeout('jcronRun()',4700) });")."\n";
        die(); //return array('tplnull'=>1);
    }

}
