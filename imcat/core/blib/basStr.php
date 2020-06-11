<?php
namespace imcat;

// String类 - by Peace(XieYS) 2012-02-18

class basStr{    

    // 把字符串转化为数组，支持中文
    static function strArr($str, $cset='utf-8'){
        $len = mb_strlen($str, $cset);
        $arr = array();
        for($i=0; $i<$len; $i++){
            $arr[] = mb_substr($str, $i, 1, $cset);
        }   
        return $arr;
    }

    // 匹配中文字符串
    // ($str,'100,ffff','3,5'), ($str,'cnchr','c124'), 
    static function getMatch($str,$case='cnchr',$len='c124'){
        $cfgs = array(
            'isasc' => array('{20}','{ff}'), // ascii码
            'noasc' => array('{100}','{ffff}'), //非ascii码
            'cnchr' => array('{4e00}','{9fbf}'), //中文
            //'dbstr' => "", //导出的db中文
        );
        $lens = array(
            'c124' => '{1,24}',
            'c196' => '{1,96}',
            'c204' => '{1,4}', //姓名
        );
        $cfg = isset($cfgs[$case]) ? $cfgs[$case] : explode(',',str_replace(",","},{",'{'.$case.'}'));
        $len = isset($lens[$len]) ? $lens[$len] : '{'.$len.'}'; "\{$len\}"; 
        if($case=='dbstr'){ 
            preg_match_all("/[\x{2000}-\x{9fa5}a-z0-9\/\:\,\.\_\-\[\]]{0,48}[\x{4e00}-\x{9fa5}]{1}[\x{2000}-\x{9fa5}a-z0-9\/\:\,\.\_\-\[\]]{0,96}/iu",$str,$m); 
        }else{
            preg_match_all("/[\x{$cfg[0]}-\x{$cfg[1]}]{$len}/u",$str,$m);
        }
        $res = empty($m[0]) ? array() : array_unique($m[0]);
        return $res;
    }

