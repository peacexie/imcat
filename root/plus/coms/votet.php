<?php 
namespace imcat;
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

// 得到投票enc
//$safix = $_cbase['safe']['safix'];
$enc = basReq::val('enc');
if(!$enc){
    safComm::urlStamp();
    $did = req('did');
    $vid = req('vid');
    $data['error'] = 0;
    $data['enc'] = devTopic::voteUrlv($did,$vid);
    die(out($data,'jsonp'));
}

// 投票操作:did,vid
extract(devTopic::voteParams()); 
$tmstr = date('Y-m-d H:i:s');
$ipstr = $_cbase['run']['userip'];
$uastr = $_cbase['run']['userag'];
$db = db();

$rp = $db->table('dext_topic')->where("did='$did'")->find();
$rv = $db->table('topic_items')->where("did='$did' AND dno='$vid'")->find();
$cfgs = devTopic::cfg2arr($rp['cexts']);

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
vote_items=3 // 同一kid(pid/did)
*/
if(empty($cfgs['vote_gaptm'])) $cfgs['vote_gaptm'] = '4h';
if(empty($cfgs['vote_items'])) $cfgs['vote_items'] = 5;
$gaptm = time() - extCache::CTime($cfgs['vote_gaptm']);
$gapmsg = str_replace(array('s','m','h','d'),array('秒','分钟','小时','天'),$cfgs['vote_gaptm']);
if(is_numeric($gapmsg)) $gapmsg .= "分钟";
$uid = usrPerm::getUniqueid('Cook','sid');
$whrsub = "did='$did' AND uid='$uid'";

$cnt1 = $db->table('topic_votes')->where("$whrsub AND atime>='$gaptm'")->count();
if($cnt1>=$cfgs['vote_items']){
    $data['error'] = 1;
    $data['msg'] = "($gapmsg)内最多只能投(".$cfgs['vote_items'].')个选项!';
    die(out($data,'jsonp'));
}
$row1 = $db->table('topic_votes')->where("$whrsub AND dno='$vid'")->find();
if($row1 && $row1['atime']>$gaptm){
    $data['error'] = 1;
    $data['msg'] = "($gapmsg)内不能再投同一选项!";
    die(out($data,'jsonp'));
}elseif($row1){
    $data = array('cnt'=>$row1['cnt']+1,'atime'=>time());
    $db->table('topic_votes')->data($data)->where("$whrsub AND dno='$vid'")->update(0);
}else{
    $kar = glbDBExt::dbAutID('topic_votes');
    $data = array(
        'kid'=>$kar[0],'kno'=>$kar[1],
        'did'=>$did,'dno'=>$vid,'uid'=>$uid,
        'aip'=>$ipstr,'atime'=>time(),'aua'=>$uastr,
    );
    $db->table('topic_votes')->data($data)->insert(0);
}
$votes = $rv['vote']+1;
$data = array('vote'=>$votes);
$res = $db->table('topic_items')->data($data)->where("did='$did' AND dno='$vid'")->update(0);

$data['error'] = 0;
$data['votes'] = $votes;
$data['msg'] = '投票成功!';
die(out($data,'jsonp'));

/*
欧阳司马,,东郭西门,,诸葛司徒
*/
