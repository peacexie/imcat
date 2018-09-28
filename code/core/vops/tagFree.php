<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

/*
    funcs: pPgbar,getData
    attrs: 
*/
// 标签解析 (数据列表)类
class tagFree extends tagList{
 
    public $pg = null;
    public $pgbar = '';

    public $dbkey = ''; // tagFunc中使用
    public $join = ''; 
    public $where = ''; 
    
    function __construct($paras=array()) {
        $this->paras = $paras;
        $this->setDbkey($paras); // 首先设置dbkey
        $this->db = glbDBObj::dbObj($this->dbkey);
        $this->setModid();
        $this->pJoin();
        $this->pOrder();
        $this->pLimit();
        $this->pOffset();
        $this->pWhere();
        $this->resData();
    }
    
    // Dbkey: 
    function setDbkey($paras=array()){
        $this->paras = $paras;
        $para = $this->p1Cfg('dbkey'); 
        $this->dbkey = empty($para[1]) ? $this->dbkey : $para[1];
    }
    // [join,$joinstr]
    function pJoin(){ 
        $cfg = $this->p1Cfg('join'); 
        if(empty($cfg)) return;
        if(!empty($cfg[1])){
            $this->join = $cfg[1];
        }
    }
    // [where,$whrstr]
    function pWhere(){ 
        $cfg = $this->p1Cfg('where'); 
        if(empty($cfg)) return;
        if(!empty($cfg[1])){
            $this->where = ' WHERE '.$cfg[1];
        }
    }
  
    // resData: 
    function resData(){
        $cfg = $this->p1Cfg('pgbar'); 
        $this->from = "m.* FROM ".glbDBObj::dbObj($this->dbkey)->table($this->modid,2)." m ";
        if($this->join) $this->from .= " $this->join ";
        return empty($cfg[1]) ? $this->resLister() : $this->resPager();
    }
    function resLister(){ 
        $offset = empty($this->sqlArr['offset']) ? '' : $this->sqlArr['offset'].',';
        $ordLimit = "ORDER BY ".$this->sqlArr['ofull']." LIMIT $offset".$this->sqlArr['limit'];
        $this->sqlAll = "SELECT {$this->from} {$this->where} $ordLimit"; 
        $this->re = $this->db->query($this->sqlAll);
        return $this->re;  
    }
    function resPager(){ 
        global $_cbase;
        $order = "m.".$this->sqlArr['order'];
        $pg = new comPager($this->from,$this->where,$this->sqlArr['limit'],$order); 
        $pg->set('odesc',$this->sqlArr['odesc']); 
        $pg->set('opkey',0); 
        $this->re = $pg->exe($this->dbkey);
        $this->sqlAll = $pg->sql; 
        // $idfirst = ''; $idend = '';
        $burl = basReq::getUri(-1,'','page|prec|ptype|pkey');
        $burl = strpos($burl,'mkv=') ? $burl : str_replace('.php?','.php?mkv=',$burl); 
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show(0,0,'',$burl)."</div>";
        $_cbase['page']['prec'] = $pg->prec; 
    }
    
    function getData(){ 
        return $this->re;
    }

}
