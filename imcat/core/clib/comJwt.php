<?php
namespace imcat;

// PHP实现jwt
class comJwt{

    //头部
    private static $header = array(
        'alg' => 'HS256', //生成signature的算法
        'typ' => 'JWT'    //类型
    );

    /**
     * 生成 jwt token
     * @param array $payload jwt载荷   格式如下非必须
     * [
     *  'iss' => 'jwt_admin',  //该JWT的签发者
     *  'iat' => time(),  //签发时间
     *  'exp' => time()+7200,  //过期时间
     *  'nbf' => time()+60,  //该时间之前不接收处理该Token
     *  'sub' => 'www.admin.com',  //面向的用户
     *  'jti' => md5(uniqid('JWT').time())  //该Token唯一标识
     * ]
     * @return bool|string 
     */
    static function make($payload)
    {
        if(is_array($payload))
        {
            $enchd = self::enc64(json_encode(self::$header,JSON_UNESCAPED_UNICODE));
            $encpl = self::enc64(json_encode($payload,JSON_UNESCAPED_UNICODE));
            $token = "$enchd.$encpl." . self::sign("$enchd.$encpl",self::$header['alg']);
            return $token;
        }else{
            return false;
        }
    }

    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $Token 需要验证的token
     * @return bool|string
     */
    static function check($Token)
    {
        $tokens = explode('.', $Token);
        if (count($tokens) != 3)
            return false; // '3seg: Wrong Token Segments'

        list($enchd, $encpl, $sign) = $tokens;

        //获取jwt算法
        $dechd = json_decode(self::dec64($enchd), 1);
        if (empty($dechd['alg']))
            return false; // head: Invalid header encoding

        //签名验证
        if (self::sign("$enchd.$encpl", $dechd['alg']) !== $sign)
            return false; // head: Invalid body encoding

        $payload = json_decode(self::dec64($encpl), 1);

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time())
            return false; // 

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time())
            return false;

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time())
            return false;

        return $payload;
    }

    /**
     * enc64 https://jwt.io/  中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    static function enc64($input)
    {
        return comConvert::sysBase64($input); // sysBase64,sysRevert
    }

    /**
     * dec64  https://jwt.io/  中base64UrlDecode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    static function dec64($input)
    {
        return comConvert::sysBase64($input, 1); // sysBase64,sysRevert
    }

    /**
     * HMACSHA256签名 https://jwt.io/  中HMACSHA256签名实现
     * @param string $input 为enc64(header).".".enc64(payload)
     * @param string $alg   算法方式
     * @return mixed
     */
    static function sign($input, $alg='HS256')
    {
        global $_cbase;
        $key = $_cbase['safe']['other'];
        $calg = array(
            'HS256' => 'sha256'
        );
        $res = hash_hmac($calg[$alg], $input, $key, true);
        return self::enc64($res);
    }

    static function req($key='autok'){
        $key = 'HTTP_' . strtoupper(str_replace(['-'], ['_'], $key)); 
        $jwt = empty($_SERVER[$key]) ? '' : $_SERVER[$key];
        return $jwt;
    }

}

/*  
    $jwt = $_SERVER['HTTP_AUTHORIZATION'] ?? false;

    return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $addlen = 4 - $remainder;
        $input .= str_repeat('=', $addlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
*/
