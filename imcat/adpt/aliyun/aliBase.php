<?php
namespace imcat;

class aliBase
{

    public $accessKeyId = '';
    public $accessKeySecret = '';

    public $dateTimeFormat = 'Y-m-d\TH:i:s\Z';
    public $vertion = '2016-11-01';

    function  __construct() 
    {
        //$this->initialize();
        // 读配置
    }

    public function composeUrl($skcfg, $params)
    {
        $apiParams = $params;
        foreach ($apiParams as $key => $value) {
            $apiParams[$key] = $this->percentEncode($value);
        }
        //$apiParams["RegionId"] = $this->getRegionId();
        $apiParams["AccessKeyId"] = $skcfg['ak'];
        $apiParams["Format"] = 'JSON';
        $apiParams["SignatureMethod"] = 'HMAC-SHA1';
        $apiParams["SignatureVersion"] = '1.0';
        $apiParams["SignatureNonce"] = md5(uniqid(mt_rand(), true));
        $apiParams["Timestamp"] = gmdate($this->dateTimeFormat);
        $apiParams["Version"] = $this->vertion;
        /*if ($credential->getSecurityToken() != null) {
            $apiParams["SecurityToken"] = $credential->getSecurityToken();
        }*/
        $apiParams["Signature"] = $this->computeSignature($apiParams, $skcfg['as']);
        $requestUrl = "";
        foreach ($apiParams as $apiParamKey => $apiParamValue) { echo "$apiParamKey=$apiParamValue\n<br>";
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
        }
        return substr($requestUrl, 0, -1);
    }

    private function computeSignature($parameters, $accessKeySecret) // , $iSigner
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach ($parameters as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));

        return $signature;
    }

    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

}
