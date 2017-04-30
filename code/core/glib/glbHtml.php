<?php
// Html类
class glbHtml{    

    // 页面结构
    static function page($mod='',$ext='',$iex=''){
        global $_cbase; 
        $imarr = array('imp','imadm','imvop','imin','imjq','imnul');
        $mtarr = array('robots','viewport','keywords','description');
        if($mod=='body'){
            echo "</head><body$ext>\n";
        }elseif($mod=='end'){
            if(empty($_cbase['run']['headed'])){
                self::page('');
            }
            if(strlen($ext)>12) echo "$ext\n";
            echo "</body></html>\n";
        }elseif(in_array($mod,$mtarr)){
            if($mod=='robots' && empty($ext)) $ext = 'noindex, nofollow';
            if($mod=='viewport' && empty($ext)) $ext = 'width=device-width, initial-scale=1';
            echo "<meta name='$mod' content='$ext'>\n"; 
        }elseif($mod=='h1'){
            echo "<h1>$ext</h1>\n";
        }elseif($mod=='init'){
            echo "<meta charset='".$_cbase['sys']['cset']."'>\n";
            echo "<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>\n";
            self::page('viewport'); 
            // 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！
            if($ext) self::page('robots'); 
            if(empty($iex)) echo "<link rel='shortcut icon' href='".PATH_SKIN."/_pub/logo/favicon.ico' />\n";
        }elseif(in_array($mod,$imarr)){
            self::imsub($mod,$ext,$iex);
        }else{ //head
            $_cbase['run']['headed'] = 1;
            $mod || $mod = $_cbase['sys_name'];
            $mod = str_replace('(sys_name)',$_cbase['sys_name'],$mod);
            echo "<!DOCTYPE html><html><head>\n";
            self::page('init',$ext,$iex);
            echo "<title>$mod</title>\n"; 
        }
    }

    // _imsub
    static function imsub($mod='',$ext='',$iex=''){
        global $_cbase; 
        $tmfix = empty($_cbase['run']['tmfix']) ? '' : $_cbase['run']['tmfix'];
        $sdir = vopTpls::def(); //可能没有定义
        $exjs = "exjs=/$sdir/b_jscss/comm$tmfix.js".(empty($ext['js'])?'':';'.$ext['js']);
        $excss = "excss=/$sdir/b_jscss/comm$tmfix.css".(empty($ext['css'])?'':';'.$ext['css']);
        if($mod=='imp'){
            $ips = self::impub();
            $ips['js'] .= "&$exjs";
            $ips['css'] .= "&$excss";    
        }elseif($mod=='imadm'){
            $ips = self::impub(0,1); 
            $ips['js'] .= "&$exjs&lang=".$_cbase['sys']['lang'];
            $ips['css'] .= "&$excss";    
        }elseif($mod=='imvop'){
            $ips = self::impub();
            $ips['js'] .= "&$exjs&$iex";
            $ips['css'] .= "&$excss";
        }elseif($mod=='imin'){
            $ips = self::impub('imin',0);
            $ips['js'] .= "&$exjs";
            $ips['css'] .= "&$excss"; 
        }elseif($mod=='imjq'){
            $ips = self::impub('imjq',0);
            $ips['js'] .= "&$exjs";
            $ips['css'] .= "&$excss"; 
        }elseif($mod=='imnul'){
            $ips = self::impub('imnul',0);
            $ips['js'] .= "&$exjs&$iex";
            $ips['css'] .= "&$excss"; 
        }
        foreach (array('css','js') as $key) {
            echo basJscss::imp("/plus/ajax/comjs.php?".$ips[$key],'',$key);
        }
    }

    // _impub
    static function impub($light=0,$layer=0){
        if(empty($light)){
            echo basJscss::imp('/plus/ajax/comjs.php?act=autoJQ'); 
            echo basJscss::imp('/bootstrap/css/bootstrap.min.css','vendui','css');
            echo basJscss::imp('/bootstrap/js/bootstrap.min.js','vendui','js');
        }elseif($light=='imjq'){
            echo basJscss::imp('/plus/ajax/comjs.php?act=autoJQ'); 
        }elseif($light=='imin'){ // 需要自行添加如下ratchet,tepto文件
            echo basJscss::imp('/plus/ajax/comjs.php?act=autoJQ&light=1'); 
            echo basJscss::imp('/ratchet/css/ratchet.min.css','vendui','css');
            echo basJscss::imp('/ratchet/js/ratchet.min.js','vendui','js');
        }elseif($light=='imsg'){ // 
            echo basJscss::imp('/_pub/a_jscss/cinfo.css');
            echo basJscss::imp('/_pub/jslib/jsbase.js');
            return;
        } // else{ /*imnul*/ }
        if(!empty($layer)){
            echo basJscss::imp('/layer/layer.js','vendui');
        }
        return array('js'=>"act=sysInit",'css'=>"act=cssInit");
    }

    // header
    static function head($type='js',$cset=''){
        $cset = $cset ? $cset : cfg('sys.cset');
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
        $allow = read('domain.dmacc','sy'); 
        if(empty($domain)){
            @$aurl = parse_url($_SERVER["HTTP_REFERER"]);
            @$domain = $aurl['host'];
        }
        if(in_array($domain, $allow)){ 
            header("Access-Control-Allow-Origin:http://$domain"); // 指定允许其他域名访问
            header('Access-Control-Allow-Methods:POST'); // 响应类型  
            header('Access-Control-Allow-Headers:x-requested-with,content-type'); // 响应头设置
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
        $fmact = basReq::getURep($fmact,'recbk');
        echo "<form id='$fmid' name='$fmid' method='post' action='$fmact' target='$win'>\n";
        $recbk = req('recbk','');
        $recbk = $recbk==='ref' ? @$_SERVER["HTTP_REFERER"] : $recbk;
        echo "<input name='recbk' type='hidden' value='$recbk' />\n"; 
        echo "<table border='$tbbrd' class='$tbcss'>\n"; 
    }    
    // form+table:(end):结束
    static function fmt_end($data='',$tabend='</table>'){
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
        $headed = cfg('run.headed');
        if(empty($headed)){
            self::page('');
        }
        if($end) echo "$end\n";
        if($msg) echo "<h1>$msg</h1>\n";
        echo "</body></html>\n";
        die();
    }
    
    // 发送HTTP状态
    static function httpStatus($code) {
        static $_status = array(
            200 => 'OK', // Success 2xx
            301 => 'Moved Permanently', // Redirection 3xx
            302 => 'Moved Temporarily ',
            400 => 'Bad Request', // Client Error 4xx
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error', // Server Error 5xx
            503 => 'Service Unavailable',
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
            header('Status:'.$code.' '.$_status[$code]); // 确保FastCGI模式下正常
        }
    }

}