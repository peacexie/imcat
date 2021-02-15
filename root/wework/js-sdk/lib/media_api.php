<?php

require_once "access_token.php";
require_once "helper.php"; 

class MEDIA_API {
  
  	private $access_token;
  	private $agentId;

	public function __construct($agentId) {
	    $this->agentId = $agentId;
	    $this->access_token = new AccessToken($agentId);
	}

	/**
	 * 在请求的企业微信接口后面自动附加token信息   
	 */
	private function appendToken($url){
	    $token = $this->access_token->getAccessToken();

	    if(strrpos($url,"?",0) > -1){
	       return $url."&access_token=".$token;
	    }else{
	       return $url."?access_token=".$token;
	    }      
	}

 	/**
	 * 上传图片
	 * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
	 * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义      
	 * @param array $data {"media":'@Path\filename.jpg'}
	 * 
	 * @return boolean|array
	 */
	public function uploadMedia($data,$type){		
		$url = "https://qyapi.weixin.qq.com/cgi-bin/media/upload?type=$type";		

		$result = http_post($this->appendToken($url),$data,true)["content"];
				
		if($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}

 
}

