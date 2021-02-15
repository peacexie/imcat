<?php

include_once "errorCode.php";
include_once "sha1.php";

/**
 * PKCS7Encoder class
 *
 * 提供基于PKCS7算法的加解密接口.
 */
class PKCS7Encoder
{
	public static $block_size = 32;

	/**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	function encode($text)
	{
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen($text);
		//计算需要填充的位数
		$amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		//获得补位所用的字符
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	/**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
	 */
	function decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > PKCS7Encoder::$block_size) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}

}

/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
    public $key = null;
    public $iv = null;

    /**
     * Prpcrypt constructor.
     * @param $k
     */
    public function __construct($k)
    {
        $this->key = base64_decode($k . '=');
        $this->iv  = substr($this->key, 0, 16);

    }

    /**
     * 加密
     *
     * @param $text
     * @param $receiveId
     * @return array
     */
    public function encrypt($text, $receiveId)
    {
        try {
            //拼接
            $text = $this->getRandomStr() . pack('N', strlen($text)) . $text . $receiveId;
            //添加PKCS#7填充
            $pkc_encoder = new PKCS7Encoder;
            $text        = $pkc_encoder->encode($text);
            //加密
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
            return [ErrorCode::$OK, $encrypted];
        } catch (Exception $e) {
            print $e;
            return [MyErrorCode::$EncryptAESError, null];
        }
    }

    /**
     * 解密
     *
     * @param $encrypted
     * @param $receiveId
     * @return array
     */
    public function decrypt($encrypted, $receiveId)
    {
        try {
            //解密
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, OPENSSL_ZERO_PADDING, $this->iv);
        } catch (Exception $e) {
            return [ErrorCode::$DecryptAESError, null];
        }
        try {
            //删除PKCS#7填充
            $pkc_encoder = new PKCS7Encoder;
            $result      = $pkc_encoder->decode($decrypted);
            if (strlen($result) < 16) {
                return [];
            }
            //拆分
            $content     = substr($result, 16, strlen($result));
            $len_list    = unpack('N', substr($content, 0, 4));
            $json_len     = $len_list[1];
            $json_content = substr($content, 4, $json_len);
            $from_receiveId = substr($content, $json_len + 4);
        } catch (Exception $e) {
            print $e;
            return [ErrorCode::$IllegalBuffer, null];
        }
        if ($from_receiveId != $receiveId) {
            return [ErrorCode::$ValidateCorpidError, null];
        }
        return [0, $json_content];
    }

    /**
     * 生成随机字符串
     *
     * @return string
     */
    private function getRandomStr()
    {
        $str     = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max     = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }
}
