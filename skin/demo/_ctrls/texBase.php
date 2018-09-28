<?php
namespace imcat\demo;

use imcat\comCookie;
use imcat\comFiles;
use imcat\basJscss;
use imcat\usrPerm;
use imcat\vopTpls;

/*
公共模板扩展函数
*/ 
class texBase{
    
    //protected $xxx = array();
    
    static function base1($show,$a=''){ 
        echo "<br>base1::";
    }
    
    static function init($obj){
        //    
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = 'jstag';
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode("\n$js")."\n";
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

}
