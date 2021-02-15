<?php

require_once "access_token.php";
require_once "helper.php";

class JSSDK {
    private $appId;  
    private $accessToken;
    private $appConfigs;
    private $agentId;

    public function __construct($agentId) {
      $this->appConfigs = loadConfig();
      $config = read('wework', 'ex');
      if(isset($this->appConfigs['AppsConfig'][$agentId])){ // 1000002, AppCS
          $agentId = $this->appConfigs['AppsConfig'][$agentId]['AgentId'];
      }
      $this->appId = $this->appConfigs['CorpId']; //dump($agentId);
      $this->accessToken = new AccessToken($agentId);
      $this->agentId = $agentId;
    }

    public function getSignPackage($isAgent=0) {
      $jsapiTicket = $this->getJsApiTicket($isAgent);

      // 注意 URL 一定要动态获取，不能 hardcode.
      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      $timestamp = time();
      $nonceStr  = createNonceStr();

      //这里参数的顺序要按照 key 值 ASCII 码升序排序
      $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

      $signature = sha1($string);

      $signPackage = array(
          "appId"     => $this->appId,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string,
          "ticket" => $jsapiTicket, // 测试用
      );
      return $signPackage; 
    }

    private function getJsApiTicket($isAgent=0) {
        $tkKey = "wework_jsticket_{$this->appId}".($isAgent ? "_{$this->agentId}" : "_corpId");
        $tkarr = \imcat\extCache::tkGet($tkKey); // 取缓存
        //$tkarr['ticket'] = '';
        if(!empty($tkarr['ticket'])){
            $ticket = $tkarr['ticket'];
        }else{
            $accessToken = $this->accessToken->getAccessToken(); //dump($accessToken);
            $url = "https://qyapi.weixin.qq.com/cgi-bin";
            if($isAgent){
                $url .= "/ticket/get?access_token=$accessToken&type=agent_config";
            }else{
                $url .= "/get_jsapi_ticket?access_token=$accessToken";
            }
            $data = http_get($url)["content"]; //dump($data);
            $res = json_decode($data); //dump($res);
            $ticket = empty($res->ticket) ? '' : $res->ticket; 
            if($ticket) { // dump($this->rspJson); imcat缓存
                $tkarr = ['ticket'=>$ticket, 'exp'=>$res->expires_in];
                \imcat\extCache::tkSet($tkKey, $tkarr, 30);
            }else{
                \imcat\basDebug::bugLogs("getJsApiTicket", $data, "$tkKey.log", 'db');
            } 
        } //dump($ticket); echo 'xxxx';
        return $ticket;

        /*
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $path = "../cache/jsapi_ticket.php";
        $data = json_decode(get_php_file($path));
        $data->expire_time = 1;
        if($data->expire_time < time()){        
            $accessToken = $this->accessToken->getAccessToken();      
            $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";          
            $res = json_decode(http_get($url)["content"]);
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                set_php_file($path, json_encode($data));
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
        */
    }
}

