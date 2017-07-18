<?php
(!defined('RUN_INIT')) && die('No Init');

// 标签解析 (数据列表)类
class tagPush extends tagList{
    
    //public $whrArr = array();

    static function check(){
        $user = usrBase::userObj('Admin');
        $grades = ",supper,ainfo,auser,advers,";
        return strpos($grades,$user->uperm['grade']);
    }
    static function load(){
        eimp('/layer/layer.js','vendui');
        eimp('/_pub/a_jscss/adpush.css');
        eimp('/_pub/a_jscss/adpush.js');
        $lngs = "{ps_pinfo:'".basLang::show('flow.ps_pinfo')."',ps_title:'".basLang::show('flow.ps_title')."'}";
        echo basJscss::jscode("Lang.push=$lngs;");
    }
    static function gets($ids){
        $db = glbDBObj::dbObj(); $res = array();
        $ids = str_replace(array("'",','),array("","','"),$ids);
        $sfrom = "aid,detail FROM ".$db->table('advs_adpush',2)." m ";
        $sqlAll = "SELECT $sfrom WHERE m.aid IN('$ids') ORDER BY `top`"; 
        $re1 = $db->query($sqlAll); 
        foreach($re1 as $row) {
            $res[$row['aid']] = comParse::jsonDecode($row['detail']); 
        } 
        return $res;
    }
    
    function __construct($paras=array()) {
        parent::__construct($paras); 
    }
    
    function getData(){ 
        $sfrom = "m.* FROM ".glbDBObj::dbObj()->table($this->sqlArr['tabid'],2)." m ";
        $where = empty($this->whrStr) ? '' : "WHERE ".$this->whrStr;
        $this->sqlAll = "SELECT $sfrom $where LIMIT 1"; //ORDER BY `top` 
        $re = $this->db->query($this->sqlAll); 
        if(empty($re[0]['detail'])){
            $re = array();
        }else{
            $re = comParse::jsonDecode($re[0]['detail']);
        }
        $this->re = $re; 
        return $re;
            
    }

    // ucfg.ctab
    // 1=Y;0=N
    static function opts($tab='1=Y;0=N',$val=''){ 
        $ops = '';
        if(strpos($tab,'.')>0){
            $arr = explode(',','000,'.cfg($tab));
            foreach ($arr as $iv) {
                $def = "$val"=="$iv" ? 'selected' : ''; 
                $ops .= "\n<option value='$iv' $def style='color:#$iv'>$iv</option>";
            } 
        }else{
            $ops = basElm::setOption($tab,$val,'Pick');
        }
        return $ops;
    }
}
