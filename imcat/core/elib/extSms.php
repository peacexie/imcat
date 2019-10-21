<?php
namespace imcat;

// 手机短信接口类
 
class extSms{

    public  $cfg_mchar = 70; // 一条信息,文字个数(小灵通65个字)
    public  $cfg_mtels = 200; // 一次发送,最多200个手机号码个数
    
    public  $api       = ''; //api接口类型(提供商)
    public  $smsdo     = NULL; //api对象
    public  $cfgs      = array(); //api配置
    
    public  $cnow      = array('name'=>'-','unit'=>'-'); //now配置
    public  $amap      = array(); //api列表
    
    //function __destory(){  }
    function __construct(){ 
        require DIR_IMCAT."/adpt/smsapi/api_cfgs.php"; // 加载
        $_cfgs = glbConfig::read('sms', 'ex');
        $api = @$_cfgs['cfg_api'];
        if($api && isset($_apis[$api])){ 
            $this->api = $api;
            $this->cfgs = $_cfgs; 
            $this->cnow = $_apis[$api];
            $cfile = 'sms'.ucfirst($this->api);
            // 统一实例化一个 api对象 // load sms libs
            require DIR_IMCAT."/adpt/smsapi/$cfile.php"; // 加载
            $class = "\\imcat\\$cfile";
            $this->smsdo = new $class($_cfgs); 
        }
        $this->amap = $_apis;
    }
    
    // 短信接口是否关闭
    function isClosed(){
        if(empty($this->api)){
            return true;
        }else{
            return false;
        } //&&$sms_cfg_api!='(close)'
    }
    
    // 余额查询
    // 结果说明：array(1,1234.5): 成功,余额为1234.5；array(-1,'失败原因'): 
    function getBalance(){ 
        return $this->smsdo->getBalance();    
    }
    
    /** 模板短信发送
     * @param    string    $mobiles     手机号码,参考sendSMS()
     * @param    array     $params      参数：array('code'=>'1234','name'=>'peace',)
     * @param    string    $tid         模版id
     * @param    string    $sign        签名
     **/
    function sendTid($mobiles, $tid='', $params, $cfgs=array()){ // , $sign='', $limit=1, $pid=''
        if(!method_exists($this->smsdo, 'sendTid')){
            return array(-1, "a:{sendTid}不支持！");
        }
        $sign = empty($cfgs['sign']) ? '' : $cfgs['sign'];
        $pid = empty($cfgs['pid']) ? 1 : $cfgs['pid'];
        $limit = empty($cfgs['limit']) ? 1 : $cfgs['limit'];
        $res = $this->smsdo->sendTid($mobiles, $params, $tid, $sign);
        $tid || $tid = '(def)'; $sign || $sign = '(def)';
        $this->loger($mobiles, json_encode($params)."(tid=$tid:sign=$sign)", $res, $limit, $pid); //写记录-db
        return $res;
    }

    /** 短信发送，支持短信模版替换，
     * @param    string    $mobiles     手机号码,参考sendSMS()
     * @param    string    $tpl         支持模版，如：{subject}{name}标记
     * @param    array     $source        替换源：array('subject'=>'hellow corp!','name'=>'peace',)
     * @param    string    $type         发送方式/发送身份,参考sendSMS()
     * @return    array    ---        结果数组,参考sendSMS()
     **/
    function sendTpl($mobiles, $tpl, $source, $cfgs=array()){
        $tpl = str_replace(array("\r\n","\r","\n"),array(' ',' ',' '),$tpl);
        if(preg_match_all('/{\s*([a-zA-Z_0-9]\w*)\s*}/i', $tpl, $matchs)){
            if(!empty($matchs[0])){
                foreach($matchs[0] as $v){
                    $k = str_replace(array('{','}'), '', $v);
                    $val = isset($source[$k]) ? $source[$k] : (isset($GLOBALS[$k]) ? $GLOBALS[$k] : "{\$$k}");
                    $tpl = str_replace($v, $val, $tpl);
                }
            }
        }
        return $this->sendSMS($mobiles, $tpl, 5, $cfgs);
    }
    
