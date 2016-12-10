<?php
//---------------------------------------------------------
//支付回调响应
//---------------------------------------------------------

require_once (dirname(__FILE__)."/common/CommonResponse.class.php");
class PayResponse extends CommonResponse{
	//通知ID
	var $NOTIFYID = "notify_id";
	
	/**
	 * 请求的request、respone以及secretKey
	 */ 
	function PayResponse($secretKey) {
		try {
			unset($this->parameters);
			$this->secretKey = $secretKey;
			/* GET */
			foreach($_GET as $k => $v) {
				$this->setParameter($k, $v);
			}
			/* POST */
			foreach($_POST as $k => $v) {
				$this->setParameter($k, $v);
			}
			$this->setParameter($this->RETCODE, "0");

			if(!$this->isRetCodeOK()){
				throw new SDKRuntimeException("服务调用异常:" . $this->getPayInfo(). "<br>");
			}
			$this->setParameter($this->RETCODE, null);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}

		$this->verifySign();
		
	}
	/**
	 * 告知财付通回调处理成功
	 */
	function acknowledgeSuccess(){
		echo "success";
		return true;
	}
	
	/**
	 * 获取通知查询ID
	 * 
	 * @return 通知查询ID
	 */
	function getNotifyId(){
		return $this->getParameter("notify_id");
	}
	
}


?>