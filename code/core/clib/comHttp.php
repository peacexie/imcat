<?php

//数据采集，doGET,doPOST,文件下载，
class comHttp
{

    public static $way = 0;
    public static $ways = array(
        '1' => array('curl_init','curlCrawl'),
        '2' => array('fsockopen','socketCrawl'),
        '3' => array('file_get_contents','fileCrawl'),
    );
    public static $cache = 0;
    public static $savep = '';

    //手动设置访问方式
    static function setWay($way){
        self::$way = intval($way);
    }
    //手动设置缓存
    static function setCache($exp){
        self::$cache = intval($exp);
    }

    //通过get方式获取数据
    static function doGet($url, $timeout=5, $header="") {    
        return self::doPost($url, array(), $timeout, $header);
    }
    //通过POST方式发送数据
    static function doPost($url, $parr=array(), $timeout=5, $header="") {
        if(empty($url)||empty($timeout)) return false;
        if(!preg_match('/^(http|https)/is',$url)) $url = "http://".$url;
        $method = ''; #self::$way = 2;
        if(isset(self::$ways[self::$way])){
            $method = self::$ways[self::$way][1];
        }else{
            foreach(self::$ways as $item) {
                if(function_exists($item[0])){
                    $method = $item[1];
                    break;
                }
            }
        } 
        return $method ? self::$method($url,$parr,$timeout,$header) : false;
    }
    
