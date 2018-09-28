<?php
namespace imcat;
// sms_emhttp；
class smsEmhttp{
    
    public $userid; // 序列号
    public $userpw; // 密码/Key
    var $urlmap = array(
        '3SDK' => 'http://sdkhttp.eucp.b2m.cn/sdkproxy/',
        '6SDK' => 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/',
        '_NUL' => 'http://sdkhttp.eucp.b2m.cn/sdkproxy/', //按官方文档
    );
    public $baseurl = '';
    public $arr = array(); // 参数
    public $way = 0; // http方式
    
    // 初始化
    function __construct($cfgs=array()){
        $this->arr['cdkey'] = $this->userid = $cfgs['user'];
        $this->arr['password'] = $this->userpw = $cfgs['pass'];
        $fix = substr($this->userid,0,4);
        if(isset($this->urlmap[$fix])){
            $this->baseurl = $this->urlmap[$fix];    
        }else{
            $this->baseurl = $this->urlmap['_NUL'];    
        }
        $this->way && comHttp::setWay($this->way);
    }
    
    // 发送短信；(utf-8编码)
    function sendSMS($mobiles,$content){
        if(is_array($mobiles)) $mobiles = implode(',',$mobiles);
        $content = comConvert::autoCSet($content,cfg('sys.cset'),"utf-8");
        $arr = $this->arr;
        $arr['phone'] = $mobiles;
        $arr['message'] = $content;
        $html = comHttp::doPost("{$this->baseurl}sendsms.action", $arr, 3); 
        $re = $this->fmtInfo($html); 
        if($re[0]=='1') $re[1] = '发送成功';
        return $re;
    }
    
    // 余额查询 
    function getBalance(){
        //return array(1,123.4);
        $url = "querybalance.action?cdkey=$this->userid&password=$this->userpw";
        $html = comHttp::doGet("{$this->baseurl}$url", 3); 
        $re = $this->fmtInfo($html,1); 
        return $re;
    }
    
    // 返回值-描述 对应表
    // - ...<error>0</error><message>-1103.0</message>
    // - ...<error>0</error><message>3718.2</message>
    function fmtInfo($html,$isq=0){
        $erno = basElm::getVal($html,'error'); 
        $emsg = basElm::getVal($html,'message'); 
        if(empty($html)){ 
            return array('',"sms-Server Error:[$html]");    
        }elseif($isq && substr($emsg,0,1)=='-'){ 
            $erno = str_replace(array(".0"),array(''),$emsg); 
            $erstr = $this->reState($erno);
            return array('-1',"Error:[$erstr]");    
        }elseif($isq){ 
            return array('1',$emsg);    
        }elseif($erno==='0'){ 
            return array('1',$emsg);    
        }else{ 
            $erstr = $this->reState($erno);
            return array('-1',"Error:[$erstr]");
        }
    }
    
    // 返回值-描述 对应表
    function reState($no){
        $a = array(
            '-1'=>'系统异常',
            '-101'=>'命令不被支持',
            '-102'=>'用户信息删除失败',
            '-103'=>'用户信息更新失败',
            '-104'=>'指令超出请求限制',
            '-111'=>'企业注册失败',
            '-117'=>'发送短信失败',
            '-118'=>'获取MO失败',
            '-119'=>'获取Report失败',
            '-120'=>'更新密码失败',
            '-122'=>'用户注销失败',
            '-110'=>'用户激活失败',
            '-123'=>'查询单价失败',
            '-124'=>'查询余额失败',
            '-125'=>'设置MO转发失败',
            '-127'=>'计费失败零余额',
            '-128'=>'计费失败余额不足',
            '-1100'=>'序列号错误,序列号不存在内存中,或尝试攻击的用户',
            '-1102'=>'序列号正确,Password错误',
            '-1103'=>'序列号正确,Key错误',
            '-1104'=>'序列号路由错误',
            '-1105'=>'序列号状态异常 未用1',
            '-1106'=>'序列号状态异常 已用2 兼容原有系统为0',
            '-1107'=>'序列号状态异常 停用3',
            '-1108'=>'序列号状态异常 停止5',
            '-113'=>'充值失败',
            '-1131'=>'充值卡无效',
            '-1132'=>'充值卡密码无效',
            '-1133'=>'充值卡绑定异常',
            '-1134'=>'充值卡状态异常',
            '-1135'=>'充值卡金额无效',
            '-190'=>'数据库异常',
            '-1901'=>'数据库插入异常',
            '-1902'=>'数据库更新异常',
            '-1903'=>'数据库删除异常',
        );    
        return isset($a[$no]) ? $no.':'.$a[$no] : "$no:(未知错误)";
    }

}
