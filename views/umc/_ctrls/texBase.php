<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basElm;
use imcat\basJscss;
use imcat\basKeyid;
use imcat\basReq;
use imcat\comCookie;
use imcat\comConvert;
use imcat\extWework;
use imcat\glbDBExt;
use imcat\usrPerm;
use imcat\vopShow;

use imcat\vopApi as api;

/*

公共模板扩展函数
*/ 
class texBase{
    
    //protected $xxx = array();
    
    static function init($obj){
        //
    }

    // 状态,人员,时间,类型类型,设备类型,关键字
    static function sqlType($vars){ 

        $user = empty($vars['uinfo']) ? ['umod'=>'(null)','uname'=>'(null)'] : $vars['uinfo'];
        $corp = empty($vars['cscorp']) ? [] : $vars['cscorp']; 

        $umod = $user['umod']; $uname = empty($user['uname']) ? '_^_' : $user['uname']; 
        $wecfgs = read('wework', 'ex'); 
        $logtab = tex('texBase')->dbtab('cslogs');
        $meget = "did IN(SELECT pid FROM $logtab WHERE auser='$uname' OR mname='$uname')";
        $meat = "(mduids LIKE '%$uname%' OR atuids LIKE '%$uname%')";
        // company
        $sql = "`show`='1'";
        if($umod=='company'){ // 客户
            $csno = empty($corp['csno']) ? '_^_' : $corp['csno'];
            //$sql .= " AND eqstr LIKE '$csno.%'"; //eqstr=2020AVHPH6
            $sql .= " AND auser='$uname'";
        }elseif($umod=='person'){ // IT工程师
            // 我发布的, 我处理的
            $mypub = req('mypub');
            if($mypub){
                $sql .= " AND auser='$uname'";
            }else{
                $sql .= " AND $meget";
            }
        }elseif($umod=='inmem'){ // 内部员工
            $uall = $wecfgs['AppCS']['perms']['alldata']; //echo ".$uall@$uname.";
            if(strstr($uall,$uname)){ // 当前者
                //$sql .= " AND 1=1"; 
            }else{ // all:所有单
                $sql .= " AND $meget";
            }
        }else{
            $sql .= " AND did='_^_'";
        }
        //$sql = $sql ? $sql : " AND 1=1"; //dump($sql); //die('');
        return $sql;
    }

    static function sqlSo(){
        $sql = '';
        // catid+equip
        foreach (['catid','equip'] as $key) {
            $re['vars'][$key] = $$key = req($key);
            if($$key){ $sql .= " AND $key='".$$key."'"; }
        }
        // sotime[a-b]+day_start+day_end
        $sotime = req('sotime');
        self::subTime($sotime, $re, $sql);
        // keywd
        $re['vars']['keywd'] = $keywd = req('keywd');
        if($keywd){ $sql .= " AND title LIKE '%".$keywd."%'"; }
        // sql+return
        #$sql = $sql ? $sql : " AND 1=1"; //dump($sql); //die('');
        return $sql;
    }

    static function subTime($sotime, &$re, &$sql){
        $sotime = $re['vars']['sotime'] = $sotime ? $sotime : req('sotime');
        $sotab = $re['vars']['sotab'] = [
            'mpre'=>'上月', 'mnow'=>'本月', 'wpre'=>'上周', 'wnow'=>'本周',
            'ypre'=>'去年', 'ynow'=>'今年', 'uset'=>'自定义', 
        ];
        if($sotime && isset($sotab[$sotime])){
            $now = strtotime(date('Y-m-d')); // 今日0时的stamp
            if($sotime=='wnow' || $sotime=='wpre'){
                $w = date('w'); // 返回当天的星期几；数字0表示是星期天,数字123456表示星期一到六
                $start = $w ? ($now-86400*$w) : $now; // 本周起始stamp
                if($sotime=='wpre'){ // 上周stamp
                    $day_start = date('Y-m-d', $start-7*86400);
                    $day_end = date('Y-m-d', $now-7*86400+86400);  
                }else{ // 本周stamp
                    $day_start = date('Y-m-d', $start);
                    $day_end = date('Y-m-d', $now+86400);
                }
            }elseif($sotime=='mnow' || $sotime=='mpre'){
                $n = date('n'); // 月份的数字表示，不带前导零（1 到 12）
                $yp = date('Y'); $np = $n-1; 
                if($sotime=='mpre'){ // 上月stamp
                    if($yp<1){
                        $yp = $yp - 1;
                        $np = 12; 
                    }
                    $day_start = "$yp-$np-1";
                    $day_end = "$yp-$n-1";
                }else{ // 本月stamp
                    $day_start = "$yp-$n-1";
                    $day_end = date('Y-m-d', $now+86400);
                }
            }elseif($sotime=='ynow' || $sotime=='ypre'){
                $y = date('Y'); $yp = $y-1; 
                if($sotime=='ypre'){ // 上年stamp
                    $day_start = "$yp-1-1";
                    $day_end = "$y-1-1";
                }else{ // 本月stamp
                    $day_start = "$y-1-1";
                    $day_end = date('Y-m-d', $now+86400);
                }
            }
        }
        $day_start = $re['vars']['day_start'] = !empty($day_start) ? $day_start : req('day_start'); 
        $day_end   = $re['vars']['day_end']   = !empty($day_end)   ? $day_end   : req('day_end');
        if($day_start){ $sql .= " AND atime>='".strtotime($day_start)."'"; }
        if($day_end){ $sql .= " AND atime<='".strtotime($day_end)."'"; }
    }

