<?php
namespace imcat;

// $m = new extEmail(); 
// $s = 'v01-Mail:'.$_cbase['mail']['type'].' : 邮件 : '.date('Y-m-d H:i');
// $c = comFiles::get('./mail_demo.htm');
// $m->send('xpigeon@163.com',$s,$c,'fromName-测试-Test');

class extEmail{
    
    public $type = ''; //phpmailer,swiftmailer
    public $cfg = array(); //
    public $umail = null;
    public $log = array();
    
    function __construct($type='', $cfg=array()){
        $this->cfg  = empty($cfg) ? glbConfig::read('mail','ex') : $cfg;
        $this->type = empty($type) ? $this->cfg['type'] : $type;
        $this->setServer($cfg);        
    }
    
    function setServer($cfg=array()){
        $cfile = 'eml'.ucfirst($this->type);
        $fp = DIR_IMCAT."/adpt/email/$cfile.php";
        if(file_exists($fp)){ 
            require $fp; // 加载
            $class = "\\imcat\\$cfile";
            $this->umail = new $class($this->cfg); 
        }
    }
    
    function send($to, $title, $body, $vname=''){
        $res = $this->umail->send($to, $title, $body, $vname);
        $from = $this->cfg['from'];
        $alog = array('to', 'title', 'body', 'from', 'vname');
        foreach ($alog as $key) {
            $this->log[$key] = $$key;
        }
        return $res;
    }

    // 写记录
    function slog($stat=1, $cfgs=array()){
        $body = $this->log['body'];
        $body = strstr($body,'<body') ? basElm::getPos($body,'body') : $body;
        $body = trim(strip_tags($body)); 
        $kid = empty($cfgs['kid'])?basKeyid::kidTemp():$cfgs['kid'];
        $pid = empty($cfgs['pid'])?'':$cfgs['pid'];
        $data = array( 
            'kid'=>$kid,'pid'=>$pid,
            'ufrom'=>$this->log['from'],'uto'=>$this->log['to'],
            'title'=>$this->log['title'],'detail'=>basReq::in($body),
            'stat'=>$stat,'api'=>$this->type,
        );
        glbDBObj::dbObj()->table('plus_emsend')->data($data)->insert();
    }

    static function hasClass($class){
        //$class = '\PHPMailer\PHPMailer\PHPMailer';
        if(!class_exists($class)){
            $msg = "Class `$class` not found,<br>
                Please install it with composer, more info see:<br>
                http://custom.txjia.com/book.php/deeps-no3rd";
            die($msg);
        }
    }
}

