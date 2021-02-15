<?php
namespace imcat\comm;

use imcat\basJscss;
use imcat\basStr;
use imcat\comCookie;
use imcat\comFiles;
use imcat\comStore;
use imcat\usrPerm;

use imcat\hi\uioCtrl;

/*
公共模板扩展函数
*/ 
class texBase{
    
    //protected $xxx = array();
    
    static function init($obj){ 
        if(!smod('cocar')){ return; }
        $ocar_items = comCookie::oget('ocar_items');
        if(strlen($ocar_items)==0){
            $db = db();
            $unqid = usrPerm::getUniqueid();
            $row = smod('cocar') ? $db->table('coms_cocar')->where("ordid='$unqid'")->count() : 0; 
            $row || $row = 0;
            comCookie::oset('ocar_items',$row);
        } 
        // user
        $uio = new uioCtrl($obj->ucfg, $obj->vars);
        $re = $uio->re; $vars = $uio->re['vars'];
        $ures = [
            'uio' => ['uflag'=>$vars['uflag'],'ckey'=>$vars['ckey'],'udebug'=>$vars['udebug'],],
            'uinfo' => $vars['uinfo'], 'uimod' => empty($vars['uimod']) ? [] : $vars['uimod'], 'unull' => $vars['null'],
        ]; //dump($res); dump($re);
        return $ures;
    }
    
    static function pend(){
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext']; 
        $base || $base = 'jstag,menu,caritems,fanyi';
        $js = "";
        $js .= "setTimeout(\"jcronRun()\",370);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
        strstr($base,'menu') && $js .= "jsactMenu();\n";
        strstr($base,'caritems') && $js .= "js_caritems();\n";
        strstr($base,'fanyi') && $js .= "js_i18nbar();\n";
        $ext && $js .= "$ext;\n";
        echo basJscss::jscode("\n$js")."\n";
    }

    static function next($mod='news', $id='', $next=1, $caid=''){
        //$kid = did, cid, uid ...
        $whr = $next ? "did<'$id'" : "did>'$id'";
        // $whr .= $caid ? " AND catid='$catid'" : "" ;
        $ord = $next ? "did-1" : "did";
        $row = \imcat\glbData::get($mod,$whr,1,$ord);
        return $row;
    }

}
