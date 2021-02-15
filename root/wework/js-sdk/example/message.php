<?php
	
	/**
	 * 企业的消息处理逻辑主要包含两部分：
	 * 1：接受企业微信推送过来的消息，解包进行验证并取出数据 
	 * 2：处理完业务逻辑后将消息封装为XML数据包推送到企业微信的接口然后push给用户	 	 
	 */

	require_once "../lib/helper.php";	
	require_once "../lib/msgcrypt.php";
	require_once "../lib/app_api.php";
	
	/**
	 * 场景1、企业主动向用户推送消息  
     * 支持文本消息、图片消息、语音消息、视频消息、文件消息、文本卡片消息、图文消息等消息类型	 	 
	 */	 
	function pushNewsMsgTest(){ 
	 	$msg = array(
 			'touser'=>'UserID', 
 			'toparty'=>'1', 
 			'msgtype'=>'news',
 			'agentid'=>1000002,
 			'news'=>array(
 				"articles"=> array(0=>array(
 					"title"=>"中秋节礼品领取",
 					"description"=>"今年中秋节公司有豪礼相送",
 					"url"=>"http://qq.com",
 					"picurl"=>"http://res.mail.qq.com/node/ww/wwopenmng/images/independent/doc/test_pic_msg1.png"
 				))
 			)
 		);
	 		 	
	 	$api = new APP_API(1000002);
	 	
	 	var_dump($api->sendMsgToUser($msg));
	}

	/**
	 * 推送文本消息	 
	 */
	function pushTextMsgTest(){ 
	 	$msg = array(
 			'touser'=>'chauvetxiao', 
 			'toparty'=>'', 
 			'msgtype'=>'text',
 			'agentid'=>1000002,
 			'text'=>array(
 				"content"=>"各部门及同事：\n".
    			"为更好的服务好再来大厦，满足大厦入驻员工的班车需求，现对部分班车路线及时刻做相应调整，自2016年9月20日零时生效。详情点击\n<a href=\"http://banche.hoolilai.com\">http://banche.hoolilai.com</a>"    			
 			)
 		);
	 		 	
	 	$api = new APP_API(1000002);
	 	
	 	var_dump($api->sendMsgToUser($msg));
	}

	/**
	 * 场景2、企业接收用户在应用的聊天窗口输入后传递过来的数据
     * 支持文本消息、图片消息、语音消息、视频消息、文件消息、文本卡片消息、图文消息等消息类型	 	 
	 */	 	 
	function receiveMsgFromQyWx(){
		//读取config文件里面的配置
		$appConfigs = loadConfig();
		$config = getConfigByAgentId(1000007);

		$token = $config->Token;
		$encodingAesKey = $config->EncodingAESKey;	
		$corpId = $appConfigs['CorpId'];
		
		$sReqMsgSig = $_GET["msg_signature"];	
		$sReqTimeStamp = $_GET["timestamp"];	
		$sReqNonce = $_GET["nonce"];	
		$sReqData = file_get_contents("php://input");		

		$sMsg = "";  // 解析之后的明文
		$wxcpt = new MsgCrypt($token,$encodingAesKey,$corpId);
		$errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);

		if ($errCode == 0) {
			// 解密成功，sMsg即为xml格式的明文
			 			
			$xml = new DOMDocument();
			$xml->loadXML($sMsg);

			$FromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;  //发送消息的UserID
			$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;  //发送的消息内容体
			
			//TODO ... 业务逻辑		
			//...
			//...
			
		} else {
			print("ERR: " . $errCode . "\n\n");			
		}
	}

	/**
	 * 场景3:企业被动回复用户的消息
	 * 支持文本消息、图片消息、语音消息、视频消息、文件消息、文本卡片消息、图文消息等消息类型	 	 
	 */	
	function replyMsgToUser(){
		// 需要发送的明文消息 
		// TODO：根据用户提交过来的操作封装此数据包
		$sRespData = "<xml>
						<ToUserName><![CDATA[mycreate]]></ToUserName>
						<FromUserName><![CDATA[wx5823bf96d3bd56c7]]></FromUserName>
						<CreateTime>1348831860</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[this is a test]]></Content>						
					  </xml>";

		$sEncryptMsg = ""; //xml格式的密文
		$wxcpt = new MsgCrypt($token,$encodingAesKey,$corpId);
		$errCode = $wxcpt->EncryptMsg($sRespData, $sReqTimeStamp, $sReqNonce, $sEncryptMsg);

		if ($errCode == 0) {			
			// 加密成功，企业需要将加密之后的sEncryptMsg返回
			
			// TODO:向企业微信的后台回复消息			
		} else {
			print("ERR: " . $errCode . "\n\n");
			// exit(-1);
		}
	}

	//e.g
	pushTextMsgTest();
?>

