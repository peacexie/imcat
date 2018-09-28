<?php
namespace imcat\rest;

use imcat\basOut;
use imcat\basReq;
use imcat\comToken;
use imcat\dopFunc;
use imcat\glbConfig;
use imcat\glbDBObj;
use imcat\glbDBExt;
use imcat\glbHtml;
use imcat\glbError;
use imcat\devBase;

class _defCtrl{
    
    public $mtypes = array('docs','users','coms','types'); // , 'advs'
    public $mkeys = array('table','list','add','edit','del');
    public $perms = array();
    public $gap = 0; // 不用?
    public $db = '';
    
    public $ucfg = array();
    public $vars = array();
    public $pid = '';
    public $dop = '';
    public $token = '';

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->check();
        $this->idop();
        $this->perms = comToken::perm($this->token,$this->mod,$this->key);
        $this->gap = comToken::limit($this->token,$this->mod,$this->key);
        $this->db = glbDBObj::dbObj();
    }

    // 结构
    function tableAct(){
        $retype = basReq::val('retype'); 
        $alltab = $this->db->tables(1);
        $tabs = array();
        foreach ($alltab as $k=>$row) {
            if(!strpos($row['Name'],"_".$this->mod)) continue;
            $fields = glbDBExt::dbComment($row['Name']);
            $row['Comment'] = empty($fields[0]['_rem']) ? $row['Comment'] : $fields[0]['_rem'];
            unset($fields[0]['_rem']);
            $row['_fields'] = $fields; 
            $tabs[$k] = $row;
        }
        if($retype){ return $this->view($tabs); }
        $data = devBase::dbDict($tabs);
        $a1 = array('>Default<','[Refresh]');
        $a2 = array('>Def.<',   '');
        $data = str_replace($a1,$a2,$data);
        $this->view($data);
    }
    // 列表
    function homeAct(){
        $tmp = glbDBExt::getTable($this->mod,'arr');
        $tab = $tmp[0]; $kid = $tmp[1];
        $page = basReq::val('page',1,'N'); if($page<1) $page = 1;
        $limit = basReq::val('limit',50,'N'); if($limit<1 || $limit>1000) $limit = 50; 
        $offset = ($page-1)*$limit;
        $order = "$kid ".(basReq::val('odesc') ? 'DESC' : '');
        $list = $this->db->table($tab)->limit("$offset,$limit")->order($order)->select();
        if($this->pid=='docs'){
            dopFunc::joinDext($list,$this->mod, 'did');
        }
        $this->view($list);
    }

    function addAct(){
        if(empty($this->skipMain)){
            $this->dop->svAKey(0); 
            if($this->pid=='docs'){
                $id = $this->dop->fmu['did'] = $this->dop->fmv['did'];
            }elseif($this->pid=='users'){
                $this->dop->svAccount('add'); 
                $id = $this->fmv['uid'];
            }elseif($this->pid=='coms'){
                $this->dop->svPKey('add');
                $id = $this->fmv['cid'];
            }
            $this->db->table($this->dop->tbid)->data($this->dop->fmv)->insert(); 
            if($this->pid=='docs'){
                $this->db->table($this->dop->tbext)->data($this->dop->fmu)->insert(0); 
            }
        }
        $this->view(array($id,$this->pid));
    }
    function editAct(){
        $id = basReq::val('id');
        if(empty($this->skipMain)){
            //$id = $dop->svEKey();
            if($this->pid=='docs'){
                $this->dop->fmu['did'] = $id;
                $this->db->table($this->dop->tbext)->data($this->dop->fmu)->replace(0);
            }elseif($this->pid=='users'){
                $this->dop->svAccount('edit'); 
            }elseif($this->pid=='coms'){
                $this->dop->svPKey('edit');
            }
            $this->db->table($this->dop->tbid)->data($this->dop->fmv)->where("did='$id'")->update();
        }
        $this->view(array($id,$this->pid));
    }
    function delAct(){
        $id = basReq::val('id');
        if(empty($this->skipMain)){
            $this->dop->opDelete($id);
        }
        $this->view(array($id,$this->pid));
    }
    
    // upd-state, show-data
    function view($data=array(),$die=1){
        $exact = $this->key.'After';
        if(method_exists($this,$exact)){
            $this->$exact($this->dop); // 后续数据调整
        }
        comToken::upd($this->token,$this->mod,$this->key);
        if(empty($data)) return;
        if(is_string($data)) die($data);
        $retype = basReq::val('retype'); 
        $retype || $retype = 'json'; 
        if(basReq::val('debug')){
            glbHtml::head('html'); 
            dump($data);
        }else{
            glbHtml::head($retype); 
            echo basOut::fmt($data,$retype);
        }
        if($die) die();
    }

    // check:token,mod,key
    function check(){
        $this->token = basReq::val('token');
        if(empty($this->token)){
            glbError::show("Error: Empty Token!");
        }
        $groups = glbConfig::read('groups');
        $this->mod = $this->ucfg['mod'];
        $this->key = empty($this->ucfg['key']) ? 'list' : $this->ucfg['key'];
        if(!isset($groups[$this->mod])){
            glbError::show("Error: [$this->mod]");
        }
        $this->pid = $groups[$this->mod]['pid'];
        $this->title = $groups[$this->mod]['title'];
        if(!in_array($this->pid,$this->mtypes)){
            glbError::show("Error: [$this->mod] (pid=$this->pid)");
        }
        if(!in_array($this->key,$this->mkeys)){
            glbError::show("Error: [$this->mod] (kid=$this->key)");
        }
        glbHtml::dallow('*');
    }
    // init-perm, init-dop, init-db
    function idop(){
        $_tmp = array(
            'docs' =>array('dopDocs','did'),
            'users'=>array('dopUser','uid'),
            'coms' =>array('dopComs','cid'),
        ); 
        if(isset($_tmp[$this->pid])){
            $_cfg = read($this->mod); 
            $_cls = '\\imcat\\'.$_tmp[$this->pid][0]; 
            $this->dop = new $_cls($_cfg); 
            if(in_array($this->key,array('add','edit'))){
                $this->dop->svFields();
            }
            $exact = $this->key.'Before';
            if(method_exists($this,$exact)){
                $this->$exact($this->dop); // 预处理数据
            }
        }else{
            if(in_array($this->key,array('add','edit','del'))){
                glbError::show("Error [$this->mod] (act=$this->key)");
            } 
        }
        
    }

}
