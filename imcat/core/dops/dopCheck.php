<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// dopCheck(data OP for Extra)
class dopCheck extends dopBase{    

    public $excfg = array();
    public $user = NULL;
    public $uname = '';
    public $ugrade = '';

    // login=1(登录发布)
    // login=cvip,ccom(会员cvip,ccom等级:登录发布)
    static function dchkLogin($ngrades=0){ 
        if(usrPerm::issup()) return; //超管
        $user = usrBase::userObj(); 
        $ugrade = empty($user->uperm['grade']) ? '(null)' : $user->uperm['grade'];
        if(!is_numeric($ngrades)){
            if(strpos("(,$ngrades,)",",{$ugrade},")<=0){
                die(basLang::show('flow.ck_grade',$ngrades));
            }
        }else{ 
            // stop
            if(strpos($ugrade,'stop')>0){
                die(basLang::show('flow.ck_stop',$ugrade));
            }
            if($user->userFlag!='Login'){
                die(basLang::show('flow.ck_login'));
            }
        }
    }

    // iprep=3(ip重复发布时间间隔)
    static function dchkIprep($num=0,$mod,$kid,$opfid=''){ 
        $ckey = "{$mod}_$kid";
        $stamp = $_SERVER["REQUEST_TIME"];
        $glife = intval($num)*60;
        $ck = comCookie::mget('diggs',$ckey); // cookie;
        //echo "($ck)";
        if(empty($ck) || ($stamp-intval($ck))>$glife){
            comCookie::mset('diggs',$glife,$ckey,$stamp,20);
        }else{
            die(basLang::show('flow.ck_rep',$glife));
        }
    }

    static function addInit($cfg=array(),$percheck=array()){ 
        $chk = new self($cfg);
        if(empty($chk->excfg)) return;
        foreach ($chk->excfg as $key => $val) {
            if(empty($val)) continue;
            $method = 'chk'.ucfirst(strtolower($key));
            if(substr($key,0,3)=='ap_'){
                $ngrade = substr($key,2);
                if($chk->ugrade==$ngrade){
                    $chk->chkAllpub($val);
                    unset($chk->excfg['allpub']);
                }
            }elseif(method_exists($chk,$method)){
                $chk->$method($val);
            }
        }
    }

    function __construct($cfg=array()){ 
        parent::__construct($cfg);
        $this->excfg = basElm::text2arr($this->cfg['cfgs']);
        $this->user = usrBase::userObj('Member'); 
        $this->uname = empty($user->uinfo['uname']) ? '(null)' : $user->uinfo['uname'];
        $this->ugrade = empty($user->uperm['grade']) ? '(null)' : $user->uperm['grade'];
        $this->tabid = glbDBExt::getTable($this->cfg['kid']); 
    }

    // showdef=1

    // login=1(登录发布)
    // login=cvip,ccom(会员cvip,ccom等级:登录发布)
    function chkLogin($ngrades=0){ 
        $clogin = 1; 
        if(!is_numeric($ngrades)){
            if(strpos("(,$ngrades,)",",{$this->ugrade},")<=0){
                glbHtml::end(basLang::show('flow.ck_grade',$ngrades));
            }
        }else{ 
            // stop
            if(strpos($this->ugrade,'stop')>0){
                glbHtml::end(basLang::show('flow.ck_stop',$this->ugrade));
            }
            if($this->user->userFlag!='Login'){
                glbHtml::end(basLang::show('flow.ck_login'));
            }
        }
    }
    
    // ap_ccom=500(会员ccom等级:发布总量) -> (500,'ccom')
    // allpub=100(会员发布总量)
    // skip_allpub=cvip,ovip(cvip,ovip不检测)
    function chkAllpub($num=0){ 
        if(!empty($this->excfg['skip_allpub'])){
            if(strpos("(,{$this->excfg['skip_allpub']},)",",{$this->ugrade},")>0){
                return;
            }
        }
        $cnt = $this->db->table($this->tabid)->where("auser='{$this->uname}'")->count();
        if($cnt>=$num){
            glbHtml::end(basLang::show('flow.ck_all',$num));
        }
    }

    // ippub=5(ip日发布量)
    // skip_ippub=cvip,ovip(cvip,ovip不检测)
    function chkIppub($num=0){ 
        if(!empty($this->excfg['skip_ippub'])){
            if(strpos("(,{$this->excfg['skip_ippub']},)",",{$this->ugrade},")>0){
                return;
            }
        }
        $cnt = $this->db->table($this->tabid)->where("aip='".basEnv::userIP()."' AND atime>='".($_SERVER["REQUEST_TIME"]-86400)."'")->count();
        if($cnt>=$num){
            glbHtml::end(basLang::show('flow.ck_day',$num));
        }
    }

    // iprep=3(ip重复发布时间间隔)
    // skip_iprep=cvip,ovip(cvip,ovip不检测)
    function chkIprep($num=0){ 
        if(!empty($this->excfg['skip_iprep'])){
            if(strpos("(,{$this->excfg['skip_iprep']},)",",{$this->ugrade},")>0){
                return;
            }
        }
        $cnt = $this->db->table($this->tabid)->where("aip='".basEnv::userIP()."' AND atime>='".($_SERVER["REQUEST_TIME"]-$num)."'")->count();
        if($cnt>0){
            glbHtml::end(basLang::show('flow.ck_rep',$num));
        }
    }

    static function headComm(){
        global $_cbase; 
        glbHtml::page($_cbase['sys_name'],1);
        echo basJscss::imp('initJs','jquery,bootstrap,layer,jspop;comm;comm(-lang)');
        echo basJscss::imp('initCss','jspop,bootstrap,stpub,jstyle;comm'); 
        echo basJscss::imp('loadExtjs','jq_base,bootstrap,layer');
        glbHtml::page('body',' style="padding:8px 5px 5px 5px;overflow-y:scroll;overflow-x:hidden;"'); 
    }

}

/*
showdef=1
login=1
ap_ccom=500  ap_xxx在allpub前面
allpub=100   skip_allpub=cvip,ovip
ippub=5      skip_ippub
iprep=3      skip_iprep
*/
