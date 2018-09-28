<?php
namespace imcat;

// Order-Car : 外贸/前后台扩展部分
class devOcar{    

    static function areaOpts($def='',$re='html'){
        $html = comFiles::get(DIR_SKIN.'/_pub/stpl/psw-ships.htm');
        if($re=='arr'){ // psw-areas.htm,psw-ships.htm
            preg_match_all("/='([\w\-\ ]+)'/", $html, $ma);
            $res = array(); unset($ma[1][0]);
            foreach ($ma[1] as $val) {
                if(strstr($val,'---')){
                    $res["^group^$val"] = $val;
                }else{
                    $res[$val] = $val;
                }
            }
        }else{
            $res = $html;
            if($def){
                $res = str_replace('<!--nowItem-->',"<option value='$def' checked>$def</option>",$res);
            } 
        } 
        return $res;
        /*<select>
            <?=devOcar::areaOpts('SDSDSSD');?>
        </select>*/
    }

    // optEdit
    static function shipFee($to,$ship,$weight,$rate){
        $rc = glbConfig::parex('user_else'); //dump($rc);
        $usd_rate = isset($rc['usd_rate']['numb']) ? $rc['usd_rate']['numb'] : 6.6;
        $sfee_rate = isset($rc['sfee_rate']['numb']) ? $rc['sfee_rate']['numb'] : 1.25;
        $rate = max($sfee_rate,floatval($rate));
        $params = "country=".urlencode($to)."&mode=$ship&weight=$weight";
        $url = "http://www.sendfromchina.com/shipfee/out_rates/?$params";
        $data = comHttp::curlGet($url,3);
        $arr = comParse::nodeParse($data); //dump($arr);
        if(empty($arr['rates']['rate']['totalfee'])) return array();
        $res = isset($arr['rates']['rate']) ? $arr['rates']['rate'] : array();
        $res['totalfee'] = isset($res['totalfee']) ? $res['totalfee'] : 0;
        $res['totalfee'] = $res['totalfee'] * $rate / $usd_rate; // 参数...
        $res['totalfee'] = round($res['totalfee'],2); 
        $res['ures'] = "US\${$res['totalfee']} &nbsp; 
            WorkDay:".@$res['deliverytime']." &nbsp; 
            Tracking:".@$res['iftracking']; 
        return $res;
    }

    // optEdit
    static function batPrice($pbat=''){
        if(empty($pbat)) return;
        $a1 = explode(';',$pbat);
        $re = "<table class='table table-bordered table-condensed pbat'>"; //  Dispatch
        $r0 = "<tr class='active'><th colspan='".(count($a1)+1)."'>Wholesale</hd></tr>";
        $r1 = "<th>Count</th>";
        $r2 = "<th>Price</th>";
        foreach ($a1 as $row) {
            $a2 = explode(':',$row);
            $r1 .= "<td>$a2[0]+</td>";
            $r2 .= "<td>\$$a2[1]</td>";
        }
        return "$re$r0<tr id='pbat1'>$r1</tr><tr id='pbat2'>$r2</tr></table>";
    }

    // optEdit
    static function optsEdit($max=6, $vals=array()){
        $re = "<table border='1' class='tbdata'>";
        for($i=11;$i<11+$max;$i++){
            $val = empty($vals["opt$i"]) ? '' : $vals["opt$i"];
            $ar1 = explode('^', $val);
            $name = $ar1['0']; $ops = "";
            if(!empty($name)){
                for ($j=1;$j<count($ar1);$j++) {
                    $ops .= (empty($ops) ? '' : "\n").$ar1[$j]."";
                }
            }
            $istr = ($i-10)." <input name='mopt$i' id='mopt$i' type='text' value='$name' class='w320 ma5' maxlength='120'>";
            $istr .= "<textarea name='copt$i' id='copt$i' rows=5 class='w350' wrap='OFF'>$ops</textarea>";
            $istr = (($i%2)==1 ? '<tr>' : '')."<td>$istr</td>".(($i%2)==0 ? '</tr>' : '');
            $re .= $istr;
        }
        return "$re</table>";
    }

    static function optsSave($max=6){
        $re = array();
        $a1 = array("^"," @ ","\r\n","\r","\n","^^");
        $a2 = array("`","@","^","^","^","^");
        for($i=11;$i<11+$max;$i++){
            $itm1 = trim(req("mopt$i"));
            $itm1 = str_replace("^","`",$itm1);
            $itm2 = trim(req("copt$i",'','Html'));
            $itm2 = str_replace($a1,$a2,$itm2);
            $itm1 && !empty($itm2) && $re["opt$i"] = "$itm1^$itm2";
        }
        return $re;
    }

