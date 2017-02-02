<?php
(!defined('RUN_INIT')) && die('No Init');
// 事件响应操作
// 如果本系统修改,就改这个文件，不用改wmp*文件

class wysQrcode extends wmpQrcode{

    public $_db = NULL;
    public $qrexpired = '5'; //5分钟过期...
    public $uniqueid = '';
    //public $maxsid = '2147000648'; //2^32=4,294,967,296; 2^31=2,147,483,648;  //mt_rand(): max(-5090297) is smaller than min(1001000123) 

    function __construct($wecfg){ 
        parent::__construct($wecfg); 
        $this->uniqueid = usrPerm::getUniqueid();
        $this->_db = db();
    }

    function getQrcode($smod, $type='temp', $extp=''){
        return $type=='temp' ? $this->getQTemp($smod,$extp) : $this->getQLimit($smod,$extp);
        /*
        这些数字区间，看怎样规划更合理...??? 
        // 1-10000               : 1-1万(固定二维码:保留区)
        // 10001-99999           : 10001-99999(固定二维码:使用区)
        //  100000<1000123       : 10-100万 保留(固定)
        // [1-9]+[999,999]       : 测试(临时二维码)
        // [100-428]+[9,999,999] : 正式使用(临时二维码)
        // 同类sid,5分钟内获取一个相同的ID,10分中后失效,
        
        //login
        //sendaid_7654321
        //sendmid_1234567
        //uparc_3
        //upcu_4
        */
    }

    // 正式使用(临时二维码) [100-428]+[9,999,999] : 
    // 同类sid,5分钟内获取一个相同的ID,10分中后失效,(设置给微信的为最大值：7天（即604800秒）), 定时清理1天内的数据
    function getQTemp($smod, $extp=''){
        $stamp = time();
        $timeNmin = $stamp-($this->qrexpired*60); //5分钟
        $row = $this->_db->table('wex_qrcode')->where("auser='$this->uniqueid' AND smod='$smod' AND atime>'$timeNmin'")->find();
        if($row){ 
            $this->_db->table('wex_qrcode')->data(array('atime'=>$stamp,'extp'=>$extp,))->where("auser='$this->uniqueid' AND smod='$smod'")->update();
            $sid = $row['sid']; 
            $ticket = $row['ticket']; 
        }else{
            $sid = mt_rand(100,428).mt_rand(1000123,4967296);
            while($this->_db->table('wex_qrcode')->where("sid='$sid'")->find()){
                $sid = mt_rand(100,428).mt_rand(1000123,4967296); //如果访问量很多，是否计数等待??? 
            }
            $qrdata = $this->qrcodeTicket($sid, 'temp'); 
            $ticket = $qrdata['ticket']; 
            $data = array(
                'sid' => $sid,
                'smod' => $smod,
                'extp' => $extp, //Label
                'ticket' => $ticket,
                'atime' => $stamp,
                'auser' => $this->uniqueid,
            ); 
            $this->_db->table('wex_qrcode')->data(basReq::in($data))->insert();
        }
        $ret = array('sid'=>$sid, 'ticket'=>$ticket, 'url'=>$this->qrcodeShowurl($ticket));
        return $ret;
    }

    // 正式使用(固定二维码) [10012,99987] :
    // 同类sid,5分钟内获取一个相同的ID,10分中后失效, (不用清理)
    function getQLimit($smod, $extp=''){
        $stamp = time();
        $timeNmin = $stamp-($this->qrexpired*60); //5分钟
        $row = $this->_db->table('wex_qrcode')->where("auser='$this->uniqueid' AND smod='$smod' AND atime>'$timeNmin'")->find();
        if($row){ 
            $this->_db->table('wex_qrcode')->data(array('atime'=>$stamp,'extp'=>$extp,))->where("auser='$this->uniqueid' AND smod='$smod' AND atime>'$timeNmin'")->update();
            $sid = $row['sid']; 
            $ticket = $row['ticket'];
        }else{
            $sid = mt_rand(10012,99987);  
            while($this->_db->table('wex_qrcode')->where("sid='$sid' AND atime>'$timeNmin'")->find()){
                $sid = mt_rand(10012,99987); //如果访问量很多，是否计数等待??? 
            }
            $row = $this->_db->table('wex_qrcode')->where("sid='$sid'")->find(); //可能存在，可能不存在...
            $data = array(
                'smod' => $smod,
                'extp' => $extp, //Label
                'atime' => $stamp,
                'auser' => $this->uniqueid,
            ); //print_r($row);
            if(empty($row['ticket'])){
                $qrdata = $this->qrcodeTicket($sid, 'fnum'); 
                $ticket = $qrdata['ticket']; 
                $data = $data + array(
                    'sid' => $sid,
                    'ticket' => $ticket,
                ); 
                $this->_db->table('wex_qrcode')->data(basReq::in($data))->insert();
            }else{
                $ticket = $row['ticket'];
                $this->_db->table($tabid)->data($data)->where("sid='$sid'")->update();  
            }

        }
        $ret = array('sid'=>$sid, 'ticket'=>$ticket, 'url'=>$this->qrcodeShowurl($ticket));
        return $ret;
    }

}
