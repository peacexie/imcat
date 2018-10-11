<?php
namespace imcat;
/**
 * Class wexControl
 */

class wexControl{

    private $error = 0;
    private $kid = '';
    private $cfg = array();
    private $ignoreEvents = array(
        'qualification_verify_success', 'qualification_verify_fail', 'naming_verify_success',
        'naming_verify_fail', 'annual_renew', 'verify_expired', // 微信认证事件推送, 
    ); //忽略事件

    function __construct(){
        
        #die(req('echostr',''));

        $actys = req('actys',''); 
        $this->kid = req('kid','admin');
        $this->cfg = wysBasic::getConfig($this->kid); 
        
        if($echostr=req('echostr','')){
            $this->checkSign($echostr);
        }
        
        $data = file_get_contents('php://input'); //@$GLOBALS["HTTP_RAW_POST_DATA"]; 
        if(!empty($data)){ 
            $this->replyPost($data); 
        } 
        
        if(!empty($actys)){ //getUinfo,getQrcode,chkLogin,chkUpload
            $data = $this->$actys();
            die($data);
        }
        
        $this->error("Error: q=[{$_SERVER['QUERY_STRING']}], data=[$data]", $data); 
        
    }
    
    // 处理post
    function replyPost($data){
        define('RUN_WECHAT','1');
        $post = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if(empty($post->MsgType)){ 
            $this->error("Error Code/Null MsgType"); 
        }else{
            if($post->MsgType=='event'){ //接收事件推送
                //忽略事件
                if(!empty($post->CardId)){ //卡券事件推送(<CardId>)
                    $class = 'Card';
                }elseif(!empty($post->PoiId)){ //(门店)审核事件推送(<PoiId>)
                    $class = 'Store';
                }elseif(!empty($post->OrderId)){ //订单付款通知(<OrderId>), 
                    $class = 'Order';
                }elseif(in_array($post->Event,$this->ignoreEvents)){ //忽略事件
                    $this->ignore();
                }else{
                    $class = 'Event'; 
                }
            }else{ //接收一般信息,作相关响应,如关键字回复
                $class = 'Reply';
            } 
            //$this->error("Error Class: [$class]", $post);
            $clsex = 'wex'.$class.ucfirst(strtolower($this->kid));
            $class = 'wys'.$class;
            $class = class_exists($clsex) ? $clsex : (class_exists($class) ? $class : ''); 
            if($class){ 
                new $class($post, $this->cfg);
            }else{
                $this->error("Error Class: [$class]");
            }
        }
    }
    
    function checkSign($echoStr){
        if(wmpBasic::checkSignature($this->cfg)){
            die($echoStr);    
        }else{
            $error = 'Error: checkSignature';
        }
    }
    
    function ignore(){
        //save;    
        die('');
    }
    
    function error($msg, $post){
        wysBasic::debugError($msg, $post);    
    }

    //微信扫描二维码登录
    function chkLogin(){
        global $_cbase;
        $uniqueid = usrPerm::getUniqueid();
        $db = db(); 
        $scene = req('scene',''); 
        $extp = req('extp',''); 
        $stampys = req('stampys',''); 
        $signys = req('signys',''); 
        $signenc = md5($_cbase['safe']['api'].$stampys.$extp); 
        if(empty($extp) || (($_cbase['run']['stamp']-$stampys)>5*60) || $signenc!=$signys){
            $res['error'] = '登录失败';
            $res['message'] = "超时或认证失败，请重新扫描。"; 
            $re = "var data = ".comParse::jsonEncode($res).""; 
            return $re;
        }
        $whrstr = "sid='$scene' AND stat='LoginOK' AND extp='$extp' AND auser='$uniqueid' AND smod='login'"; //安全吗？!!! 
        $row = $db->table('wex_qrcode')->where("$whrstr AND atime>'".($_cbase['run']['stamp']-5*60)."'")->find();
        if(!empty($row['openid'])){ //!empty($row['extp']) && 
            $uname = usrExtra::setLoginLogger($row['openid']);
            $this->status['error'] = '';
            $this->status['message'] = "登录成功。";
            $this->status['uname'] = $uname;
            $db->table('wex_qrcode')->data(array('atime'=>'0'))->where("sid='$scene'")->update(); 
        }else{
            $this->status['error'] = '登录失败';
            $this->status['message'] = "未扫描确认。";    
        }
        $re = "var data = ".comParse::jsonEncode($this->status)."";
        return $re;
    }

