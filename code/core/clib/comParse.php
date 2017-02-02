<?php 
/* string,array,file:解析类
 * serial : serEncode
 * json : jsonFormat,jsonDecode,jsonEncode,
 * xml(node): nodeParse
 * csv: csvPuts,csvGets
*/

// 编码转化,加密类
class comParse{    
    
    // base64,其它 -------------------------------------------------- 
    
    // 适合把中文用base64编码用于url传输(比url编码短,且都是安全字符),获取时用这个来解码
    // $s : 原字符串, 支持数组
    // $de : 0-编码, 1-解码, a-解码返回数组
    static function urlBase64($s,$de=0,$type='mkv'){
        $a2 = $type=='mkv' ? ".-" : "_~"; //《!()+,-.;@^_`~》安全13个
        if(is_array($s)) $s = basElm::arr2text($s,"\n",'=');
        if($de){
            $s = str_pad(strtr($s,$a2,"+/"),strlen($s)%4,'=',STR_PAD_RIGHT);
            $s = base64_decode($s);
            if($de=='a') $s = basElm::text2arr($s); 
        }else{
            $s = base64_encode($s);
            $s = rtrim(strtr($s,"+/",$a2),'=');
        }
        return $s;
    }
    // urlEncode,不转化中文 : 《:/?=&#%》URL 《"&<>》HTML
    static function urlEncode($str,$ext=0,$percent=0){ //url,ext,percent
        if($percent){
            $str = str_replace('%','%25',$str);
        } 
        $a = array( '#'   , '&' ); 
        $b = array( '%23' , '%26' ); 
        $str = str_replace($a,$b,$str);
        // $str = str_replace(array(' ','<','>','"',"'","\r","\n"),'',$str);, 不是参数
        $c = array( '+'   , ' ' , '"'   ,"'"   , '<'   , '>'   , "\r"  , "\n"  , "\\"  );
        $d = array( '%2B' , '+' , '%22' ,'%27' , '%3C' , '%3E' , '%0D' , '%0A' , '%5C' );
        if($ext){
            $str = str_replace($c,$d,$str);
        }
        return $str;
    }
    
    // json -------------------------------------------------- 
    
    static function csvGets($file) {
        $handle = fopen($file,'r'); 
        $arr = array();
        while ($data = fgetcsv($handle)) { //每次读取CSV里面的一行内容  
            $arr = $data; //此为一个数组，要获得每一个数据，访问数组下标即可  
        }  
        fclose($handle); 
        return $arr;
    }

    static function csvPuts($file, $data) {
        $handle = fopen($file, 'r+');
        foreach ($data as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
    }

    // xml(node) -------------------------------------------------- 
    
    static function nodeParse($node) {
        $array = false;
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $array[$attr->nodeName] = $attr->nodeValue;
            }
        }
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1) {
                $array[$node->firstChild->nodeName] = getArray($node->firstChild);
            } else {
                foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeType != XML_TEXT_NODE) {
                    $array[$childNode->nodeName][] = self::nodeParse($childNode);
                } }
            }
        } else {
            return $node->nodeValue;
        }
        return $array;
    }
    // json -------------------------------------------------- 
    
    // 将数组转换为JSON字符串（兼容中文）
    static function jsonEncode($array) {        
        if(version_compare(PHP_VERSION,"5.4",">=")){
            $json = json_encode($array, JSON_UNESCAPED_UNICODE); 
        }else{
            self::jsonFormat($array, 'urlencode');
            $json = json_encode($array);
            $json = urldecode($json);
        } 
        $json = str_replace("\"},\"","\"}\n,\"",$json);
        $json = str_replace(",\",\"",",\"\n,\"",$json);
        return $json;
    }
    static function jsonDecode($str) { 
        $str = comFiles::killBOM($str);
        $str = trim($str); 
        $arr = json_decode($str,1);
        return $arr;
    }
    // 使用特定function对数组中所有元素做处理
    // $func 不用了，用self::jsonChinese代替
    static function jsonFormat(&$array, $func){ 
        if(!is_array($array)) return;
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            glbError::show("possible deep recursion attack!"); 
        }
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                self::jsonFormat($array[$key], $func);
            } else {
                $array[$key] = self::jsonChinese($value);
                //$array[$key] = $func($value); // $func='urlencode'
            }
        }
        $recursive_counter--;
    }
    //  获取只编码中文的字符串（目前只支持UTF-8编码的字符串）
    static function jsonChinese($string){
        if (preg_match_all('/[\x7f-\xff]+/', (string) $string, $chinse)){
            $array = array();
            foreach ($chinse[0] as &$_chinse){
                $array[] = urlencode($_chinse);
            }
            $string = str_replace($chinse[0], $array, $string);
        }
        return $string;
    }
    
    // serialize -------------------------------------------------- 
    
    // 反序列化（将某些特殊字符的utf8编码转为asc码解析）不建议使用原生的unserialize。
    static function serEncode($str) { 
        $str = str_replace("\r", "", $str);
        $str = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $str ); 
        return unserialize($str); 
    }

}
