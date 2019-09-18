<?php
namespace imcat;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * phpmailer
 */
class emlPhpmail{
    
    public $cfg = [];
    public $umail = null;

    // 初始化
    function __construct($cfg=array()){
        $this->cfg = $cfg;
        $this->umail = new PHPMailer(true); 
        $this->umail->SMTPOptions = array(  
            'ssl' => array(  
                'verify_peer' => false,  
                'verify_peer_name' => false,  
                'allow_self_signed' => true,  
            )  
        );
        $this->umail->IsSMTP(); 
        $this->umail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码 
        $this->umail->SMTPAuth = true; //开启认证
        if(!empty($this->cfg['ssl'])){
            $this->umail->SMTPSecure = 'ssl'; // 加密
        }
        $this->umail->Host = $this->cfg['smtp']; 
        $this->umail->Port = $this->cfg['port']; 
        $this->umail->Username = $this->cfg['user']; 
        $this->umail->Password = $this->cfg['pass'];
    }
    
    function send($to, $title, $body, $vname=''){
        //$this->umail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示 
        #$this->umail->AddReplyTo("phpddt1990@163.com","mckee");//回复地址 
        $this->umail->From = $this->cfg['from']; 
        $this->umail->FromName = $vname; 
        if(strpos($to,',')){
            $toa = explode(',',$to);
            foreach($toa as $to1){
                if(!empty($to1)) $this->umail->AddAddress($to1); 
            }
        }else{
            $this->umail->AddAddress($to); 
        }
        if(!empty($this->cfg['cc'])){
            $this->umail->addCC($this->cfg['cc']);
        }
        if(!empty($this->cfg['re_addr'])){
            $this->umail->addReplyTo($this->cfg['re_addr'], $this->cfg['re_name']);
        }
        $this->umail->Subject = $title;
        $this->umail->Body = $body; 
        #$this->umail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略 
        #$this->umail->WordWrap = 80; // 设置每行字符串的长度 
        #$this->umail->AddAttachment("f:/test.png"); //可以添加附件 
        $this->umail->IsHTML(true); 
        try {
            $this->umail->Send(); 
            $re = 'SentOK';
        } catch (phpmailerException $e) {
            $re = $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            $re = $e->getMessage(); //Boring error messages from anything else!
        }
        return $re;
    }

}

