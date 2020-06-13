<?php 
namespace imcat;

/* string,array,file:解析类
 * serial : serEncode
 * json : jsonFormat,jsonDecode,jsonEncode,
 * xml(node): nodeParse
 * csv: csvPuts,csvGets
*/

// 编码转化,加密类
class comParse{    

    // 适合把中文用base64编码用于url传输(比url编码短,且都是安全字符),获取时用这个来解码
    // $s : 原字符串, 支持数组
    // $de : 0-编码, 1-解码, a-解码返回数组
    static function urlBase64($s,$de=0,$chrs=''){
        $a2 = $chrs ? $chrs : "-_"; // "-_" , ".~"; //《!()+,-.;@^_`~》安全13个
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
    
    // csv 
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

    // xml(node) 
    static function nodeParse($data,$cset='') {
        if(is_string($data)){
            $hfix = $cset ? "<?xml version='1.0' encoding='$cset'?>" : '';
            $data = simplexml_load_string($hfix.$data); // obj(自动转:utf-8)
        }
        $json = json_encode($data);
        $arr = json_decode($json, true);
        return $arr;
    }
    
    // 将数组转换为JSON字符串（兼容中文）
    static function jsonEncode($array) {
        $pmj = defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
        $json = @json_encode($array, $pmj); // JSON_UNESCAPED_UNICODE:PHP>=5.4生效
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

    // 反序列化（将某些特殊字符的utf8编码转为asc码解析）不建议使用原生的unserialize。
    static function serEncode($str) { 
        $str = str_replace("\r", "", $str);
        #$str = preg_replace('/s:(\d+):"(.*?)";/se', '"s:".strlen("$2").":\"$2\";"', $str);
        $str = preg_replace_callback('/s:(\d+):"(.*?)";/s', function($arr){
            return 's:'.strlen($arr[2]).':"'.$arr[2].'";';
        }, $str);
        return unserialize($str); 
    }

    # fix: %u, &#
    private function uniEncode($str, $fix='%u'){
        preg_match_all('/./u', $str, $matches);
        $reStr = "";
        foreach($matches[0] as $m){
            $itm = bin2hex(iconv('UTF-8',"UCS-4",$m));
            $reStr .= $fix.strtoupper(substr($itm,4));
        }
        return $reStr;
    }

    // unicode 
    static function uniDecode($str, $type='u'){
        if($type=='u'){ // '\u4E00'
            $reg = '/\\\\u([0-9a-f]{4})/i';
        }else{ // '&#x4E00'
            $reg = '/\&\#x([0-9a-f]{4})\;/i';
        }
        $str = preg_replace_callback($reg, function($arr){
            return mb_convert_encoding(pack('H*', $arr[1]), 'UTF-8', 'UCS-2BE');
        },  $str);
        return $str;
    }

}
