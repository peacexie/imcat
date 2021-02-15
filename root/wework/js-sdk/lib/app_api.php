<?php

require_once "access_token.php";
require_once "helper.php"; 

class APP_API {
  
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
   * 查询应用的详细信息   
   */
  public function queryAppInfo(){
      $url = "https://qyapi.weixin.qq.com/cgi-bin/agent/get?agentid=$this->agentId";

      return http_get($this->appendToken($url))["content"];
  }  
  
  /**
   * 更新应用信息
   * @param  [Array like Object] $data  更新的目标应用JSON格式数据      
   */
  public function updateAppInfo($data){
      if($data["agentid"]){
        return http_post($this->appendToken("https://qyapi.weixin.qq.com/cgi-bin/agent/set"),$data)["content"];               
      }else{
        return '{"errcode":-2,"errmsg":"params is missing"}';      
      }
  }

  /**
   * 查询应用的菜单配置信息
   * 注意：此接口需要将应用设置为回调模式
   */
  public function queryAppMenuInfo(){

      $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/get?agentid=$this->agentId";

      return http_get($this->appendToken($url))["content"];
  }

  /**
   * 删除应用的菜单配置
   * 注意：此接口需要将应用设置为回调模式
   */
  public function deleteAppMenu(){

      $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/delete?agentid=$this->agentId";

      return http_get($this->appendToken($url))["content"];
  }

  /**
   * 更新应用的菜单信息
   * @param  [Array like Object] $data  
   * 
   * TODO：建议开发者将每个应用的菜单信息单独进行存储
   */
  public function updateAppMenu($data){
      $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/create?agentid=$this->agentId";

      return http_get($this->appendToken($url))["content"];
  }

  /**
   * 企业主动发送消息给用户
   * @param  [Array like Object] $msg JSON格式的消息体   
   */
  public function sendMsgToUser($msg){
      $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send";

      return http_post($this->appendToken($url),$msg)["content"];
  }
}