    static function printLink($did='', $time=3600){ // did, check
        global $_cbase;
        $stamp = $_cbase['run']['stamp']; 
        $sform = $_cbase['safe']['safil'];
        $safix = $_cbase['safe']['safix'];
        if(strlen($did)>8){ // 
            $encode = comConvert::sysEncode("$time.$did.$sform", $stamp);
            $uid = comConvert::sysRevert($time, 0);
            return surl('task-print')."?did=$did&{$safix}[tm]=$stamp&{$safix}[enc]=$encode&puid=$time";
        }else{ // check
            $flag = 0; 
            $re_stamp = basReq::ark($safix, 'tm');
            $re_encode = basReq::ark($safix, 'enc'); 
            $re_did = req('did');
            $puid = req('puid');
            if(empty($re_stamp) || empty($re_encode)) $flag = 'empty';
            if($stamp-$re_stamp>$time) $flag = 'timeout';
            if(!($re_encode==comConvert::sysEncode("$puid.$re_did.$sform", $re_stamp))) $flag = 'encode';
            if($flag){
                vopShow::msg("链接超时或资料不存在，请重新打开企业微信进入打印页！");
            }
            return $flag;
        }
    }

    static function mapLink($pos=''){
        $tmp = explode(',',"$pos,");
        $title = '打卡位置';
        $url = "https://map.qq.com/?type=marker&isopeninfowin=1&markertype=1&pointx={$tmp[0]}&pointy={$tmp[1]}&name=$title&zoomLevel=16";
        return $url;
    }

    static function oauth2Link($state=''){
        $reuri = urlencode(surl('user-login','',1));
        $state || $state = 'imcat_wxwork_login'; 
        $CorpId = read('wework.CorpId', 'ex');
        //$config['CorpId']
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$CorpId&redirect_uri=$reuri&response_type=code&scope=snsapi_base&state=$state#wechat_redirect";
        return $url;
    }

