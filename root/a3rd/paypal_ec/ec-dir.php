<?php 
require dirname(__FILE__).'/ec-cfg.php';

if(strpos('(,mailSend,ofeeDebug,opayDebug,)',",$act,")>0){
    $a_user = usrBase::userObj('Admin'); 
    if($a_user->userFlag!='Login'){
        die('Please Login!');
    }
    $order = $db->table('coms_corder')->where("cid='{$ordid}'")->find();
}

if($act=='ofee'){ //运费计算

    $ret = array(); 
    $mstate = req('mstate'); 
    $weight2 = req('weight2','0','N'); //echo $weight2;
    $list = $db->table('bext_paras')->where("pid='logmode_en' AND enable=1")->order('top')->select();
    foreach($list as $row){ $skid=str_replace('(Large)','',$row['kid']);
    $res = devOcar::shipFee($mstate,$skid,$weight2,$row['numa']); 
    if(!empty($res)){
        $ret[$row['kid']] = array($res['totalfee'],$res['ures']);
    } }
    $json = json_encode($ret);
    die($json);

}elseif($act=='oadd'){ //提交订单

    $re2 = safComm::formCAll('vsms4');
    if(!empty($re2[0])){ 
        $readd['error'] = $re2[0]; // 'Error Captcha'
    }else{
        $readd = exvOcar::oadd($unqid,$user);
        $readd['error'] = '';
        $readd = $readd + devOcar::setEncs($readd['ordid'], $readd['enc']);
    }
    $json = json_encode($readd);
    die($json);

}elseif($act=='opayDebug'){

    $url = $_cbase['run']['roots'].'/a3rd/paypal_ec/ec-ipn.php';
    $params = devOcar::setPpdata($order); //dump($params);
    $data = comHttp::curlCrawl($url, $params);
    echo '(test)PayOK!';
    dump($data); 

}elseif($act=='ofeeDebug'){

    $mstate = req('mstate'); 
    $weight2 = req('weight2'); 
    $ordship = req('ordship');
    $forg = basReq::arr('forg'); dump($forg);
    $row = $db->table('bext_paras')->where("kid='$ordship'")->find(); 
    if(empty($forg)){
        $res = devOcar::shipFee($mstate,$ordship,$weight2,$row['numa']);
        dump($res);
    }else{
        $params = "country=".urlencode($mstate)."&mode=$ordship&weight=$weight2";
        $url = "http://www.sendfromchina.com/shipfee/out_rates/?$params";
        header("location:$url");
    }

}elseif($act=='mailSend'){

    //

}elseif($act=='odel'){ //取消订单
    //$erow = exvOcar::odel($ordid);
    $msg = "删除".($erow ? '成功！' : '失败！');
    $url = surl(0).($erow ? surl('cargo') : "?mkv=$this->mkv&ordid=$ordid&enc=$enc");
    die(basMsg::show("$msg",'Redir',$url,1));
}elseif($act=='oedit'){ //edit订单
    //$erow = exvOcar::oedit($ordid);
    $msg = "编辑".($erow ? '成功！' : '失败(可能没有修改项)！');
    die(basMsg::show("$msg",'Redir',surl(0)."?mkv=$this->mkv&ordid=$ordid&enc=$enc",1));
}elseif($act=='invoce'){
    //$ouser = exvOcar::whruser();
    $where = "ordid='$ordid'"; 

}else{

    // "2018-16-gu71.".(time()).".amount"
    $qtmp = empty($qstr) ? array() : comConvert::sysRevert($qstr,1);
    $qarr = explode('.',$qtmp); echo "$qtmp<br>\n";
    $url = "?mkv=ocar.{$qarr[0]}&enc=$qstr&".safComm::urlStamp('init');
    header('Location:'.surl('ven:0').$url); 
    echo $url;

}
