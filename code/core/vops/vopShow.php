<?php
/*

*/
// 标签解析,显示 总控类
class vopShow{
    
    protected $vars = array(); //存放变量信息
    
    public $tplCfg = array(); //模板配置
    public $ucfg = array(); //url-Configs
    public $err = ''; 
    
    public $pgflag = array();
    public $pgbar = array(); //分页信息
    public $mod,$key,$view,$type;
    public $tplname,$tplorg,$tplnull; 
    
    function __construct($start=1){
        global $_cbase;
        $this->tplCfg = $_cbase['tpl'];
        $start && $this->run();
    }
    
    // 解析后的模板内容
    static function tpl($file,$ext='',$data=array()){
        global $_cbase; 
        $fpath = self::inc($file,$ext); 
        extract($data, EXTR_OVERWRITE); 
        ob_start(); 
        include $fpath;
        $res = ob_get_contents();
        ob_end_clean(); 
        return $res;
    }
    // 包含html区块（通过模板解析）
    // vopShow::inc('_pub:rhome/home',0,1);
    // include vopShow::inc('_pub:rhome/home');
    static function inc($file,$ext='',$inc=0){
        global $_cbase; 
        $ext || $ext = $_cbase['tpl']['tpl_ext']; 
        $cac = '/_vinc/'.substr($file,strpos($file,':')+1);
        $tplfull = DIR_CTPL.$cac.$_cbase['tpl']['tpc_ext'];
        if(!file_exists($tplfull) || !$_cbase['tpl']['tpc_on']){
            $template = vopTpls::pinc($file,$ext); 
            $template = comFiles::get($template); 
            $btpl = new vopComp();
            $template = $btpl->bcore($template);
            comFiles::chkDirs($cac,'ctpl',1); 
            comFiles::put($tplfull, $template); //写入缓存
        }
        $_cbase['run']['tplname'] = $file;
        if($inc){
            include $tplfull;
        }else{
            return $tplfull;
        }
    }
    //init-js
    function rjs($data=''){
        global $_cbase;
        $data || $data = basReq::val($data,'','');
        $temp = vopCTag::tagParas($data,'arr'); 
        $tagfile = @$temp[0]; 
        $tagname = @$temp[1]; $varid = 't_'.$tagname;
        $tagpath = "/$tagfile.$tagname.comjs.php";
        if(!file_exists(vopTpls::path('tpc').$tagpath)){
            vopComp::main($tagfile);
        } 
        $temp = @$temp[2];
        $tagtype = @$temp[0][0];
        $tagre = @$temp[0][1]; $tagre || $tagre = 'v';
        unset($temp[0]);
        $tagparas = $temp;
        $unv = in_array($tagtype,array('One')) ? $tagre : $varid;
        $$unv = $this->tagParse($tagname,$tagtype,$temp);
        //显示模板  
        $_groups = glbConfig::read('groups'); 
        include vopTpls::path('tpc').$tagpath;
    }
    //run
    function run($q=''){ 
        global $_cbase; 
        vopTpls::check($_cbase['tpl']['tpl_dir']);
        $this->vars = array(); // 重新清空(初始化),连续生成静态需要
        $this->ucfg = vopUrl::init($q); 
        if(empty($this->ucfg)) { return; }
        foreach(array('mkv','mod','key','view','type','tplname',) as $k){
            $this->$k = $this->ucfg[$k];
        }
        $this->getVars(); // 读取数据,赋值 $this->set('name', 'test_Name');
        $this->extActs(); // 操作扩展(vars,tpls)
        $tplfull = $this->tplCheck(); // 
        if(!$tplfull){
            return '';
        }elseif($this->err){ // 考虑生成静态,不要die,返回给生产静态的处理
            $this->ucfg = array(); 
            return $this->msg($this->err);
        }else{
            extract($this->vars, EXTR_OVERWRITE); 
        }
        $_cbase['mkv'] = $this->ucfg; 
        $_cbase['run']['tplname'] = $this->tplname;
        include $tplfull; //加载编译后的模板缓存
    }
    function tplCheck() { // 检查+编译
        if(!defined('RUN_STATIC') && $this->ucfg['hcfg']['vmode']=='static'){
            $file = vopStatic::getPath('home','home',1);
            if($path=tagCache::chkUpd($file,$this->ucfg['hcfg']['stexp'],0)){ 
                include $path; 
                echo "\n<!--".basDebug::runInfo()."-->";
                return '';
            }
        }
        if(!empty($this->tplnull)){
            return '';
        }
        if(empty($this->tplname)){
            $msgk = $this->ucfg['vcfg']['vmode']=='close' ? 'closemod' : 'parerr';
            return $this->err = "$this->mkv:".basLang::show("core.vop_$msgk");
        }
        if(is_string($this->vars)){
            return $this->err = $this->vars;
        }
        if(!empty($this->tplorg)){
            return vopTpls::path('tpl')."/{$this->tplorg}".$this->tplCfg['tpl_ext'];
        }
        $tplfull = vopTpls::path('tpc')."/{$this->tplname}".$this->tplCfg['tpc_ext'];
        if(empty($_cbase['tpl']['tpc_on']) || !file_exists($tplfull)){
            vopComp::main($this->tplname);
        }
        return $tplfull;
    }
    function extActs() {
        $class = $this->mod.'Ctrl'; 
        $fp = vopTpls::path('tpl')."/b_ctrls/$class.php";
        if(file_exists($fp)){
            include_once $fp;
            if(class_exists($class)){
                $aex = new $class($this->ucfg,$this->vars);
                $method = empty($this->key) ? 'homeAct' : ($this->type=='detail' ? '_detailAct' : $this->key.'Act');
                if(method_exists($aex,$method)){
                    $res = $aex->$method();
                }elseif($this->type=='mtype' && method_exists($aex,'_emptyAct')){
                    $res = $aex->_emptyAct();
                }
            }
        }
        if(!empty($res['vars'])){ 
            $this->vars = array_merge($this->vars,$res['vars']);
        }
        if(!empty($res['newtpl'])){
            $this->tplname = $res['newtpl'];
        }else{
            $this->extTpl(); // 模板扩展
        }
        if(!empty($res['tplorg'])){
            $this->tplname = $this->tplorg = $res['tplorg'];
        } 
        if(!empty($res['tplnull'])){
            $this->tplnull = $res['tplnull'];
        }
    }

