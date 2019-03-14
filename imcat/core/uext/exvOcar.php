<?php
namespace imcat;

// Ocar相关函数
// 各模版公用
class exvOcar{

    static $account = "<clientid>E000193WS</clientid><authtoken>XErFHPQIzva24WY7nTuS</authtoken>";

    static function shipPrice($to, $weight){
        $s_data = '<?xml version="1.0" encoding="utf-8"?>';
        $s_data .= '<GePriceServiceRequest>'; 
        $s_data .= self::$account; 
        $s_data .= "<country>".strtoupper($to)."</country>"; 
        $s_data .= "<rweight>$weight</rweight>"; 
        $s_data .= '</GePriceServiceRequest>'; 
        $api = 'http://hm.kingtrans.cn/APIPrice?action=getPrice';
        if(strstr($_SERVER["HTTP_HOST"],'test.com')){
            $res = file_get_contents(DIR_PROJ.'/@read/ship-fee.xml');
            usleep(400123); //die($res);
        }else{
            $res = comHttp::curlCrawl($api, 'xml='.urldecode($s_data), 8);
        }
        $res = preg_replace("/<note[\s\S]*?<\/note>/i", "", $res);
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $jarr = json_decode($json, true);
        $res = array();
        foreach ($jarr['Price'] as $k=>$row){
            unset($row['note']);
            $res[$row['channelid']] = $row;
        }
        return $res;
    }

    static function shipTabs(){
        $api = 'http://hm.kingtrans.cn/PostOrderInterface?method=searchChannel';
        $s_data = '<?xml version="1.0" encoding="utf-8"?>';
        $s_data .= '<CreateAndPreAlertOrderService>';
        $s_data .= self::$account;
        $s_data .= '</CreateAndPreAlertOrderService>';
        $header = "Content-type: text/xml";
        $res = comHttp::curlCrawl($api, $s_data, 5, $header);
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $jarr = json_decode($json, true);
        $res = array();
        foreach ($jarr['channelid'] as $k=>$key){
            $res[$key] = array('en'=>'','cn'=>$jarr['channelname'][$k]); 
        }
        return $res;
    }

    static function shipfee($from,$to,$weight){ 
        $weight || $weight = 0.1;
        $from = self::shipfix($from); 
        $to = self::shipfix($to); 
        $url = "http://www.chakd.com/index.php?action=search&start=$from&end=$to&weight=$weight&Submit=1";
        $data = comHttp::doGet($url, 3); 
        $data = comConvert::autoCSet($data,'gb2312','utf-8'); 
        $arr = explode('</tr>',$data);
        $data = array();
        foreach($arr as $row){
            if(strpos($row,'天</div></td>') && strpos($row,'元</div></td>')){
                $row = strip_tags($row,'<td>'); 
                $ar2 = explode('</td>',$row); 
                foreach($ar2 as $k=>$v){
                    $v = preg_replace("/\s(?=\s)/","\\1",strip_tags($v)); 
                    $v = trim($v);
                    $ar2[$k] = $v;
                }
                $data[] = $ar2;
            }
        } 
        $devs = glbDBExt::getExtp('logmode_cn'); 
        foreach($devs as $key=>$row){ 
            foreach($data as $fee){
                if(strstr($fee[0],$row['title'])){
                    $devs[$key]['ufee'] = $fee[1];
                    $devs[$key]['uday'] = $fee[2];
                }
            }
        }
        return $devs;
    }
    
    static function shipfix($area){
        if($area=='中山'){ $area = '中山市'; } //单个修正
        elseif(in_array($area,array('三沙','钓鱼岛'))){ $area = '暂无.服务'; } //单个修正
        elseif(in_array($area,array('宜兰','桃园','苗栗','彰化','南投','云林','屏东','台东','花莲','澎湖','金门','马祖','新竹','台中','嘉义','台南'))){ $area = '台湾'; } //台湾 台北基隆新竹台中嘉义台南'高雄',
        elseif(in_array($area,array('定安县','五指山市','屯昌县','琼海市','澄迈县','儋州市','临高县','文昌市','白沙县','万宁市','昌江县','东方市','乐东县','陵水县','保亭县','琼中县'))){ $area = '海口'; } //海南， '海口','三亚', 
        elseif(in_array($area,array('北京','天津','上海','重庆','香港','澳门'))){ ; } //原型, 
        elseif(strlen($area)>6){ ; } //原型, 乌鲁木齐
        else{ ; } //通用 $area = $area.'市';
        $area = urlencode(comConvert::autoCSet($area,'utf-8','gbk'));
        return $area;
    }
    
