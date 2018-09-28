<?php
namespace imcat\mob;

use imcat\basJscss;

class texBase{
    
    //protected $xxx = array();
    
    static function init($obj){
        $user = user('Member'); 
        return $user;
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext'];
        $base || $base = 'jstag,menu'; 
        $js = "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        echo basJscss::jscode("\n$js")."\n";    
    }

}
