<?php
namespace imcat;

/*
    get($mod, $whr='', $lmt='10', $ord='', $ops=[]);
    $mod: news, news.join
    $whr: `show`='all', `show`='0', 
    $lmt: 1, 10, 3,10, 10.page, count
    $ord: atime-1, atime
    $ops: xxx
*/  

class glbData{

    static $cfg = []; 
    static $mod, $whr, $lmt, $ord;
    static $type, $sql; // , $res=[]

    // 
    static function get($mod, $whr='', $lmt='10', $ord='', $ops=[]){ 
        self::imod($mod);
        self::iwhr($whr);
        self::ilmt($lmt);
        self::iord($ord);
        return self::data();
    }
    static function data(){
        $db = glbDBObj::dbObj();
        $sfrom = "* FROM ".$db->pre.self::$cfg['tab'].$db->ext;
        $where = empty(self::$whr) ? '' : self::$whr;
        // data
        if(self::$type=='count'){
            $res = $db->table(self::$cfg['tab'])->where($where)->count();
            self::$sql = $db->getSql();
            return $res;
        }elseif(self::$type=='page'){
            $pres = self::dpage($sfrom, $where);
            self::$sql = $pres[1];
            $res = $pres[0]; 
        }else{
            $where && $where = "WHERE ".self::$whr;
            $limit = self::$type=='list' ? self::$lmt : '1';
            self::$sql = "SELECT $sfrom $where ORDER BY ".self::$ord." LIMIT $limit";
            $res = $db->query(self::$sql); 
        } //dump(self::$sql);
        // join
        $fpk = self::$cfg['fpk'];
        if(!empty(self::$cfg['join']) && in_array($fpk, ['did','kid'])){
            dopFunc::joinDext($res, self::$mod, $fpk);
        }
        if(self::$type=='1' && $res) $res = $res[0];
        return $res ?: [];
    }
    static function dpage($sfrom, $where){
        global $_cbase; 
        $ord = str_replace([' ASC','DESC'], '', self::$ord);
        $pg = new comPager($sfrom, $where, self::$lmt, $ord);
        $pg->set('odesc', strpos(self::$ord,' ASC')?0:1); // ? 1,0
        $pg->set('opkey', 0);
        $res = $pg->exe(); $sql = $pg->sql; 
        $idfirst = ''; $idend = '';
        if($res){
            $i = current($res); $idfirst = current($i); 
            $i = end($res); $idend = current($i); 
        }
        $scname = $_SERVER["REQUEST_URI"]; //REQUEST_URI,SCRIPT_NAME
        $burl = basReq::getUri(-1,'','page|prec|ptype|pkey');
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show($idfirst,$idend,'',$burl)."</div>";
        $_cbase['page']['prec'] = $pg->prec;
        return [$res, $sql[0]];
    }

    # ================================== 

    // ord: atime-1, atime
    static function iord($ord){
        $ord || $ord = self::$cfg['ord'].'-1';
        if(strpos($ord,'-')){
            $tmp = explode('-',$ord);
            $ord = "$tmp[0] ".(empty($tmp[1]) ? 'ASC' : 'DESC');
        }else{
            $ord = "$ord DESC";
        }
        self::$ord = $ord;
    }
    // lmt: 1, 10, 3,10, 10.page, count
    static function ilmt($lmt){
        if($lmt=='count'){
            self::$lmt = '';
            self::$type = 'count';
        }elseif(strpos($lmt,'.')){
            self::$lmt = intval($lmt);
            self::$type = 'page';
        }else{
            self::$lmt = $lmt;
            self::$type = self::$lmt!='1' ? 'list' : '1';
        }
    }
    // whr: `show`='all'
    static function iwhr($whr){
        $whr = $whr ?: '';
        $_groups = glbConfig::read('groups'); 
        $pid = self::$cfg['pid'];
        if(in_array($pid,array('docs','users','coms','advs'))){
            if(strstr($whr,"`show`='all'")){
                $whr = str_replace(["`show`='all'"," AND `show`='all'"],'',$whr);
            }elseif(!strstr($whr,'`show`=')){
                $whr .= " AND (`show`='1')";
            }
        }
        if(substr($whr,0,5)==' AND ') $whr = substr($whr,5);
        self::$whr = $whr;
        return $whr;
    }
    // mod: news, news.join
    static function imod($mod){
        $_groups = glbConfig::read('groups');
        self::$cfg['join'] = strpos($mod,'.');
        $tmp = explode('.', $mod);
        self::$mod = $mod = $tmp[0];
        if(empty($_groups[$mod])){ glbError::show("{$mod} NOT Found!",0); }
        self::$cfg['pid'] = $pid = $_groups[$mod]['pid'];
        // infos
        if($pid=='docs'){
            $tab = 'docs_'.$mod;
            $ord = $fpk = 'did';
        }elseif($pid=='users'){
            $tab = 'users_'.$mod;
            $ord = 'atime'; $fpk = 'uid';
        }elseif($pid=='advs'){
            $tab = 'advs_'.$mod;
            $ord = 'atime'; $fpk = 'aid';
        }elseif($pid=='coms'){    
            $tab = 'coms_'.$mod;
            $ord = $fpk = 'cid';
        }elseif($pid=='types'){    
            $tab = empty($_groups[$mod]['etab']) ? 'types_common' : 'types_'.$mod;
            $ord = $fpk = 'kid'; //不使用 
        }
        foreach(['tab','ord','fpk'] as $k0){
            self::$cfg[$k0] = $$k0;
        }
        return self::$cfg;
    }

}
