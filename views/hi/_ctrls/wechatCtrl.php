<?php
namespace imcat\hi;

use imcat\basDebug;
use imcat\basReq;
use imcat\wmpBasic;
use imcat\wmpUser;
use imcat\wysBasic;

/*
    旧版:wexControl
*/ 
class wechatCtrl{ 
    
    public $ucfg = array();
    public $vars = array();

    public $key = '';
    public $view = '';
    public $cfg = array();

    public $ignoreEvents = array(
        'qualification_verify_success', 'qualification_verify_fail', 'naming_verify_success',
        'naming_verify_fail', 'annual_renew', 'verify_expired', // 微信认证事件推送, 
    ); //忽略事件

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        $this->init();
    }
    function init(){
        $this->key = $this->ucfg['key'] ? $this->ucfg['key'] : 'admin';
        $this->view = $this->ucfg['view'] ? $this->ucfg['view'] : '';
        $this->cfg = wysBasic::getConfig($this->key);
        // 接口配置认证
        if($echostr=req('echostr','')){
            $this->checkSign($echostr);
        }
        // 自定义方法
        if(method_exists($this, $this->view)){ //getUinfo,getQrcode,chkLogin,chkUpload
            $method = $this->view;
            $data = $this->$method();
            die($data);
        }
    }
    // 消息推送
    function homeAct(){
        return $this->_defAct();
    }
    function _defAct(){
        // 消息推送
        $data = file_get_contents('php://input'); //@$GLOBALS["HTTP_RAW_POST_DATA"]; 
        if(!empty($data)){ 
            $this->replyPost($data); 
        } 
        die("Error: q=[{$_SERVER['QUERY_STRING']}], data=[$data]");
    }

    function checkSign($echoStr){
        //basDebug::bugLogs("replyPost", $_GET, "replyPost.log", 'db');
        if(wmpBasic::checkSignature($this->cfg)){
            die($echoStr);    
        }else{
            die('Error: checkSignature');
        }
    }
    // 处理post
    function replyPost($data){
        define('RUN_WECHAT','1');
        $post = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        //basDebug::bugLogs("replyPost", $post, "post.log", 'db');
        //*
        if(empty($post->MsgType)){ 
            basDebug::bugLogs("Error-MsgType", $data, "replyPost", 'db');
        }else{
            if($post->MsgType=='event'){ //接收事件推送
                $class = 'Event'; 
            }else{ //接收一般信息,作相关响应,如关键字回复
                $class = 'Reply';
            } 
            $clsex = '\imcat\wex'.$class.ucfirst(strtolower($this->key));
            $class = '\imcat\wys'.$class;
            $class = class_exists($clsex) ? $clsex : (class_exists($class) ? $class : ''); 
            if($class){ 
                try{
                    new $class($post, $this->cfg);
                }catch(Exception $e){
                    basDebug::bugLogs("Exception", $e, "e", 'db');
                } 
            }else{
                basDebug::bugLogs("Error-Class", $post, "replyPost", 'db');
            }
        }//*/
        die();
    }

    // 
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

/*


*/
