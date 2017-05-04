<?php

// $m = new extEmail(); 
// $s = 'v01-Mail:'.$_cbase['mail']['type'].' : 邮件 : '.date('Y-m-d H:i');
// $c = comFiles::get('./mail_demo.htm');
// $m->send('xpigeon@163.com',$s,$c,'fromName-测试-Test');

class extEmail{
    
    public $type = ''; //phpmailer,swiftmailer
    public $cfg = array(); //
    public $umail = null;
    public $message = null;
    public $re = '';
    public $log = array();
    
    function __construct($type='',$cfg=array()){
        $this->cfg  = empty($cfg) ? read('mail','ex') : $cfg;
        $this->type = empty($type) ? $this->cfg['type'] : $type;
        $this->setServer($cfg);        
    }
    
    function setServer($cfg=array()){
        $this->cfg = empty($cfg) ? $this->cfg : $cfg;
        if($this->type=='phpmailer'){
            require_once DIR_VENDOR.'/PHPMailer/PHPMailerAutoload.php';
            $this->umail = new PHPMailer(true); 
            $this->umail->IsSMTP(); 
            $this->umail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码 
            $this->umail->SMTPAuth = true; //开启认证 
            $this->umail->Host = $this->cfg['smtp']; 
            $this->umail->Port = $this->cfg['port']; 
            $this->umail->Username = $this->cfg['user']; 
            $this->umail->Password = $this->cfg['pass']; 
        }else{
            require_once DIR_VENDOR.'/swiftmailer/swiftmailer/lib/swift_required.php';
            $transport = Swift_SmtpTransport::newInstance($this->cfg['smtp'], $this->cfg['port'])
              ->setUsername($this->cfg['user']) //注意中转邮箱要和下面的From 邮箱一致
              ->setPassword($this->cfg['pass']);
            $this->umail = Swift_Mailer::newInstance($transport); 
            #$logger = new Swift_Plugins_Loggers_EchoLogger();
            #$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger)); 
            #$body_str = comFiles::get('./_demo_mailer.htm');
            $this->message = Swift_Message::newInstance(); 
        }
        //$this->umail = &$mail;
    }
    
    function send($to,$title,$detail,$from=''){
        $re = 'SentOK';
        $from || $from = $this->cfg['from'];
        if($this->type=='phpmailer'){
            try {
                //$this->umail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示 
                #$this->umail->AddReplyTo("phpddt1990@163.com","mckee");//回复地址 
                $this->umail->From = $this->cfg['from']; 
                $this->umail->FromName = $from; //"www.txjia.com"; 
                if(strpos($to,',')){
                    $toa = explode(',',$to);
                    foreach($toa as $to1){
                        if(!empty($to1)) $this->umail->AddAddress($to1); 
                    }
                }else{
                    $this->umail->AddAddress($to); 
                }
                $this->umail->Subject = $title;
                $this->umail->Body = $detail; 
                #$this->umail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略 
                $this->umail->WordWrap = 80; // 设置每行字符串的长度 
                #$this->umail->AddAttachment("f:/test.png"); //可以添加附件 
                $this->umail->IsHTML(true); 
                $this->umail->Send(); 
            } catch (phpmailerException $e) {
                $re = $e->errorMessage(); //Pretty error messages from PHPMailer
            } catch (Exception $e) {
                $re = $e->getMessage(); //Boring error messages from anything else!
            }
        }else{
            $tos = array();
            if(strpos($to,',')){
                $toa = explode(',',$to);
                foreach($toa as $to1){
                    if(!empty($to1)) $tos[$to1] = $to1; 
                }
            }else{
                $tos = array($to => $to); 
            }
            try {
                $this->message
                ->setEncoder(Swift_Encoding::get8BitEncoding())
                ->setSubject($title) 
                ->setFrom(array($this->cfg['from'] => $from))
                ->setTo($tos)
                ->setBody($detail, 'text/html');
                $result = $this->umail->send($this->message); 
            } catch (Exception $e) { 
                $re = $e->getMessage(); //Boring error messages from anything else!
            }
        }
        $alog = array('to','title','detail','from',);
        foreach ($alog as $key) {
            $this->log[$key] = $$key;
        }
        return $re;
    }

    // 写记录
    function slog($stat=1,$cfgs=array()){
        $detail = basElm::getPos($this->log['detail'],'body');
        //$detail = basElm::getPos($detail,'</head>(*)</html>'); // 保险
        $detail = trim(strip_tags($detail)); 
        $kid = empty($cfgs['kid'])?basKeyid::kidTemp():$cfgs['kid'];
        $pid = empty($cfgs['pid'])?'':$cfgs['pid'];
        $data = array( 
            'kid'=>$kid,'pid'=>$pid,
            'ufrom'=>$this->log['from'],'uto'=>$this->log['to'],
            'title'=>$this->log['title'],'detail'=>basReq::in($detail),
            'stat'=>$stat,'api'=>$this->type,
        );
        db()->table('plus_emsend')->data($data)->insert();
    }

}

