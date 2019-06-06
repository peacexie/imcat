<?php
namespace imcat\dev;

use imcat\basEnv;
use imcat\basJscss;
use imcat\basStr;
use imcat\comConvert;
use imcat\comFiles;
use imcat\extMkdown;
use imcat\glbHtml;
use imcat\usrBase;
use imcat\vopTpls;

/*
公共模板扩展函数
*/ 
class texBase{

    static $data = array();
    static $dcfg = array('basic','main',); //'base','frame'

    static function init($obj){
        global $_cbase;
        if(!empty($_cbase['login_dev'])){
            $user = usrBase::userObj(); $msg = '';
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
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = basEnv::isMobile() ? 'jstag' : 'jstag,menu';
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode("\n$js")."\n";
    }
    
    static function uplog_furl(){ 
        include vopTpls::tinc("d_uplog/a_cfgs",0);
        $mkv = 'uplog';
        foreach ($cfgs as $key => $val) {
            if($key!='readme'){
                $mkv = "$mkv-$key";
                break;
            }
        }
        echo surl($mkv);
    }

    static function coder($tpl=''){ 
        $ext = strpos($tpl,'.') ? '' : '.htm';
        $file = vopTpls::tinc($tpl.$ext, 0);
        if(!file_exists($file)) return '';
        $code = comFiles::get($file);
        $code = highlight_string($code,1);
        return $code;
    }
    
    static function docer($mkey=''){ 
        $file = vopTpls::tinc("$mkey.txt",0); 
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
        $path = vopTpls::path('tpl',1)."/tester/"; 
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
    
    static function texter($key='', $conv=0){ 
        $file = vopTpls::tinc($key,0);
        if(!file_exists($file)) return '';
        $data = comFiles::get($file);
        if($conv){
            $data = comConvert::autoCSet($data,'gbk');
        }else{
            $flag = basStr::isConv($data);
            $flag && $data = comConvert::autoCSet($data,'gbk');  
        }
        return $data;
    }
    
    # ---------------------------------------------------------

    static function rndIP(){
        $s = rand(3,254);
        for($a=0;$a<3;$a++){
            $s .= ".".rand(10,240);    
        }
        return $s;    
    }

    static function getKeyTitle($mod,$key){
        if(empty(self::$data[$mod])){
            foreach (self::$dcfg as $mkey) {
                $data = comFiles::get(vopTpls::tinc("c_demo/{$mod}_{$mkey}.htm",0));
                if($data){
                    self::$data[$mod] = $data;
                    break;
                }
            }
        }
        $data = self::$data[$mod]; 
        //     'tplsuit' => '整套模版',
        preg_match_all("/['|\"]{1}{$key}['|\"]{1}\s*\=\>\s*['|\"]{1}([^\']+)['|\"]{1}\,/is", $data, $m);
        $re = empty($m[1][0]) ? "[$key]" : $m[1][0]; 
        return $re;
    }

}
