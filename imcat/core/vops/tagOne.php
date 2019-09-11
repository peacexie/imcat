<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

/*

*/
// 标签解析 (单条数据)类
class tagOne extends tagBase{
    
    //public $whrArr = array();
    
    function __construct($paras=array()) {
        parent::__construct($paras); 
        $this->pOrder();
        $this->pStype();
        $this->pWhere();
    }
    
    function getData(){ 
        $sfrom = "m.* FROM ".glbDBObj::dbObj()->table($this->sqlArr['tabid'],2)." m ";
        $where = empty($this->whrStr) ? '' : "WHERE ".$this->whrStr;
        $this->sqlAll = "SELECT $sfrom $where ORDER BY ".$this->sqlArr['ofull']." LIMIT 1"; 
        $res = $this->db->query($this->sqlAll); 
        if(!empty($res[0])){
            $res = $this->getJoin($res);
            $re = $res[0];
        }else{
            $re = array();
        }
        $this->re = $re; 
        return $re;
    }

}
