<?php
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
        $sfrom = "m.* FROM ".db()->table($this->sqlArr['tabid'],2)." m ";
        $where = $this->whrStr; 
        $pg = new comPager($sfrom,$where,$this->sqlArr['limit'],"m.".$this->sqlArr['order']); 
        $pg->set('odesc',$this->sqlArr['odesc']); 
        $pg->set('opkey',$this->sqlArr['ordef']==$this->sqlArr['order'] ? 1 : 0); 
        $this->re = $pg->exe(); 
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
        /*$mkv = req('mkv','','Key',24);
        if(empty($mkv) && !empty($_cbase['mkv']['mkv'])){
            $mkv = $_cbase['mkv']['mkv']; 
        }*/
        if(strpos($scname,'plus/ajax/cron.php') || strpos($scname,'plus/ajax/jshow.php')){
            $burl = surl(0)."?mkv=$mkv";
        }else{
            $burl = basReq::getUri(-1,'','page|prec|ptype|pkey'); 
            $burl = strstr($burl,'mkv=') ? $burl : str_replace('.php?','.php?mkv=',$burl);     
        }
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show($idfirst,$idend,'',$burl)."</div>";
        $_cbase['page']['prec'] = $pg->prec; 
    }
    
    function getData(){ 
        //$this->debug('pgbar');
        $re = $this->re;
        return $this->getJoin($re);
    }

}
