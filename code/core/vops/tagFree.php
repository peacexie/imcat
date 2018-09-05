<?php
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
        $this->pPgbar();
    }
    
    // Dbkey: 
    function setDbkey($paras=array()){
        $this->paras = $paras;
        $para = $this->p1Cfg('dbkey'); 
        $this->dbkey = empty($para[1]) ? $this->dbkey : $para[1];
    }
    // pgbar: 
    function pPgbar(){
        $cfg = $this->p1Cfg('pgbar'); 
        if(!empty($cfg[1])){
            return $this->dPager();
        }else{
            return $this->dLister();
        }
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
  
    function dLister(){ 
        $sfrom = "m.* FROM ".glbDBObj::dbObj($this->dbkey)->table($this->modid,2)." m ";
        if($this->join) $sfrom .= " $this->join ";
        $offset = empty($this->sqlArr['offset']) ? '' : $this->sqlArr['offset'].','; 
        $this->sqlAll = "SELECT $sfrom {$this->where} ORDER BY ".$this->sqlArr['ofull']." LIMIT $offset".$this->sqlArr['limit']; 
        $this->re = $this->db->query($this->sqlAll);
        return $this->re;  
    }
    function dPager(){ 
        global $_cbase; 
        $sfrom = "m.* FROM ".glbDBObj::dbObj($this->dbkey)->table($this->modid,2)." m ";
        if($this->join) $sfrom .= " $this->join ";
        $pg = new comPager($sfrom,$this->where,$this->sqlArr['limit'],"m.".$this->sqlArr['order']); 
        $pg->set('odesc',$this->sqlArr['odesc']); 
        $pg->set('opkey',0); 
        $this->re = $pg->exe($this->dbkey);
        $this->sqlAll = $pg->sql; 
        $idfirst = ''; $idend = '';
        if($this->re){
            $i = current($this->re); 
            $idfirst = current($i); 
            $i = end($this->re); 
            $idend = current($i); 
        }
        $scname = $_SERVER["SCRIPT_NAME"]; //REQUEST_URI
        $mkv = vopUrl::umkv('mkv');
        if(strpos($scname,'plus/ajax/cron.php') || strpos($scname,'plus/ajax/jshow.php')){
            $burl = vopUrl::fout(0)."?mkv=$mkv";
        }else{
            $burl = basReq::getUri(-1,'','page|prec|ptype|pkey');
            $burl = strpos($burl,'mkv=') ? $burl : str_replace('.php?','.php?mkv=',$burl); 
        } 
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show($idfirst,$idend,'',$burl)."</div>";
        $_cbase['page']['prec'] = $pg->prec; 
    }
    
    function getData(){ 
        return $this->re;
    }

}
