<?php
(!defined('RUN_INIT')) && die('No Init');
// 被动回复信息(这里只组消息结构,不能直接die，因为可能下发消息后，还有继续其它操作)
// 随微信规则更新

class wmpMsgresp extends wmpBasic{
    
    public $reAuto = 'https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=';
    public $post = NULL;
    //public $return = '';
    
    //function __destory(){  }
    function __construct($post,$cfg=array()){
        if(!empty($cfg)){
            parent::__construct($cfg); 
        }
        $this->post = $post;
        //$this->return = $return;
    }

     function rebHead($type='text'){
        $from = @$this->post->FromUserName;
        $to = @$this->post->ToUserName; //Trying to get property of non-object in
        $data = "<ToUserName><![CDATA[$from]]></ToUserName>";
        $data .= "<FromUserName><![CDATA[$to]]></FromUserName>";
        $data .= "<CreateTime>".time()."</CreateTime>";
        $data .= "<MsgType><![CDATA[$type]]></MsgType>";
        return $data;
    }
    
     function rebEnd($data){
         $data = "<xml>$data</xml>";
         return $data;
     }
     
     // 回复text消息
     function remText($text, $conv=1){
        $data = $this->rebHead('text');
        $data .= "<Content><![CDATA[$text]]></Content>";
        return $this->rebEnd($data);
    }
    
    // 回复Image消息
    function remImage($mediaid) {
        $data = $this->rebHead('image');
        $data .= "<Image><MediaId><![CDATA[$mediaid]]></MediaId></Image>";
        return $this->rebEnd($data);
    }

    // 回复语音消息
    function remVoice($mediaid) {
        $data = $this->rebHead('voice');
        $data .= "<Voice><MediaId><![CDATA[$mediaid]]></MediaId></Voice>";
        return $this->rebEnd($data);
    }

    // 回复视频消息
    function remVideo($mediaid, $title="", $description="") {
        $dext = "<Title><![CDATA[$title]]></Title><Description><![CDATA[$description]]></Description>";
        $data = $this->rebHead('video');
        $data .= "<Video><MediaId><![CDATA[$mediaid]]></MediaId>$dext</Video>";
        return $this->rebEnd($data);
    }

    // 回复音乐消息
    function remMusic($title = '', $description = '', $url = '', $hq_url = '') {
        $dext = "<Title><![CDATA[$title]]></Title><Description><![CDATA[$description]]></Description>";
        $dext .= "<MusicUrl><![CDATA[$url]]></MusicUrl><HQMusicUrl><![CDATA[$hq_url]]></HQMusicUrl>";
        $data = $this->rebHead('music');
        $data .= "<Music>$dext</Music>";
        return $this->rebEnd($data);
    }
    
    // 回复新闻消息
    function remNews($news){
        $data = $this->rebHead('news');
        $list = isset($news['title']) ? array($news) : $news;
        $cnt = 0; $recs = "";
        foreach($list as $news){
            if(empty($news['title'])) continue;
            $cnt++;
            $urls = array('url','picurl');
            foreach ($urls as $key) {
                if(!empty($news[$key])){
                    $news[$key] = wysBasic::fmtUrl($news[$key]);
                }
            }
            $recs .= "<item>";
            $recs .= "<Title><![CDATA[{$news['title']}]]></Title>";
            empty($news['desc']) || $recs .= "<Description><![CDATA[{$news['desc']}]]></Description>";
            empty($news['picurl']) || $recs .= "<PicUrl><![CDATA[{$news['picurl']}]]></PicUrl>"; //大图360*200，小图200*200
            empty($news['url']) || $recs .= "<Url><![CDATA[{$news['url']}]]></Url>";
            $recs .= "</item>";
        }
        $data .= "<ArticleCount>$cnt</ArticleCount><Articles>$recs</Articles>";
        return $this->rebEnd($data);
    }
    
    // 获取自动回复规则
    function getRule($actoken=''){
        $url = $this->reAuto."{$actoken}";
        $data = comHttp::doGet($url, 3);
        return wysBasic::jsonDecode($data,$this->reAuto);
    }
    
    function getMethod($type){
        $method = 're'.ucfirst(strtolower($this->post->$type)); 
        if(method_exists($this,$method)){
            return $method;
        }elseif(method_exists($this,"{$method}Base")){
            return "{$method}Base";
        }else{
            wysBasic::debugError("getMethod:$method",$this->post,'');
        }
    }

    //保存地理位置(发送位置/自动上报)共用(本来放这有点不合适,但为了共用一段代码...)
    //type=0, 返回地址信息
    function savePos($type='auto'){ 
        $row = $this->_db->table('wex_locate')->where("openid='{$this->post->FromUserName}' AND appid='{$this->cfg['appid']}'")->find();
        if(!$type) return $row;
        $data = array(
            'type' => $type,
            'latitude' => $type=='auto' ? $this->post->Latitude : $this->post->Location_X,
            'longitude' => $type=='auto' ? $this->post->Longitude : $this->post->Location_Y,
            'extra' => $type=='auto' ? $this->post->Precision : $this->post->Scale, //Label
            'atime' => time(),
        ); 
        if($row){
            $this->_db->table('wex_locate')->data($data)->where("openid='{$this->post->FromUserName}' AND appid='{$this->cfg['appid']}'")->update();  
        }else{ 
            $data = $data + array(
                'appid' => $this->cfg['appid'],
                'openid' => $this->post->FromUserName,
            ); 
            $this->_db->table('wex_locate')->data($data)->insert();
        }
    }
    
}
