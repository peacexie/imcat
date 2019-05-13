<?php
namespace imcat\comm;

use imcat\basJscss;
use imcat\comCookie;
use imcat\usrPerm;

/*
公共模板扩展函数
*/ 
class texBase{
    
    //protected $xxx = array();
    
    static function init($obj){
        $ocar_items = comCookie::oget('ocar_items'); 
        if(strlen($ocar_items)==0){
            $db = db();
            $unqid = usrPerm::getUniqueid();
            $row = $db->table('coms_cocar')->where("ordid='$unqid'")->count(); 
            $row || $row = 0;
            comCookie::oset('ocar_items',$row);
        }
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext']; 
        $base || $base = 'jstag,menu,caritems,fanyi';
        $js = "";
        $js .= "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        strstr($base,'caritems') && $js .= "js_caritems();\n";
        strstr($base,'fanyi') && $js .= "js_i18nbar();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode("\n$js")."\n";
    }
    

}
