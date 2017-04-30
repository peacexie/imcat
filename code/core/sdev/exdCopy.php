<?php

// ...类exdCopy
class exdCopy{    

    private $tab = '';
    private $tabOld = '';
    private $mod = '';
    private $pid = '';
    private $db = null;

    private $exd_tabs = array(
        'exd_crawl',
        'exd_oimp',
        'exd_psyn',
    );

    //function __destory(){  }
    function __construct($mtab='', $type='mdata'){ 
        $_groups = read('groups'); 
        if($type=='tabid' && in_array($mtab,$this->exd_tabs)){
            $this->setTable($mtab);
        }elseif(isset($_groups[$mtab])){
            $this->mod = $mtab;
            $this->pid = $_groups[$this->mod]['pid'];
            $tabid = glbDBExt::getTable($this->mod,0);
            $this->setTable($tabid);
        }else{
            die(__CLASS__.'::'.__FUNCTION__);
        }
        $this->db = db();
    }
    function setTable($tab=''){ 
        if(empty($tab)){
            $this->tab = $this->tabOld;
        }else{
            $this->tab = $this->tabOld = $tab;
        }
    }
    
    // 复制一条数据(文档/资讯|会员/交互)
    // ('2015-9c-p2k1', '2016-9c-p2k1', 'New Title~');
    function cdata($id, $nid='', $ntilte=''){
        $kid = glbDBExt::getKeyid($this->mod);
        $this->crow("$kid='$id'", array($kid=>$nid,'title'=>$ntilte));
        if(in_array($this->pid,array('docs'))){
            $tbext = glbDBExt::getTable($this->mod,1);
            $this->setTable($tbext);
            $this->crow("$kid='$id'", array($kid=>$nid));
            $this->setTable(''); //还原            
        }
    }

    // 复制一个方案
    // ('demo_dede', 'copy_dede2', 'Copy织梦导入');
    function cplan($id='', $nkey='', $ntilte=''){
        $this->crow("kid='$id'", array('kid'=>$nkey,'title'=>$ntilte));
        $this->setTable('exd_sfield');
        $this->cbat("model='$id'", array('model'=>$nkey));
        $this->setTable('');
    }

    // 复制一组数据
    // (array("model='demo_data'"), array('model'=>'copy_dede5'));
    function cbat($old, $new){
        $whr = $this->_getWhr($old);
        $list = $this->db->table($this->tab)->where($whr)->select();
        if(empty($list)) return false; 
        foreach ($list as $row) {
            $this->crow(0, $new, $row); 
        }
    }
    
    // 复制一行数据
    // $new : array("kid"=>"newid") / array("model"=>"newmod")
    function crow($old, $new, $row=array()){
        if(empty($row)){
            $whr = $this->_getWhr($old); 
            $row = $this->db->table($this->tab)->where($whr)->find(); 
            if(empty($row)) return false;
        }
        $rn = array(); $arr = array('atime','etime');
        foreach ($row as $key => $value) {
            if(in_array($key,$arr)){
                $rn[$key] = $_SERVER["REQUEST_TIME"];
            }else{
                $rn[$key] = isset($new[$key]) ? $new[$key] : $value;
            }
        }
        return $this->db->table($this->tab)->data(basReq::in($rn))->insert();
    }

    // _getWhr
    // 格式："kid='detail'" 
    // array("kid='detail'","model='demo'")  
    // array("kid"=>"detail","model"=>"copyid")
    function _getWhr($arr){
        $whr = '';
        if(is_array($arr)){
            foreach ($arr as $value) {
                if(is_array($value)){
                    $first = reset($value);
                    $key = key($value);
                    $value = "`$key`='$first'";
                }
                $whr .= (empty($whr)?'':' AND ')."$value";
            }
        }else{
            $whr = $arr;
        }
        return $whr;
    }

    // xxxrepVal
    static function xxxrepVal($val){
        //$val = '';
        return $val;
    }

}

/*

*/

