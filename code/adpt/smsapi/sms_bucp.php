<?php
// sms_bucp；
//http://sdk2.entinfo.cn/z_send.aspx?sn=SDK-DHX-010-xxx&pwd=222&mobile=13712748215&content=Test你好
//http://sdk2.entinfo.cn/z_balance.aspx?sn=SDK-DHX-010-xxx&pwd=222
//http://117.79.237.3:8060/webservice.asmx/

class sms_bucp{
    
    public $userid; // 序列号
    public $userpw; // 密码/Key 
    public $baseurl = 'http://sdk2.entinfo.cn/'; 
    public $arr = array(); // 参数
    public $way = 0; // http方式; 0-自动,1-curl_init, 2-fsockopen, 3-file_get_contents
    
    // 初始化
    function __construct($cfgs=array()){
        $this->arr['sn'] = $this->userid = $cfgs['user'];
        $this->arr['pwd'] = $this->userpw = $cfgs['pass'];
        $this->way && comHttp::setWay($this->way);
    }
    
    // 发送短信；(gb2312编码)
    function sendSMS($mobiles,$content){
        if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
        $content = comConvert::autoCSet($content,cfg('sys.cset'),"gb2312");
        $arr = $this->arr;
        $arr['mobile'] = $mobiles;
        $arr['content'] = $content;
        $html = comHttp::doPost("{$this->baseurl}z_send.aspx?", $arr, 3); 
        $re = $this->fmtInfo($html); 
        return $re;
    }
    
    // 余额查询 
    function getBalance(){
        //return array(1,1234);
        $url = "z_balance.aspx?sn=$this->userid&pwd=$this->userpw"; 
        $html = comHttp::doGet("{$this->baseurl}$url", 3);  
        $re = $this->fmtInfo($html,1); 
        return $re;
    }
    
    function fmtInfo($html,$isq=0){
        if($isq){
            $re = $html<0 ? array('-1',"[".$this->reInfo($html)."]") : array(1,$html);
        }else{
            $re = in_array($html,array('0','1')) ? array('1',"发送OK") : array('-1',"Error:[".$this->reInfo($html)."]");
        }
        return $re;
    }
    
    // 返回值-描述 对应表
    function reInfo($no){
        $a = array(
            '0'=>'成功',
            '-1'=>'发送失败',
            '-2'=>'参数错误',
            '-3'=>'序列号密码错误',
        );    
        return isset($a[$no]) ? $no.':'.$a[$no] : "$no:(未知错误)";
    }
    
}
