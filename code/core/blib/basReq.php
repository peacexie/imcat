<?php

// Request,表单,Url参数处理类
class basReq{    

    /* *****************************************************************************
      *** system系统常用函数
    - get,xxx前缀
    - by Peace(XieYS) 2012-02-18
    ***************************************************************************** */

    // [request_order], php5.3="GP", php5.2="CGP"
    static function gp($key,$def=''){ 
        if(isset($_GET[$key])){
            $val = $_GET[$key];
            /*if(is_array($val) && isset($_POST[$key]){
                $val = $val + $_POST[$key];
            }*/ // (如果有这个情况,是否也不合理???)
        }elseif(isset($_POST[$key])){
            $val = $_POST[$key];    
        }else{
            $val = $def;
        }
        return $val;
    }

    // Request Vars
    // Demo : extract(basReq::sysVars());
    static function sysVars(){ //in($_GET['fm'],'Title'); 
        $sy_sids = glbConfig::read('sysids','sy');
        $re = array();
        foreach($sy_sids['GET'] as $key){
            $re[$key] = self::gp($key,array());
        }
        foreach(array('Title','Key','N') as $k0){
            $items = $sy_sids[$k0];
            foreach($items as $k){
                $def = $k0=='N' ? 0 : ($k0=='Key' ? 24 : 255);
                $val = self::val($k, '', $k0, $def);
                $re[$k] = $val;
            }
        } 
        return $re;
    }
    
    static function val($key,$def='',$type='Title',$len=255){ 
        $val = self::gp($key);
        return is_array($val) ? $val : self::fmt($val,$def,$type,$len);
    }
    static function arr($fix,$type='Title',$len=255){ 
        $val = self::gp($fix,array());
        if($type && !empty($val)){
            foreach($val as $k=>$v)    {
                $val[$k] = is_array($v) ? $v : self::fmt($v,'',$type,$len);
            }
        }
        return $val;
    }
    static function ark($fix,$key,$type='Title',$len=255){ 
        $tmp = self::gp($fix,array());
        $val = isset($tmp[$key]) ? $tmp[$key] : '';
        return is_array($val) ? $val : self::fmt($val,'',$type,$len);
    }

    // type=D,N,Key,Title,Html
    static function fmt($data,$def='',$type='Title',$len=255){ 
        if($type=='N'){
           if(is_numeric($data)) return $data; 
           else return $def;  
        }elseif($type=='D'){
           if(strtotime($data)) return $data; 
           else return $def; 
        }
        switch ($type){ 
        case "Key" : 
        case "Title" : 
        case "Safe4" : 
           $Tmp = basStr::cutCount($data,$len);
           $Tmp = $type=='Key' ? basStr::filKey($Tmp,'_.-@') : basStr::filTitle($Tmp);
           $Tmp = strlen($Tmp)==0 ? $def : $Tmp;
           return $Tmp; break;
        default:  // Html
           $Tmp = basStr::filHtml($data); 
           $Tmp = strlen($Tmp)==0 ? $def : $Tmp;
           //$Tmp = self::in($Tmp);
           return $Tmp; break;  //处理 '"\
        }  
    }
    
    // *** fmtNum
    static function fmtNum($num,$dec=2,$kdot=''){ 
        $num = number_format($num,$dec);
        if(empty($kdot)) $num = str_replace(',','',$num);
        return $num;
    }
    
    // *** 获取Checkbox安全数据
    static function getCBox($key,$re='s'){ 
        $a = self::arr($key);
        if($re=='s'){ // 返回字符串:array -> string
            $s = '';
            foreach($a as $v) $s .= (empty($s) ? '' : ',').$v.',';    
            return $s;
        }else{ // return array
            return $a;    
        }
    }
    
    static function in($data,$type=''){
        if(is_string($data)){
            $data = $type ? self::fmt($data,'',$type) : addslashes($data);//防止sql注入
        }else if(is_array($data)){ //如果是数组采用递归过滤
            foreach($data as $key=>$value){
                 $data[$key]=self::in($value,$type);
            }
        }
        return $data;
    }
    static function out($data){
        if(is_string($data)){
            $data = stripslashes($data);
        }else if(is_array($data)){ //如果是数组采用递归过滤
            foreach($data as $key=>$value){
                $data[$key]=self::out($value);
            }
        }
        return $data;  
    }
    
    //获取 url并替换参数
    //re:第几个参数,-2:array;-1:full; 
    static function getUri($re=-1,$ura='',$skip=''){
        $ura = $ura ? explode('?',$ura) : array($_SERVER['SCRIPT_NAME'],$_SERVER['QUERY_STRING']);
        $ura[1] && $ura[1] = '?'.$ura[1];
        $skip && $ura[1] = preg_replace("/[\?|\&]($skip)=[^\f\n\r\t\v\&\#]{0,80}/i",'',$ura[1]);
        //《"&<>》HTML《:/?=&#%》URL《\/*?"<>|》FILE
        if($re==-2) return $ura;
        elseif($re==-1) return implode($ura);
        elseif(isset($ura[$re])) return $ura[$re];
        else return '';
    }
    static function getURep($url,$key,$val=''){
        $url || $url = $_SERVER["REQUEST_URI"];
        $para = empty($val) ? '' : "$key=$val";
        if(strpos($url,"$key=")){
            $url = preg_replace("/$key=([^\f\n\r\t\v\&\#]{0,80})/i",$para,$url);    
        }else{
            $url = strstr($url,'?') ? $url : "$url?";
            $para && $url .= "&$para";
            $url = str_replace(array("?&","&&"),array("?","&"),$url);    
        }
        return $url;
    }

}

