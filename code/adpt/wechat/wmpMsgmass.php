<?php
(!defined('RUN_INIT')) && die('No Init');
// 群发信息
// 随微信规则更新
// 除文本外消息外，其它接口需要相应的media_id

class wmpMsgmass extends wmpBasic{
	
	protected $msgSend = "https://api.weixin.qq.com/cgi-bin/message/mass/send";
	protected $msgSall = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall";
	protected $msgSurl = "";
	protected $upVideo = "https://file.api.weixin.qq.com/cgi-bin/media/uploadvideo";
	protected $msgTpl = "https://api.weixin.qq.com/cgi-bin/message/template/send"; 
	
	function __construct($cfg=array()){
		if(strpos(@$cfg['kid'],'08_')) die('Forbid');
		parent::__construct($cfg); 
	}	

    /**群发Tpl消息
     */
    function sendTpl($openids, $tpl, $data, $link=''){
		$openids = explode(',', $openids);
		$res = array();
		defined('WERR_RETURN') || define('WERR_RETURN',1); //错误信息返回
		foreach ($openids as $openid) {
			$url = $this->msgTpl."?access_token={$this->actoken}";
			$paras = '{"touser":"'.$openid.'","template_id":"'.$tpl.'","url":"'.$link.'","data":{'.$data.'}}';
			//dump($paras);
			$data = comHttp::doPost($url, $paras, 3); 
			$res[$openid] = wysBasic::jsonDecode($data,$this->msgTpl);
		}
		return $res;
    }

    /**群发文本客服消息
     */
    function sendText($content, $group_id=0){
		$content = wysBasic::jsonEncode($content); 
		$filter = $this->getFilter($group_id);
		$url = $this->msgSurl."?access_token={$this->actoken}";
		$paras = '{'.$filter.',"msgtype":"text","text":{"content":'.$content.'}}';
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSurl);
    }

    /**
     * 群发图片客服消息
     */
    function sendImage($media_id, $group_id=0) {
		$filter = $this->getFilter($group_id);
		$url = $this->msgSurl."?access_token={$this->actoken}";
		$paras = '{'.$filter.',"msgtype":"image","image":{"media_id":'.$media_id.'}}';
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSurl);
    }

    /**
     * 群发语音客服消息
     */
    function sendVoice($media_id, $group_id=0) {
		$filter = $this->getFilter($group_id);
		$url = $this->msgSurl."?access_token={$this->actoken}";
		$paras = '{'.$filter.',"msgtype":"voice","voice":{"media_id":'.$media_id.'}}';
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSurl);
    }

    /**
     * 群发视频客服消息
	 * 注意分两次getResource，详情请看官方文档
     */
    function sendVideo($media_id, $group_id=0, $title='', $description='') {
		$url = $this->upVideo."?access_token={$this->actoken}";
		$msgup = array();
		$msgup['media_id'] = $media_id;
		$msgup['title'] = $title;
		$msgup['description'] = $description;
		$paras = wysBasic::jsonEncode($message); //echo $paras;
		$data = comHttp::doPost($url, $paras, 3);  
		$re = wysBasic::jsonDecode($data,$this->upVideo);
		if(!empty($re['media_id'])){
			$filter = $this->getFilter($group_id);
			$url = $this->msgSurl."?access_token={$this->actoken}";
			$paras = '{'.$filter.',"msgtype":"mpvideo","mpvideo":{"media_id":'.$re['media_id'].'}}';
			$data = comHttp::doPost($url, $paras, 3);  
			return wysBasic::jsonDecode($data,$this->msgSurl);
		}
    }

    /**
     * 群发图文客服消息
     */
    function sendNews($media_id, $group_id=0) {
		$filter = $this->getFilter($group_id);
		$url = $this->msgSurl."?access_token={$this->actoken}";
		$paras = '{'.$filter.',"msgtype":"mpnews","mpnews":{"media_id":'.$media_id.'}}';
		$data = comHttp::doPost($url, $paras, 3);  
		return wysBasic::jsonDecode($data,$this->msgSurl);
    }
	
    /**
     * 群发卡券消息
     */
    function sendCard($card_id, $group_id=0) {
		$filter = $this->getFilter($group_id);
		$url = $this->msgSurl."?access_token={$this->actoken}";
		$paras = '{'.$filter.',"msgtype":"wxcard","wxcard":{"card_id":'.$card_id.'}}';
		$data = comHttp::doPost($url, $paras, 3);  
		return wysBasic::jsonDecode($data,$this->msgSurl);
    }
	
	// group_id: 0:  群发所有；
	//     非0数字:  按会员组群发；
	//     openid1,openid2…:   按openid群发【订阅号不可用，服务号认证后可用】
    function getFilter($group_id=0){
		$re = '';
		$this->msgSurl = $this->msgSall;
		if(empty($group_id)){
			$re = '"filter":{"is_to_all":true}';
		}elseif(is_int($group_id)){
			$re = '"filter":{"is_to_all":false,"group_id":"'.$group_id.'"}';
		}else{
			$re = '"touser":["'.str_replace(',','","',$group_id).'"]';
			$this->msgSurl = $this->msgSend;
		}
		return $re;
	}

}
