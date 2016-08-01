<?php
(!defined('RUN_MODE')) && die('No Init');
// 网页授权
// 随微信规则更新

class wmpOauth extends wmpBasic{
	
	// 获取code的链接
    private $accodeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
    
	// 获取网页授权调用凭证access_token的链接
	private $actokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
	
	// 获取通过授权的微信用的基本信息
	private $acuinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    
	//刷新access_token
	private $acupdUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN';
	
    // 获取的access_token信息
    protected $accessToken;
    
	function __construct($cfg=array()){
		parent::__construct($cfg); 
	}
    
    /**
     * 第三方网页通过Oauth2.0获取用户授权
     * 获取code
     */
    function getCode($redirect, $scope='', $state='system') {
        //$redirect = urlencode($redirect);
		$redirect = str_replace(array("?&","&","#"),array("?","%26","%23"),$redirect);
		$scope = in_array($scope,array('snsapi_base','snsapi_userinfo')) ? $scope : 'snsapi_base';
        return sprintf($this->accodeUrl,$this->cfg['appid'],$redirect,$scope,$state);  
    }

    /**
     * 通过code获取access_token
     */
    function getACToken($code){
    	if($this->accessToken == null){
    		$url = sprintf($this->actokenUrl,$this->cfg['appid'],$this->cfg['appsecret'],$code);
			$accessInfo = comHttp::doGet($url,3); 
    		$this->accessToken = $accessToken = json_decode($accessInfo,true);
    		if(isset($accessToken['errcode'])){
    			return array('errcode'=>$accessToken['errcode'],'errmsg'=>$accessToken['errmsg'],'result'=>null);
    		}
    	} 
        return array('errcode'=>0,'errmsg'=>'','result'=>$this->accessToken);
    }

    /**
     * 第三方网页通过Oauth2.0获取用户授权
     * 刷新access_token
     */
    function oauthRefresh($refresh_token) {
        //return self::get(self::$links['oauth_refresh'] . "?appid={$this->appid}&grant_type=refresh_token&refresh_token={$refresh_token}");
    }

    /**
     * 第三方网页通过Oauth2.0获取用户权限
     * 获取用户信息，仅限scope为snsapi_userinfo
     */
    function getUserInfo($actoken, $openid) {
        $url = sprintf($this->acuinfoUrl,$actoken,$openid);
		$data = comHttp::doGet($url,3);
		return wysBasic::jsonDecode($data,$this->acuinfoUrl);
    }

}
