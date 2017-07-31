<?php
/*
*/ 
class homeCtrl{
    
    public $ucfg = array();
    public $vars = array();
    public $sid = '';
    public $sess = '';
    public $vret = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->_init();
    }

    function _init(){
        $this->sid = 'rest_'.usrPerm::getSessid();
        $this->sess = empty($_SESSION[$this->sid]) ? '' : $_SESSION[$this->sid];
        $this->op = basReq::val('op');
        $db = glbDBObj::dbObj();
        $this->test = $db->table('token_rest')->where("kid='test'")->find();
        $vars['test'] = $this->test;
        $vars['sess'] = $this->sess;
        $this->vret = $vars;
    }

    function homeAct(){
        $db = glbDBObj::dbObj();
        $test = $db->table('token_rest')->where("kid='test'")->find();
        $res['vars'] = $this->vret;
        $text = comFiles::get(vopTpls::pinc("home/mhome",'.txt'));
        $text = str_replace('{tk}',$this->test['token'],$text);
        $res['vars']['text'] = extMkdown::pdext($text);
        return $res;
    }

    function tokenAct(){
        $db = glbDBObj::dbObj();
        $kid = basReq::val('kid');
        $pass = basReq::val('pass');
        if(!empty($this->sess)){
            $row = $db->table('token_rest')->where("kid='$this->sess'")->find();
            $row['expstr'] = $row['exp'] ? date('Y-m-d H:i:s',$row['exp']) : '-(Expired)-';
            if(empty($row['token']))$row['expstr'] = '-(Null-Token)-';
            $this->vret['row'] = $row;
        }else{
            $this->vret['row'] = array();
        }
        if($this->op=='out'){
            $_SESSION[$this->sid] = '';
            return $this->errorAct('Logout OK!');
        }elseif($this->op=='Login'){
            return $this->tokLogin($db,$kid,$pass);
        }elseif($this->op=='Submit'){
            return $this->tokSubmit($db,$kid,$pass); 
        }
        $res['vars'] = $this->vret;
        return $res;
    }
    function tokSubmit($db,$kid,$pass){
        $pasn = basReq::val('pasn');
        $sop = basReq::val('sop');
        // check-row
        if(empty($this->sess) || empty($kid) || $this->sess!==$kid){
            return $this->errorAct("Error User/Password[s1]!");
        }
        if($sop=='view'){
            header("Location:?token");
        }
        if(empty($pass) || empty($this->vret['row']['pass']) || $pass!==$this->vret['row']['pass']){
            return $this->errorAct("Error User/Password[s2]!");
        }
        if($sop=='edit'){ // password
            if(empty($pasn) || $pasn==$pass){
                return $this->errorAct("Error User/Password[s3]!");
            }else{
                $db->table('token_rest')->data(array('pass'=>$pasn))->where(array('kid'=>$kid))->update(0);
                return $this->errorAct("Your new password is[ <b>$pasn</b> ]!");
            }
        }elseif($sop=='reset'){
            $ntok = comToken::guid($kid);
            $db->table('token_rest')->data(array('token'=>$ntok))->where(array('kid'=>$kid))->update(0);
            return $this->errorAct("Your New Token is [ <b>$ntok</b> ]!");
        }else{ // exp : 1d,1w,1m,12m
            $stamp = $_SERVER["REQUEST_TIME"] + extCache::CTime($sop);
            $db->table('token_rest')->data(array('exp'=>$stamp))->where(array('kid'=>$kid))->update(0);
            return $this->errorAct("Your Expire-time Reset to [ ".(date('Y-m-d H:i:s',$stamp))." ]!");
        }
    }
    function tokLogin($db,$kid,$pass){
        $vcode = basReq::val('vcode');
        $vre = safComm::formCVimg('fmrest', $vcode, 'check', 600);
        if(empty($kid) || empty($pass)){
            return $this->errorAct("Error User/Password[f1]!");
        }elseif($vre){
            return $this->errorAct($vre);
        }elseif($row=$db->table('token_rest')->where("kid='$kid' AND pass='$pass'")->find()){
            $this->sess = $_SESSION[$this->sid] = $kid;
            header("Location:?token");
        }else{
            return $this->errorAct("User/Password Error[f2]!");
        }
    }

    function errorAct($msg=''){
        $msg = empty($msg) ? 'Error Message!' : $msg;
        $res['vars']['title'] = strpos("($msg)",'Error') ? 'REST Error!' : 'REST Info!';
        $res['vars']['msg'] = $msg;
        $res['vars']['ref'] = empty($_SERVER['HTTP_REFERER']) ? '?' : $_SERVER['HTTP_REFERER'];
        $res['newtpl'] = 'home/error';
        glbHtml::httpStatus(404);
        return $res;
    }

}
