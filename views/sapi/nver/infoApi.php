<?php
namespace imcat;

class infoApi extends bextApi{
    
    public $base = 'http://imblog.txjia.com/root/run/sapi.php';

    function homeAct(){ 
        return $this->_defActs();
    }

    function listAct(){
        return $this->_defActs();
    }

    function _detailAct(){
        return $this->_defActs();
    }

    function wallAct(){
        return $this->_defActs();
    }

    function _defActs(){
        //echo $sss; echo 444/0; die();
        $q = empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING'];
        $sk = req('sk');
        $fq = str_replace("&sk=$sk", "", $q);
        $fp = "/remote/sapi--".comHttp::fpCache($fq, []);
        $res = extCache::cfGet($fp, 30, 'vars', 'str');
        if(!$res){
            $res = comHttp::doGet("$this->base?$q");
            if(strpos($res,'"ercode":0,')>0 && !strpos($res,'<b>Notice</b>') && !strpos($res,'<b>Warning</b>')){
                extCache::cfSet($fp, $res, 'vars');
            }
        }
        $res = json_decode($res,1);
        return $res;
    }

}
