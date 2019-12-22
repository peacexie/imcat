<?php
namespace imcat;

/*
    comHttp::setCache(30); // 缓存30分钟
    $opt:array('ref'=>'http://x_yz.com/file.htm'); 来路模拟
    $opt:array('proxy'=>['ip','80']); proxy设置
    $opt:array('cookie'=>'user=admin'); cookie设置
    $header:'X-FORWARDED-FOR:8.8.8.8'.PHP_EOL.'CLIENT-IP:8.8.8.8' // ??? 来源IP模拟
*/

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
    static function doGet($url, $params=array()) {    
        return self::doPost($url, array(), $params);
    }
    //通过POST方式发送数据
    static function doPost($url, $data=array(), $params=array()) {
        if(empty($url)) return false; 
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
        return $method ? self::$method($url, $data, $params) : false;
    }
    
    //通过 curl get/post数据 ($data: str:xml,str:json,array)
    static function curlCrawl($url, $data=array(), $opt=array()) {
        // getCache
        $cres = self::getCache($url, $data);
        if(!empty($cres[1])) return $cres[1];
        // init
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        self::_timeout($opt, $ch); // Timeout
        self::curlProxy($ch, $opt); // ref/proxy
        # curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $header = self::_heads($opt, $data); // header/cookie/type
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        self::curlData($ch, $opt, $data); // data,gzip
        // https
        if(substr($url,0,8)=='https://'){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            if(isset($opt['sslv'])){ // 2-7, 默认情况下PHP会自己检测这个值
                curl_setopt($ch, CURLOPT_SSLVERSION, $opt['sslv']);
            } // ['TLSv1_3','TLSv1_2','TLSv1_1','TLSv1_0','SSLv3','SSLv2','SSLv1']
        }
        // saveCache & return
        $result = curl_exec($ch);
        if(!empty($cres[0])) self::saveCache($cres[0], $result);
        curl_close($ch);
        return $result;
    }
    // data,gzip
    static function curlData(&$ch, &$opt=array(), $data=array()){
        if(!empty($data)){
            if(isset($opt['type']) && in_array($opt['type'],array('xml','json'))){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            }else{
                curl_setopt($ch, CURLOPT_POST, true);  
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
        }
        if(isset($opt['gzip'])){ //模拟来源地址
            $gzip = empty($opt['gzip']) ? 'gzip,deflate' : $opt['gzip'];
            curl_setopt($ch, CURLOPT_ENCODING, $gzip); //curl解压gzip页面内容
        } 
    }
    // ref/proxy
    static function curlProxy(&$ch, &$opt=array()){
        if(isset($opt['ref'])){ //模拟来源地址
            curl_setopt($ch, CURLOPT_REFERER, $opt['ref']);
        }
        if(isset($opt['proxy'])){
            $proxy = $opt['proxy'];
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式  
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]); //代理服务器地址   
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]); //代理服务器端口
        }
    }

    //通过 socket get/post数据
    static function socketCrawl($url, $data=array(), $opt=array()) {
        // getCache
        $cres = self::getCache($url, $data);
        if(!empty($cres[1])) return $cres[1];
        // timeout/header
        $timeout = self::_timeout($opt);
        $header = self::_heads($opt, $data);
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
        $in .= "Host: " . $url["host"] . "$eol".implode($eol,$header);
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
                self::saveCache($cres[0], $result);
                return $result;
            }else{ return false; }
        }else{ return false; }
    }
    //通过 file_get_contents 函数post数据
    static function fileCrawl($url, $data=array(), $opt=array()){
        // getCache
        $cres = self::getCache($url, $data);
        if(!empty($cres[1])) return $cres[1];
        // timeout/header
        $timeout = self::_timeout($opt);
        $header = self::_heads($opt, $data);
        $opts = array( 
            'http'=>array(
                'protocol_version'=>'1.0',//http协议版本(若不指定php5.2系默认为http1.0)
                'timeout' => $timeout ,
                'header'=> implode(PHP_EOL,$header),  
                'method'=> (empty($data) ? 'GET' : 'POST'),
            )
        ); 
        if(!empty($data)){
            $pstr = http_build_query($data); 
            $opts['http']['header'] = "Content-length: ".strlen($pstr);
            $opts['http']['content'] = $pstr;
        }
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        // saveCache
        $cres && self::saveCache($cres[0], $result);
        // return
        return $result;
    }
    
    // exp
    static function _timeout(&$opt=array(), &$ch='re'){
        $exp = 5;
        if(is_numeric($opt)){
            $exp = $opt;
            $opt = array();
        }elseif(!empty($opt['exp'])){
            $exp = $opt;
        }
        if($ch=='re') return $exp;
        curl_setopt($ch, CURLOPT_TIMEOUT, $exp);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $exp);
    }
    // head/cookie: 'null', empty(), cookie
    static function _heads(&$opt=array(), &$data=array()){
        if(empty($opt['head'])){
            $header = self::_defHeader();
        }elseif(is_array($opt['head'])){
            $header = implode(PHP_EOL, $opt['head']);
        }else{
            $header = $opt['head']=='null' ? array() : $opt['head'];
        }
        if(isset($opt['cookie'])){ // cookie设置
            $header[] = "Cookie: ".$data['cookie'];
        }
        $tag = array(
            'html'=>'text/html', // plain
            'xml'=>'text/xml', 
            'json'=>'application/json',
            'down'=>'application/octet-stream',
        );
        if(isset($opt['type']) && isset($tag[$opt['type']])){ // json
            $header[] = "Content-Type: ".$tag[$opt['type']];
            if($opt['type']=='json'){
                if(is_array($data)) $data = json_encode($data);
                $header['json'] = "Content-Length: ".strlen($data);
            }
        }
        return $header;
    }

    //默认模拟的header头 
    static function _defHeader($header=""){
        if(!empty($header)) return $header;
        $defs = array(
            'HTTP_USER_AGENT' => 'comHttp@imcat.txjia.com',
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
        if(!$url) return;
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
            throw new \Exception("[$url]Not Exists!");
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

    static function fpCache($url, $data=[]) {
        $fp = extCache::fName($url, 1, 2);
        return $fp.'---'.md5($url.'+'.json_encode($data)).'.htm';
    }
    // 缓存处理
    static function getCache($url, $data) {
        if(!self::$cache) return false;
        $fp = self::fpCache($url, $data);
        $data = extCache::cfGet("/remote/$fp", self::$cache, 'vars', 'str');
        return [$fp, $data];
    }
    static function saveCache($fp, $data) {
        if(!self::$cache) return false;
        extCache::cfSet("/remote/$fp", $data, 'vars');
    }

    //兼容方法(后续去掉)
    static function curlGet($url, $params=array()) {
        return self::curlCrawl($url, array(), $params);
    }
    static function curlPost($url, $data=array(), $params=array()) {    
        return self::curlCrawl($url, $data, $params);
    }

}

