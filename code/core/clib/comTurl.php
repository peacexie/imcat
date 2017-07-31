<?php

// tiny-url
// if(!empty($qstr) && !is_numeric($qstr))
// http://t.im/
 
class comTurl{

    public  $xxx   = ''; //

    //function __destory(){  }
    function __construct(){ 
        //;
    }

    function run(){}

    function runLang(){}

    function runAdvs(){}

    function runJump(){}

    function runDef(){}

    function runTiny(){}

    // 
    static function set($url='',$n=0){
        $db = glbDBObj::dbObj();
        $m = $n<3 ? 2 : ($n>5 ? 5 : $n);
        $kn = basKeyid::kidRand('22',1).basKeyid::kidRand('30',$m);
        $rec = $db->table('token_turl')->where("kid='$kn%'")->find(); 
        if($rec){
            return self::set($url,++$n);
        }else{
            $db->table('token_turl')->data(array('kid'=>$kn,'url'=>$url,'type'=>'(def)'))->find(); 
            return $kn;
        }
    }
    
    // 
    static function get(){
        $db = glbDBObj::dbObj();
        //
    }
    
}
