<?php
require dirname(__FILE__).'/_cfgall.php'; 

#safComm::urlFrom();

// did,dno
$did = req('did');
$dno = req('dno'); // flower,word
if(!$did || !in_array($dno,array('flower','word'))){
    die("Error-Params.");
}
if($dno=='word'){
    $mname = req('mname', 'Title', 120);
    $word = req('word', 'Title', 36);
    if(empty($mname) || empty($word)){
        die("Null-name/word.");
    }
} 

$tmstr = date('Y-m-d H:i:s');
$ipstr = $_cbase['run']['userip'];
$uastr = $_cbase['run']['userag'];
$db = db();

$rp = $db->table('dext_topic')->where("did='$did'")->find();
$cfgs = devTopic::cfg2arr($rp['cexts']);
$fcfg = devTopic::cfg2arr($rp['cform']);
if(empty($rp) || !strstr($rp['tplname'],'memory/')){
    die("参数错误!");
}

/*
vote_gaptm=30 // extCache::CTime() // 30s,60m,12h,7d,4w,12M
*/
if(empty($cfgs['vote_gaptm'])) $cfgs['vote_gaptm'] = '4h';
$gaptm = time() - extCache::CTime($cfgs['vote_gaptm']);
$gapmsg = str_replace(array('s','m','h','d'),array('秒','分钟','小时','天'),$cfgs['vote_gaptm']);
if(is_numeric($gapmsg)) $gapmsg .= "分钟";
$uid = usrPerm::getUniqueid('Cook','sid');
$whrsub = "did='$did' AND auser='$uid' AND atime>'$gaptm'"; // AND dno='$dno' 


$_rck = comCookie::oget("mck_{$did}_{$dno}");
if($_rck || ($dno=='flower'&&$db->table('topic_form')->where("$whrsub")->find())){
    $data['error'] = 1;
    die("($gapmsg)内已经提交过数据!");
}elseif($dno=='flower'){
    $tabfull = $db->table('topic_items',2);
    $db->query("UPDATE $tabfull SET click=click+1 WHERE did='$did' AND dno='$dno' ");
}else{ // word
    $kar = glbDBExt::dbAutID('topic_form');
    $data = array(
        'kid'=>$kar[0],'kno'=>$kar[1],'did'=>$did, 'show'=>'0',
        'aip'=>$ipstr,'atime'=>time(),'auser'=>$uid,
    );    
    $fm['mname'] = $mname;
    $fm['detail'] = $word;
    $fm = $fm + $data;
    $db->table('topic_form')->data($fm)->insert(); // 0
}
comCookie::oset("mck_{$did}_{$dno}", '1', 3600); 


//$res['error'] = 0;
//$res['msg'] = ($dno=='word'?'留言':'送花').'成功!';
die('success');

