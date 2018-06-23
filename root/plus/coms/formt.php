<?php 

# vote-topic : for:topic

require dirname(__FILE__).'/_cfgall.php'; 

glbHtml::head('js');

// public检查
$_rck = comCookie::oget('v_rck');
if(empty($_rck)){
    $data['error'] = 1;
    $data['msg'] = 'Timeout(rck-null)!';
    die(out($data,'jsonp'));
}
safComm::urlStamp();

$re2 = safComm::formCAll('topfm');
if(!empty($re2[0])){ 
    $data['error'] = 1;
    $data['msg'] = '验证码错误!';
    die(out($data,'jsonp'));
}

$did = req('did');
$tmstr = date('Y-m-d H:i:s');
$ipstr = $_cbase['run']['userip'];
$uastr = $_cbase['run']['userag'];
$db = db();

$rp = $db->table('dext_topic')->where("did='$did'")->find();
$cfgs = devTopic::cfg2arr($rp['cexts']);
$fcfg = devTopic::cfg2arr($rp['cform']);
#die(out($cfgs,'jsonp'));

/*
'vote_begtm=2015-12-29
vote_endtm=2016-01-03 12:00:00
vote_allowip=127.0.0.1|127.*.2.1
vote_stopip=127.0.2.1|127.*.2.1
vote_alloua=MicroMessenger|MQQBrowser
vote_stopua=Bot|Crawl|Spider
*/
if($cfgs['vote_begtm']>$tmstr || $cfgs['vote_endtm']<$tmstr){
    $data['error'] = 1;
    $data['msg'] = '现在不是投票时间!';
    die(out($data,'jsonp'));
}
if(!empty($cfgs['vote_allowip']) && !preg_match("/(".$cfgs['vote_allowip'].")/",$ipstr)){
    $data['error'] = 1;
    $data['msg'] = '此IP不允许操作!';
    die(out($data,'jsonp'));
}
if(!empty($cfgs['vote_stopip']) && preg_match("/(".$cfgs['vote_stopip'].")/",$ipstr)){
    $data['error'] = 1;
    $data['msg'] = '此IP禁止操作!';
    die(out($data,'jsonp'));
}
if(!empty($cfgs['vote_alloua']) && !preg_match("/(".$cfgs['vote_alloua'].")/",$uastr)){
    $data['error'] = 1;
    $data['msg'] = '此设备不允许操作!';
    die(out($data,'jsonp'));
}
if(!empty($cfgs['vote_stopua']) && preg_match("/(".$cfgs['vote_stopua'].")/",$uastr)){
    $data['error'] = 1;
    $data['msg'] = '此设备禁止操作!';
    die(out($data,'jsonp'));
}

/*
vote_gaptm=30 // extCache::CTime() // 30s,60m,12h,7d,4w,12M
*/
if(empty($cfgs['vote_gaptm'])) $cfgs['vote_gaptm'] = '4h';
$gaptm = time() - extCache::CTime($cfgs['vote_gaptm']);
$gapmsg = str_replace(array('s','m','h','d'),array('秒','分钟','小时','天'),$cfgs['vote_gaptm']);
if(is_numeric($gapmsg)) $gapmsg .= "分钟";
$uid = usrPerm::getUniqueid('Cook','sid');
$whrsub = "did='$did' AND auser='$uid' AND atime>'$gaptm'";


$row1 = $db->table('topic_form')->where("$whrsub")->find();
if($row1){
    $data['error'] = 1;
    $data['msg'] = "($gapmsg)内已经提及过数据!";
    die(out($data,'jsonp'));
}else{
    $dpost = $dvote = array();
    $detail = basReq::arr('detail');
    $tmps = $db->table('topic_items')->where("did='$did'")->select();
    $datas = array(); foreach ($tmps as $kd=>$vd) { $datas[$vd['dno']]=$vd; }
    foreach ($fcfg as $k2=>$title){ 
        if(devTopic::skip($k2)) continue;
        if(!isset($datas[$k2])) continue;
        $data = $datas[$k2];
        $tmps = json_decode($data['detail'],1); 
        $flags = $tmps['flags']; $des = $tmps['des'];
        $names = basElm::line2arr($tmps['name'], 'kv');
        $tags = basElm::line2arr($data['tags'], 'kv');
        $dv = isset($detail[$k2]) ? $detail[$k2] : ''; 
        $da = is_array($dv) ? $dv : array($dv);
        $dpost[$k2] = is_array($dv) ? implode(',',$dv) : $dv;
        $dvk2 = "";
        foreach ($names as $k3=>$v3){
            $oldv = empty($tags[$k3]) ? 0 : intval($tags[$k3]);
            $newv = in_array($k3,$da) ? 1 : 0;
            $dvk2 .= "$k3=".($oldv+$newv)."\n";
        }
        $dvote[$k2] = $dvk2;
    }
    $kar = glbDBExt::dbAutID('topic_form');
    $data = array(
        'kid'=>$kar[0],'kno'=>$kar[1],'did'=>$did,
        'aip'=>$ipstr,'atime'=>time(),'auser'=>$uid,
    );
    $fm['mname'] = basReq::ark('fm','mname');
    $fm['mtel'] = basReq::ark('fm','mtel');
    $fm['detail'] = in(comParse::jsonEncode($dpost));
    $fm = $fm + $data;
    $db->table('topic_form')->data($fm)->insert(0);
}

foreach ($dvote as $k2=>$v2){ 
    if(strlen($v2)<3) continue;
    $data = array('tags'=>$v2);
    $db->table('topic_items')->data($data)->where("did='$did' AND dno='$k2'")->update(0);
}

//$res['fm'] = $fm;
//$res['fcfg'] = $fcfg;
//$res['dpost'] = $dpost;
//$res['dvote'] = $dvote;

$res['error'] = 0;
$res['msg'] = '投票成功!';
die(out($res,'jsonp'));

/*

*/
