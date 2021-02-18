<?php
namespace imcat\hi;

use imcat\basDebug;
use imcat\comParse;
use imcat\extWework;
use imcat\glbDBExt;

/*
*/ 
class weworkCtrl{
    
    public $ucfg = array();
    public $vars = array();

    public $wecfg = array();
    public $AppId = '';
    public $acfg = array();

    //function __destory(){  }
    function __construct($ucfg=array(),$vars=array()){ 
        $this->ucfg = $ucfg;
        $this->vars = $vars;
        //$this->test1();
        $this->check();
        //$this->getmsg();
    }

    function Key1000002(){
        echo('Run.Key1000002');
        //header('Location:'."?user");
    }
    function KeyAppCS(){
        echo('Run.KeyAppCS');
        //header('Location:'."?user");
    }
    
    function _defAct(){
        //die('_defAct');
        $re['tplnull'] = 1; // 设置不需要模板
        return $re;
        //header('Location:'."?user");
    }

    function check(){
        // check appid
        $this->wecfg = read('wework', 'ex');
        if(empty($this->wecfg['isOpen'])){
            die('请配置:[ex_wework.php]:isOpen=1');
        }
        $this->AppId = $this->ucfg['key']; 
        $wew = new extWework($this->AppId);
        if($sVerifyEchoStr = urldecode(req('echostr'))){
            $wew->chkUrl($sVerifyEchoStr);
        }elseif($encMesg=file_get_contents("php://input")){
            $res = $wew->getMsg($encMesg);
            if($res['errCode']){
                $strMsg = $res['strMsg']; // TODO: 对明文的处理
                basDebug::bugLogs("msg-Error", $res, "msg-Error".$res['errCode'].".log", 'db');
            }else{
                // basDebug::bugLogs("msg-OK", $msg, "msg-OK.log", 'db');
                $msg = comParse::nodeParse($res['strMsg']);
                $ev = isset($msg['Event']) ? $msg['Event'] : ''; $ag = $msg['AgentID'];
                $method = "{$ev}_{$ag}";
                if(method_exists($this,$method)){
                    $this->$method($msg);
                }
            }
        }
    }

    function open_approval_change_1000019($msg){ 
        if(isset($msg['ApprovalInfo']['OpenSpStatus'])){
            $status = $msg['ApprovalInfo']['OpenSpStatus'];
            $thirdNo = $msg['ApprovalInfo']['ThirdNo']; $tmp = explode('.', "$thirdNo..");
            if($thirdNo){
                $step = $msg['ApprovalInfo']['ApproverStep'];
                $appUid = $msg['ApprovalInfo']['ApplyUserId']; // 发布人
                $nowUid = $this->getNowNode($msg, $step, 'ItemUserId');
                {
                    $loga = ['exno'=>$status, 'mname'=>$nowUid]; // exmsg exno
                    db()->table('coms_cslogs')->data($loga)->where("exmsg='$thirdNo'")->update();
                    if($status=='1'){ // 审批中
                        $doc = ['douid'=>$nowUid]; // 
                        db()->table('docs_cstask')->data($doc)->where("did='{$tmp[1]}'")->update();    
                    }
                }
                if($status!='1'){ // ['1'=>'审批中', '2'=>'已通过', '3'=>'已驳回', '4'=>'已取消'];
                    $tab = ['1'=>'审批中', '2'=>'已通过', '3'=>'已驳回', '4'=>'已取消']; // 4撤销
                    $sName = isset($tab[$status]) ? $tab[$status] : "($status)";
                    $kar = glbDBExt::dbAutID('coms_cslogs');
                    $logb = [
                        'cid' => $kar[0], 'cno' => $kar[1], 'pid' => $tmp[1], 'exmsg' => '',
                        'mflag'=>'redo', 'title'=>"$sName,系统重新派工", 'mname'=>$appUid, 'auser'=>$nowUid
                    ]; 
                    db()->table('coms_cslogs')->data($logb)->insert();
                    // update 状态
                    $doc = ['mflag'=>'redo', 'douid'=>$appUid];
                    db()->table('docs_cstask')->data($doc)->where("did='{$tmp[1]}'")->update();
                    // 发信息
                    $title = "工单({$tmp[1]})$sName,重新派工";
                    $row = ['did'=>$tmp[1], 'title'=>$title, 'mflag'=>'redo'];
                    #tex('texBase')->msgSend($row, 'AppCS', '1', [$nowUid]); 
                }
            }
            basDebug::bugLogs("appCS-ok", $loga, "appCS-ok.log", 'db');
        }else{
            basDebug::bugLogs("appCS-err", $msg, "appCS-err.log", 'db');
        }
        die('');
    }

