<?php
namespace imcat;

// Html类
class glbHtml{    

    // viewport-scale
    static function wpscale($width=480, $script=1){
        $jstr = "
        var ua=navigator.userAgent, wscr=parseInt(window.screen.width),
          scale=wscr/$width, wpus='user-scalable=no', wpstr='';
        if(/Android/.test(ua)) { wpus = 'minimum-scale='+scale+', maximum-scale='+scale; }
        wpstr='<meta name=\"viewport\" content=\"width=$width, '+wpus+', target-densitydpi=device-dpi\">';
        if(wscr<$width) document.write(wpstr);\n";
        if($script){ $jstr = "<script>$jstr</script>\n";}
        return $jstr;
    }

    // 页面结构
    static function page($mod='',$ext='',$iex=''){
        global $_cbase; 
        if($mod=='body'){
            echo "</head><body$ext>\n";
        }elseif($mod=='end'){
            if(empty($_cbase['run']['headed'])) self::page('');
            if(strlen($ext)>12) echo "$ext\n";
            echo "</body></html>\n";
        }elseif($mod=='aumeta'){ // 去掉/修改:author-meta标签在这里
            $auweb = "http://imcat.txjia.com, https://github.com/peacexie/imcat";
            echo "<meta name='author' content='$auweb, 贴心猫(Imcat)'>\n";
        }elseif(in_array($mod,array('robots','viewport','keywords','description'))){
            if($mod=='robots' && empty($ext)) $ext = 'noindex, nofollow';
            if($mod=='viewport' && empty($ext)) $ext = 'width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no';
            echo "<meta name='$mod' content='$ext'>\n"; 
        }elseif($mod=='init'){
            echo "<meta charset='".$_cbase['sys']['cset']."'>\n";
            echo "<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>\n";
            self::page('viewport'); 
            if($ext) self::page('robots'); 
            if(empty($iex)) echo "<link rel='shortcut icon' href='".PATH_VIEWS."/base/assets/logo/favicon.ico' />\n";
        }else{ //head
            $_cbase['run']['headed'] = 1;
            $mod || $mod = $_cbase['sys_name'];
            $mod = str_replace('(sys_name)',$_cbase['sys_name'],$mod);
            echo "<!DOCTYPE html><html><head>\n";
            self::page('init',$ext,$iex);
            echo "<title>$mod</title>\n"; 
        }
    }

    // header
    static function head($type='js',$cset=''){
        global $_cbase;
        $cset = $cset ? $cset : $_cbase['sys']['cset'];
        $a = array(
            'html'=>'text/html',
            'css'=>'text/css',
            'js'=>'text/javascript', 
            'json'=>'application/json',
            'jsonp'=>'application/jsonp',
            'xml'=>'text/xml', 
            'down'=>'application/octet-stream',
        );
        header("Content-Type:$a[$type]; charset=$cset");
    }
    
    // domain_allow跨域允许
    static function dallow($domain=''){
        if($domain=='*'){ // 请先自行认证,如oauth
            $allow = array('*'); 
        }else{
            $allow = glbConfig::read('domain.dmacc','sy'); 
            if(empty($domain)){
                @$aurl = parse_url($_SERVER["HTTP_REFERER"]);
                @$domain = $aurl['host'];
            }
        }
        if(in_array($domain, $allow)){ 
            $aldom = $domain=='*' ? '*' : "http://$domain"; // https ?
            header("Access-Control-Allow-Origin:$aldom"); // 指定允许其他域名访问
            header('Access-Control-Allow-Methods:POST'); // 响应类型  
            header('Access-Control-Allow-Headers:x-requested-with,content-type'); // 响应头设置
            header('Access-Control-Allow-Credentials:true'); // 允许携带 用户认证凭据（也就是请求携带Cookie）
            header('X-Frame-Options:ALLOWALL'); //ALLOWALL，ALLOW-FROM
        } 
    }
    