    //检查传图
    function chkUpload(){
        global $_cbase;
        $uniqueid = usrPerm::getUniqueid();
        $db = db(); 
        $scene = req('scene',''); 
        $extp = req('extp',''); 
        $stampys = req('stampys',''); 
        $signys = req('signys',''); 
        $signenc = md5($_cbase['safe']['api'].$stampys.$extp); 
        if(empty($extp) || (($_cbase['run']['stamp']-$stampys)>60*60) || $signenc!=$signys){
            $res['error'] = 'errorCheck';
            $res['message'] = "超时或认证失败，请重新扫描。";    
            $re = "var data = ".comParse::jsonEncode($res).""; 
            return $re;
        }
        $whrstr = "sid='$scene' AND extp='$extp' AND auser='$uniqueid' AND smod='upload'"; //安全吗？!!! 
        $row = $db->table('wex_qrcode')->where("$whrstr AND atime>'".($_cbase['run']['stamp']-5*60)."'")->find();
        if(!empty($row['openid'])){
            $list = $db->table('wex_msgget')->where("openid='{$row['openid']}' AND `type`='image' AND atime>'".($_cbase['run']['stamp']-5*60)."'")->select(); 
            if($list){ foreach($list as $r){
                $res['res'][] = $r;
            }    }
            $res['message'] = "近5分钟传的图…";
            $res['error'] = '';
        }else{
            $res['error'] = "noScan";    
            $res['message'] = "还未扫描。";    
        }
        $res = "var data = ".comParse::jsonEncode($res)."";
        return $res;
    }
    
    function loadFile(){ 
        $mediaid = req('mediaid','',255);
        $wmat = new wmpMaterial($this->cfg);
        $data = $wmat->loadMedia($mediaid);
        if(strstr($data,'"errmsg"')){
            $data = json_decode($data,1);
            die("[{$data['errcode']}]{$data['errmsg']}");
        }else{
            //comFiles::put('./aaa.jpg.txt',$data);
            header("Content-type:image/jpeg");
            die($data);    
        }
    }
    
    function getQrcode(){ //注意:用总站的wecfg
        global $_cbase;
        $qrmod = req('qrmod','login'); 
        #$this->chkQropen($qrmod);
        $extp = req('extp',''); 
        //if(strlen($extp)<6){  }
        $wxqr = new wysQrcode($this->cfg); 
        $qrcode = $wxqr->getQrcode($qrmod, 'limit', $extp); 
        $qrcode['stampys'] = $_cbase['run']['stamp']; //注意:用总站的wecfg
        $qrcode['signys'] = md5($_cbase['safe']['api'].$qrcode['stampys'].$extp); 
        $re = "var data = ".json_encode($qrcode)."";
        return $re;
    }
    
    function getUinfo(){
        //权限?!
        #if($re0==3) die("没有权限"); 
        $ustr = req('ustr','',2048); 
        $weixin = new wmpUser($this->cfg);
        $data = $weixin->getUserBatch($ustr,1);
        $re = "var data = $data;";
        return $re;
    }
    
    function kidExists(){
        $kid = basReq::ark('fm','kid','Key'); 
        $oldval = req('oldval'); 
        if(strlen($kid)<3){
            echo "[kid]错误！";
        }elseif($kid===$oldval){
            die("success");
        }elseif($flag=db()->table('wex_apps')->where("kid='$kid'")->find()){
            echo "[$kid]已被占用！";
        }else{
            die("success");
        }
    }
    function appidExists(){
        $appid = basReq::ark('fm','appid','Key');
        $oldval = req('oldval'); 
        if(strlen($appid)<6){
            echo "[appid]错误！";
        }elseif($appid===$oldval){
            die("success");
        }elseif($flag=db()->table('wex_apps')->where("appid='$appid'")->find()){
            echo "[$appid]已被占用！";
        }else{
            die("success");
        }
    }
    
}