    function getNowNode($msg, $step='0', $key=''){ 
        $nodes = $msg['ApprovalInfo']['ApprovalNodes']['ApprovalNode'];
        $item = [];
        if(isset($nodes['Items'])){
            $item = $nodes['Items']['Item'];
        }else{
            //$cnt = count($nodes);
            $item = $nodes[$step]['Items']['Item'];
        }
        return $key ? $item[$key] : $item;
    }

    function test1(){

        $strMsg = '<xml><ToUserName><![CDATA[ww16ac986b672da9fc]]></ToUserName><FromUserName><![CDATA[sys]]></FromUserName><CreateTime>1595665789</CreateTime><MsgType><![CDATA[event]]></MsgType><Event><![CDATA[open_approval_change]]></Event><AgentID>1000019</AgentID><ApprovalInfo><ThirdNo><![CDATA[b762585b87.2020-7q-hc01.purch10003]]></ThirdNo><OpenSpName><![CDATA[工单配件申请]]></OpenSpName><OpenTemplateId><![CDATA[9de288d16634b9fa1c043d741b3d337f_913900360]]></OpenTemplateId><OpenSpStatus>2</OpenSpStatus><ApplyTime>1595664322</ApplyTime><ApplyUserName><![CDATA[谢永顺]]></ApplyUserName><ApplyUserId><![CDATA[XieYongShun]]></ApplyUserId><ApplyUserParty><![CDATA[技服部/技术组]]></ApplyUserParty><ApplyUserImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ApplyUserImage><ApprovalNodes><ApprovalNode><NodeStatus>4</NodeStatus><Items><Item><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId><ItemStatus>4</ItemStatus><ItemSpeech><![CDATA[333]]></ItemSpeech><ItemOpTime>1595664357</ItemOpTime></Item></Items><NodeAttr>1</NodeAttr><NodeType>1</NodeType></ApprovalNode><ApprovalNode><NodeStatus>4</NodeStatus><Items><Item><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId><ItemStatus>4</ItemStatus><ItemSpeech><![CDATA[66]]></ItemSpeech><ItemOpTime>1595664484</ItemOpTime></Item></Items><NodeAttr>1</NodeAttr><NodeType>1</NodeType></ApprovalNode><ApprovalNode><NodeStatus>4</NodeStatus><Items><Item><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId><ItemStatus>4</ItemStatus><ItemSpeech><![CDATA[333]]></ItemSpeech><ItemOpTime>1595664672</ItemOpTime></Item></Items><NodeAttr>1</NodeAttr><NodeType>1</NodeType></ApprovalNode><ApprovalNode><NodeStatus>4</NodeStatus><Items><Item><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId><ItemStatus>4</ItemStatus><ItemSpeech><![CDATA[测试222]]></ItemSpeech><ItemOpTime>1595660600</ItemOpTime></Item></Items><NodeAttr>1</NodeAttr><NodeType>1</NodeType></ApprovalNode><ApprovalNode><NodeStatus>1</NodeStatus><Items><Item><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId><ItemStatus>1</ItemStatus><ItemSpeech><![CDATA[]]></ItemSpeech><ItemOpTime>1595660600</ItemOpTime></Item></Items><NodeAttr>1</NodeAttr><NodeType>1</NodeType></ApprovalNode></ApprovalNodes><NotifyNodes><NotifyNode><ItemName><![CDATA[谢永顺]]></ItemName><ItemImage><![CDATA[http://wework.qpic.cn/bizmail/BjC66Q9NCoDEXbnwPGDvDWR18el18XQSzcamWLrPibFVL7QpZ8VTFRA/0]]></ItemImage><ItemUserId><![CDATA[XieYongShun]]></ItemUserId></NotifyNode></NotifyNodes><ApproverStep>1</ApproverStep></ApprovalInfo></xml>';
        $msg = comParse::nodeParse($strMsg);

        if(isset($msg['ApprovalInfo']['ApprovalNodes']['ApprovalNode']['Items']['Item']['ItemUserId'])){
            echo $msg['ApprovalInfo']['ApprovalNodes']['ApprovalNode']['Items']['Item']['ItemUserId'];  
        }

        dump($msg); 
        die();
        // check-perm
        if($fm['douid_old']!==$this->revars['wew_uid']){ 
            //$fm['douid_old'] = 'PeaceXie';
            $row = ['did'=>$fm['did'], 'title'=>$fm['title'], 'mflag'=>$fm['mflag']];
            tex('texBase')->msgSend($row, 'AppCS', '1', [$fm['douid_old']]);
            echo basOut::outJson($res);
        }else{
            $res = ['errno'=>'NoPerm','errmsg'=>'没有催单权限'];
            echo basOut::outJson($res);
        }
        die('');
    }

}

/*


*/
