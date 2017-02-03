<?php
/*

*/ 
class tex_main{
    
    public $mod = '';
    public $act = '';

    protected $vars = array(); //存放变量信息
    
    public $tplCfg = array(); //模板配置
    public $ucfg = array(); //url-Configs
    public $err = ''; 
    
    public $pgflag = array();
    public $pgbar = array(); //分页信息
    //public $mod,$key,$view,$type; 
    
    function __construct() {
        $this->init();
        $this->check();
        $this->vars();
        $this->view();
    }
    
    function init(){
        // 
        $qs = empty($_SERVER['QUERY_STRING'])?'':$_SERVER['QUERY_STRING']; //可能为0
        $this->qs = $qs;
        parse_str($qs,$ua); unset($ua['_r'],$ua['_'],$ua[cfg('safe.safix')]);
        $this->ua = $ua;
        $this->hcfgs = read('home','va'); 
    }
    // 
    function perm(){
        // close, from-domain
    }
    // mod, ua
    function check(){
        if(empty($this->qs) || $this->qs=='home'){
            $this->view('home');
        }
        $flag = safComm::signApi('flag'); 
        if($flag){
            $this->view($this->error('signApi'));
        }
        if(empty($this->ua['mod'])){
            $this->view($this->error('empty-mod'));
        }
        $this->mod = $this->ua['mod'];
        $this->act = empty($this->ua['act']) ? '' : $this->ua['act'];
        // close, imp...
    }
    
    function vars(){
        //dump($this);
        if(in_array($this->mod,$this->hcfgs['extra'])){
            $this->view($this->mod);
        }
        global $_cbase; 
        //初始化
        //die();

        $this->vars = array(); //重新清空,连续生成静态需要
        $this->ucfg = $_cbase['mkv'] = vopUrl::init($q); 
        if(empty($this->ucfg)) { return; }
        foreach(array('mkv','mod','key','view','type','tplname',) as $k){
            $this->$k = $this->ucfg[$k];
        }
        //读取数据,赋值 $this->set('name', 'test_Name');

        $_groups = read('groups');
        if(!($this->type=='detail')) return array();
        $pid = @$_groups[$this->mod]['pid'];
        $key = in_array($pid,array('types')) ? "kid" : substr($pid,0,1).'id';
        $data = $dext = array();
        if(in_array($pid,array('docs','users','coms','advs','types'))){
            $db = db();
            $tabid = glbDBExt::getTable($this->mod);
            $data = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
            if(empty($data)){ return $this->msg("[{$this->key}]".lang('core.vshow_uncheck')); }
            if(in_array($pid,array('docs'))){
                $tabid = glbDBExt::getTable($this->mod,1);
                $dext = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
                $dext && $data += $dext; 
            }
        }
        return $this->vars = $data;

        //模板:判断+编译+显示
        $this->getTpl($vars); 
        $_groups = read('groups'); //显示
        include(vopTpls::path('tpc')."/$this->tplname".$this->tplCfg['tpc_ext']);//加载编译后的模板缓存
    }

    function error($msg='',$state='',$no=0){
        $res = array(
            'errno' => $no ? $no : 1,
            'state' => $state ? $state : 'error',
            'msg' => $msg,
            'data' => $this->ua,
        );
        return $res;
    }

    function view($vars=array(),$stop=1){
            global $_cbase;
            $_groups = read('groups');
        if(!empty($vars)){
            if(is_array($vars)){
                $type = req('retype','json');
                echo basOut::fmt($vars,$type );
            }else{ // if(is_string($vars))
                include(vopTpls::pinc("b_files/v{$vars}"));
            }
        }else{

            echo vopTpls::pinc("b_files/v{$this->mod}");
            include(vopTpls::pinc("b_files/v{$this->mod}"));
        }
        if($stop) die();
    }

}
