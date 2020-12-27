<?php
namespace imcat\base;

use imcat\basJscss;
use imcat\basStr;
use imcat\comCookie;
use imcat\comFiles;
use imcat\comStore;
use imcat\usrPerm;


/*
公共模板扩展函数
*/ 
class texComm{
    
    //protected $xxx = array();
    
    static function init($obj){
        $ocar_items = comCookie::oget('ocar_items'); 
        if(strlen($ocar_items)==0){
            $db = db();
            $unqid = usrPerm::getUniqueid();
            $row = smod('cocar') ? $db->table('coms_cocar')->where("ordid='$unqid'")->count() : 0; 
            $row || $row = 0;
            comCookie::oset('ocar_items',$row);
        }
    }
    
    static function pend(){
        //
    }

    # multi-news-start
    static function vdoUrl($uvdo){ 
      if(empty($uvdo)) return;
      $uvdo = comStore::revSaveDir($uvdo);
      return $uvdo;
    }
    static function downParas($ufile){ //dump($ufile);
        $res = [];
        if(empty($ufile)) return $res;
        $ticon = comFiles::getTIcon($ufile);
        $res['type'] = $ticon['type'];
        $res['icon'] = PATH_STATIC."/icons/file18/{$ticon['icon']}.gif";
        $res['ufpath'] = comStore::revSaveDir($ufile);
        $ufdir = comStore::revSaveDir($ufile,'dir'); 
        $ufdir = str_replace([PATH_URES,PATH_STATIC], [DIR_URES,DIR_STATIC], $ufdir);
        $ufsize = file_exists($ufdir) ? filesize($ufdir) : 0; 
        $res['ufsize'] = $ufsize ? basStr::showNumber($ufsize,'Byte') : '';
        $res['vpath'] = basename($ufile); 
        return $res;
    }
    # multi-news-end

}