    // 计算字符串字节数，英文算一个字节,不管[GBK/utf-8]编码中文算两个字节
    static function chrCount($str){
        global $_cbase;
        $ch = $_cbase['sys']['cset']=='utf-8' ? 3 : 2; //中文宽度
        $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str)); 
        if($length){
            return strlen($str) - $length + intval($length / $ch) * 2;
        }else{
            return strlen($str);
        }
        //return strlen(preg_replace('/([x4e00-x9fa5])/u','**',$str));
    }
    
    // *** 截取字符串，英文算一个,中文算一个 
    static function cutCount($str,$len=255){ 
        global $_cbase;
        $ch = $_cbase['sys']['cset']=='utf-8' ? 3 : 2; //中文宽度
        $n = strlen($str); //php函数原始长度
        $p = 0; //指针
        $cnt = 0; // 计数,英文算一个字符
        if($n > $len) {
            for($i=0; $i<$n; $i++) {
                if($p>=$n) break; //结尾
                if($cnt>=$len) break; //最大文字个数
                if(ord($str[$p]) > 127) { $p += $ch; }
                else { $p++; }
                $cnt++;
            }
            return substr($str,0,$p);
        }else return $str;
    }
    
    // *** 截取字符串，得到等宽字符串
    static function cutWidth($string, $length=24, $etc='...') {
        global $_cbase;
        if(!$string) return ""; 
        $clen = $_cbase['sys']['cset']=='utf-8' ? 3 : 2; //中文宽度
        $olen = strlen($string); $mblen=mb_strlen($string); $len2 = $length*2; //中文宽计算
        $pos = 0; $width = 0; $mbcnt = 0;
        if($mblen > $length) {
            //将$length换算成实际UTF8格式编码下字符串的长度
            for($i=0; $i<$olen; $i++) {
                if ($pos >= strlen($string)) { break; }
                if ($width >= $len2) { break; }
                //当检测到一个中文字符时, //大概按一个汉字宽度相当于两个英文字符的宽度
                if(ord($string[$pos]) > 127) { $pos += $clen; $width += 2; }
                else { $pos++; $width++; }
                $mbcnt++;
            }
            $res = $mblen>$mbcnt ? mb_substr($string,0,$mbcnt-1).$etc : $string;
            return $res;
        }else return $string;
    }
    // *** 格式化数字,$type='Byte'显示占用空间
    static function showNumber($num, $type=0){
        $b = 0; $bfix = ''; // Byte
        if(is_numeric($num)){ $b = $num; }
        if($type==='Byte'){
            // TB,PB,EB,ZB,YB,NB,DB
            if($b>pow(1024,4)){
                $b = number_format($b/(pow(1024,4)),2)." (TB) ";
            }else if($b>pow(1024,3)) {
                $b = number_format($b/(pow(1024,3)),2)." (GB) ";
            }else if($b>pow(1024,2)) {
                $b = number_format($b/(pow(1024,2)),2)." (MB) ";
            }else if($b>pow(1024,1)) {
                $b = number_format($b/(pow(1024,1)),2)." (KB) ";
            }else{
                $b = $b." (B) ";
            }
            return $b;
        }else{
            return number_format($b,$type);
        }
    }
    // *** 显示状态
    // "Y;N;X;-","已审;未审;未过;未知",$SetShow);
    static function showState($xState,$xMsg,$val){
        global $_cbase; 
        if($xMsg==""){ $xMsg=$xState; }
        $sc = '333,'.$_cbase['ucfg']['ctab'].',999'; $ac = explode(',',$sc); 
        $ak = explode(';',$xState); $am = explode(';',$xMsg);
        $j=0; $r="<span style='color:#CCC'>-</span>";
        for($i=0;$i<sizeof($ak);$i++) { 
            if($j>=sizeof($ac)) { $j=0; }
            if($val==$ak[$i]){
                $r = "<span style='color:#".$ac[$j]."'>".$am[$i]."</span>";
                break;
            }
            $j++;
        }
        return $r;
    }
    // *** 显示颜色
    static function showColor($Text,$Color){
        if(substr($Color,0,1)!='#') $Color='#'.$Color;
        $Text = str_replace('<',"&lt;",$Text);
        $Text = str_replace('>',"&gt;",$Text);
        if((strlen($Color)>3)&&($Color!='#000000')){ 
            $Text = "<span style='color:$Color'>$Text</span>"; 
        }
        return $Text;
    }
    
    // Filter转义/还原

    // *** 文本文件
    static function filText($str,$cbr=1){
        if(is_array($str)) {
            foreach($str as $key => $val) $str[$key] = self::filText($val,$cbr);
        }else{// 不用:html(specialchars/entities) : 1-不好记, 2-不兼容数组, 3-php5.4+gbk返回空
            $str = str_replace(array('&','"',"'",'<','>'), array('&amp;','&quot;',"&#039;",'&lt;','&gt;'), $str);
            if($cbr) $str = nl2br($str); // htmlentities($str,3,"UTF-8");
        }
        return $str;
    }
    // *** 过滤危险的HTML
    static function filHtml($val){
        $string = $val;
        $searcharr = array("/(javascript|jscript|js|vbscript|vbs|about):/i","/on(mouse|exit|error|click|dblclick|key|load|unload|change|move|submit|reset|cut|copy|select|start|stop)/i","/<script([^>]*)>/i","/<iframe([^>]*)>/i","/<frame([^>]*)>/i","/<link([^>]*)>/i","/@import/i");
        $replacearr = array("\\1\n:","on\n\\1","&lt;script\\1&gt;","&lt;iframe\\1&gt;","&lt;frame\\1&gt;","&lt;link\\1&gt;","@\nimport");
        $string = preg_replace($searcharr,$replacearr,$string);
        $string = str_replace("&#","&\n#",$string);
        return $string;
    }
    // *** 从Html内容中截取字符串，
    static function filHText($xStr,$xLen=0) {
        $xStr = preg_replace("/<\!--.*?-->/si","",$xStr); //注释
        $xStr = strip_tags($xStr); 
        //$xStr = htmlspecialchars_decode($xStr); // 不含<>字符
        $xStr = self::filTrim($xStr); 
        if($xLen) $xStr = self::cutWidth($xStr,$xLen);
        return $xStr; // nl2br($s);
    }
    // *** 从Html内容过滤样式，适合wap显示
    static function filHWap($str){
        $str = preg_replace('/<img.*?src=["\']?([^"\'\s]*)["\']?[^>]*>/is',"<img src=\"$1\" />",$str);//图片格式化
        $str = preg_replace('/<p[^>]*>\s*<img[^>]*src=["\']?([^"\'\s]*)["\']?[^>]*>/is',"<p style=\"text-align:center;\" ><img src=\"$1\" />",$str);//图片居中
        // text
        $str = preg_replace('/>(\s|&nbsp;|　)*/is',">",$str);//去除空格
        $str = preg_replace('/text-indent:[^;\'"]*;?/i','',$str);
        // iframe
        $str = preg_replace('/width=(\d+)&amp;height=(\d+\.{0,9}\d*)/is',"",$str);
        $str = preg_replace('/<iframe height="\d+" width="\d+"/','<iframe height="250" width="100%"',$str);
        return $str;
        #$val = preg_replace('/\[#.*?#\]/','',safestr($val)); // 安全,分页
    }
    // *** Form中字符编码
    static function filForm($str) {
        return self::filText($str,0);
    }
    // *** Account过滤帐号
    static function filKey($str,$ext='_'){
        $url = array(
            'url' => FILTER_SANITIZE_URL,
            'email' => FILTER_SANITIZE_EMAIL,
        );
        if(isset($url[$ext])){
            return filter_var($str, $url[$ext]);
        }
        $re = '';
        if(strlen($str)){ //$str=0
            $tmp = KEY_NUM10.KEY_CHR26.$ext; //-._@
            $tab = strtoupper($tmp).strtolower($tmp); 
            for($i=0;$i<strlen($str);$i++) { 
                if(strstr($tab,substr($str,$i,1))) $re .= substr($str,$i,1);
            }
        } 
        return $re;
    }
    // *** Title过滤标题
    static function filTitle($xStr, $exa=array('\r','\n')){
        $def = array('<','>','"',"'","\\");
        if($exa=='file') $exa = array('/',':','*','?','|'); // \/:*?"<>|
        if(!empty($exa)){
            $def = array_merge($def, $exa);
        }
        $xStr = str_replace($def,'',$xStr); 
        return $xStr;
    }
    // *** Safe4过滤表单
    static function filSafe4($xStr, $exa=array('%')){
        return self::filTitle($xStr, $exa);
    }
    // *** 过滤空行和注释
    static function filNotes($str){
        $str = preg_replace('/\/\*(.*?)\*\//is','',$str);
        $str = preg_replace('/\/\/(.*?)\ /is','',$str);
        $str = preg_replace('( [\s| ]* )'," ",$str);
        $str = preg_replace("/<\!--.*?-->/si","",$str); //注释
        return $str; 
    }

    // 去除多余的空格和换行符
    static function filTrim($str,$mode=0){
        $str = trim($str);
        if($mode==1){ 
            // 去除多个空白
            $str = preg_replace("/[\s]{2,}/", '', $str);
        }elseif($mode==2){ 
            // 去除所有的空格和换行符
            $str = preg_replace("/\s+/", '', $str);
        }else{
            // 去除多余的空格和换行符，只保留(第)一个
            $str = preg_replace("/\s(?=\s)/","\\1",$str); 
        }
        return $str;
    }

    // Check字符处理

    static function isKey($str,$m1=3,$m2=12,$type='') {
        $new = self::filKey($str,$type);
        $len = strlen($new);
        return $str===$new && $len>=$m1 && $len<=$m2;
    }

    // type:mob,400,800,tel
    static function isTel($val,$type='mob') {
        if($type=='mob'){ // 138-1234-5678
            return strlen($val) > 10 && preg_match("/^1[\d]{2}[\-]{0,1}[\d]{4}[\-]{0,1}[\d]{4}$/", $val);
        }elseif($type=='400'){ // 400-123-4567
            return strlen($val) > 9 && preg_match("/^400[\-]{0,1}[\d]{3}[\-]{0,1}[\d]{4}$/", $val);
        }elseif($type=='800'){ // 800-123-4567
            return strlen($val) > 9 && preg_match("/^800[\-]{0,1}[\d]{3}[\-]{0,1}[\d]{4}$/", $val);
        }else{ // tel: 010-1234-5678
            return strlen($val) > 9 && preg_match("/^0[\d]{2,3}[\-]{0,1}[\d]{3,4}[\-]{0,1}[\d]{4}$/", $val);
        }
        
    }
    static function isUrl($url) {
        return filter_var($email, FILTER_VALIDATE_URL);
    }
    static function isMail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    // 检查字符串是否是UTF8编码,是返回true,否则返回false
    static function isUtf8($str,$re='T/F'){
        $flag = mb_detect_encoding($str, array('ASCII','GB2312','GBK','BIG5','UTF-8'));
        if($re=='T/F'){
            return $flag === 'UTF-8';
        }else{
            return $flag;
        }
    }
    // 避免重复转utf-8，只转需要转的文档
    static function isConv($str = ''){
        if(!$str) return FALSE;
        if(mb_detect_encoding($str,'UTF-8',TRUE) == 'UTF-8') return FALSE; //本就是UTF8的编码
        return TRUE;    
    }

    // 隐藏电话,手机,邮件,qq,ip的中间一部分
    // ('13712345678','',3) -> 137****5678
    static function subReplace($str,$char='',$dstart=0){
        $char = empty($char) ? '*' : $char;
        if(strpos($str,'@')>0){
            $a = explode('@',$str);
            $suf = '@*'.substr($a[1],strrpos($a[1],'.'));
            $str = $a[0];;
        }else{
            $suf = '';
        }
        $len = strlen($str);
        if($len<3) return $str.$suf;
        if($len<6) $n = 2;
        else $n = 4;
        $start = $dstart ? $dstart : (($len-6)<1 ? 1 : $len-6);
        $re = ''; for($i=0;$i < $n;$i++) $re .= $char;
        $str = substr_replace($str,$re,$start,$n);
        return $str.$suf;
    }

    // 模板替换
    static function tplReplace($tpl,$source=array()){
        global $_cbase; 
        if(preg_match_all('/{\s*([\$]{0,1}\w*)\s*}/i', $tpl, $matchs)){
            if(!empty($matchs[0])){ 
                foreach($matchs[0] as $ik=>$iv){
                    $k = str_replace('$','',$matchs[1][$ik]); 
                    $val = isset($source[$k]) ? $source[$k] : '';
                    $cks = array('ucfg','sys','server',);
                    foreach ($cks as $ic) {
                        if(strlen($val)==0 && isset($_cbase[$ic][$k])){ 
                            $val = $_cbase[$ic][$k]; 
                            break;
                        } 
                    }
                    if(strlen($val)==0){ 
                        $val = isset($_cbase[$k]) ? $_cbase[$k] : "($k)"; 
                    } 
                    $tpl = str_replace($iv,$val,$tpl);
                }
            }
        }
        return $tpl;
    }

}