    //通过 curl get/post数据 ($data: str:xml,str:json,array)
    // $data:array('_ref'=>'http://down.chinaz.com/soft/37712.htm'); 来路模拟
    // $header:'X-FORWARDED-FOR:8.8.8.8'.PHP_EOL.'CLIENT-IP:8.8.8.8' //来源IP模拟
    static function curlCrawl($url, $data=array(), $timeout=5, $header="") {
        // getCache
        $cres = self::getCache($url, $data);
        if($cres!==false) return $cres;
        // header
        $header = self::_getHeader($header);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(isset($data['_ref'])){ //模拟来源地址
            curl_setopt($ch, CURLOPT_REFERER, $data['_ref']);
            unset($data['_ref']);
        }
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        if(substr($url,0,8)=='https://'){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            // CURL_SSLVERSION_TLSv1_2
        }
        $result = curl_exec($ch);
        // saveCache
        self::saveCache($result);
        // return
        curl_close($ch);
        return $result;
    }
    //通过 socket get/post数据
    static function socketCrawl($url, $data=array(), $timeout=5, $header="") {
        // getCache
        $cres = self::getCache($url, $data);
        if($cres!==false) return $cres;
        // header
        $header = self::_getHeader($header);
        $url = parse_url($url); $errno = 0; $errstr = '';
        $url["path"] = ($url["path"] == "" ? "/" : $url["path"]);
        $url["port"] = empty($url["port"]) ? 80 : $url["port"];
        $url["query"] = isset($url["query"]) ? $url["query"] : "";
        if(($fsock = fsockopen(gethostbyname($url["host"]), $url['port'], $errno, $errstr, $timeout)) < 0){
            return false;
        }
        $request = $url["path"].($url["query"] ? "?" . $url["query"] : ""); 
        $eol = PHP_EOL; $eol2 = $eol.$eol; 
        $in  = (empty($data) ? 'GET' : 'POST')." $request HTTP/1.0$eol";
        $in .= "Host: " . $url["host"] . "$eol$header";
        if(!empty($data)){
            $pstr = http_build_query($data);  
            $in .= "Content-type: application/x-www-form-urlencoded$eol";
            $in .= "Content-Length: " . strlen($pstr) . $eol;
            $in .= "Connection: Close$eol2$pstr$eol2";
        }else{
            $in .= "Connection: Close$eol2";
        }
        if(!fwrite($fsock, $in, strlen($in))){
            fclose($fsock);
            return false;
        }
        $out = null; 
        while($buff = @fgets($fsock, 2048)){
            $out .= $buff;
        }
        fclose($fsock);
        $pos = strpos($out, $eol2);
        $head = substr($out, 0, $pos);    
        $status = substr($head, 0, strpos($head, $eol));    
        if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
            if(intval($matches[1]) / 100 == 2){
                $result = substr($out, $pos + 4, strlen($out) - ($pos + 4));
                // saveCache
                self::saveCache($result);
                return $result;
            }else{ return false; }
        }else{ return false; }
    }
    //通过 file_get_contents 函数post数据
    static function fileCrawl($url, $data=array(), $timeout=5, $header=""){
        // getCache
        $cres = self::getCache($url, $data);
        if($cres!==false) return $cres;
        // header
        $header = self::_getHeader($header);
        $opts = array( 
            'http'=>array(
                'protocol_version'=>'1.0',//http协议版本(若不指定php5.2系默认为http1.0)
                'timeout' => $timeout ,
                'header'=> $header,  
                'method'=> (empty($data) ? 'GET' : 'POST'),
            )
        ); 
        if(!empty($data)){
            $pstr = http_build_query($data); 
            $header .= "Content-length: ".strlen($pstr);
            $opts['http']['content'] = $pstr;
        }
        $context = stream_context_create($opts);
        $result = @file_get_contents($url,false,$context);
        // saveCache
        self::saveCache($result);
        // return
        return $result;
    }
    
    //默认模拟的header头 
    static function _getHeader($header="", $restr=1){
        if(!empty($header)) return $header;
        $defs = array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
        );
        $cfgs = array(
            'HTTP_ACCEPT' => 'Accept',
            'HTTP_ACCEPT_LANGUAGE' => 'Accept-Language',
            'HTTP_ACCEPT_CHARSET'=>'Accept-Charset',
            'HTTP_USER_AGENT' => 'User-Agent', 
        );
        $header = array();
        foreach($cfgs as $ks=>$kn){
            if(isset($_SERVER[$ks])){
                $header[] = "$kn: ".$_SERVER[$ks];
            }elseif(isset($defs[$ks])){
                $header[] = "$kn: ".$defs[$ks];
            }
        }
        // 乱码问题: 遇到了外部$_cbase中加入,这里添加
        // 'HTTP_ACCEPT_ENCODING' = 'Accept-Encoding: gzip, deflate';
        $restr && $header = implode(PHP_EOL,$header);
        return $header;
    }

    // 下载web中的(或URL)文件
    static function downLoad($url, $showname='', $expire=1800){
        $size = self::downCheck($url, $showname, 1);
        basEnv::obClean();
        $type = basNodef::mimeType($url);
        //发送Http Header信息 开始下载
        header("Pragma: public");
        header("Cache-control: max-age=".$expire);
        //header('Cache-Control: no-store, no-cache, must-revalidate');
        header("Expires: " . gmdate("D, d M Y H:i:s",$_SERVER["REQUEST_TIME"]+$expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s",$_SERVER["REQUEST_TIME"]) . "GMT");
        header("Content-Disposition: attachment; filename=".$showname);
        header("Content-Length: $size"); 
        header("Content-type: ".$type);
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary" );
        readfile($url);
        return true;
    }
    // 保存远程页面(图片)到本地
    static function downSave($url, $showname='', $curl=0){
        self::downCheck($url, $showname);
        if($curl){
            $data = self::curlCrawl($url);
        }else{
            ob_start();
            readfile($url);
            $data = ob_get_contents();
            ob_end_clean();
        }
        comFiles::put($showname, $data);
        return $showname;
    }
    // downCheck
    static function downCheck(&$url, &$showname, $chkexists=0){
        $fsize = 0;
        if(preg_match('/^http:\/\//',$url)){
            ini_set('allow_url_fopen', 'On');
        }
        if(empty($url) || ($chkexists && !file_exists($url))) {
            throw new InvalidArgumentException("[$url]Not Exists!");
        }else{
            $fsize = @filesize($url);
        }
        if(empty($showname)){
            $info = parse_url($url);
            $_path = explode("/",$info['path']);
            $showname = end($_path);
        }
        return $fsize;
    }
    
    // 格式化返回数据
    static function fmtContent($content){
        $content = trim($content); // BOM标记？空白行？
        if (!$content) { return ''; }
        // json
        if(substr($content,0,1)=='{'){
            $response = json_decode($content, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                parse_str($content, $response);
                return $response; 
            }
        }
        // xml
        if(substr($content,0,1)=='<'){
            $xml = @simplexml_load_string($content); 
            if(is_object($xml)) return $xml;
        }
        // html, text
        return $content;
    }

    // 缓存处理
    static function getCache($url, $data) {
        if(!self::$cache) return false;
        $fp = preg_replace("/https?\:\/\//", '', $url);
        $fp = str_replace(array('/','?','&',), array('!','---',',',), $fp);
        $fp = basStr::filSafe4($fp);
        if(strlen($fp)>120){
            $md5 = md5("$url+++".json_encode($data));
            $fp = substr($fp,0,80).'---'.substr($fp,-20).'---'.$md5;
        }
        self::$savep = $fp;
        $data = extCache::cfGet("/remote/$fp", self::$cache, 'dtmp', 'str');
        return $data;
    }
    static function saveCache($data) {
        if(!self::$cache) return false;
        extCache::cfSet("/remote/".self::$savep, $data);
    }

    //兼容方法(后续去掉)
    static function curlGet($url, $timeout=5, $header="") {    
        return self::curlCrawl($url, array(), $timeout, $header);
    }
    static function curlPost($url, $parr=array(), $timeout=5, $header="") {    
        return self::curlCrawl($url, $parr, $timeout, $header);
    }

}

/*
    comHttp::setCache(30); // 缓存30分钟

    $url = "http://imcat.txjia.com/chn.php?news-n1012";
    $data = comHttp::curlCrawl($url);
    $url = "http://imcat.txjia.com/chn.php?news-n1014";
    $data = comHttp::socketCrawl($url);
    $url = "http://imcat.txjia.com/chn.php?news-n1022";
    $data = comHttp::fileCrawl($url);
*/

