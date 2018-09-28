<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 消息回复（被动回复）
// 如果本系统修改,就改这个文件，不用改wmp*文件
// 各扩展系统需求变化很大,re开头的方法都加Base,扩展类里面不加Base,先检测执行无Base的方法,在找含有Base的方法
// - 实现:1.关键字回复, 
// -      2.传图(后续利用)
// -      3.地理位置消息(是否保存??? 供需要时调用)

class wysReply extends wmpMsgresp{
    
    public $_db = NULL;
    
    function __construct($post,$cfg,$re=0){ 
        parent::__construct($post,$cfg); 
        $this->_db = db(); 
        if($re) return;
        $method = $this->getMethod('MsgType');
        return $this->$method();
    }
    
    // 文本消息
    function reTextBase($re=0){  
        $detail = $this->post->Content;
        //获取关键字
        $reauto = $this->getKeyList($detail); 
        //保存收到的消息... (开关???)
        $retype = empty($reauto['rexml']) ? '-' : 'Auto'; 
        $this->saveMsg('text', $detail, $retype);
        if(empty($reauto['rexml'])){ 
            if($re) return '';
            die('');
        }
        $rexml = $reauto['rexml']; 
        //保存回复消息... 
        $this->saveReply($reauto); 
        //回复消息给微信服务器... 
        if($re) return $rexml;
        die($rexml); 
    }

    // 地理位置消息
    function reLocationBase(){ 
        $this->savePos('send');
        die('');    
    }

    // 图片消息
    function reImageBase($re=0){ 
        $stamp = $_SERVER["REQUEST_TIME"];
        $picUrl = $this->post->PicUrl;
        $this->saveMsg('image', $picUrl, '-', $this->post->MediaId);
        //保持会话不过期
        $timeNmin = $stamp-(5*60);
        $this->_db->table('wex_qrcode')->data(array('atime'=>$stamp))->where("openid='{$this->post->FromUserName}' AND smod='upload' AND atime>'$timeNmin'")->update(); 
        if($re) return;
        die('');
    }
    
    // 语音消息
    function reVoiceBase(){ 
        //return $this->remText("remVoice:xxx");  
        die('');   
    }    

    // 视频消息
    function reVideoBase(){ 
        //return $this->remText("remVideo:xxx");  
        die(''); 
    }

    // 小视频消息
    function reShortvideo(){ 
        //return $this->remText("remShortvideo:xxx"); 
        die(''); 
    }
    
    // 链接消息
    function reLinkBase(){ 
        //return $this->remText("remLink:xxx");  
        die('');   
    }    

    //获取关键字列表(本系统的)
    function getKeyList($detail,$relist='0'){ 
        $klist = array(); 
        $list = $this->_db->table('wex_keyword')->where("appid='{$this->cfg['appid']}'")->select(); 
        if($list){ foreach($list as $row){
            $key = $row['keyword']=='follow_autoreply_info' ? $row['keyword'] : $row['kid'];
            $klist[$key] = $row;
        } } 
        if($relist=='follow_autoreply_info' && isset($klist['follow_autoreply_info'])){
            return $klist['follow_autoreply_info']['detail'];
        }else{
            unset($klist['follow_autoreply_info']);    
        }
        if($relist) return $klist;
        $retype = ''; $rexml = ''; $remsg = '';
        //查找关键字,获取自动回复内容
        foreach($klist as $v){
            if(empty($v['keyword'])) continue;
            preg_match("/".str_replace(',','|',$v['keyword'])."/i",$detail,$rea);
            if(!empty($rea)){ //考虑回复其它类型的消息？//strstr($detail,$v['keyword'])
                $remsg .= $v['detail'];
                if(!empty($v['picurl'])){
                    $remsg .= "\n<img src='".wysBasic::fmtUrl($v['picurl'])."'>\n";
                }
                if(!empty($v['url'])){
                    $remsg .= "\n<a href='".wysBasic::fmtUrl($v['url'])."'>详情 >> </a>";
                }
                $retype = $v['type']; 
                $retype || $retype = 'text';
                $method = 'rem'.ucfirst($retype);
                $rexml = method_exists($this,$method) ? $this->$method($remsg) : '';
                break;
            }
        }
        return array('type'=>$retype, 'rexml'=>$rexml, 'remsg'=>$remsg);
    }
    
    //保存信息
    function saveMsg($retype, $detail, $restat='Auto', $media_id=''){ //已Auto自动回复
        $data = array(
            'kid' => basKeyid::kidTemp(),
            'type' => $retype,
            'detail' => basReq::in($detail),
            'restate' => $restat,
            'atime' => $_SERVER["REQUEST_TIME"],
            'appid' => $this->cfg['appid'],
            'openid' => $this->post->FromUserName,
        );
        $media_id && $data['media_id'] = $media_id;
        $this->_db->table('wex_msgget')->data($data)->insert();
    }
    
    //保存回复消息
    function saveReply($reauto){ 
        $data = array(
            'kid' => basKeyid::kidTemp(),
            'type' => $reauto['type'],
            'detail' => basReq::in($reauto['remsg']),
            'atime' => $_SERVER["REQUEST_TIME"],
            'appid' => $this->cfg['appid'],
            'openid' => $this->post->FromUserName,
        ); 
        $this->_db->table('wex_msgsend')->data($data)->insert();
    }

}
