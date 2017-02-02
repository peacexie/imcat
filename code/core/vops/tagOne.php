<?php
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
        $sfrom = "m.* FROM ".db()->table($this->sqlArr['tabid'],2)." m ";
        $where = empty($this->whrStr) ? '' : "WHERE ".$this->whrStr;
        $this->sqlAll = "SELECT $sfrom $where ORDER BY ".$this->sqlArr['ofull']." LIMIT 1"; 
        $this->re = $this->db->query($this->sqlAll); //echo $this->sqlAll;
        if(!empty($res[0])){
            $re = $res[0];
        }else{
            $re = array();    
        }
        $this->re = $re;
        return $this->getJoin($re);
            
    }

}
