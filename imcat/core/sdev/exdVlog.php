<?php
namespace imcat;

// ...exdVlog
class exdVlog{    

    // vdata:一笔访问记录,一个标记
    static function vdata($vdir, $mkv){
        global $_cbase; 
        $vrow['aip']   = $_cbase['run']['userip'];
        $vrow['atime'] = $_SERVER["REQUEST_TIME"];
        $vrow['auser'] = $_cbase['run']['userag'];
        $vrow['vmod'] = $vdir; # mtype,detail
        if(strpos($mkv,'.')>0){
            $mka = explode('.', $mkv);
            $mkv = $mka[0].'.detail';
            $kid = $mka[1];
        }elseif(strpos($mkv,'-')>0){
            $mka = explode('-', $mkv);
            $mkv = $mka[0].'-mtype';
            $kid = $mka[1];
        }else{
            $kid = '';
        }
        $vrow['mkv'] = $mkv;
        $vrow['kid'] = $kid;
        $vrow['uri'] = basReq::val('uri');
        $vrow['ref'] = basReq::val('ref');
        return $vrow;
    }
    # vcheck
    static function vcheck($vrow){
        $soag = basReq::val('soag');
        $rec = '0';
        // 1. 过滤:搜索引擎及okhttp
        if($soag || basEnv::isRobot($vrow['auser'])){
            $rec = 'soua';
        }
        // 2. 过滤渲染ip: 111.206.198.*/111.206.222.* + Mozilla/5.0
        if(strstr($vrow['aip'],'111.206') && strlen($vrow['auser'])<24){
            $rec = 'fip';
        }
        // 3. 基本过滤(url不能随便修改)
        if(!$vrow['mkv'] || !$vrow['vmod'] || !$vrow['uri']){
            $rec = 'base';
        }
        // 4. 过滤:直接打开(不好判断?) --- 另直接访问，也来到这里；
        $sref = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
        if(empty($sref)){
            $rec = 'noref';
        }
        return $rec; 
    }

    # main
    static function main($vdir, $mkv, $chk=1){
        $vrow = self::vdata($vdir, $mkv);
        $vchk = $chk ? self::vcheck($vrow) : '0';
        # cache
        $khour = '/store/khour-'.date('H').'.vlog';
        $chour = extCache::cfGet($khour, 90, 'dtmp', 'str');
        # 1小时监测一次
        if(!$chour){
            $fno = self::don();
            extCache::cfSet($khour, date('m-d H:i:s'));
            basDebug::bugLogs($fno, $vrow, 'detmp', 'db');
        }
        # save-vlog/debug-loger
        if(empty($vchk)){
            self::vlog($vrow);
            return $vchk;
        }else{
            basDebug::bugLogs("main-$vchk", $vrow, 'detmp', 'db'); ### debug
            return "main-$vchk";
        }
    }

    # 独占模式-运行n个方法
    static function don(){
        $fno = '';
        $fp = DIR_VARS."/dtmp/store/flock_vlog_don.txt";
        $file = fopen($fp, "w+"); 
        if(flock($file, LOCK_EX|LOCK_NB)){
            $kday = '/store/kday-'.date('d').'.vlog'; 
            $dmins = date('H')*60 + date('i') + 1; // 今日的分钟数(有效期)
            $cday = extCache::cfGet($kday, $dmins, 'dtmp', 'str');
            if(!$cday){
                self::sumer(-1); // 当天首次-统计前1天
                extCache::cfSet($kday, date('m-d H:i:s'));
                $fno = 'dpre';
            }else{
                self::sumer(0); // 其余-统计当天
                $fno = 'dnow';
            }
            $dno = intval(date('d'));
            if($dno>10 && $dno<20){
                self::transi(); // 转存上1月数据
                $fno .= '-fun2';
            }
            fclose($file);
        }
        return $fno;
    }

    // 转存某一月数据(前1月)
    static function transi(){
        $db = db();
        $m = date('ym'); // 1910
        $tbfull = $db->table('vlog_list', 2);
        $tbnew = str_replace('vlog_list', "vlog_list_$m", $tbfull);
        $tabs = $db->tables();
        if(in_array($tbnew, $tabs)){ return; }
        $d0 = strtotime(date("Y-m-15")) - 86400*30;
        $d1 = strtotime(date('Y-m-01',$d0));
        $d2 = strtotime(date('Y-m-01'));
        $whr = "atime>=$d1 AND atime<$d2";
        // $sql = "CREATE TABLE {$tbnew} SELECT * FROM {$tbfull} WHERE $whr"; $db->query($sql);
        $db->query("CREATE TABLE {$tbnew} LIKE {$tbfull}");
        $db->query("INSERT INTO {$tbnew} SELECT * FROM {$tbfull} WHERE $whr");
        #dump("// $sql\n"); 
        // 清理某一月数据(2个月前数据)
        $stamp = time() - 60*86400;
        $db->table('vlog_list')->where("atime<'$stamp'")->delete(); 
    }