    static function nowUser($code, $agentId=''){
        include_once(DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php"); 
        $agentId || $agentId = 'AppCS';
        $CorpId = read('wework.CorpId', 'ex');
        $api = new \CorpAPI($CorpId, $agentId);
        //var_dump($api);
        try {
            return $api->GetUserInfoByCode($code, 1); 
            //$UserInfo = $api->GetUserById($UserBase->UserId); 
        } catch (Exception $e) {
            return ['errcode'=>'errNowUser', 'errmsg'=>$e->getMessage()];
            //dump($e->getMessage());
        }
    }

    static function doUser($doTemp, $utab, $deps){ // $utab = $this->revars['utab']; $deps = $this->revars['deps']; 
        $re = ['doName'=>'-', 'doDepart'=>'-'];
        if(!empty($doTemp['doLogs'])){
        foreach($doTemp['doLogs'] as $tr) {
            if($tr['mflag']=='assign'){
                $uid = $tr['mname']; $ur = extWework::getUser($uid, 'AppCS'); //dump($ur);
                $re['doUserid'] = $uid;
                $re['doName'] = isset($utab[$uid]) ? $utab[$uid]['name'] : $tr['mname'];
                foreach($deps as $dv) {
                    if($dv['id']==$ur['main_department']){
                        $re['doDepart'] = $dv['name'];
                        break;
                    }
                }
                break;
            }
        } }
        return $re;
    }

    static function ugroup(){

        $vars['deps'] = extWework::getContacts('deps', 'AppCS');
        $vars['utab'] = extWework::getContacts('utab', 'AppCS');

        //$re['vars'] = $this->revars;
        $udep1 = [];
        foreach($vars['deps'] as $k1=>$r1) {
            if($r1['parentid']=='1'){
                $udep1[$k1]['name'] = $r1['name'];
                $udep1[$k1]['subs'][] = $r1['id'];
                foreach($vars['deps'] as $k2=>$r2) {
                    if($r2['parentid']==$r1['id']){
                        $udep1[$k1]['subs'][] = $r2['id'];
                    }
                }
                foreach($vars['utab'] as $uk=>$u1) {
                    if(in_array($u1['department'][0],$udep1[$k1]['subs'])){
                        $udep1[$k1]['tabs'][$u1['userid']] = $u1['name'];
                    }
                }
            }
        }
        return $udep1;
    }

    static function wewIdop($wew_id=''){
        $cfgs = read('wework', 'ex');
        $key = $cfgs['sktab']['ck']; 
        if($wew_id){ // 加密保存cookie
            $enc_uid = comConvert::sysRevert($wew_id, 0, $key);
            comCookie::oset('wew_uid', $enc_uid, 86400*7);
        }else{ // 取出cookie并解密
            $enc_uid = comCookie::oget('wew_uid');
            $wew_id = comConvert::sysRevert($enc_uid, 1, $key);
            if($wew_id){ // 重新保存7天
                comCookie::oset('wew_uid', $enc_uid, 86400*7);
            }
            /*
                # cache
                $khour = '/store/khour-'.date('H').'.vlog';
                $chour = extCache::cfGet($khour, 90, 'dtmp', 'str');
                # 1小时监测一次
                if(!$chour){
                    // do sth
                }
            */
        }
        return $wew_id;
    }

    static function msgSend($row=[], $agentId='', $act='0', $touids=[], $toparty=[]){
        $agentId || $agentId = 'AppCS';
        $acts = [
            '0' => '请处理',
            '1' => '请(加急)处理',
            'tip' => '提到了您',
            'remind' => '工单时间提醒：'.(empty($row['remind_time'])?'':$row['remind_time']),
            'exqa' => '有新评论',
        ];
        $acmsg = isset($acts[$act]) ? $acts[$act] : $act;
        $premsg = basEnv::isLocal() ? '本地测试•' : '';
        // msg
        $mflag = empty($row['mflag']) ? 'apnew' : $row['mflag'];
        $mfmsg = $act=='exqa' ? $row['exqa'] : implode(',', \imcat\vopCell::optArray('cstask.mflag', $mflag, 0));
        $des = "<div class=\"gray\">".date('Y-m-d H:i:s')."</div> <div class=\"normal\">工单[$row[did]]{$acmsg}</div><div class=\"highlight\">状态：$mfmsg</div>";
        $url = surl("umc:task.$row[did]",'',1)."?uflag=notice";
        $msg = ['title'=>"{$premsg}$row[title]", 'des'=>$des, 'url'=>$url, 'btntxt'=>'查看详情'];
        //  
        $to = ['uids'=>$touids, 'party'=>$toparty, 'tag'=>[]]; 
        $res = extWework::smsgCard($agentId, $msg, $to); 
        return $res;
    }

    static function dbtab($mod,$nofix=2){
        $tab = glbDBExt::getTable($mod);
        return db()->table($tab, $nofix);
    }

    // 转化
    static function convData(&$data){
        foreach ($data as $dk=>$drow){
            if(!is_array($drow)){ continue; }
            foreach ($drow as $rk=>$rv){
                if(!is_string($rv)){ continue; }
                $data[$dk] = self::convRow($drow);
            }
        }
    }
    // 转化
    static function convRow($drow){
        $utab = extWework::getContacts('utab'); 
        $tabEquip = basElm::text2arr('cstask.equip');
        $tabMflag = basElm::text2arr('cstask.mflag');
        $tabCatid = []; $Catids = read('cstask.i');
        foreach ($Catids as $ck=>$cv) {
            $tabCatid[$ck] = $cv['title'];
        }
        foreach ($drow as $rk=>$rv){
            if(!is_string($rv)){ continue; }
            if($rk=='catid'){
                $drow["{$rk}Str"] = ($rv=='c6018'&&$drow['catstr']) ? $drow['catstr'] : (isset($tabCatid[$rv]) ? $tabCatid[$rv] : "($rv)");
            }
            if($rk=='atime'){
                $drow["{$rk}Str"] = date('Y-m-d H:i:s',$rv);
            }
            if($rk=='mflag'){
                $drow["{$rk}Str"] = isset($tabMflag[$rv]) ? $tabMflag[$rv] : "($rv)";
            }
            if($rk=='douid'){
                $drow["{$rk}Str"] = isset($utab[$rv]) ? $utab[$rv]['name'] : "($rv)";
            }
            if($rk=='atuids'){
                $ta = explode(',', $rv); $tmp = '';
                foreach ($ta as $uk){
                    $tmp .= ($tmp?", ":'') . (isset($utab[$uk]) ? $utab[$uk]['name'] : "($uk)");
                }
                $drow["{$rk}Str"] = $tmp;
            }
            if($rk=='mname'){
                $drow["{$rk}Str"] = isset($utab[$rv]) ? $utab[$rv]['name'] : "($rv)";
            }
        }
        return $drow;
    }

    static function pend(){
        return;
        $tpl = cfg('tpl');
        $base = $tpl['tplpend'];
        $ext = $tpl['tplpext']; 
        $base || $base = ''; // login-check,jstag,menu,caritems,fanyi
        $js = "";
        //$js .= "setTimeout(\"jcronRun()\",370);\n";
        strstr($base,'jstag') && $js .= "jtagSend();\n";
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


/*

*/