    static function whruser(){ 
        $stamp = $_SERVER["REQUEST_TIME"];
        $user = usrBase::userObj('Member'); 
        $uadm = usrBase::userObj('Admin'); 
        $enc = basReq::val('enc');
        if($uadm->userFlag=='Login'){
            $re['flag'] = 'Admin';
            $re['sql'] = '1=1';
            $re['uname'] = @$uadm->uinfo['unamexx'];
            //uinfo
        }elseif($uadm->userFlag=='Login'){
            $re['flag'] = 'Member';
            $re['sql'] = "auser='{$uadm->uinfo['uname']}' AND ordstat='new' AND atime>'".($stamp-30*60)."'";
            $re['uname'] = @$uadm->uinfo['uname'];
        }else{
            $re['flag'] = 'Guest';
            $re['sql'] = "eip='$enc' AND ordstat='new' AND atime>'".($stamp-30*60)."'";
            $re['uname'] = @$uadm->uinfo['uname'];    
        }
        return $re;
    }
    
    static function ostat($ouser,$order){
        $cfg1 = array('feeship','feedis','feetotle','trakeno','tradeurl',);
        $cfg2 = array('ordstat','ordpay','ordship',);
        $cfg3 = array('btndel','btnedit','btnpay',);
        $res = array();
        $flag = $ouser['flag'];
        $ordstat = $order['ordstat'];
        $stamp = $_SERVER["REQUEST_TIME"]-$order['atime']; //<($flag=='Admin' ? 5*86400 : 30*60);
        //$flag = 'xxMember'; 
        //$stamp = 6666666;
        if($flag=='Admin'){
            foreach($cfg1 as $key){
                $res[$key] = "";    
            }
            foreach($cfg2 as $key){
                $res[$key] = "";    
            }
            $res['uinfos'] = "";
            $res['uexp'] = "";
        }elseif($flag=='Member'){ 
            foreach($cfg1 as $key){
                $res[$key] = $stamp<30*36 ? '' : " disabled style='background:#EEE;'";    
            }
            foreach($cfg2 as $key){
                $res[$key] = $stamp<30*36 ? '' : " disabled style='background:#EEE;'";
            }
            $res['uinfos'] = $stamp<30*36 ? '' : " disabled style='background:#EEE;'";    
            $res['uexp'] = $order['auser']==$ouser['uname'] ? '' : "exp";    
        }else{ 
            foreach($cfg1 as $key){
                $res[$key] = " disabled style='background:#EEE;'";    
            }
            foreach($cfg2 as $key){
                $res[$key] = " disabled style='background:#EEE;'";    
            }
            $res['uinfos'] = " disabled style='background:#EEE;'";    
            $res['uexp'] = $stamp<30*36 ? '' : "exp";    
        }
        if($res['uexp']){
            $res['trakeno'] = '';
            $res['tradeurl'] = '';
        }
        $res['btndel'] = ($flag=='Admin' || $stamp<30*36) ? '' : " disabled style='color:#999'";
        $res['btnedit'] = ($flag=='Admin' || $stamp<30*36) ? '' : " disabled style='color:#999'";    
        $res['btnpay'] = (in_array($ordstat,array('new','doing'))) ? '' : " disabled style='color:#999'";
        return $res;
    }
    
    static function oadd($unqid,$user){
        $db = glbDBObj::dbObj();
        $fm = basReq::arr('fm');
        if(empty($fm['feetotle']) || empty($fm['ordcnt'])){
            die('Error Order-Data, Please try again!');
        }
        $kar = glbDBExt::dbAutID('coms_corder', date('Y-m')>'2018-06'?'md2':'md3');
        $fm['title'] = $fm['cid'] = $kar[0]; 
        //$fm['title'] = '-';
        $fm['cno'] = $kar[1];
        $fm['ordstat'] = 'New';
        // bing-user : memail,mpass
        /*
        $fu = basReq::arr('fu');
        $mtel = $fm['mtel'];
        $fm['memail'] = $fm['auser'] = $memail = $fu['memail'];
        if(!empty($memail) && !empty($fu['mpass'])){
            $encpw = comConvert::sysPass($memail,$fu['mpass'],'person');
            $rea = $db->table('users_uacc')->where("uname='$memail'")->find(); 
            if(empty($rea)){ // add-user
                $re3 = usrMember::addUser('person',$memail,$fu['mpass'],$memail,$mtel,$memail,array());
                $fm['ordstps'] = "Add User($memail)";
                //auto-login
            }elseif($encpw==$rea['upass']){ // user-ok
                $fm['ordstps'] = "Bind User($memail)";
                $db->table('users_person')->data(array('mtel'=>$mtel))->where("uid='$memail'")->update(0);
                //auto-login
            }else{ // error!
                $fm['auser'] = "($unqid)";
                $fm['ordstps'] = "Error User($memail)";
            }
        }elseif(!empty($user->uinfo['uname'])){
            $fm['auser'] = $user->uinfo['uname'];
            $fm['ordstps'] = "Logined User($memail)";
        }else{
            $fm['auser'] = "($unqid)";
            $fm['ordstps'] = "Guest(-)";
        }*/
        $fm['auser'] = empty($user->uinfo['uname']) ? "($unqid)" : $user->uinfo['uname'];
        $fm['eip'] = comConvert::sysEncode(time().$unqid);
        // 加数据
        $db->table('coms_corder')->data(basReq::in($fm))->insert('a');
        // 转数据
        $db->table('coms_cocar')->data(array('ordid'=>$fm['cid'],'eip'=>$fm['eip']))->where("ordid='$unqid'")->update();
        $db->query("INSERT INTO {$db->pre}coms_coitem{$db->ext} SELECT * FROM {$db->pre}coms_cocar{$db->ext} WHERE ordid='{$fm['cid']}'");
        $db->table('coms_cocar')->where("ordid='{$fm['cid']}'")->delete();
        // 重置状态
        comCookie::oset('ocar_items',0);
        return array('ordid'=>$fm['cid'],'feetotle'=>@$fm['feetotle'],'enc'=>$fm['eip']);
    }
    
