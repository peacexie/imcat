<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

// dopExtra(data OP for Extra)
class dopExtra extends dopBase{    

    #public $ktmp = '22';
    public $_kid = 'kid';

    //function __destory(){  }
    function __construct($tab='',$cfg=array()){ 
        empty($cfg['pid']) && $cfg['pid'] = '';
        parent::__construct($cfg,$tab);
        if(isset($cfg['typfid'])) $this->typfid = $this->so->typfid = 'catid';
        $this->soset($cfg);
        $dord = basReq::val('order','atime'); // cfg.soorders
        if(isset($cfg['soorders']) && !isset($cfg['soorders'][$dord])){
            die("Error `{$dord}`");
        }
        $this->order = $this->so->order = empty($cfg['order']) ? $dord : $cfg['order'];
    }
    // 翻页条,批量操作
    function pgbar($idfirst,$idend,$ops="(null)"){
        if($ops=='(null)') $ops="dele|".basLang::show('flow.op_delsel')."\ndnow|".basLang::show('flow.op_delnow')."";
        $pg = $this->pg->show($idfirst,$idend);
        $op = "".basElm::setOption($ops,'',basLang::show('flow.op0_bacth'));
        dopFunc::pageBar($pg,$op);
    }
    // 搜索条 // check,fields
    function sobar($msg='',$width=30,$sor2='-1',$khid=array()){ 
        $mod = $this->mod; $sbar = ''; 
        $sbar .= "\n&nbsp; ".$this->so->Word(80,80,basLang::show('flow.op0_filt'),$this->sofields);
        if(!empty($this->soarField)){
            $sbar .= "\n&nbsp; $this->soarMsg:".$this->so->Area(1,50,$this->soarField);
        }
        $sbar .= "\n&nbsp; ".$this->so->Order($this->soorders,100,basLang::show('flow.op0_order'),$sor2);
        $this->so->Form($sbar,$msg,$width,$khid);
    }
    
    // opDelnow。
    function opDelnow($days=0){ 
        $where = empty($this->so->whrstr) ? '' : (substr($this->so->whrstr,5));
        if(empty($where)) return basLang::show('msg_delxng');
        $this->db->table($this->tbid)->where($where)->delete(); 
        return basLang::show('msg_delxok',basJscss::jsShow($where)); 
    }    
    // 搜索 init
    function soset($cfg=array()){ 
        $this->sofields = $cfg['sofields'];
        foreach($this->sofields as $k=>$v){
            if(is_numeric($k))    {
                $this->so->cfg['f'][$v] = $v;
            }else{
                $this->so->cfg['f'][$k] = $v;    
            }
        }
        $this->soorders = $cfg['soorders'];
        if(!empty($cfg['soarea'])){
            $this->soarField = $cfg['soarea'][0];
            $this->soarMsg = $cfg['soarea'][1];
            $this->so->fext = array($this->soarField=>$this->soarMsg);
        }
    }
    
}