    //extTpl
    function extTpl() { 
        global $_cbase; 
        $vars = $this->vars;
        $tplname = &$this->tplname; 
        // 处理:detail 设置的模板 
        if(!empty($vars['tplname'.$this->view])){ 
            $tplname = $vars['tplname'.$this->view];
        }elseif($this->type=='detail'){ 
            $cfgs = '';
            if(isset($vars['grade'])){
                $mcfgs = glbConfig::read('grade','dset');
                $cfgs = $mcfgs[$vars['grade']];
            }elseif(isset($vars['catid'])){
                $mcfgs = glbConfig::read($this->mod);
                $cfgs = $mcfgs['i'][$vars['catid']];
            }
            $cfgs = empty($cfgs['cfgs']) ? array() : basElm::text2arr($cfgs['cfgs']); 
            if(!empty($cfgs['tplname'.$this->view])){
                $tplname = $cfgs['tplname'.$this->view];
            } 
        } 
        if(!empty($this->ucfg['vcfg']['tmfix']) && basEnv::isMobile()){
            $tmfix = $this->ucfg['vcfg']['tmfix']; 
            $_cbase['run']['tmfix'] = $tmfix; // -mob标记用于css,js后缀
            $tplname .= $tmfix; 
        }
    }

    //GetVars
    function getVars() { 
        $_groups = glbConfig::read('groups');
        if(!($this->type=='detail')) return array();
        $pid = @$_groups[$this->mod]['pid'];
        $key = in_array($pid,array('types')) ? "kid" : substr($pid,0,1).'id';
        $data = $dext = array();
        if(in_array($pid,array('docs','users','coms','advs','types'))){
            $db = glbDBObj::dbObj();
            $tabid = glbDBExt::getTable($this->mod);
            $data = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
            if(empty($data)){ return $this->msg("[{$this->key}]".basLang::show('core.vshow_uncheck')); }
            if(in_array($pid,array('docs'))){
                $tabid = glbDBExt::getTable($this->mod,1);
                $dext = $db->table($tabid)->where(substr($pid,0,1)."id='{$this->key}'")->find();
                $dext && $data += $dext; 
            }
        }
        return $this->vars = $data;
    }
    
    //分页标签
    function chkPage($tagname) {
        global $_cbase; 
        $nowtpl = $_cbase['run']['tplnow'];
        if(empty($this->pgflag)){
            $this->pgflag = array('tpl'=>$nowtpl,'tag'=>$tagname,);
        }else{
            $msg0 = "<b>".basLang::show('core.vshow_1pagetag')."</b>";
            $msg1 = '<br>tpl:'.$this->pgflag['tpl'].', tag:'.$this->pgflag['tag'];
            $msg2 = '<br>tpl:'.$nowtpl.', tag:'.$tagname;
            $this->msg("$msg0$msg1$msg2");
        }
    }

