<?php
// sysCookie
class comCookie{    

    // *** Cookie操作 user@peace+pass@123456
    
    // mset :
    static function mset($gkey,$glife,$key='',$value='',$max=5){
        if(is_array($key)){
            $garr = $key;
            $max = 99;
        }else{
            $gtxt = self::oget($gkey);
            $garr = basElm::text2arr($gtxt); 
            $garr[$key] = $value; 
            if($gkey=='vcodes') $max = 3; //100B,最多40个,all-4K内
            //if($gkey=='clicks') $max = 5; //050B,最多80个,all-4K内
        }
        $str = ''; $n = 0; $cnt = count($garr);
        foreach($garr as $k=>$v){
            $n++;
            if(($cnt-$n) > $max) continue; 
            $str .= "$k=$v\n";
        } 
        self::oset($gkey,$str,$glife);
    }
    // mget :
    static function mget($gkey,$key='(array)'){
        $gtxt = self::oget($gkey); 
        $garr = basElm::text2arr($gtxt); 
        if($key=='(array)') return $garr;
        return isset($garr[$key]) ? $garr[$key] : '';    
    }
    
    // set(k,'v',n),get(k),del(k,''),clear()
    // set/del
    static function oset($key='',$value='',$life=0,$pre='(def)'){
        global $_cbase; 
        $kbak = $key; $fset = 0;
        $ckpre = isset($_cbase['ck']['pre']) ? $_cbase['ck']['pre'] : 'sysCookie';
        $key = ($pre=='(def)'?$ckpre:$pre).$key; 
        $life && $life = time() + $life + $_cbase['sys']['tmzone']*3600; //,  + 72 * 3600
        $path = isset($_cbase['ck']['path']) ? $_cbase['ck']['path'] : '/'; 
        $domain = isset($_cbase['ck']['domain']) ? $_cbase['ck']['domain'] : '';
        $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
        //basDebug::bugLogs('test1',"($value),($life=".date('Y-m-d H:i:s',$life)."),$path,$domain,$secure");
        setcookie($key,$value,$life,$path,$domain,$secure); 
    }

    static function oget($key=''){
        global $_cbase; 
        $kbak = $key; $fset = 0;
        $ckpre = isset($_cbase['ck']['pre']) ? $_cbase['ck']['pre'] : 'sysCookie';
        $key = $ckpre.$key; 
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
    }
    
}

