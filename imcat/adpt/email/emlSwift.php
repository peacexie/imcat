<?php
namespace imcat;

#use Swiftmailer\Swiftmailer\Swiftmailer;

/**
 * emlSwift
 */
class emlSwift{
    
    public $cfg = [];
    public $umail = null;
    public $message = null;

    // 初始化
    function __construct($cfg=array()){
        $this->cfg = $cfg;
        #require_once DIR_VENDOR.'/swiftmailer/swiftmailer/lib/swift_required.php';
        $class = '\Swift_SmtpTransport';
        extEmail::hasClass($class);
        $transport = (new $class($this->cfg['smtp'], $this->cfg['port']))
          ->setUsername($this->cfg['user']) //注意中转邮箱要和下面的From 邮箱一致
          ->setPassword($this->cfg['pass']);
        $this->umail = new \Swift_Mailer($transport); 
        #$logger = new Swift_Plugins_Loggers_EchoLogger();
        #$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger)); 
        #$body_str = comFiles::get('./_demo_mailer.htm');
    }
    
    function send($to, $title, $body, $vname=''){
        $this->message = new \Swift_Message($title); 
        $tos = array();
        if(strpos($to,',')){
            $toa = explode(',',$to);
            foreach($toa as $to1){
                if(!empty($to1)) $tos[$to1] = $to1; 
            }
        }else{
            $tos = array($to => $to); 
        }
        $from = $this->cfg['from']; 
        try {
            $this->message
            //->setEncoder(\Swift_Encoding::get8BitEncoding())
            //->setSubject($title) 
            ->setFrom(array($this->cfg['from'] => $from))
            ->setTo($tos)
            ->setBody($body, 'text/html');
            $re = $this->umail->send($this->message); 
        } catch (Exception $e) { 
            $re = $e->getMessage(); // Boring error messages from anything else!
        }
        return $re;
    }

}

