<?php
/*

*/ 
class tex_func{
    
    protected $vars = array(); //存放变量信息
    
    function x__construct() {
        //
    }
    
    static function detail_kid($pmod,$id,$ext=1){
        $whr1 = substr($pmod,0,1)."id='$id'";
        if($ext){
            $whr1 .= ' AND '.self::list_show($pmod,array());
        }
        return $whr1;
    }

    static function list_show($pmod,$mcfg){
        $stype = req('stype','','Key');
        $uname = req('uname','','Key');
        $uself = req('uself','','N');
        $whr1 = '';
        if($uname){
            $whr1 = "`auser`='$uname'";
            if($uself){
                $whr1 .= ""; // 1=1
            }else{
                $whr1 .= " AND `show`=1";
            }
        }else{
            $whr1 = "`show`=1";
        }
        return $whr1;
    }
    static function list_stype($pmod,$mcfg){
        $stype = req('stype','','Key');
        $whr1 = '';
        if($stype){
            if(in_array($pmod,array('docs','advs'))){ 
                $whr1 = basSql::whrTree($mcfg['i'],'catid',$stype);
            }elseif(in_array($pmod,array('users'))){
                $whr1 = " AND grade='$stype'";
            }
        }
        return $whr1;
    }
    static function list_pid($pmod,$mcfg){
        $pid = req('pid','','Key');
        $whr1 = '';
        if($pid && in_array($pid,array('coms'))){
            $whr1 = " AND pid='$pid'";
        }
        return $whr1;
    }
    static function list_sfkw($pmod,$mcfg){
        $sfid = req('sfid','title','Key');
        $sfop = req('sfop','eq','Key');
        $sfkw = req('sfkw');
        $whr1 = '';
        if($sfkw && isset($mcfg['f'][$sfid])){ 
            if($sfop=='ll') $whr1 = " AND `$sfid` LIKE '$sfkw%'";
            if($sfop=='lb') $whr1 = " AND `$sfid` LIKE '%$sfkw%'";    
            if($sfop=='lr') $whr1 = " AND `$sfid` LIKE '%$sfkw'";
            if($sfop=='eq') $whr1 = " AND `$sfid`='$sfkw'";
        } 
        return $whr1;
    }

    static function list_limit(){
        $page = req('page','1','N');
        $psize = req('psize','20','N');
        if($page<=0) $page = 1;
        if($psize<=1) $psize = 10;
        $offset = ($page-1)*$psize;
        $limit = "$offset,".$psize;
        return $limit;
    }

    static function list_excfg(&$whrarr,$ccfg){ 
        if(!empty($ccfg['skip'])){
            foreach ($ccfg['skip'] as $sk) {
                unset($whrarr[$sk]);
            }
            unset($ccfg['skip']);
        }
        if(!empty($ccfg)){
            foreach ($ccfg as $wk=>$wv) {
                $wval = req($wk,'','Key');
                if(!empty($wval)){
                    if(is_array($wv)){
                        $whr1 = '';
                        foreach ($wv as $wv1) {
                            $wvt = explode('[=]',$wv1);
                            if($wval==$wvt[0]){
                                $whrarr[$wk] = ' AND '.str_replace('{val}',$wval,$wvt[1]);
                                break;
                            }
                        }
                    }else{
                        $whrarr[$wk] = ' AND '.str_replace('{val}',$wval,$wv);
                    }
                }
            }
        }
    }

}









