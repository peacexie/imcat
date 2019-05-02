<?php
namespace imcat;

// Element基本html元素类
class basElm{    

    // option,"upd|更新;del|删除;show|启用", 符号;\n表示一行, 符号|=分开键值
    //        "upd,更新;del|删除\nshow=启用"
    static function setOption($cfgs,$val='',$title='-(def)-',$fpid=0){
        #if(empty($cfgs)) return '';
        $title = $title=='-(def)-' ? basLang::show('core.opt_first') : $title;
        if(is_string($cfgs)){
            if(!strpos($cfgs,'(')){ // 排除:class::func(pa,p2,..)
                $cfgs = str_replace(array(';','|',','),array("\n",'=','='),$cfgs);
            }
            $cfgs = self::text2arr($cfgs);
        }
        if($title) $cfgs = array(''=>$title) + $cfgs; 
        $str = $pid = '';
        foreach($cfgs as $k=>$v){ 
            $v = is_string($v) ? $v : $v['title']; // isset($v['title']) 某些环境下出错...
            if(strstr($k,'^group^')){
                $str .= "\n<optgroup label='$v'></optgroup>";
                $pid = str_replace('^group^','',$k);
            }else{
                $def = "$val"==="$k" ? 'selected' : ''; 
                $pids = $fpid ? " pid='$pid'" : '';
                $str .= "\n<option value='$k' $def$pids>$v</option>";    
            }
        }
        return $str;
    }
    // checkbox,
    static function setCBox($k0,$cfgs,$val='',$brn=0){
        $cfgs = self::text2arr($cfgs);
        $varr = empty($val) ? array() : explode(',',$val);
        $str = "\n<input id='fm[$k0][]' name='fm[$k0][]' type='hidden' value='' />"; 
        $i = 0; $j = 0;
        foreach($cfgs as $k=>$v){ $i++; $j++;
            $v = is_string($v) ? $v : $v['title'];
            $def = in_array($k,$varr) ? " checked='checked' " : '';
            if($brn && $j>$brn) { $str .= "<br>"; $j=1; }
            $str .= "\n<label><input type='checkbox' class='rdcb' name='fm[$k0][]' id='fm_{$k0}_$i' value='$k' $def>$v</label>";
        }
        return $str;
    } // array_shift(), array_filter()
    // radio,
    static function setRadio($k0,$cfgs,$val='',$brn=0){
        $cfgs = self::text2arr($cfgs);
        $str = ""; $i = 0; $j = 0;
        foreach($cfgs as $k=>$v){ $i++; $j++;
            $v = is_string($v) ? $v : $v['title'];
            $def = "$val"==="$k" ? " checked='checked' " : '';
            if($brn && $j>$brn) { $str .= "<br>"; $j=1; }
            $str .= "\n<label><input type='radio' class='rdcb' name='fm[$k0]' id='fm_{$k0}_$i' value='$k' $def>$v</label> ";
        }
        return $str;
    }
    
    // arr2text: full,外观图;side,侧面图;inn,内部图;att,附件图;
    static function arr2text($arr,$row=';',$col=','){ 
        if(is_string($arr)){
            $arr = str_replace(array(';',','),array("\n",'='),$arr);
            $arr = self::text2arr($arr);
        }
        $s = '';
        foreach($arr as $k=>$v){
            $s .= "$k$col$v$row";
        }
        return $s;
    }
    // form: text2arr
    static function text2arr($text){
        $_groups = glbConfig::read('groups');
        $text = is_array($text) ? $text : trim($text);
        if(is_array($text)){
            if(!empty($text['fmextra']) && !empty($text['fmexstr']) && $text['fmextra']=='winpop'){
                return self::text2arr($text['fmexstr']);
            }elseif(isset($text['title']) && !empty($text['cfgs'])){
                return self::text2arr($text['cfgs']);
            }
            return $text;
        }elseif(empty($text)){ 
            return array();
        }elseif(isset($_groups[$text])){ 
            $rei = glbConfig::read($text); 
            $rei = $rei['i'];
            foreach($rei as $k=>$v){ 
                $re[$k] = $v['title'];
            }
        }elseif(preg_match("/^bext_paras\.\w+$/",$text)){ //bext_paras.logmode_cn
            $re = glbDBExt::getExtp(substr($text,11));
        }elseif(preg_match("/^\w+\.\w+$/",$text)){ //corder.ordstat
            $t = explode('.',$text);
            $mcfg = glbConfig::read($t[0]); 
            if(!empty($mcfg['i'][$t[1]])){
                foreach($mcfg['i'] as $k=>$v){ if($v['pid']==$t[1]){
                    $re[$k] = $v['title'];
                } }
            }else{
                $re = self::text2arr($mcfg['f'][$t[1]]['cfgs']);
            } //dump($mcfg);
        }elseif(preg_match("/^(\w+)::(\w+)\(([\w\ \,]{0,24})\)$/",$text)){
            $text = str_replace(array("\r","\n",' '),'',$text);
            preg_match("/^(\w+)::(\w+)\(([\w\,]{0,24})\)$/",$text,$ma);
            $cfile = $ma[1]; $method = $ma[2];
            $pa = explode(',',$ma[3].',,,,');
            foreach ($pa as $pk=>$pv) {
                $pa[$pk] = strlen($pa[$pk])>0 ? $pa[$pk] : null;
            } // \imcat\class::func(pa,p2,..)
            $re = basClass::obj($cfile)->$method($pa[0],$pa[1],$pa[2],$pa[3],$pa[4],$pa[5]);
        }else{
            $text = str_replace(array(' ',"\n","\r"),array('','&','&'),$text);
            parse_str($text, $re);
        } 
        return $re;
    }
    // re: 0:直接返回, kv:key=val, text:字符串
    static function line2arr($text,$re=0,$extf=''){ 
        if(!empty($extf)){ $text = str_replace($extf,"\n",$text); }
        $text = str_replace(array("\r\n","\r"),"\n",$text);
        $text = str_replace(array("\n\n\n\n","\n\n\n","\n\n"),"\n",$text);
        $text = explode("\n",$text);
        $text = array_filter($text);
        if(!$re) return $text; // 直接返回数组
        if($re=='text') return implode("\n", $text); // 返回文本
        // kv:key=val //  返回键值对数组
        $res = array(); 
        foreach ($text as $row) {
            $row = trim($row);
            $pos = strpos($row,'=');
            if($pos>0){
                $key = trim(substr($row,0,$pos));
                $res[$key] = trim(substr($row,$pos+1));
            }else{
                $key = preg_replace('/\s/', '', $row);
                $res[$key] = $row;
            }
        } // print_r($res);
        return $res;
    }

