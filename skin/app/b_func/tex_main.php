<?php
/*

*/ 
class tex_main{
    
    public $mod = '';
    public $act = '';
    public $vars = array(); //存放变量信息
    
    function __construct() { if(req('mod')=='mod') die('sss');
        $this->init();
        $this->check();
        $this->vars();
        $this->view();
    }
    
    function init(){
        $qs = empty($_SERVER['QUERY_STRING'])?'':$_SERVER['QUERY_STRING']; //可能为0
        $this->qs = $qs;
        parse_str($qs,$ua); unset($ua['_r'],$ua['_'],$ua[cfg('safe.safix')]);
        $this->ua = $ua;
        $this->hcfgs = read('home','va'); 
        if(!empty($this->hcfgs['c']['close'])){
            $this->vars = $this->error('closed-all(init)');
            $this->view('~');
        }
        $domain = req('domain','');
        if($domain && in_array($domain, $this->hcfgs['c']['dmacc'])){ 
            header("Access-Control-Allow-Origin:*"); // 指定允许其他域名访问  
            header('Access-Control-Allow-Methods:POST'); // 响应类型  
            header('Access-Control-Allow-Headers:x-requested-with,content-type'); // 响应头设置
            header('Access-Control-Allow-Credentials:true'); // 允许携带 用户认证凭据（也就是请求携带Cookie）
            header('X-Frame-Options:ALLOWALL'); //ALLOWALL，ALLOW-FROM    
        } 
        vopTpls::pinc('tex_func');
    }

    // mod, ua
    function check(){
        if(empty($this->qs) || $this->qs=='home'){
            $this->view('home',0);
        }
        $flag = safComm::signApi('flag'); 
        if($flag){
            $this->vars = $this->error('signApi(check)');
            $this->view('~');
        }
        if(empty($this->ua['mod'])){
            $this->vars = $this->error('empty-mod(check)');
            $this->view('~');
        }
        $this->mod = $this->ua['mod'];
        $this->act = empty($this->ua['act']) ? '' : $this->ua['act'];
        $this->id = req('id','');
        if(in_array($this->mod,$this->hcfgs['close'])){
            $this->vars = $this->error("closed-{$this->mod}(init)");
            $this->view('~');
        }
    }
    
    function vars(){
        $_groups = read('groups');
        if(in_array($this->mod,$this->hcfgs['extra'])){
            $this->view($this->mod);
        }
        if(isset($_groups[$this->mod])){
            $file = $this->id ? 'vdetail' : 'vlist';
            $this->view($file);
        }else{
            $this->vars = $this->error("Error-mod(vars):{$this->mod}");
            $this->view('~');
        }
    }

    function error($msg='',$state='',$no=0){
        $res = array(
            'errno' => $no ? $no : 1,
            'state' => $state ? $state : 'error',
            'msg' => $msg,
            'ua' => $this->ua,
        );
        return $res;
    }

    function view($file='info',$vout=1,$die=1){
        global $_cbase;
        $_groups = read('groups');
        $db = db();
        $vars = $this->vars;
        $fp = vopTpls::pinc("b_files/$file");
        if(file_exists($fp)){
            include($fp); 
        }
        $type = req('retype','json');
        if($vout){ 
            if(req('debug')){
                glbHtml::head('html'); 
                dump($vars);
            }else{
                glbHtml::head($type); 
                echo basOut::fmt($vars,$type);
            }
        }
        if($die) die();
    }

}
