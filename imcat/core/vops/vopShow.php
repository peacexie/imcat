<?php
namespace imcat;

// 标签解析,显示 总控类
class vopShow{
    
    protected $vars = array(); //存放变量信息
    
    public $tplCfg = array(); //模板配置
    public $ucfg = array(); //url-Configs
    public $err = ''; 
    
    public $pgflag = array();
    public $pgbar = array(); //分页信息
    public $mod, $key, $view, $type;
    public $tplname, $tplorg, $tplnull; 
    
    function __construct($start=1){
        global $_cbase;
        $this->tplCfg = $_cbase['tpl'];
        vopTpls::check($_cbase['tpl']['vdir'], 1);
        $start && $this->run();
    }
    //init-js
    function rjs($data=''){
        global $_cbase;
        $data || $data = basReq::val($data,'','');
        $temp = vopCTag::tagParas($data,'arr'); 
        # 2019-08-31:安全修正
        foreach($temp[2] as $tk=>$tv){
            if(in_array($tv[0],['order','where'])){
                die("Error `{$tv[0]}`");
            }
        }
        $tagfile = @$temp[0]; 
        $tagname = @$temp[1]; $varid = 'T_'.$tagname;
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
    function run(){ 
        global $_cbase; 
        $this->vars = array(); // 重新清空(初始化),连续生成静态需要
        $this->ucfg = vopUrl::init(); 
        $_cbase['mkv'] = $this->ucfg;
        foreach(array('mkv','mod','key','view','type','tplname',) as $k){
            $this->$k = $this->ucfg[$k];
        }
        $this->homeStatic(); // 首页静态
        $this->getVars(); // 读取数据
        $this->extActs(); // 操作扩展(vars,tpls)
        $tplfull = $this->chkTpl(); // 检查模板,编译
        if(!$tplfull){
            $this->ucfg = array(); // 生成静态判断
            return;
        }
        extract($this->vars, EXTR_OVERWRITE); 
        $_cbase['run']['tplname'] = $this->tplname;
        include $tplfull;
    }
    // 检查模板,编译
    function chkTpl() {
        global $_cbase;
        if(!empty($this->tplnull)){
            return ''; // 不要后续模板显示-直接返回
        }
        if(empty($this->tplname) || is_string($this->vars) || $this->ucfg['vcfg']['vmode']=='close'){
            $msgk = $this->ucfg['vcfg']['vmode']=='close' ? 'closemod' : 'parerr';
            $ermsg = is_string($this->vars) ? $this->vars : "$this->mkv:".basLang::show("core.vop_$msgk");
            $this->msg($ermsg);
            return ''; // 返回空,终止操作
        }
        if(!empty($this->tplorg)){
            $tplorg = vopTpls::tinc("{$this->tplorg}.htm",0);
        }else{
            $tplorg = vopTpls::tinc("{$this->tplname}.htm",0);
        }
        if(!file_exists($tplorg)){ // 原始模板是否存在
            $this->msg("$tplorg NOT Exists!");
            return ''; // 返回空,终止操作
        }
        if(!empty($this->tplorg)){
            $tplfull = $tplorg; // 原始包含,不要解析判断
        }else{ // 编译
            $tplfull = vopTpls::path('tpc')."/{$this->tplname}".$this->tplCfg['tpc_ext'];
            if(empty($_cbase['tpl']['tpc_on']) || !file_exists($tplfull)){
                vopComp::main($this->tplname);
            }   
        }
        $tplfull = empty($_cbase['tpl']['fixmkv']) ? $tplfull : $tplfull.'.'.$_cbase['tpl']['fixmkv'];
        return $tplfull;
    }
    // 扩展操作
    function extActs() {
        if($class=vopTpls::impCtrl($this->mod)){
            $aex = new $class($this->ucfg,$this->vars);
            $method = empty($this->key) ? 'homeAct' : ($this->type=='detail' ? '_detailAct' : $this->key.'Act');
            if(method_exists($aex,$method)){
                //$method = $method;
            }elseif($this->type=='mtype' && method_exists($aex,'_defAct')){
                $method = '_defAct';
            }else{
                $method = '';
            }
            if($method){
                //$exact = $method.'Before'; // 预处理数据
                //if(method_exists($aex,$exact)) $aex->$exact();
                $res = $aex->$method();
                //$exact = $method.'After'; // 后续数据调整
                //if(method_exists($aex,$exact)) $aex->$exact();
            }
        }
        if(!empty($res['vars'])){ 
            $this->vars = array_merge($this->vars,$res['vars']);
        }
        if(!empty($res['tplnull'])){
            $this->tplnull = $res['tplnull'];
        }elseif(!empty($res['newtpl'])){
            $this->tplname = $res['newtpl'];
        }elseif(!empty($res['tplorg'])){
            $this->tplname = $this->tplorg = $res['tplorg']; 
        }else{ // extActs/extTpl:取其一扩展模板
            $this->extTpl();
        }
    }
    // 扩展模板
    function extTpl() {
        global $_cbase; 
        $vars = $this->vars;
        $tplname = &$this->tplname; 
        // 处理:detail 设置的模板 
        if(!empty($vars['tplname'.$this->view])){ 
            $tplname = $vars['tplname'.$this->view];
        }elseif($this->type=='detail'){ 
            $cfgs = array();
            if(isset($vars['grade'])){
                $mcfgs = glbConfig::read('grade','dset');
                $cfgs = isset($mcfgs[$vars['grade']]) ? $mcfgs[$vars['grade']] : '';
            }elseif(isset($vars['catid'])){
                $mcfgs = glbConfig::read($this->mod);
                $cfgs = isset($mcfgs['i'][$vars['catid']]) ? $mcfgs['i'][$vars['catid']] : array();
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
            $data = glbData::getRow($this->mod, $this->key, $pid);
            if(empty($data)){ return $this->vars = "[{$this->key}]".basLang::show('core.vshow_uncheck'); }
        }
        $this->vars = $data;
    }
    // 首页静态
    function homeStatic() {
        if($this->mod=='home' && !defined('RUN_STATIC') && $this->ucfg['hcfg']['vmode']=='static'){
            $file = vopStatic::getPath('home','home',0);
            if($data=extCache::cfGet("/$file",$this->ucfg['hcfg']['stexp'],'html','str')){ 
                echo $data;
                echo "\n<!--".basDebug::runInfo()."-->";
                die(); // 可以终止,生成静态不走这里
            }
        }
    }    
    // 分页标签
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
            $class = "\\imcat\\tag$type";
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
        $tpldir = $this->tplCfg['vdir']; 
    }
    // unset
    function tagEnd($tname=''){}
    //模板赋值
    function set($name, $value=''){}

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

}
