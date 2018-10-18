<?php
namespace imcat;

function edwimp($file){
    echo basJscss::write(basJscss::imp($file,'vendui'))."\n";
}

namespace imcat\base;

use imcat\basJscss;
use imcat\comFiles;
use imcat\glbHtml;
use imcat\vopTpls;


class editorCtrl{

    public $ucfg = array();
    public $vars = array();
    public $key = '';

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->key = $this->ucfg['key']; // api_ue
    }

    function apiOne($api){
        global $_cbase;
        $lang = $_cbase['sys']['lang']; 
        glbHtml::head('js'); 
        $mod = req('mod','demo'); 
        $kid = req('kid','');
        echo "var edt_langType = '".@$lang."';\n"; 
        echo "var edt_sysMod = '".@$mod."';\n"; 
        echo "var edt_sysKid = '".@$kid."';\n"; 
        $d1 = comFiles::get(vopTpls::tinc("assets/editor-func.js", 0));
        $d2 = comFiles::get(vopTpls::tinc("assets/comm-$lang.js", 0));
        echo "$d1\n\n//($lang)\n$d2";
        include vopTpls::tinc("editor/api_$api", 0);
    }

    function tplOne($tpk){
        global $_cbase;
        glbHtml::head('html');
        $fid = req('fid','content');
        $pSub = req('pSub','peace'); // peace,baidu,eweb //// peace,def
        $lang = $_cbase['sys']['lang']; 
        glbHtml::page(lang('plus.edt_tplchar'),1);
        eimp('initJs','jquery,bootstrap,layer;comm;comm(-lang);editor_func');
        eimp('initCss','bootstrap,stpub,jstyle;comm;editor_style'); 
        glbHtml::page('body');
        $itpl = lang('plus.edt_tpl');
        $ichr = lang('plus.edt_spchar');
        echo  "\n<table style='margin:auto' class='table table-hover'><tr>";
        $arr = array('peace','baidu','eweb');
        foreach ($arr as $key) {
            $cls = $pSub==$key ? 'class="act"' : '';
            echo "<td $cls><a href='?editor-tpl_char&fid=$fid&pSub=$key'>$ichr($key)</a></td>";
        }
        $arr = array('align','common');
        foreach ($arr as $key) {
            $cls = $pSub==$key ? 'class="act"' : '';
            echo "<td $cls><a href='?editor-tpl_doc&fid=$fid&pSub=$key'>$itpl($key)</a></td>";
        }
        echo  "\n</tr></table>";
        include vopTpls::tinc("editor/tpl_$tpk", 0);
    }

    // _defAct
    function _defAct(){
        $fix = substr($this->key, 0, 4);
        $key = substr($this->key, 4);
        if($fix=='api_'){
            $this->apiOne($key);
        }elseif($fix=='tpl_'){
            $this->tplOne($key);
        }
        die(); 
    }

}
