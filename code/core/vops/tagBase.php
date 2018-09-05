<?php
/*
*/
// 标签解析 基类

class tagBase{
    
    public $modid = '';
    public $paras = array();
    public $mcfg = array();

    public $sqlArr = array();
    public $sqlAll = '';
    public $whrArr = array();
    public $whrStr = '';
    public $jonArr = array();
    
    public $re = array();
    public $exFld1 = array('aip','eip','atime','etime','show',);
    public $exFld2 = array(); //did,cid,uid,aid,kid ...
    public $fpk = 'kid'; //
    public $db = NULL;
    
    function __construct($paras=array()) {
        $this->paras = $paras;
        $this->db = glbDBObj::dbObj(); 
        $this->setModid();
        $this->setFrom();
        $this->pJoin();
        $this->mcfg = glbConfig::read($this->modid);
    }
    
    // 所有公用: [idfix,top] -=> array('idfix','top')
    function p1Cfg($key='modid'){
        foreach($this->paras as $k=>$p1){
            if($p1[0]==$key){ 
                return $p1;
            }
        }
        return array();
    }
    // 所有公用: 
    function setModid(){
        $para = $this->p1Cfg('modid');
        $this->modid = empty($para[1]) ? $this->mod : $para[1];
    }
    // List, Page, One: 
    function setFrom(){ 
        $_groups = glbConfig::read('groups');
        $mod = $this->modid;
        if(empty($_groups[$mod])){ 
            glbError::show("{$mod} NOT Found!",0); 
        }
        if($_groups[$mod]['pid']=='docs'){
            $tabid = 'docs_'.$mod;
            $ordef = $fpk = 'did';
            $exFld2[] = 'did';
        }elseif($_groups[$mod]['pid']=='users'){
            $tabid = 'users_'.$mod;
            $ordef = 'atime'; 
            $exFld2[] = $fpk = 'uid';
        }elseif($_groups[$mod]['pid']=='advs'){
            $tabid = 'advs_'.$mod;
            $ordef = 'atime';
            $exFld2[] = $fpk = 'aid';
        }elseif($_groups[$mod]['pid']=='coms'){    
            $tabid = 'coms_'.$mod;
            $ordef = $fpk = 'cid';
            $exFld2[] = 'cid';
        }elseif($_groups[$mod]['pid']=='types'){    
            $tabid = empty($_groups[$mod]['etab']) ? 'types_common' : 'types_'.$mod;
            $ordef = $fpk = 'kid'; //不使用
            $exFld2[] = 'kid';    
        }
        $this->sqlArr['tabid'] = $tabid;
        $this->sqlArr['prefix'] = $this->db->pre; 
        $this->sqlArr['suffix'] = $this->db->ext; 
        $this->sqlArr['ordef'] = $ordef;
        $this->fpk = $fpk;
        $this->exFld2 = $exFld2;
    }
    
    // Join (目前仅支持一个join)
    // [join,detail]      -=>  INNER JOIN dext_news    d ON d.did=m.did 
    function pJoin(){ 
        $cfg = $this->p1Cfg('join');
        $this->jonArr = $cfg;
    }
    
    // [order,0,kid1+f02+f03,1]
    // order, odesc
    function pOrder(){ 
        $cfg = $this->p1Cfg('order');
        if(empty($cfg)){
            $order = '';
        }elseif(!empty($cfg[1])){
            $order = $cfg[1]; //认证?
        }elseif(empty($cfg[1]) && !empty($cfg[2])){
            $order = basReq::val('order','','Key',24); 
            $a = explode('+',$cfg[2]);
            if($order && !in_array($order,$a)){ //认证?
                $order = '';    
            }
        }else{
            $order = '';    
        }
        $order || $order = $this->sqlArr['ordef'];
        $odesc = isset($cfg[3]) ? $cfg[3] : basReq::val('odesc','1','N');
        $this->sqlArr['order'] = $order;
        $this->sqlArr['odesc'] = $odesc;
        $this->sqlArr['ofull'] = 'm.'.$order.($odesc ? ' DESC' : '');
    }
    
    // 
    function pStype(){ 
        $cfg = $this->p1Cfg('stype'); 
        if(empty($cfg)) return;
        //优先取标签属性,再去url的stype
        $stype = empty($cfg[1]) ? vopUrl::umkv('key','stype') : $cfg[1];
        if(empty($stype)) return;
        $_groups = glbConfig::read('groups'); 
        $pid = $_groups[$this->modid]['pid'];
        if($stype && in_array($pid,array('docs','advs'))){
            $sql = basSql::whrTree($this->mcfg['i'],'m.catid',$stype);
            $this->whrArr[] = $sql ? substr($sql,5) : '';
        }elseif($stype && in_array($pid,array('users'))){
            $this->whrArr[] = "m.grade='$stype'";
        }else{ //交互等,无stype
            
        }
    }
    
    // `-=>', [where,did=`2004-33-2ycx`]
    function pWhere(){ 
        $whr = ''; 
        foreach($this->whrArr as $w){
            $whr .= ' AND '.$w;
        }
        $cfg = $this->p1Cfg('where'); 
        if(!empty($cfg[1])){ 
            $whr .= (substr($cfg[1],0,5)!=' AND ' ? ' AND ':'').$cfg[1];  
        }
        if(substr($whr,0,5)==' AND ') $whr = substr($whr,5);
        $this->whrStr = $whr; 
    }
    
    function getJoin($re){ 
        if(empty($re)) return $re;
        $minfo = glbConfig::read($this->modid); 
        if(!empty($this->jonArr[1]) && $this->jonArr[1]=='detail'){
            if(in_array($minfo['pid'],array('docs','types'))){
                dopFunc::joinDext($re,$this->modid, $minfo['pid']=='docs'?'did':'kid');
            }
        }
        return $re;
    }
    
    function getData(){ 
        $type = $this->modid;
        $re[0] = array('kid'=>'docs','did'=>'yyyy-12-7890','title'=>'4-title'.$type);
        $re[1] = array('kid'=>'advs','aid'=>'yyyy-34-1234','title'=>'5-title'.$type);
        $re[2] = array('kid'=>'coms','cid'=>'yyyy-56-5678','title'=>'6-title'.$type);
        return $re;
    }

    function debug($key=''){ 
        if($key){
            echo "<pre>debug\n";
            print_r($this->$key);    
            echo "</pre>";
        }else{
            print_r($this->sqlArr);
            print_r($this->sqlAll);
            print_r($this->whrArr);
            print_r($this->whrStr);    
            if(isset($this->pgbar)) print_r($this->pgbar);    
        }
    }
    
}