    // 统计某一天数据(前1天)
    static function sumer($first){
        $tim0 = strtotime(date('Y-m-d'));
        if($first){
            $date = date('Y-m-d', $tim0-86400);
            $whr = "atime>='".($tim0-86400)."' AND atime<'$tim0'";
        }else{
            $date = date('Y-m-d');
            $whr = "atime>='$tim0' AND atime<'".($tim0+86400)."'";
        }
        $db = db();
        $tbfull = $db->table('vlog_list', 2);
        $sql = "SELECT vmod, mkv, COUNT(mkv) AS nPV, SUM(isfirst) AS nUV, COUNT(DISTINCT aip) AS nIP
            FROM $tbfull WHERE $whr GROUP BY vmod,mkv";
        $list = $db->query($sql); //echo $sql;
        if(!$list) return;
        foreach($list as $itm){
            $data2 = $itm;
            $whr2['vmod'] = $itm['vmod'];
            $whr2['mkv'] = $itm['mkv'];
            $whr2['vdate'] = $date;
            $row2 = $db->table('vlog_dsum')->where($whr2)->find();
            if($row2){
                unset($data2['vmod'],$data2['mkv']);
                $db->table('vlog_dsum')->where($whr2)->data($data2)->update(0);
            }else{
                $data2['vdate'] = $date;
                $db->table('vlog_dsum')->data($data2)->insert(0);
            } //dump($itm);
        }
        #if($first){ # mkv>'a' - 排除(all)
            $tbdsum = $db->table('vlog_dsum', 2);
            $sql2 = "SELECT vmod, vdate, SUM(nPV) AS nPV, SUM(nUV) AS nUV
                FROM $tbdsum WHERE vdate='$date' AND mkv>'a' GROUP BY vmod,vdate";
            $list2 = $db->query($sql2); #echo $sql2;
            if(!$list2) return;
            foreach($list2 as $itm2){
                $data3 = $itm2;
                $whr3['vmod'] = $itm2['vmod'];
                $whr3['mkv'] = $data3['mkv'] = '(all)';
                $whr3['vdate'] = $date;
                // nIP-Start
                $sql4 = "SELECT COUNT(DISTINCT aip) AS nIP FROM $tbfull WHERE $whr AND vmod='$itm2[vmod]'";
                $re4 = $db->query($sql4); #echo $sql4;
                $data3['nIP'] = empty($re4[0]['nIP']) ? 0 : $re4[0]['nIP']; 
                // nIP-End
                $row3 = $db->table('vlog_dsum')->where($whr3)->find();
                if($row3){
                    unset($data3['vmod'],$data3['mkv']);
                    $db->table('vlog_dsum')->where($whr3)->data($data3)->update(0);
                }else{
                    $data3['vdate'] = $date;
                    $db->table('vlog_dsum')->data($data3)->insert(0);
                } //dump($itm);

            }
        #}
        return '-sumer-';
    }

    // 访问(点击)写记录
    static function vlog($vrow){
        // vmod,mkv,uri,ref; aip,atime,auser; 
        $ck = $vrow['vmod']; //.'_'.$vrow['mkv'];
        $ckval = comCookie::oget("vfirst_$ck");
        if(!$ckval){
            comCookie::oset("vfirst_$ck", date('Y-m-d H:i:s'), 30*86400);
            $vrow['ftime'] = '1900-01-01';
            $vrow['isfirst'] = 1;
        }else{
            $vrow['ftime'] = $ckval;
            $vrow['isfirst'] = 0;
        }
        $vrow['vhour'] = date('H');
        db()->table('vlog_list')->data($vrow)->insert();
    }

}

/*

http://127.0.0.1/peace/imcat/catmain/index.php
?ajax-cron&tpldir=comm&rf=keres
&_528[tm]=1568894920&_528[enc]=3f39e4b9d5ca908e
&soag=0
&uri=http://127.0.0.1/peace/imcat/catmain/chn.php/keres
&ref=http://127.0.0.1/peace/imcat/catmain/chn.php/keres.2015-9d-q5d1
&_r=1568894922169&lang=cn&_=1568894920793

*/
