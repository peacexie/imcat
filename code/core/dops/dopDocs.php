<?php
(!defined('RUN_INIT')) && die('No Init');

// dopDocs(data OP for Docs)
class dopDocs extends dopBase{    

    public $kfix  = 'yyyy-md-';
    public $ktmp  = '31';
    public $tbext = '';
    public $_kid = 'did';
    public $_kno = 'dno';

    //function __destory(){  }
    function __construct($cfg,$percheck=array()){ 
        $mod = $cfg['kid'];
        parent::__construct($cfg,$cfg['pid']."_$mod");
        if(!empty($percheck)){
            dopCheck::addInit($cfg,$percheck);
        }
        $this->typfid = $this->so->typfid = 'catid';
        $this->dskey  = $this->so->dskey  = 'title'; 
        $this->order  = $this->so->order  = req('order','did'); 
        $this->tbext  = "dext_$mod";
    }
    // 翻页条,批量操作
    function pgbar($idfirst,$idend){
        $pg = $this->pg->show($idfirst,$idend);
        $op = "".basElm::setOption(lang('flow.op_op3'),'',lang('flow.op0_bacth'));
        dopFunc::pageBar($pg,$op);
    }
    // 搜索条 // check,fields
    function sobar($msg='',$width=30){ 
        $mod = $this->mod;
        $sbar = "\n".$this->so->Type(90,lang('flow.op0_cat')); 
        if(method_exists($this,"sobar_$mod")){ //中间部分定制
            $sbar .= $this->{"sobar_$mod"}($msg,$width);
        }else{
            $sbar .= "\n&nbsp; ".$this->so->Word(80,80,lang('flow.op0_filt'));
            $sbar .= "\n&nbsp; ".$this->so->Show(60);
        }
        $sbar .= "\n&nbsp; ".$this->so->Order(array('did' => lang('flow.dops_ordkidd'),'did-a' => lang('flow.dops_ordkida'),),80);
        $this->so->Form($sbar,$msg,$width);
    }
    // 搜索条:模块(pro)扩展
    //function sobar_pro($msg='',$width=30){}
    
    // 属性设置
    function fmProp(){ 
        dopFunc::fmSafe();
        echo "<tr><th nowrap>".lang('flow.title_attrset')."</th><th class='tr'>---</th></tr>\n";
        glbHtml::fmae_row(lang('flow.title_attrtitle'),' &nbsp; ID:'.$this->fmSetID()); //'显示:'.$this->fmShow().
        $this->fmAE3();
    }
    // svEKey，
    function svEKey(){
        $this->svMoveFiles($this->fme['did']);
        return preg_replace('/[^0-9A-Za-z\.\-]/','',$this->fme['did']);
    }
    
    // opDelete。
    function opDelete($id){
        parent::opDelete($id);
        $this->db->table($this->tbext)->where("did='$id'")->delete();      
        return 1;
    }
    // opCopy。
    function opCopy($id){ //docs,users
        // get-kid
        $kar = glbDBExt::dbAutID($this->tbid,$this->kfix,$this->ktmp);
        $kid = $kar[0]; $kno = $kar[1];    
        // insert-2
        foreach(array($this->tbid, $this->tbext) as $tabid){
            $fm = $this->db->table($tabid)->where("did='$id'")->find();
            $fm['did'] = $kid;
            $this->db->table($tabid)->data(basReq::in($fm))->insert();    
        }
        return 1;
    }
    
}
