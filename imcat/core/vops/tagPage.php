<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

/*
    funcs: pPgbar,getData
    attrs: 
*/
// 标签解析 (数据列表)类
class tagPage extends tagList{
    
    public $pg = null;
    public $pgbar = '';
    
    function __construct($paras=array()) {
        parent::__construct($paras); 
        $this->pPgbar();
    }
    
    function pPgbar(){ 
        global $_cbase; 
        $sfrom = "m.* FROM ".glbDBObj::dbObj()->table($this->sqlArr['tabid'],2)." m ";
        $where = $this->whrStr; 
        $pg = new comPager($sfrom,$where,$this->sqlArr['limit'],"m.".$this->sqlArr['order']); 
        $pg->set('odesc',$this->sqlArr['odesc']); 
        $pg->set('opkey',(!empty($this->exFld2)&&$this->exFld2==$this->sqlArr['order']) ? 1 : 0); 
        $this->re = $pg->exe(); 
        $this->sqlAll = $pg->sql; 
        $idfirst = ''; $idend = '';
        if($this->re){
            $i = current($this->re); 
            $idfirst = current($i); 
            $i = end($this->re); 
            $idend = current($i); 
        }
        $scname = basEnv::serval('REQUEST_URI'); //REQUEST_URI,SCRIPT_NAME
        if(strpos($scname,'ajax-cron') || strpos($scname,'ajax-jshow')){
            $mkv = basReq::val('rf');
            $burl = vopUrl::fout('base:0')."?rf=$mkv";
        }else{
            $burl = basReq::getUri(-1,'','page|prec|ptype|pkey');    
        }
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show($idfirst,$idend,'',$burl)."</div>";
        $_cbase['page']['cfg'] = $pg->cfg;
        $_cbase['page']['prec'] = $pg->prec; 
        $_cbase['page']['pcnt'] = $pg->pcnt;
    }
    
    function getData(){ 
        //$this->debug('pgbar');
        $re = $this->re;
        return $this->getJoin($re);
    }

    # 自定义分页样式: 默认:pgbar('pg_bar', 'pagination')
    static function pgbar($cls1='', $cls2=''){ 
        global $_cbase; 
        $pgbar = $_cbase['page']['bar'];
        if($cls1){ $pgbar = str_replace('pg_bar', $cls1, $pgbar); }
        if($cls2){ $pgbar = str_replace('pagination', $cls2, $pgbar); }
        // 或者这里全部自定义返回...
        return $pgbar;
    }

}
