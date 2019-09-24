<?php
namespace imcat;

class vopSapi{
 
    public $ver = 'nver'; // 
    public $cfgs = []; // mkv,mksp,mod,key,func
    public $res = [];

    //function __destory(){  }
    function __construct(){
        $this->init();
        $this->mkvs();
        # exdVlog 开启统计模块
        if(!empty($_cbase['ucfg']['stats']) && strstr($_cbase['ucfg']['stats'],'sapi')){
            exdVlog::main('sapi', $this->cfgs['mkv'], 0); # uri,ref
            //echo basDebug::runInfo();
        }
        $this->view($this->res);
    }

    function mkvs(){
        $this->cfgs['mkv'] = $mkv = req('mkv', 'root-home');
        $this->cfgs['mksp'] = $mksp = strpos($mkv,'.')>0 ? '.' : '-';
        $tmp = explode($mksp, $mkv);
        $this->cfgs['mod'] = $mod = $tmp[0]; // mkv,mksp,mod,key,func
        $this->cfgs['key'] = $key = empty($tmp[1]) ? '' : $tmp[1]; 
        $this->cfgs['func'] = $key ? ($mksp=='.' ? '_detailAct' : "{$key}Act") : 'homeAct';
        // 
        $fp = DIR_API."/{$this->ver}/{$mod}Api.php";
        if(file_exists($fp)){
            $res = $this->refp();
        }else{
            $res = $this->redef();
        }
        $this->res = $res;
    }
    // 得到`默认`的数据 / mkv=china
    function redef(){
        extract($this->cfgs);
        $_groups = glbConfig::read('groups');
        if(isset($_groups[$mod])){
            $pid = $_groups[$mod]['pid'];
            if($pid=='types' && !$key){
                $res['list'] = read("$mod.i");
            }elseif(in_array($pid,array('docs','users','coms','types')) && $mksp=='.'){
                $res['row'] = glbData::getRow($mod, $key, $pid);
                if(empty($res['row'])){ $this->error("(redef) Error `$key`!"); }
            }
        }
        if(empty($res)){
            $this->error("(redef) Error `$mkv`!");
        }
        return $res;
    }
    // 得到`类-方法`的数据 // root-home
    function refp(){
        extract($this->cfgs);
        require DIR_API."/comm/baseApi.php";
        require DIR_API."/{$this->ver}/bextApi.php";
        $fp = DIR_API."/{$this->ver}/{$mod}Api.php";
        require $fp; 
        $cls = "\\imcat\\{$mod}Api";
        $api = new $cls($this->cfgs);
        if(!method_exists($api, $func)){
            $this->error("(refp) `$func` NOT Found!");
        }
        return $api->$func();
    }

    // 检查
    function init(){
        global $_cbase;
        // check-sk
        define('DIR_API', DIR_VIEWS.'/'.$_cbase['tpl']['vdir']);
        $ver = req('ver', 'nver');
        if($ver!='nver' && is_dir(DIR_API.'/v'.$ver)){ $this->ver = 'v'.$ver; }
        $sk = $_cbase['safe']['api'];
        $usk = req('sk');
        // dallow
        $alp = '*'; //basEnv::isLocal() ? '*' : '';
        glbHtml::dallow($alp);
    }

    // 通用方法

    static function view($data=array()){
        if(empty($data['ercode'])){
            $data['ercode'] = 0;
            $data['ermsg'] = '';            
        }
        $re = req('re', 'json');
        $debug = req('debug');
        if($debug){
            dump($data);
        }else{
            $re = req('re', 'json');
            $res = basOut::fmt($data, $re);
            die($res);
        }
    }

    static function error($msg='', $code=0){
        $msg = empty($msg) ? 'Error Message!' : $msg;
        $res['ercode'] = $code ? $code : 1;
        $res['ermsg'] = $msg;
        $res['ref'] = empty($_SERVER['HTTP_REFERER']) ? '?' : $_SERVER['HTTP_REFERER'];
        glbHtml::httpStatus(404);
        self::view($res);
        die('');
    }

    static function verr($msg='', $code=0){
        self::error($msg, $code);
    }

}

/*

*/
