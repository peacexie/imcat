<?php
namespace imcat\base;

use imcat\basJscss;
use imcat\basLang;
use imcat\basMsg;
use imcat\basReq;
use imcat\glbHtml;
use imcat\vopTpls;

class fileCtrl{

    public $ucfg = array();
    public $vars = array();
    public $user = null;

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        // check-perm
        $this->user = user(array('Admin','Member'));
        $this->user->userFlag=='Guest' && basMsg::show('Not Login.','die'); //未登录
    }

    function updeelAct(){
        global $_cbase;
        $user = $this->user;
        $allpars = tex('fileCtrl')->fopPars(0);
        include vopTpls::tinc('info/file-updeel',0);
        die();
    }

    // _defAct
    function _defAct(){
        global $_cbase;
        $tpl = $this->ucfg['tplname'];
        $tpl = str_replace('file/', 'info/file-', $tpl);
        return array('newtpl'=>$tpl); // file/upone -=> info/file-upbat
    }

    # copy from texFile.php

    function fopCfg(){
        $re = basLang::ucfg('urparts'); 
        if(strlen(req('kid','','Key'))<10){ unset($re['now']); }
        return $re;
    }
    function fopPars($arr=1){
        $re = array(); $allpars = "";
        foreach(array('act,form','fid,content','parts,comms','dir,logo','mod,demo','kid,,Key') as $t){
            $a = explode(',',$t);
            $v = req($a[0], $a[1], empty($a[2])?'Title':$a[2]);
            $re[$a[0]] = $v;
            $allpars .= "&$a[0]=$v";
        }
        $allpars = substr($allpars,1);
        if(empty($arr)) return $allpars;
        $re['allpars'] = substr($allpars,1);
        return $re;
    }

    function fopHead($parts,$title=''){
        glbHtml::page($title,1);
        eimp('initJs','jquery,bootstrap,layer;comm;comm(-lang);/base/assets/alib/file-func');
        eimp('initCss','bootstrap,stpub,jstyle;comm;/base/assets/alib/file-style');
        glbHtml::page('body',' style="margin:0px 2px;"');
        $allpars = self::fopPars(0);
        $cfg_parts = self::fopCfg();
        $cfg_dirs = read('urdirs','sy');
        $mod = req('mod','demo');
        $str = "\n<table class='file_bar'><tr>";
        $tmppars = basReq::getURep($allpars,'parts','{p}');
        foreach($cfg_parts as $k=>$v){ 
            $v0 = $k; $t0 = $v; 
            if(is_array($v)){ $v0 = $v[1]; $t0 = $v[0]; }
            $paras = basReq::getURep(str_replace('{p}',$k,$tmppars),'dir',$v0);
            $mkey = in_array($k,array('upbat','media')) ? $k : 'fview';
            $str .= "\n<td ".($parts==$k?'class="act"':'')."><a href='?file-$mkey&".$paras."'>$t0</a></td>";
        } 
        $str .= "\n</tr></table>";
        echo $str;
    }

}