    static function odel($ordid){ 
        $db = glbDBObj::dbObj();
        $ouser = self::whruser();
        $where = $ouser['sql'];
        $erow = $db->table('coms_corder')->where("cid='$ordid' AND $where")->delete();
        $erow && $db->table('coms_coitem')->where("ordid='$ordid'")->delete();
        return $erow;
    }
    static function oedit($ordid){ 
        $ouser = self::whruser();
        $where = $ouser['sql'];
        $fm = basReq::arr('fm');
        if(empty($fm)) return 0; // ??? 
        $erow = glbDBObj::dbObj()->table('coms_corder')->data(basReq::in($fm))->where("cid='$ordid' AND $where")->update();
        return $erow;
    }
    
    static function iadd($unqid,$user){ 
        $db = glbDBObj::dbObj();
        //$fm['cid'] = basReq::val('cid');
        $fm['pid'] = basReq::val('pid');
        $fm['ordid'] = $unqid;
        $fm['ordcnt'] = basReq::val('ordcnt','0','N');
        $fm['ordprice'] = basReq::val('ordprice','0','N');
        $fm['title'] = basReq::val('title');
        $fm['ordweight'] = basReq::val('ordweight','0','N');
        //$fm['ordvolume'] = basReq::val('ordvolume','0','N');
        //$fm['weight2'] = basReq::val('weight2','0','N');
        //$fm['freeship'] = basReq::val('freeship','0','N');
        $cfgs = basReq::arr('cfgs','Html');
        $fm['detail'] = basReq::in(json_encode($cfgs));
        $kar = glbDBExt::dbAutID('coms_cocar');
        $fm['cid'] = $kar[0]; 
        $fm['cno'] = $kar[1];
        $fm['auser'] = @$user->uinfo['uname'];
        if($db->table('coms_cocar')->where("ordid='$unqid' AND pid='$fm[pid]'")->find()){
            $msg = "Item exists already!";
        }else{
            $res = $db->table('coms_cocar')->data($fm)->insert('a');
            if($res){
                $msg = $fm['title'].' : Add to car OK!';
            }else{
                $msg = "Error, Please try again!";
                //$db->table('coms_cocar')->where("ordid='$unqid' AND pid='$fm[pid]'")->delete();
            }
        }
        return $msg;
    }
    
    static function ilist($tabid,$where,$limit=99){ 
        $list = glbDBObj::dbObj()->table($tabid)->where($where)->limit($limit)->select();
        $data = array(); $aweight2 = 0.0; $freeship = 1;
        $afee = 0.0; $aweight = 0.0; $avolume = 0.0; $acnt = 0; 
        if($list){ foreach($list as $i=>$r){ 
            $r['i'] = $i+1;
            $r['ifee'] = $r['ordcnt']*$r['ordprice'];
            $afee += $r['ifee'];
            $aweight += $r['ordcnt']*$r['ordweight'];
            //$avolume += $r['ordvolume'];
            //$aweight2 += max($r['ordweight'],$r['weight2']);
            $acnt += $r['ordcnt'];
            $r['ifee'] = basReq::fmtNum($r['ifee']);
            $r['ordprice'] = basReq::fmtNum($r['ordprice']);
            $r['ordweight'] = basReq::fmtNum($r['ordweight']);
            if(empty($r['freeship'])) $freeship = 0;
            $data[] = $r;
        } } 
        $sum = array('afee'=>basReq::fmtNum($afee),
            'acnt'=>$acnt,'aweight'=>$aweight,'freeship'=>$freeship);
        return array('data'=>$data,'sum'=>$sum);
    }
    
    
}
