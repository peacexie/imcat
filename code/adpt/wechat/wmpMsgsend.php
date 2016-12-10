<?php
(!defined('RUN_INIT')) && die('No Init');
/**
 * 发送消息 > 客服接口-发消息
 * 随微信规则更新
 *
 * 当用户主动发消息给公众号的时候（包括发送信息、点击自定义菜单、订阅事件、扫描二维码事件、支付成功事件、用户维权），微信将会把消息数据推送给开发者，开发者在一段时间内（目前修改为48小时）可以调用客服消息接口，通过POST一个JSON数据包来发送消息给普通用户，在48小时内不限制发送次数。
 */

class wmpMsgsend extends wmpBasic{
	
	protected $msgSend = "https://api.weixin.qq.com/cgi-bin/message/custom/send";
	
	function __construct($cfg=array()){
		parent::__construct($cfg); 
	}	

    /**发送文本客服消息
     *
     * @param $openid
     * @param $content
     *
     * @return bool|mixed
     */
    function sendText($openid, $content){
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
		$message['touser'] = $openid;
		$message['msgtype'] = "text";
		$message['text']['content'] = $content;
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3);
		return wysBasic::jsonDecode($data,$this->msgSend);
    }

    /**
     * 发送图片客服消息
     *
     * @param $openid
     * @param $mediaid
     *
     * @return bool|mixed
     */
    function sendImage($openid, $mediaid) {
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
        $message['touser'] = $openid;
        $message['msgtype'] = "image";
        $message['image']['media_id'] = $mediaid;
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3);
		return wysBasic::jsonDecode($data,$this->msgSend);
    }

    /**
     * 发送语音客服消息
     *
     * @param $openid
     * @param $media_id
     *
     * @return bool|mixed
     */
    function sendVoice($openid, $media_id) {
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
		$message['touser'] = $openid;
		$message['msgtype'] = "voice";
		$message['voice']['media_id'] = $media_id;
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3);
		return wysBasic::jsonDecode($data,$this->msgSend);
		
    }

    /**
     * 发送视频客服消息
     *
     * @param        $openid
     * @param        $media_id
     * @param string $title
     * @param string $description
     *
     * @return bool|mixed
     */
    function sendVideo($openid, $media_id, $title = "", $description = "") {
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
		$message['touser'] = $openid;
		$message['msgtype'] = "video";
		$message['video']['media_id'] = $media_id;
		$message['video']['title'] = $title;
		$message['video']['description'] = $description;
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSend);
		
    }

    /**
     * 发送音乐客服消息
     *
     * @param        $openid
     * @param        $url
     * @param        $hq_url
     * @param        $media_id
     * @param string $title
     * @param string $description
     *
     * @return bool|mixed
     */
    function sendMusic($openid, $url, $hq_url, $media_id, $title = "", $description = "") {
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
		$message['touser'] = $openid;
		$message['msgtype'] = "music";
		$message['music']['title'] = $title;
		$message['music']['description'] = $description;
		$message['music']['musicurl'] = $url;
		$message['music']['hqmusicurl'] = $hq_url;
		$message['music']['thumb_media_id'] = $media_id;
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSend);
		
    }

    /**
     * 发送图文客服消息
     *
     * @param $openid
     * @param $items
     *
     * @return bool|mixed
     * @throws Exception
     */
    function sendNews($openid, $items) {
		$url = $this->msgSend."?access_token={$this->actoken}";
		$message = array();
		$message['touser'] = $openid;
		$message['msgtype'] = "news";
		if ($items && is_array($items)){
			foreach ($items as $item){
				if (is_array($item) && (isset($item['title']) || isset($item['description']) || isset($item['picurl']) || isset($item['url']))){
					$it['title'] = isset($item['title']) ? $item['title'] : "";
					$it['description'] = isset($item['description']) ? $item['description'] : "";
					$it['url'] = isset($item['url']) ? $item['url'] : "";
					$it['picurl'] = isset($item['picurl']) ? $item['picurl'] : "";
					if ($it['title'] && $it['description'] && $it['url'] && $it['picurl'])
						$message['news']['articles'][] = $it;
				}
			}
		}
		$paras = wysBasic::jsonEncode($message);
		$data = comHttp::doPost($url, $paras, 3); 
		return wysBasic::jsonDecode($data,$this->msgSend);
		
    }

}
