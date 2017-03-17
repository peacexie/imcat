<?php

// Ocar相关函数
// 各模版公用
class exvOcar{
    
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
        $stamp = time();
        $user = user('Member'); 
        $uadm = user('Admin'); 
        $enc = req('enc');
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
        $stamp = time()-$order['atime']; //<($flag=='Admin' ? 5*86400 : 30*60);
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
        $db = db();
        $fm = basReq::arr('fm');
        $kar = glbDBExt::dbAutID('coms_corder','yyyy-md-','32');
        $fm['cid'] = $fm['title'] = $kar[0]; 
        $fm['cno'] = $kar[1];
        $fm['ordstat'] = 'new';
        $fm['auser'] = @$user->uinfo['uname'];
        $fm['eip'] = comConvert::sysEncode(@$fm['atime'].$unqid);
        // 加数据
        $db->table('coms_corder')->data(basReq::in($fm))->insert();
        // 转数据
        $db->table('coms_cocar')->data(array('ordid'=>$fm['cid'],'eip'=>$fm['eip']))->where("ordid='$unqid'")->update();
        $db->query("INSERT INTO {$db->pre}coms_coitem{$db->ext} SELECT * FROM {$db->pre}coms_cocar{$db->ext} WHERE ordid='{$fm['cid']}'");
        $db->table('coms_cocar')->where("ordid='{$fm['cid']}'")->delete();
        // 重置状态
        comCookie::oset('ocar_items',0);
        return array('ordid'=>$fm['cid'],'enc'=>$fm['eip']);
    }
    
    static function odel($ordid){ 
        $db = db();
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
        $erow = db()->table('coms_corder')->data(basReq::in($fm))->where("cid='$ordid' AND $where")->update();
        return $erow;
    }
    
    static function iadd($unqid,$user){ 
        $db = db();
        $fm['cid'] = req('cid');
        $fm['pid'] = req('pid');
        $fm['ordid'] = $unqid;
        $fm['ordcnt'] = req('ordcnt','0','N');
        $fm['ordprice'] = req('ordprice','0','N');
        $fm['title'] = req('title','');
        $fm['ordweight'] = req('ordweight','0','N');
        $kar = glbDBExt::dbAutID('coms_cocar','yyyy-md-','32');
        $fm['cid'] = $kar[0]; 
        $fm['cno'] = $kar[1];
        $fm['auser'] = @$user->uinfo['uname'];
        if($db->table('coms_cocar')->where("ordid='$unqid' AND pid='$fm[pid]'")->find()){
            $msg = "该商品已经在购物车！";
        }else{
            $db->table('coms_cocar')->data(basReq::in($fm))->insert();
            $msg = $fm['title'].' : 添加成功！'; 
        }
        return $msg;
    }
    
    static function ilist($tabid,$where,$limit=99){ 
        $list = db()->table($tabid)->where($where)->limit($limit)->select();
        $data = array(); $afee = 0.00; $aweight = 0.00; $acnt = 0;
        if($list){ foreach($list as $i=>$r){ 
            $r['i'] = $i+1;
            $r['ifee'] = $r['ordcnt']*$r['ordprice'];
            $afee += $r['ifee'];
            $aweight += $r['ordweight'];
            $acnt += $r['ordcnt'];
            $r['ifee'] = basReq::fmtNum($r['ifee']);
            $r['ordprice'] = basReq::fmtNum($r['ordprice']);
            $r['ordweight'] = basReq::fmtNum($r['ordweight']);
            $data[] = $r;
        } } 
        return array('data'=>$data,'sum'=>array('afee'=>basReq::fmtNum($afee),'acnt'=>$acnt,'aweight'=>$aweight));
    }
    
    
}
