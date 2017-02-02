<?php

// basJscss类
class basJscss{    

    static function imFiles($exfiles,$lang=''){
        $arr = explode(';',$exfiles); 
        foreach ($arr as $val) { 
            $itm = explode(':',$val);
            if(!empty($itm[1]) || in_array(substr($itm[0],0,6),array('/plus/','/skin/','/_pub/','/tools'))){
                $ipath = self::imPath($itm[0],(empty($itm[1])?'':$itm[1]),'dir');
            }else{
                $ipath = DIR_SKIN.$val;
            } 
            if(file_exists($ipath)) include($ipath); 
            if($lang){
                $flang = str_replace(".js","-{$lang}.js",$ipath); 
                if(file_exists($flang)) include($flang); 
            }    
        } 
    }


    static function imPath($path,$base='',$pflg='path'){
        if(strpos($path,'://')||strpos($path,'../')){
            $base = '';
        }elseif(in_array(substr($path,0,6),array('/plus/','/tools'))){
            $base = $pflg=='path' ? PATH_ROOT : DIR_ROOT;
        }elseif(in_array(substr($path,0,5),array('/skin','/_pub'))){
            $base = $pflg=='path' ? PATH_SKIN : DIR_SKIN;
            if(strstr($path,'/_pub/')) $path = "/skin$path";
            if(strstr($path,'/skin/')){
                $path = str_replace('/skin/','/',$path);
            }
        }elseif($pcfg = comStore::cfgDirPath($base,$pflg)){
            $base = $pcfg;    
        }else{
            $base = $base=='null' ? '' : (empty($base) ? ($pflg=='path' ? PATH_ROOT : DIR_ROOT) : $base);    
        }
        return $base.$path;
    }

    // imp css/js
    static function imp($path,$base='',$mod='auto'){
        global $_cbase; 
        if(!strpos($_cbase['run']['jsimp'],$path)){
            $_cbase['run']['jsimp'] .= "$path,";
        }else{
            return;    
        }
        if(strpos($path,'/comjs.php')&&$mod=='auto') $mod = 'js';
        $path = self::imPath($path,$base); //"$base$path";
        if(empty($mod) || $mod=='auto') $mod = strpos($path,'.css') ? 'css' : 'js'; 
        if(empty($_cbase['tpl']['tpc_on'])){
            $_r = '_r='.time();
            $path .= strpos($path,'?') ? "&$_r" : "?$_r";
        } //echo "\n$mod:$path";
        if($mod=='js') return self::jscode('',$path)."\n";
        else return "<link href='$path' type='text/css' rel='stylesheet'/>\n";
    }
    
    /*《"&<>》HTML
    《:/?=&#%》URL 
    《\/*?"<>|》FILE
    《'$[]{}》SQL,PHP,下标,变量
    《!()+,-.;@^_`~》安全13个 
    《*+-./@_》7个js-escape安全escape*/
    /* *****************************************************************************
      *** js字符处理
    - js前缀
    - by Peace(XieYS) 2012-02-24
    ***************************************************************************** */
    
    // keyid, subject('"<>\n\r), js
    static function Alert($xMsg,$xAct='prClose',$xAddr='',$head=0){
      global $_cbase;
      if($head && empty($_cbase['run']['headed'])) glbHtml::page();
      if(empty($xAddr)) $xAddr = @$_SERVER["HTTP_REFERER"];
      $s = "\n<script language='javascript'>\n";
      $s .= "alert('$xMsg');\n";
      switch ($xAct) { 
      case "Back" : 
        $s .= "history.go($xAddr);\n";
        break; 
      case "Close" : 
        $s .= "window.close();\n";
        break; 
      case "prClose" : 
        if(@$_cbase['sys_open']==='4'){ 
            $s .= "parent.location.reload();\n";
        }else if(@$_cbase['sys_open']==='1'){ 
            $s .= "window.opener.location.reload();\n";
            $s .= "window.close();\n";
        }else{ 
            $s .= "window.close();\n";
        }
        break; 
      case "Open" : 
        $s .= "window.open('$xAddr');\n";
        break; 
      case "Redir" : 
        $s .= "location.href='$xAddr';\n";
        break;
      default: 
        break; 
      }
      $s .= "</script>\n";
      return $s;
    }
    // $enchf=1,编码html标记：尖括号，
    static function jsShow($xStr, $enchf=1){
       $Tmp = $xStr;
       $enchf && $Tmp = str_replace(array('<','>'),array('&lt;','&gt;'),$Tmp);
       $Tmp = str_replace(array("\\","'",'"'),array("\\\\","\\'",'\\"'),$Tmp); 
       $Tmp = str_replace(array("\r\n","\r","\n"),array("\\r\\n","\\r","\\n"),$Tmp);
       return $Tmp;
    }
    static function jsKey($xStr) {
       $xStr = str_replace(array('[',']',' '),array('_','_',''),$xStr);
       $xStr = str_replace(array('/','-','.'),array('_','_','_'),$xStr);
       return $xStr;    
    }
    // document.write
    static function write($xStr){ // ,array(),array()
        return "document.write('".self::jsShow($xStr, 0)."');";
    }
    static function jscode($code,$url=''){ 
        if($url){
            return "<script src='$url'></script>"; 
        }else{
            return "<script>\n$code</script>";
        }
    }
}