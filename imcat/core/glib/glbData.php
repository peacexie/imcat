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

    // whr: "catid='abc'", "did='2020-key'", "catid='abc' AND xxx"
    static function cdata($whr, $lmt=1, $ord='click-0'){
        $cats = read('cdata.i');
        if(isset($cats[$whr])){
            $where = "catid='$whr'";
        }elseif(preg_match("/[\w|\-]+/i", $whr)){
            $where = "did='$whr'";
        }else{
            $where = $whr;
        }
        $data = self::get('cdata.join', $where, $lmt, $ord);
        if(!$data) return [];
        $tarr = $lmt>1 ?$data : [$data] ;
        foreach($tarr as $rk=>$row){
            $vtype = $row['vtype'];
            $vext = $row['vext'];
            if($vtype=='kv'){
                $vdata = basElm::text2arr($vext);
            }elseif($vtype=='md'){
                $vdata = extMkdown::pdorg($vext);
            }elseif($vtype=='json'){
                $vdata = comParse::jsonDecode($vext);
            }elseif($vtype=='sec'){
                $vext = preg_replace_callback("/\s+\[\-\-\-\]+\s+/i", function($itms){
                    return "[---]"; // 去=号两边空白
                }, $vext);
                $vdata = explode('[---]', $vext);
            }else{ // text
                $vdata = '';
            }
            if(!empty($row['exfile'])){
                $tarr[$rk]['exfile'] = comStore::picsTab($row['exfile'],0); 
            }
            $tarr[$rk]['vdata'] = $vdata;
        }
        return $lmt>1 ? $tarr : $tarr[0];
    }

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
        $scname = basEnv::serval("REQUEST_URI"); //REQUEST_URI,SCRIPT_NAME
        $burl = basReq::getUri(-1,'','page|prec|ptype|pkey');
        $_cbase['page']['bar'] = "<div class='pg_bar'>".$pg->show($idfirst,$idend,'',$burl)."</div>";
        $_cbase['page']['prec'] = $pg->prec;
        $_cbase['page']['pcnt'] = intval($pg->pcnt);
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
                $whr = str_replace([" AND `show`='all'","`show`='all' AND ","`show`='all'"],'',$whr);
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

    // 得到一笔数据:docs','users','coms','advs','types
    static function getRow($mod, $key, $pid=''){
        if(!$pid){
            $_groups = glbConfig::read('groups');
            $pid = $_groups[$mod]['pid'];
        }
        $kid = $pid=='types' ? 'kid' : substr($pid,0,1).'id';
        $db = glbDBObj::dbObj();
        $tabid = glbDBExt::getTable($mod);
        $res = $db->table($tabid)->where("$kid='{$key}'")->find();
        if(empty($res)){
            return array();
        }
        if(in_array($pid,array('docs'))){
            $tabid = glbDBExt::getTable($mod,1);
            $dext = $db->table($tabid)->where("did='{$key}'")->find();
            $dext && $res += $dext; 
        }
        return $res;
    }

    static function fmtRow($row, $mod, $opts=array()){
        foreach($row as $k => $val){
            if($k=='catid'){
                $row["{$k}Name"] = vopCell::cOpt($val, $mod, ',');
            }
            if($k=='mpic'){ // cPic($val,$def='',$resize=0)
                $resize = isset($opts['mpic_resize']) ? $opts['mpic_resize'] : 0;
                $mpic = vopCell::cPic($val, '', $resize);
                $row["mpic"] = self::fmtUrl($mpic);
            }
            if(in_array($k,['atime','etime'])){
                $row["{$k}Str"] = vopCell::cTime($val);
            }
            if($k=='detail'){
                $row["detail"] = basStr::filHWap($row["detail"]);
            }
            if(!empty($opts[$k])){
                $cfg = $opts[$k];
                $mod = empty($cfg['mod']) ? $mod : $cfg['mod'];
                if($cfg['type']=='cOpt'){
                    $row["{$k}Name"] = vopCell::cOpt($val, $cfg['mod'], ',');
                }
                if($cfg['type']=='cTime'){
                    $fmt = empty($cfg['fmt']) ? 'auto' : $cfg['fmt'];
                    $row["{$k}Name"] = vopCell::cTime($val, $fmt);
                }
            }
        }
        return $row;
    }
    static function fmtList($list, $mod, $opts=array()){
        foreach($list as $k => $row){
            $list[$k] = self::fmtRow($row, $mod, $opts);
        }
        return $list;
    }
    static function fmtUrl($url){
        if(!$url) return '';
        global $_cbase;
        $rc = $_cbase['run'];
        return $rc['iss'].':'.$rc['rsite'].$url;
    }

}