    // 格式化标签
    static function fmtFlag($flag){  
        if(is_string($flag) && strpos($flag,'(*)')){ $af = explode('(*)',$flag); }
        else{ $af = is_array($flag) ? $flag : array("<$flag>","</$flag>"); }
        return $af;
    } 
    // 获取xml/html标签 的一个值 或 一个innerHTML
    // 标签法 ($data,'<div class="content">(*)</div>') 
    static function getVal($xStr,$flag){  
        return self::getPos($xStr,$flag);
    }  
    // 获取xml/html标签 的一个值 或 一个innerHTML
    // 定点法 ($data,'<div class="content">(*)id="link"')
    static function getPos($xStr,$flag){  
        $af = self::fmtFlag($flag); 
        $p1 = strpos($xStr, $af[0]); 
        $xStr = is_int($p1) ? substr($xStr,$p1+strlen($af[0])) : '';
        $p2 = strpos($xStr, $af[1]); 
        $xStr = is_int($p2) ? substr($xStr, 0, $p2) : '';
        return $xStr;
    }
    // 获取xml/html标签 的一组值 或 一组innerHTML 加参数$no获取单个下标
    // 标签法 ($data,'<li class="cls22">(*)</li>') 或 ($data,'<li class(*)</li>')
    static function getArr($xStr,$flag,$no=-1){  
        $af = self::fmtFlag($flag); 
        $re = self::getTags($xStr,$af[0],$af[1]);
        if($no==-1) return $re;
        else return isset($re[$no]) ? $re[$no] : '';
    }
    // 获取xml/html标签 的一组值 或 一组innerHTML 加参数$no获取单个下标
    // 正则法 ($data,'<li class="cls1">(*)</li>') //  [\\d]{1,4}  
    static function getPreg($xStr,$flg1,$reg="[^\n|\r]{1,1200}",$no=-1){  
        $flag = preg_quote($flg1); // =\(\*\)
        $flag = str_replace("/","\\/",$flag);
        $flag = str_replace('\(\*\)',"($reg)",$flag); 
        preg_match_all("/$flag/i",$xStr,$m);
        if($no==-2){
            return $m;
        }elseif($no==-1){
            return $m[1];
        }else{ 
            return isset($m[1][$no]) ? $m[1][$no] : '';
        }
    }
    // 获取xml/html标签 的一组属性 加参数$no获取单个下标
    // ($data,'witdh','val',1) 或 ($data,'href','url')
    static function getAttr($str,$flag,$reg='key',$no=-1){ 
        if(strlen($reg)<=3){
            $a = array(
                'key' => '[\w]{2,24}',
                'url' => '[^"|\'|>| ]{1,120}', //"\S*", 
                'val' => '[^>]{1,255}',
            );
            $reg = $a[$reg];
        } 
        preg_match_all("/\b$flag=[\"|']*($reg)[\"|']*/i",$str,$attr); 
        $ra = empty($attr[1]) ? array() : $attr[1];
        $re = ($no==-1 || strlen($no)==0) ? $ra : (isset($ra[$no]) ? $ra[$no] : ''); 
        return $re;
    }

    static function getLinks($html,$re=0){
        preg_match_all("/<a(.*?)href=\"(.*?)\"(.*?)>(.*?)<\/a>/i",$html,$urls);
        $re = array();
        foreach($urls[2] as $key=>$val) {
            $re[$key] = array($val,$urls[4][$key]);
        }
        return $re;
    }

    // 取一组tag<ul>资料
    // ($str,'a'), ($str,'li'), ($str,'<div class="logo">','</div>')
    static function getTags($str,$tg1='ul',$tg2=''){
        if(empty($tg2)){
            $tg2 = "</$tg1>"; $tg1 = "<$tg1"; 
        }
        $uls = explode($tg1,$str); 
        $res = array();
        foreach ($uls as $i=>$row) {
            if($i==0) continue;
            $tmp = explode($tg2,$row);
            if(isset($tmp[1])){
                $res[] = $tg1.$tmp[0].$tg2;
            }
        }
        return $res;
    }
    // 去tag<img>
    static function moveTag($str,$tag='img',$close=0){
        $str = preg_replace("/<{$tag}[^>]*>/is", '', $str);
        if(!$close) return $str;
        $str = str_replace("</$tag>", '', $str);
        return $str;
    }
    // 去<tag属性>
    static function moveAttr($html){
        $html = preg_replace('/<(\w+)[^>]*>/i', '<\1>', $html);
        return $html;
    }
    
}

