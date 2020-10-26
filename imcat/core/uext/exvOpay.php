<?php
namespace imcat;

// exvOpay相关函数
// 各模版公用
class exvOpay{

    static function getCfgs(){ 
        $cfg = array(
            'paydemo' => array(
                'dir' => 'paydemo_dir',
                'method' =>'Demopay',
            ),
            'alidirect' => array(
                'dir' => 'alipay_direct',
                'method' =>'AliDirect',
            ),
            'aliwscow' => array(
                'dir' => 'alipay_wscow',
                'method' =>'AliWscow',
            ),
            'aliwapdir' => array(
                'dir' => 'alipay_wapdir',
                'method' =>'AliWapdir',
            ),
            'tenpay' => array(
                'dir' => 'tenpay_sdk',
                'method' =>'Tenpay',
            ),
            'paypalec' => array(
                'dir' => 'paypal_ec',
                'method' =>'Paypal',
            ),
            'weixinpay' => array(
                'dir' => 'weixin_pay',
                'method' =>'Weixin',
            ),
        );
        return $cfg;    
    }
    
    static function getParas($order){ 
        $cfg = self::getCfgs();
        if(!isset($cfg[$order['ordpay']]['method'])) return array();
        $method = 'fmarr'.$cfg[$order['ordpay']]['method'];
        if(!method_exists('\imcat\exvOpay',$method)) return array();
        $arr = self::$method($order,$cfg[$order['ordpay']]);
        return $arr;
    }

    static function fmarrDemopay($order,$cfg){ 
        $arr = array();
        $arr['out_trade_no'] = $order['cid'];
        $arr['subject'] = "Web(".cfg('tpl.vdir').")".basLang::show('core.opay_order');
        $arr['total_fee'] = $order['feetotle'];
        $arr['ordbody'] = '-';
        $arr['showurl'] = '-';
        $arr['a']['apidir'] = $cfg['dir'];
        return $arr;
    }
    
    static function fmarrAliDirect($order,$cfg){ 
        $arr = array();
        $arr['ordid'] = $order['cid'];
        $arr['title'] = "Web(".cfg('tpl.vdir').")".basLang::show('core.opay_order');
        $arr['feetotle'] = $order['feetotle'];
        $arr['ordbody'] = '-';
        $arr['showurl'] = '-';
        $arr['a']['apidir'] = $cfg['dir'];
        return $arr;
    }
    static function fmarrAliWscow($order,$cfg){ 
        return self::fmarrAliDirect($order,$cfg);
    }
    static function fmarrAliWapdir($order,$cfg){ 
        $data = self::fmarrAliDirect($order,$cfg);
        $dcfg = array('receive_name'=>'mname','receive_address'=>'maddr','receive_zip'=>'123456','receive_phone'=>'mtel','receive_mobile'=>'mtel');
        foreach($dcfg as $key=>$val){
            $data[$k] = empty($order[$val]) ? $val : $order[$val];
        }
        return $data;
        /*
        //收货人姓名如：张三
        $receive_name = $_POST['receive_name'];
        //收货人地址如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        $receive_address = $_POST['receive_address'];
        //收货人邮编如：123456
        $receive_zip = $_POST['receive_zip'];
        //收货人电话号码如：0571-88158090
        $receive_phone = $_POST['receive_phone'];
        //收货人手机号码如：13312341234
        $receive_mobile = $_POST['receive_mobile'];
        */
    }
    static function fmarrTenpay($order,$cfg){ 
        $arr = array();
        $arr['out_trade_no'] = $order['cid'];
        $arr['subject'] = "Web(".cfg('tpl.vdir').")".basLang::show('core.opay_order');
        $arr['total_fee'] = $order['feetotle'];
        $arr['ordbody'] = '-';
        $arr['showurl'] = '-';
        $arr['a']['apidir'] = $cfg['dir'];
        return $arr;
    }
    
    static function notifyDemopay($flag,$expar=''){ 
        $data = array();
        $data['ordid'] = basReq::val('out_trade_no');
        $data['apino'] = basReq::val('trade_no');
        $data['ufrom'] = '-';
        $data['uto'] = '-';
        $data['amount'] = basReq::val('total_fee','0');
        $data['api'] = 'demopay';
        $data['stat'] = $flag;
        $data['auser'] = basReq::val('buyer_email');
        self::saveLoger($data,$expar);
    }
    
    static function notifyAliDirect($flag,$expar='',$api='alidirect'){ 
        $data = array();
        $data['ordid'] = basReq::val('out_trade_no');
        $data['apino'] = basReq::val('trade_no');
        $data['ufrom'] = '-';
        $data['uto'] = '-';
        $data['amount'] = basReq::val('total_fee','0');
        $data['api'] = $api;
        $data['stat'] = $flag;
        $data['auser'] = basReq::val('buyer_email');
        self::saveLoger($data,$expar);
    }
    static function notifyAliWscow($flag,$expar=''){ 
        self::notifyAliDirect($flag,$expar,'aliwscow');
    }
    static function notifyAliWapdir($flag,$expar=''){ 
        self::notifyAliDirect($flag,$expar,'aliwapdir');
    }
    static function notifyTenpay($flag,$expar=''){ 
        $data = array();
        $data['ordid'] = basReq::val('out_trade_no');
        $data['apino'] = basReq::val('trade_no');
        $data['ufrom'] = '-';
        $data['uto'] = '-';
        $data['amount'] = basReq::val('total_fee','0');
        $data['api'] = 'tenpay';
        $data['stat'] = $flag;
        $data['auser'] = basReq::val('buyer_email');
        self::saveLoger($data,$expar);
    }
    
    static function saveLoger($data,$expar=''){ 
        global $_cbase;
        $plog = $_cbase['debug']['pay_log'];
        if(empty($plog) && $data['stat']=='fail') return; //失败记录只在调试模式下记录
        //check 1.ordid在corder表? 2.from接口
        $data['kid'] = basKeyid::kidTemp('(def)');
        $expar && $data['expar'] = $expar;
        glbDBObj::dbObj()->table('plus_paylog')->data(basReq::in($data))->insert();
        basDebug::bugLogs('opay',$data,'detmp','db');
    }

    static function payDebug($fk, $fid=8){ 
        $cfgs = array('_GET','_POST');
        $logs = "";
        foreach ($cfgs as $k1) {
            $logs .= "    [$k1]\n";
            $v1 = $GLOBALS[$k1];
            if(empty($v1)){
                $logs .= "(null)\n";
            }else{
                foreach($v1 as $k2=>$v2){
                    $v2 = is_array($v2) ? json_encode($v2,1) : $v2;
                    $logs .= "$k2=[$v2]\n";
                }
            }

        }
        // fid: 
        if(is_numeric($fid) && $fid<100){
            $dno = (date('d')-1)/$fid; 
            $fid = date('m').(floor($dno)+1);
        }
        basDebug::bugLogs($fk,$logs,"$fk-$fid.txt",'file');
    }

/*
 
*/

}