    // 解析
    function tagParse($tagname,$type,$paras=array()){ 
        global $_cbase; 
        $res = tagCache::comTag($type,@$this->mkv,$paras);
        if(!empty($res[1])){ //缓存
            $data = $res[1]; 
            $_cbase['page']['bar'] = @$data['page_bar'];
            unset($data['page_bar']);
        }else{
            if($type=='Page') $this->chkPage($tagname);
            $this->tagRun('tagnow',$tagname);
            $class = "tag$type";
            $_1tag = new $class($paras);
            $data = $_1tag->getData();
            if($res[0]){
                tagCache::setCache($res[0],$data,1);
            }    
        }
        return $data;
    }
    // setRun
    function tagRun($key,$val='',$ext=''){
        global $_cbase;
        $_cbase['run'][$key] = $val; 
        if($ext) $_cbase['run'][substr($key,0,3).$ext][] = $val;
        $tpldir = $this->tplCfg['tpl_dir']; 
    }
    // unset
    function tagEnd($tname=''){}

    //模板赋值
    function set($name, $value = '') {
        if( is_array($name) ){
            foreach($name as $k => $v){
                $this->vars[$k] = $v;
            }
        } else {
            $this->vars[$name] = $value;
        }
    }
    // msg分析
    static function msg($msg=''){
        if(!defined('RUN_STATIC')){
            glbError::show($msg,0);    
        }else{
            return $msg;    
        }
    }
    
    // page-meta
    function pmeta($title='',$keywd='',$desc=''){
        global $_cbase; 
        $mcfg = glbConfig::read($this->mod);
        if($this->type=='detail'){
            if(empty($title) && !empty($this->vars['title'])) $title = $this->vars['title'];
            if(empty($keywd) && !empty($this->vars['seo_key'])) $keywd = $this->vars['seo_key'];
            if(empty($desc) && !empty($this->vars['seo_des'])) $desc = $this->vars['seo_des'];
        }elseif(in_array($this->type,array('mhome','mext'))){
            if(empty($title)) $title = @$mcfg['title'];
            if(isset($mcfg['i']) && empty($keywd)){ 
                $a = comTypes::getSubs($mcfg['i'],'0','1'); $gap = ''; 
                foreach($a as $k=>$v){
                    $keywd .= "$gap$v[title]"; $gap = ',';
                } //公司新闻,客户新闻,行业新闻
            }
        }elseif($this->type=='mtype'){ 
            if(empty($title) && !empty($mcfg['i'][$this->key]['title'])){ 
                $title = $mcfg['i'][$this->key]['title']; 
                if(!empty($mcfg['i'][$this->key]['pid'])){
                    $title .= '-'.$mcfg['i'][$mcfg['i'][$this->key]['pid']]['title'];
                } //子类3-栏目四
            }
        } 
        if($title){
            $title = str_replace('(sys_name)',$_cbase['sys_name'],$title);
            if(!strstr($title,$_cbase['sys_name'])) $title .= " - ".$_cbase['sys_name'];
        }
        $ua = $this->ucfg['ua']; $up = empty($ua['page']) ? 1 : $ua['page']; //??? 
        $ext = count($ua)>5 || $up>5;
        glbHtml::page('init',$ext);
        if($title) echo "<title>".basStr::filTitle($title)."</title>\n";
        if($keywd) echo "<meta name='keywords' content='".basStr::filTitle($keywd)."' />\n";
        if($desc) echo "<meta name='description' content='".basStr::filTitle($desc)."' />\n";
    }
    
    // page-import
    // {php $this->pimp(); }
    // {php $this->pimp(array('css'=>'~tpl/b_jscss/home.css','js'=>'/jquery/jq_imgChange.js:vendui')); } 
    // {php $this->pimp('/b_jscss/home.css'); }
    // {php $this->pimp('/jquery/jq_imgChange.js','vendui'); }
    // {php $this->pimp('act=1&exjs=/_pub/jslib/jsmove.js;~tpl/b_jscss/shapan.js','comjs.php','js'); }
    function pimp($imp='',$base='tpl'){
        global $_cbase; 
        $sdir = vopTpls::def(); //可能没有定义
        $eimp = $imp ? str_replace('~tpl',"/skin/$sdir",$imp) : '';
        if(empty($imp)||is_array($imp)){
            $extp = "user=1&mkv={$this->mkv}&tpldir=$sdir&lang={$_cbase['sys']['lang']}";
            glbHtml::page('imvop',$eimp,$extp);
        }elseif($base && $base=='comjs.php'){
            $type = strpos($eimp,'&excss') ? 'css' : 'js';
            echo basJscss::imp("/plus/ajax/comjs.php?$eimp",'',$type);
        }else{
            // tpl(tpl,'',0),skin,root,vendor,vendui,static
            $base = empty($base) ? '' : $base;
            if($base=='tpl'){ 
                $base = '';
                $imp = "/skin/$sdir$imp";
            } 
            echo basJscss::imp($imp,$base);
        }
    }

}