    /** 短信发送
     * @param    string    $mobiles     手机号码,array/string(英文逗号分开)
     * @param    string    $content     255个字符以内
     * @param    string    $limit       xxx ：
     * @return    array    ---        结果数组,如：array(1,'操作成功'): 
     **/
    function sendSMS($mobiles, $content, $limit=1, $cfgs=array()){
        $pid = empty($cfgs['pid']) ? '' : $cfgs['pid'];
        if(!method_exists($this->smsdo, 'sendSMS')){
            return array(-1, "a:{sendSMS}不支持！");
        }
        // 格式化 $mobiles,$content, 
        $atel = $this->telFormat($mobiles);
        $amsg = $this->msgCount($content);
        if(empty($atel)) return array('-2',basLang::show('sms_errtel'));    
        if(empty($amsg[0])) return array('-2',basLang::show('sms_errmsg'));
        $nmsg = count($atel)*$amsg[1];
        // 需扣费计算条数,检查余额
        $balance = $this->smsdo->getBalance(); 
        if((float)$balance[1]<=0){
            $mobiles = implode(',',$atel);
            $this->balanceWarn("--tels:$mobiles\n --cmsg:$content"); //写记录
            return array('-2',basLang::show('sms_charge0'));        
        } 
        if($limit && $limit<$nmsg){
            return array('-2',basLang::show('sms_charged'));    
        }
        // 发送及结果
        if(count($atel)>$this->cfg_mtels){ // 分组发送
            $groups = array_chunk($atel,$this->cfg_mtels);
            $res = array('-2',basLang::show('sms_msenderr'));
            $flag = false; //成功标记
            foreach($groups as $group){ 
                $res_temp = $this->smsdo->sendSMS($group, $content);
                if($res_temp[0]=='1'){ //只要一组发送成功,则都算成功.
                    $res = $res_temp;    
                }
            }
        }else{
            $res = $this->smsdo->sendSMS($atel, $content);
        }    
        $this->loger($atel, $amsg[0], $res, $nmsg, $pid); //写记录-db
        return $res;
    }
    
    // 写记录-db
    function loger($tel, $msg, $res, $nmsg=1, $pid=''){
        // 写记录-db
        $stel = is_array($tel) ? implode(',',$tel) : $tel; 
        if(strlen($stel)>255) $stel = substr($stel,0,240).'...'.substr($stel,strlen($stel)-5,255);
        $data = array( 
            'kid'=>basKeyid::kidTemp(), 'pid'=>$pid,
            'tel'=>$stel, 'msg'=>basReq::in($msg),
            'res'=>implode(':',$res),'api'=>$this->api,'amount'=>$nmsg,
        );
        $data = $data + basSql::logData();
        glbDBObj::dbObj()->table('plus_smsend')->data($data)->insert();
        // 扣钱 for 0test_balance.txt
        if($this->api=='0test' && $res[0]=='1'){
            $this->smsdo->deductingCharge($nmsg);
        }
    }


    /** 余额报警检测,余额报警记录
     * @param    int        $flag     int/string数字/
     *                    数字,多少小时被修改(记录了余额不足)过,
     *                    flag=str,记录信息内容
     * @return    NULL    
     **/
    function balanceWarn($flag){
        global $_cbase;
        $file = "debug/balance_apiwarn.wlog"; 
        comFiles::chkDirs($file,'dtmp');
        if(is_numeric($flag)){ //检查文件,多少时间(day)内修改过
            return extCache::cfGet("/$file",'24h');
        }else{ 
            $onlineip = $_cbase['run']['userip']; $data = ''; 
            if(file_exists($file)){
                $data = comFiles::get($file);
            }
            $fp = fopen(DIR_VARS."/$file", 'a');
            $data = date('Y-m-d H:i:s')."^ ".'mname'." ^ $onlineip \n $flag\r\n\r\n$data";
            flock($fp, 2); fwrite($fp, $data); fclose($fp);
        }
    }

    // 电话号码 格式化/过滤
    // 初始的电话号码array/string
    // 格式化并过滤后的电话号码
    function telFormat($tel){
        if(is_string($tel)){
            $tel = str_replace(array("-","("," ",')'), '', $tel);
            $tel = str_replace(array("\r\n","\r","\n",';'), ',', $tel);
            $arr = explode(',',$tel);
        }else{
            $arr = $tel;    
        }
        $arr = array_filter($arr);
        $re = array();
        for($i=0;$i<count($arr);$i++){
            //  手机/^1\d{4,10}$/; 95168合法号码/^[1-9]{1}\d{4,10}$/; 0769-12345678小灵通
            if(preg_match('/^\d{5,12}$/',$arr[$i])) $re[] = $arr[$i];
        }
        return $re;    
    }
    // 短信内容 截取/计数
    // param    string    $msg     初始的短信内容
    // param    int        $slen     最多截取多少文字
    // return    array    $re        返回array(文字,信息条数,文字个数)
    function msgCount($msg, $slen=255){
        global $_cbase;
        $cset = $_cbase['sys']['cset'];
        $cnt = mb_strlen($msg, $cset);
        if($cnt>255){
            $msg = mb_substr($str, 0, 250, $cset); 
            $cnt = 250;
        }
        if($cnt>$this->cfg_mchar){ // >70字
            $ncnt = ceil($cnt/($this->cfg_mchar-3)); //(70-3)个字算一条信息
        }else{
            $ncnt = 1;
        }
        return array($msg,$ncnt,$cnt); 
    }
    
    // 部分接口有此方法
    function chargeUp($charge){
        return $this->smsdo->chargeUp($charge);
    }

}