    static function optsView($vals){
        $re = ''; //dump($vals); 
        for($i=11;$i<25;$i++){
            if(empty($vals["opt$i"])) continue;
            $val = $vals["opt$i"];
            $ar1 = explode('^', $val); 
            $name = trim($ar1['0']); $ops = "";
            if(empty($name)) continue;
            $itm = "<select name='cfgs[$name]' class='form-control ma3'>\n";
            $itm .= "<option value='' vext=''>- $name -</option>\n";
            for ($j=1;$j<count($ar1);$j++) {
                $tmp = explode('@', $ar1[$j].'@'); 
                $tmp[0] = trim($tmp[0]);
                if(empty($tmp[0])) continue;
                $tmp['1'] = preg_replace("/[^\d|\.|\,]/", '', $tmp['1'].',,,,,');
                $tmp['1'] = str_replace(array(",,",",,"), array(',0,',',0,'), $tmp['1']); 
                if($tmp['1'][0]==',') $tmp['1'] = '0'.$tmp['1']; //echo "\n({$tmp['1']})\n";
                $tmv = explode(',', trim($tmp['1']));
                $tmsg = $tmp['0'].(empty($tmv[0]) ? '' : " (+\${$tmv[0]})");
                $itm .= "<option value='".$tmp['0']."' vext='".@$tmp['1']."'>".$tmsg."</option>\n";
            }
            $re .= "$itm</select>\n";
        }
        return $re;
    }

    static function setOrdstps($ordstps, $act){
        $ordstps = (empty($ordstps) ? '' : $ordstps.',').$act;
        if(strlen($ordstps)>120) $ordstps = substr($ordstps,0,60).'...'.substr($ordstps,-20);
        $ordstps = str_replace($act.','.$act, $act, $ordstps);
        return $ordstps;
    }

    static function setEncs($ordid, $eip){
        $stamp = time();
        $encs['custom'] = comConvert::sysRevert("$ordid.$stamp.$eip");
        $encs['cancel'] = comConvert::sysRevert("$ordid.$stamp.cancel");
        $encs['return'] = comConvert::sysRevert("$ordid.$stamp.return");
        $encs['webok']  = comConvert::sysRevert("$ordid.$stamp.webok");
        $encs['repay']  = comConvert::sysRevert("$ordid.$stamp.repay");
        return $encs;
    }

    static function setPpdata($order){ 
        $encs = devOcar::setEncs($order['cid'], $order['eip']);
        $feetotle = $order['feetotle'] - $order['feedis'];
        $ch2 = basKeyid::kidRand('A',2);
        $data = array(
            'mc_gross' => $feetotle,
            'protection_eligibility' => 'Eligible',
            'address_status' => 'confirmed',
            'payer_id' => 'SJ3'.basKeyid::kidRand('A',10),
            'address_street' => '', //'address_street.'.basKeyid::kidRand(),
            'payment_date' => date('Y-m-d H:i:s'),
            'payment_status' => 'Completed',
            'charset' => 'utf-8',
            'address_zip' => basKeyid::kidRand('0',5),
            'first_name' => '', // Peace
            'mc_fee' => '0.'.basKeyid::kidRand('0',2),
            'address_country_code' => '', //$ch2,
            'address_name' => '', // Peace Xie
            'notify_version' => '3.8',
            'custom' => $encs['custom'],
            'payer_status' => 'unverified',
            'business' => '13790321373@163.com',
            'address_country' => '', //'address_country.'.basKeyid::kidRand(),
            'address_city' => '', //'Seoul',
            'quantity' => '1',
            'verify_sign' => basKeyid::kidRand('A',10).'.'.basKeyid::kidRand('k',45),
            'payer_email' => '', // peace@txjia.com
            'txn_id' => '6YJ'.basKeyid::kidRand('A',14),
            'payment_type' => 'instant',
            'last_name' => '', // Xie
            'address_state' => '', //'address_state.'.basKeyid::kidRand(),
            'receiver_email' => '13790321373@163.com',
            'payment_fee' => '0.'.basKeyid::kidRand('0',2),
            'receiver_id' => '668'.basKeyid::kidRand('A',10),
            'txn_type' => 'web_accept',
            'item_name' => $order['cid'],
            'mc_currency' => 'USD',
            'item_number' => '',
            'residence_country' => $ch2,
            'transaction_subject' => '',
            'payment_gross' => $feetotle,
            'ipn_track_id' => 'e25'.basKeyid::kidRand('0',10),
        );
        return $data;
    }

    static function sendMail($act,$order){ 
        global $_cbase;
        $ucfg = read('user','sy'); 
        $sys_name = cfg('sys_name'); 
        $re3['act'] = $act;
        $re3['site'] = $sys_name;
        $re3['time'] = date('Y-m-d H:i'); 
        $re3['root'] = $_cbase['run']['rmain'];
        $re3 = $re3 + $order;
        // tpl,email
        $detail = vopTpls::show($ucfg['utpls']['mail-order'],'',$re3);
        $mail = new extEmail();
        //dump($detail);
        /*
        $order['memail'] = 'xpigeon@163.com';
        //$order['memail'] = '80893510@qq.com';
        //*/
        $rem = $mail->send($order['memail'],"Order Notice({$re3['cid']})",$detail,$sys_name);
        // log,return
        if($rem=='SentOK'){
            $msg = "Send Order Notice Mail [SentOK]";
        }else{
            $msg = "Send Mail Error ($rem)";
        }
        // $mail->slog(1,$cfgs);
        // mail-end
        return $msg;
    }

}
