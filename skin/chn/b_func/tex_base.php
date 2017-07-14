<?php
/*
公共模板扩展函数
*/ 
class tex_base{
    
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
        $base || $base = 'jstag,menu,cklogin,fanyi,aheight';
        $js = "var ocar_url = '".surl('ocar')."';\n";
        $js .= "var umc_url = '".surl('umc:0')."';\n";
        $js .= "setTimeout(\"jcronRun()\",3700);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        strstr($base,'cklogin') && $js .= "js_cklogin();\n";
        strstr($base,'fanyi') && $js .= "js_i18nbar();\n";
        strstr($base,'aheight') && $js .= "js_aheight();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode("\n$js")."\n";
    }
    

}