    // table:(bar): 头
    static function tab_bar($title,$cont,$w1=25,$css2='tc'){    
        echo "<table border='0' class='tbbar1'><tr>\n";
        echo "<th class='tc h150' width='$w1%k'>$title</th>\n";
        echo "<td class='$css2'>$cont</td>\n"; 
        echo "</tr></table>\n";
        
    }
    // form+table:头
    static function fmt_head($fmid,$fmact,$tbcss='',$win='',$tbbrd=1){
        global $_cbase; 
        if($tbcss=='tblist') $_cbase['run']['tabResize'] = 1;
        $fmact = basReq::getURep($fmact,'recbk');
        echo "<form id='$fmid' name='$fmid' method='post' action='$fmact' target='$win'>\n";
        $recbk = basReq::val('recbk','');
        $recbk = $recbk==='ref' ? @$_SERVER["HTTP_REFERER"] : $recbk;
        echo "<input name='recbk' type='hidden' value='$recbk' />\n"; 
        echo "<table border='$tbbrd' class='table $tbcss'>\n"; 
    }    
    // form+table:(end):结束
    static function fmt_end($data='',$tabend='</table>'){
        global $_cbase; 
        if(!$data){ echo "\n$tabend</form>"; return; }
        if(is_array($data)){
            $arr = $data;
        }else{
            $arr[] = $data;    
        }
        $str = '';
        foreach($arr as $v){
            $itm = explode('|',"$v|");
            //if($itm[1]) $itm[1] = $itm[0];
            $str .= "\n<input name='$itm[0]' type='hidden' value='$itm[1]' />";
        }
        echo "$str$tabend</form>";
        // utabResize
        if(!empty($_cbase['run']['tabResize'])){
            echo basJscss::imp('/base/assets/cssjs/resizeCols.js');
        }
    }
    
    // form:(增加/修改):一行
    static function fmae_row($title,$msg,$hid=0){
        echo "<tr ".($hid?"style='display:none'":'')."><td class='tc'>$title</td>\n";
        echo "<td class='tl'>$msg</td></tr>\n";
    }
    // form:(增加/修改):头
    static function fmae_top($title,$msg,$width=25){
        echo "<tr><th width='$width%'>$title</th>\n";
        echo "<th class='tr'>$msg</th></tr>\n";
    }
    // form:(增加/修改):提交
    static function fmae_send($fmid,$title,$width=0,$bcls='tc'){
        $input = "<input name='$fmid' type='submit' class='btn' value='$title' />";
        echo "<tr><td class='tc' ".(!empty($width) ? "width='$width%'" : "").">$title</td>\n";
        echo "<td class='$bcls'>$input".($bcls=='tr' ? " &nbsp; 　 " : "")."</td></tr>\n";
    }
    
    static function null_cell($str,$char='Y'){
        return empty($str) ? "<span class='cCCC'>---</span>" : ($char=='Y' ? 'Y' : $str);
    }
    
    static function ieLow_js(){
        $tags = 'abbr,article,aside,audio,canvas,datalist,details,dialog,eventsource,figure,footer';
        $tags .= ',header,hgroup,mark,menu,meter,nav,output,progress,section,time,video';
        $s = '<!--[if lt IE 9]><script>';
        $s .= '(function(){';
        $s .= 'if(! /*@cc_on!@*/ 0) return;';
        $s .= 'var e = "$tags".split(",");';
        $s .= 'for(var i=0;i<e.length;i++){document.createElement(e[i]);} ';
        $s .= '})()</script><![endif]-->';
        echo "\n$s\n";
    }
    static function ieLow_html($mie=9,$css='LowIE',$msg=''){
        $msg || $msg = basLang::show('core.ie_low',$mie); 
        $s = "<!--[if lt IE $mie]>\n"; //<!--[if lt IE 9]>
        $s .= "<div class='$css'>$msg</div>\n";
        $s .= "<![endif]-->";
        echo "\n$s\n";
    }
    // 清空缓存 
    static function clearCache(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // 让它在过去就“失效”
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // 永远是改动过的
        header("Cache-Control: no-store, no-cache , must-revalidate"); // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache"); // HTTP/1.0
        header("Cache-control: max-age=0"); // IE6
    }
    
    // PageEnd()
    static function end($msg='',$end=''){
        global $_cbase;
        if(empty($_cbase['run']['headed'])){
            self::page('');
        }
        if($msg) echo "<h1>$msg</h1>\n";
        if($end) echo "$end\n";
        echo "</body></html>\n";
        die();
    }
    
    // 发送HTTP状态
    static function httpStatus($code) {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
            header('Status:'.$code.' '.$_status[$code]); // 确保FastCGI模式下正常
        }
    }

}