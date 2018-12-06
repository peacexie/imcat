<?php
namespace imcat;

//cp错误异常类
class glbError extends \Exception {

    private $erMsg='';
    private $erFile = '';
    private $erLine = 0;
    private $erCode = 0;
    private $erLevel = 0;
    private $trace = array();
    private $trMsgs = '';
    private $dbMsgs = '';

    //抛出错误信息，用于外部调用
    static function show($msg="",$code=0) {
         new self($code,$msg); 
    }

    function __construct($erCode=0,$erMsg='',$erFile='',$erLine=0) { 
        parent::__construct(); //$erMsg,$erCode
        $this->detail($erCode,$erMsg,$erFile,$erLine);
        $this->errView(); 
    }

    //detail
    protected function detail($erCode=0,$erMsg='',$erFile='',$erLine=0) {
        $msgmy = $msgphp = ''; 
        if(is_array($erMsg)){ // && $erMsg['msg']=='dberr'
            unset($erMsg['msg']);
            $tInfo = ''; 
            foreach($erMsg as $ek=>$ev) {
                $ev = basDebug::hidInfo($ev,1);
                if($ek=='sql'){
                    $ev = basSql::fmtShow($ev,2);
                    $tInfo .= "<li><div class='divta' contenteditable='true'>$ev</div></li>\n";
                }else{
                    $tInfo .= "<li>$ek : $ev </li>\n";
                }
            }
            $this->dbMsgs = $tInfo;
            $this->erCode = $erCode;
        }elseif(is_object($erCode)){ 
            $this->erMsg = $erCode->getMessage();
            $this->erFile = basDebug::hidInfo($erCode->getFile());
            $this->erLine = $erCode->getLine();
            $this->trace = $erCode;
            $this->erCode = -24;
        }elseif(!empty($erFile)){
            $this->erMsg = $erMsg;
            $this->erCode = $erCode;
            $this->erFile = basDebug::hidInfo($erFile);
            $this->erLine = $erLine;
        }elseif(!empty($erMsg)){
            $this->erMsg = $erMsg;
            $this->erCode = $erCode;
        }else{ 
            $errs = error_get_last(); 
            if(!empty($errs)){
                $this->erMsg = $erMsg ? $erMsg : "<i>Message</i>: ".$errs['message']."<br>";
                $this->erFile = basDebug::hidInfo($errs['file']);
                $this->erLine = $errs['line'];
                $this->erCode = $errs['type'];
            }
        }
        $this->dtrace();
    }
    
    //获取trace信息
    protected function dtrace() { 
        $trace = empty($this->trace) ? $this->getTrace() : $this->trace;
        $tInfo = '';
        foreach($trace as $t) {
            $class = isset($t['class']) ? $t['class'] : '';
            $type = isset($t['type']) ? $t['type'] : '';
            $function = isset($t['function']) ? $t['function'] : '';
            $tInfo .= "<li>".@$t['file'] . ' (' . @$t['line'] . ') ';
            $tInfo .= $class . $type . $function . "</li>\n";
        }
        $this->trMsgs = $tInfo ;
    }
    
    //错误等级
    protected function getLevel() {
       $arr = array(    
            1=> '(E_ERROR)',
            2 => '(E_WARNING)',
            4 => '(E_PARSE)',  
            8 => '(E_NOTICE)',  
            16 => 'E_CORE_ERROR',  
            32 => 'E_CORE_WARNING',  
            64 => '(E_COMPILE_ERROR)', 
            128 => '(E_COMPILE_WARNING)',  
            256 => '(E_USER_ERROR)',  
            512 => '(E_USER_WARNING)', 
            1024 => '(E_USER_NOTICE)',  
            2047 => 'E_ALL', 
            2048 => 'E_STRICT',
            '-24' => 'EXCEPT_HANDLER',
        );
        return isset($arr[$this->erCode]) ? $arr[$this->erCode] : $this->erCode;
    }
    
    //输出错误信息
     protected function errView($message=''){
        //if(defined('DEBUG') && false == DEBUG){ exit; } 
        $message = empty($message) ? $this->erMsg : $message;
        $message = basDebug::hidInfo($message);
        $sCode = empty($this->erCode) ? '[RUN]' : $this->erCode;
        $sLevel = $this->getLevel();
        $sLevel = empty($sLevel) ? '[RUN]' : $this->getLevel();
        $title = empty($this->dbMsgs) ? basLang::show('stinc_errun')." : $sCode" : basLang::show('stinc_erdbsql')." $sCode";
        $traceInfo = basDebug::hidInfo($this->trMsgs);
        $dberrInfo = basDebug::hidInfo($this->dbMsgs);
        glbHtml::httpStatus('404');
        //define('RUN_AJAX',1); 
        if(defined('RUN_AJAX')){
            ob_start(); 
            include vopTpls::cinc("base:stpl/err_ajax");
            $re = ob_get_contents(); 
            $re = strip_tags($re,'<li>');
            $re = str_replace(array('<li>','</li>'),array("  - ",""),$re);
            ob_clean();
            die($re); 
        }else{
            #dump($traceInfo);
            include vopTpls::cinc("base:stpl/err_info");
            die();
        }
    }
}