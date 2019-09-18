<?php
namespace imcat;

use Alidayu\Signature;

class aliBase
{

    public $accessKeyId = '';
    public $accessKeySecret = '';

    function  __construct() 
    {
        //$this->initialize();
        // 读配置
    }

    public function composeUrl($akId, $akSecret, $params, $method='GET')
    {
        $qstr = Signature::signUrl($akId, $akSecret, $params, $method);
        return $qstr;
    }

}
