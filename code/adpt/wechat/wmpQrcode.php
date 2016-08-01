<?php
(!defined('RUN_MODE')) && die('No Init');
// 生成带参数的二维码
// 随微信规则更新

class wmpQrcode extends wmpBasic{

    protected $qrcode_ticket = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=';
    protected $qrcode_show = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
	protected $short_url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=';
    
	function __construct($cfg=array()){
		parent::__construct($cfg); 
	}

    /**
     * 创建二维码ticket
     * @param array $data 参数
     */
    function qrcodeTicket($sid,$type='temp',$exp=86400){ //7天（即604800秒）
		$url = $this->qrcode_ticket."{$this->actoken}";
		$cfg = array(
			'temp' => "{'expire_seconds': $exp, 'action_name': 'QR_SCENE', 'action_info': {'scene': {'scene_id': $sid}}}",
			'fnum' => "{'action_name': 'QR_LIMIT_SCENE', 'action_info': {'scene': {'scene_id': $sid}}}",
			'fstr' => "{'action_name': 'QR_LIMIT_STR_SCENE', 'action_info': {'scene': {'scene_str': '$sid'}}}",
		);
		$paras = isset($cfg[$type]) ? $cfg[$type] : $cfg['temp'];
		$paras = str_replace("'",'"',$paras);
		$data = comHttp::doPost($url, $paras, 3);
		return wysBasic::jsonDecode($data,$this->qrcode_ticket);
    }
	
    /**
     * 通过ticket换取二维码(地址)
     */
    function qrcodeShowurl($ticket){
		if(is_array($ticket)) $ticket = $ticket['ticket'];
		return $this->qrcode_show."{$ticket}";
    }
	
    /**
     * 长链接转短链接接口
     */
    function shortUrl($longurl){
		$url = $this->short_url."{$this->actoken}";
		$paras = "{\"action\":\"long2short\",\"long_url\":\"$longurl\"}"; 
		$data = comHttp::doPost($url, $paras, 3);
		return wysBasic::jsonDecode($data,$this->short_url);
    }
	
}
