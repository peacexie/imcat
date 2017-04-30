<?php
/*
公共模板扩展函数
*/ 
class tex_base{
    
    //protected $xxx = array();
    
    static function base1($show,$a=''){ 
        echo "<br>base1::";
    }
    
    static function init($obj){
        $user = user();
        return $user;    
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

}
