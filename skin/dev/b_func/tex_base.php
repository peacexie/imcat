<?php
/*
公共模板扩展函数
*/ 
class tex_base{
    
    //protected $xxx = array();

    static function init($obj){
        global $_cbase;
        if(!empty($_cbase['login_dev'])){
            $user = user(); $msg = '';
            if(empty($user)){
                $msg = '需要'.($_cbase['login_dev']=='adminer' ? 'adminer' : '').'登录查看！';
            }elseif($_cbase['login_dev']=='adminer' && $user->userType!='adminer'){
                $msg = '需要adminer登录！';
            }
            if($msg){
                glbHtml::page('需要登录查看');
                glbHtml::page('body');
                echo "\n<p>$msg</p>\n";
                glbHtml::page('end');
            }
        }else{
            $user = NULL;
        }
        return $user;
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = 'jstag,menu,aheight';
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        strstr($base,'aheight') && $js .= "js_aheight();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode($js)."\n";
    }
    
    static function coder($tpl=''){ 
        $path = vopTpls::path('tpl',1); 
        $file = "$path/$tpl".(strpos($tpl,'.php') ? '' : cfg('tpl.tpl_ext'));
        if(!file_exists($file)) return '';
        $code = comFiles::get($file);
        $code = highlight_string($code,1);
        return $code;
    }
    
    static function docer($mkey=''){ 
        $path = vopTpls::path('tpl',1);
        $file = "$path/$mkey.txt"; 
        if(!file_exists($file)) return array();
        $re = array(); $key=''; 
        $text = comFiles::get($file); 
        $text = extMkdown::pdext($text);
        $arr = explode('<h1>',$text);
        foreach($arr as $block){ 
            if(empty($block)) continue;
            $b = explode('</h1>',$block); 
            $c = explode('#',$b[0]); 
            if(empty($c[0]) || empty($c[1]) || empty($b[1])) continue;
            $key = $c[0];
            $re[$key]['title'] = $c[1];
            $re[$key]['detail'] = self::filter($b[1]);
        } 
        return $re;
    }
    
    static function lister($key=''){ 
        $path = vopTpls::path('tpl',1)."/d_tester/"; 
        $re = comFiles::listDir($path);
        $re = $re['file']; $re2 = array();
        foreach($re as $k=>$v){
            if(empty($key) && substr($k,0,1)=='u'){
                $re2[] = str_replace(array('.php'),array(''),$k);
            }elseif(strstr($k,"u{$key}_")){
                $re2[] = str_replace(array('.php'),array(''),$k);
            }
        } 
        return $re2;
    }
    
    static function filter($str){ 
        $svr = cfg('server');
        $a1 = array(
            "{svrtxmao}","{svrtxcode}","{svrtxjia}",
            '{static}','{pathpro}',
        );
        $a2 = array(
            $svr['txmao'],$svr['txcode'],$svr['txjia'],
            PATH_STATIC,PATH_PROJ,
        ); 
        $str = str_replace($a1,$a2,$str);
        return $str;
    }
    
    static function texter($key='',$conv=0){ 
        $path = vopTpls::path('tpl',1);
        $file = "$path/$key"; 
        if(!file_exists($file)) return '';
        $data = comFiles::get($file);
        $conv && $data = comConvert::autoCSet($data,'gbk');
        return $data;
    }
    
    
}
