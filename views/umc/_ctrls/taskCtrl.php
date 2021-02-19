<?php
namespace imcat\umc;

use imcat\basEnv;
use imcat\basElm;
use imcat\basKeyid;
use imcat\basMsg;
use imcat\comConvert;
use imcat\basOut;
use imcat\basReq;
use imcat\extWework;
use imcat\glbDBExt;
use imcat\vopShow;

use imcat\vopApi as api;

/*
*/

class taskCtrl extends bcsCtrl{

    #public $re = [];

    //function __destory(){  }
    function __construct($ucfg=array(), $vars=array()){ 
        parent::__construct($ucfg, $vars);
        $this->init($ucfg, $vars);
    }

    function doLqas($exqa, $next=0){
        // 2020-09-04 12:03@ChenZhenHong@PeaceXie@陈振鸿^追问^谢永顺@打卡位置不对：罚！
        // 2020-09-27 10:33@XieYongShun@XieYongShun@谢永顺 补充@sss
        $tmp = explode("\n",$exqa);
        $res = [];
        foreach ($tmp as $itm) {
            $rt = explode('@', "$itm@@@@");
            $row['time'] = $rt[0];
            $row['from'] = $rt[1];
            $row['to'] = $rt[2];
            $row['names'] = $rt[3];
            $row['msg'] = $rt[4];
            if(!$next){ // 2020-11-16:统一显示`备注`
                #$row['names'] = str_replace(['补充','回复','追问'], '备注', $row['names']);
            }
            if($row['names'] && $row['msg']){
                $res[] = $row;
            }
        }
        return $res;
    }
    // doLogs
    function doLogs($did, $udoc=[]){
        $re = $this->re;
        $tabMflag = $re['vars']['tabMflag'];
        $data = data('cslogs',"pid='$did' AND `show`='all'",99,'atime-0');
        $doLogs = []; 
        $uFlags = ['hasDone'=>0, 'hasFee'=>0, 'hasScore'=>0, 'hasPaied'=>0, 'hasClose'=>0, 
            'lastMflag'=>0, 'lastUser'=>'', 'datetime'=>'', 'payFee'=>0, 'urow'=>[]];
        if(!empty($data)){ 
            //$exqn = ''; // 上一个exqn资料
            foreach ($data as $key => $row) {
                $row['exqnext'] = [];
                $row['mflagName'] = isset($tabMflag[$row['mflag']]) ? $tabMflag[$row['mflag']] : $row['mflag'];
                $row['urow'] = $this->urow($row['auser']); // 用户
                // 时间要求，打卡位置，费用，评分（备用）
                $row['exstr'] = $row['exno'] = ''; 
                if(in_array($row['mflag'],['apnew','aptime']) && !empty($row['exmsg'])){ // servchk
                    $datetime = $row['exmsg']; 
                    $uFlags['datetime'] = $datetime;
                    $row['exstr'] = "<span class='excss'>约定时间: {$datetime}</span>";
                }elseif($row['mflag']=='attapply'){ // 配件申请
                    $aprid = $row['exmsg']; 
                    $row['exstr'] = "<span class='excss' onclick=\"purchView('$aprid')\">查看</span>";
                    $tab = ['1'=>'审批中', '2'=>'已通过', '3'=>'已驳回', '4'=>'已取消']; // 4撤销
                    $tabmsg = isset($tab[$row['exno']]) ? $tab[$row['exno']] : '(未知状态)';
                    $row['exno'] = $row['exno'] ? "<span class='excss'>$tabmsg</span>" : '(未提交)';
                }elseif($row['mflag']=='done' && !empty($row['exmsg'])){ // 费用
                    $fee = $row['exmsg']; 
                    $row['exstr'] = "<span class='excss'>费用:{$fee}(￥)元</span>";
                    $uFlags['hasFee'] = 1;
                    $uFlags['payFee'] = $row['exmsg'];
                }elseif($row['mflag']=='paied'){ // 付款
                    $uFlags['hasFee'] = 1;
                }elseif($row['mflag']=='score' && !empty($row['exmsg'])){ // 评分
                    $score = $row['exmsg']; $cfg = $re['vars']['tabScore'][$score];
                    $row['exstr'] = "<span class='excss' style='background:#{$cfg['rgb']}'>{$score}分, {$cfg['text']}</span>";
                }elseif($row['mflag']=='served' && !empty($row['exmsg'])){ // map
                    $mapLink = tex('texBase')->mapLink($row['exmsg']);
                    $row['exstr'] = "<a class='excss curhand' href=".(extWework::isWework()?"'javascript:;' onclick=\"mapOpen('$row[exmsg]')\"":"'$mapLink'")." target='_map'>打开地图</a>";
                }
                if(!$row['exstr'] && !$row['exno'] && !$row['title']) $row['title'] = '(无备注)';
                $row['exqa'] = empty($row['exqa']) ? [] : $this->doLqas($row['exqa']);
                if(!empty($row['exqn'])){ $row['exqnext'] = $this->doLqas($row['exqn'],1); }
                //$exqn = $row['exqn'];
                if($retype=req('retype')){ 
                    $row = texBase::convRow($row);
                }
                $doLogs[$key] = $row;
                /* if(!$uFlags['lastMflag']){ // 第一个 对应 时间的最近一个 }*/
                //if($row['mflag']=='apnew') { $uFlags['datetime'] = $row['exmsg']; } 
                if($row['mflag']=='done') { $uFlags['hasDone'] = 1; } // 已完成
                if($row['mflag']=='score'){ $uFlags['hasScore'] = 1; } // 已评分
                if($row['mflag']=='paied'){ $uFlags['hasPaied'] = 1; } // 已付款
                if($row['mflag']=='close'){ $uFlags['hasClose'] = 1; } // 已关闭
                $uFlags['lastMflag'] = $row['mflag']; // 最后一个 对应 时间的最近一个
                $uFlags['lastUser'] = $row['auser']; // 最后一个 对应 时间的最近一个, auser,mname,
            } 
            $skip = ($row['auser']=='(scaner)') || ($row['mflag']=='close');
            if($row['auser']!==$row['mname'] && !$skip){
                $uFlags['urow'] = $this->urow($row['mname']); // 用户
            }
        } 
        return ['doLogs'=>$doLogs, 'uFlags'=>$uFlags];
    }

    function autoClose($fm, $re, $kar){ // score, paied, 
        $type = $fm['mflag']; $did = $fm['did'];
        $doc = ['mflag'=>$type, 'douid'=>$fm['douid']];
        db()->table('docs_cstask')->data($doc)->where("did='$did'")->update(); 
        $nofee = empty($re['vars']['uFlags']['hasFee']);
        $f1 = $type=='score' && $nofee; // 评分 且 无费用
        $f2 = $type=='score' && !$nofee && !empty($re['vars']['uFlags']['hasPaied']); // 评分 且 有费用 且 已付费
        $f3 = $type=='paied' && !empty($re['vars']['uFlags']['hasScore']); // 支付 且 评分
        if($f1 || $f2 || $f3){
            $kar1a = substr($kar[0],0,8); $kar1b = substr($kar[0],8,4);
            $title = '已评价,' . ($nofee ? "不收费" : "已付款");
            $loga = [
                'cid' => $kar1a.basKeyid::kidNext('',$kar1b,'1000',1,4), 'cno' => $kar[1], 'pid' => $did, 
                'mflag'=>'close', 'title'=>"$title,系统自动关闭", 'mname'=>$fm['douid'], 'auser'=>'(system)'
            ]; 
            db()->table('coms_cslogs')->data($loga)->insert();
            // update
            $doc = ['mflag'=>'close', 'douid'=>$fm['douid']];
            db()->table('docs_cstask')->data($doc)->where("did='$did'")->update(); 
        }
    }

    // 评分初始化,检查
    function scoreCheck(){
        $re = &$this->re;
        $re['vars']['row'] = [];
        $did = req('did'); $did || $did = basReq::ark('fm','did'); //$fm['did'];
        $re['vars']['did'] = $did;
        $row = data('cstask.join',"did='$did'",1);
        $re['vars']['row'] = $row;
        $re['vars']['whrstr'] = "pid='$did'"; //  AND (m.show IN('0','1'))
        // doLogs
        $tmp = $this->doLogs($did, $row); 
        #$tmp['uFlags']['hasScore'] = 0; # forTest
        #$tmp['uFlags']['hasPaied'] = 0; # forTest
        $re['vars']['doLogs'] = $tmp['doLogs'];
        $re['vars']['uFlags'] = $tmp['uFlags'];
        // check
        $tab = ['hasClose'=>'已关闭,不能再评分!', 'hasScore'=>'已评分,不能再评分!'];
        foreach($tab as $tk=>$msg) {
            if($tmp['uFlags'][$tk]){
                $res = ['errno'=>$tk, 'errmsg'=>$msg];
                //echo basOut::outJson($res);
                $re['vars']['errtip'] = "($tk)$msg";
                $re['vars']['errmsg'] = $re['vars']['umsg'] = $msg;
                $re['newtpl'] = 'home/info'; // 设置模板
                return $re;
            }
        }
        if(!in_array($tmp['uFlags']['lastMflag'], ['done','paied'])){
            // 还未处理完,不能评分
        }
        return api::v($res);
    }
    // 提交评分
    function scoreDoAct(){
        $re = $this->scoreCheck();
        $fm = basReq::arr('fm');
        $fm['douid'] = empty($fm['douid']) ? '(扫码者)' : $fm['douid'];
        $fm['auser'] = empty($fm['auser']) ? '(scaner)' : $fm['auser'];
        if(!empty($re['newtpl'])){
            $res = ['errno'=>'scoreError','errmsg'=>$re['vars']['msg']];
        }else{
            $res = ['errno'=>0,'errmsg'=>'评分成功!'];
        }
        // --- 
        $did = $re['vars']['did'];
        $row = ['did'=>$did, 'title'=>$fm['title'], 'mflag'=>'score'];
        //if(!$fm['douid']) { $fm['douid'] = $fm['douid_old']; }
        // deel-log
        $exmsg = empty($fm['exmsg']) ? '3' : intval($fm['exmsg']);
        $kar = glbDBExt::dbAutID('coms_cslogs');
        $loga = [
            'cid' => $kar[0], 'cno' => $kar[1], 'pid' => $did, 'exmsg' => $exmsg,
            'mflag'=>'score', 'title'=>$fm['domsg'], 'mname'=>$fm['douid'], 'auser'=>$fm['auser']
        ]; 
        db()->table('coms_cslogs')->data($loga)->insert();
        // update+close

        $this->autoClose($fm, $re, $kar);
        // return
        return api::v($res, 'api');
    }
    // 评分界面
    function scoreAct(){
        $re = $this->scoreCheck();
        if(empty($re['newtpl'])){
            $re['newtpl'] = 'task/score'; // 设置模板
        }
        return $re;
    }

    // 催单
    function urgeAct(){
        $res = &$this->re; 
        $fm = basReq::arr('fm'); 
        // check-perm
        if($fm['douid_old']!==$res['vars']['uname']){ 
            //$fm['douid_old'] = 'PeaceXie';
            $row = ['did'=>$fm['did'], 'title'=>$fm['title'], 'mflag'=>$fm['mflag']];
            tex('texBase')->msgSend($row, 'AppCS', '1', [$fm['douid_old']]);
        }else{
            $res = ['errno'=>'NoPerm','errmsg'=>'没有催单权限'];
        }
        return api::v($res, 'api');
    }

    // 取消
    function cancelAct(){
        $res = &$this->re; 
        $fm = basReq::arr('fm');
        // check-perm 
        if($fm['auser']==$res['vars']['uname']){
            $doc = ['show'=>'0', 'euser'=>$res['vars']['uname']];
            db()->table('docs_cstask')->data($doc)->where("did='$fm[did]'")->update(); 
        }else{
            $res = ['errno'=>'NoPerm','errmsg'=>'没有取消权限'];
        }
        return api::v($res, 'api');
    }
    // 撤销一步
    function back1Act(){
        $res = &$this->re; 
        $cid = basReq::val('cid'); //dump($fm);
        $did = basReq::val('did'); 
        $crow = db()->table('coms_cslogs')->where("cid='$cid'")->find(); // 当前处理记录
        $mrow = db()->table('coms_cslogs')->where("pid='$did'")->order("cid DESC")->find(); // 最后那条记录
        $prow = db()->table('docs_cstask')->where("did='$did'")->find(); // 当前工单记录
        if(empty($crow) || $mrow['cid']!=$cid){
            $res = ['errno'=>'NoPerm','errmsg'=>'参数错误!'];
            echo basOut::outJson($res); die();
        }
        if($crow['auser']!=$res['uname'] || time()-$crow['atime']>3680){
            $res = ['errno'=>'Timeout','errmsg'=>'非自己处理的或超过一小时不能撤销!'];
            echo basOut::outJson($res); die();
        }
        if(!strstr($prow['mduids'],$crow['auser'])){
            $prev = db()->table('coms_cslogs')->where("pid='$did' AND cid<'$cid'")->order("cid DESC")->find(); // 前一条处理记录
            if(empty($prev)){
                $res = ['errno'=>'NoPerm','errmsg'=>'参数错误!'];
                echo basOut::outJson($res); die();
            }
            $doc = ['mflag'=>$prev['mflag'], 'douid'=>$prev['mname']];
            db()->table('docs_cstask')->data($doc)->where("did='$did'")->update();         
        }
        db()->table('coms_cslogs')->where("cid='$cid'")->delete();
        // 
        return api::v($res, 'api');
    }
    // 多人处理(增援)
    function muldoAct(){
        $res = &$this->re; 
        $fm = basReq::arr('fm'); $did = $fm['did']; 
        $atuids = empty($fm['atuids']) ? '' : $fm['atuids']; // mduids
        $row = data('cstask.join',"did='$did' AND `show`='all'",1); 
        // check-perm
        $doLogs = $this->doLogs($did, $row); 
        $mdUser3 = in_array($res['vars']['uname'], [$row['auser'],$doLogs['uFlags']['lastUser'],'ChenZhenHong']); // 创建人, 当前处理人，陈总
        $mdSetps = in_array($row['mflag'], ['assign','servchk','ushift','aptime','served']);
        $uMuldo = $mdUser3 && $mdSetps; // 多人处理
        if(!$uMuldo){
            $res = ['errno'=>'NoPerm','errmsg'=>'没有处理权限'];
            return api::v($res);
        }
        // 
        $mduids = "{$row['mduids']},$atuids"; 
        $doc = ['mduids'=>implode(',', array_unique(array_filter(explode(',',$mduids))))]; //dump($doc);
        db()->table('docs_cstask')->data($doc)->where("did='$did'")->update(); 
        $row['mflag'] = '请求增援';
        $skips = $row['douid'].','.$row['mduids'];
        $mduids = array_unique(array_filter(array_diff(explode(',',$atuids), explode(',',$skips)))); //dump($mduids);
        tex('texBase')->msgSend($row, 'AppCS', 'tip', $mduids);
        return api::v($res, 'api');
    }

    // 评论
    function qaAct(){
        $res = &$this->re; 
        $uname = $res['vars']['uname'];
        $utab = $res['vars']['utab'];
        //
        $cid = req('cid');
        $to = req('to');
        $type = req('type');
        $tip = req('tip');
        $msg = trim(req('msg'));
        $msg = str_replace(['@',"\n","^","'"], '', $msg);
        $exqn = req('exqn'); $exqkey = $exqn ? 'exqn' : 'exqa';
        //{cid:exqa_cid, type:exqa_type, tip:exqa_tip, msg:msg}
        if(!$cid || !$to || !$msg){
            $res = ['errno'=>'NullData','errmsg'=>'数据不完整'];
        }else{
            $time = date('Y-m-d H:i');
            $tipre = isset($utab[$uname]) ? $utab[$uname]['name'] : $uname;
            $exqa = "$time@$uname@$to@$tipre $tip@$msg";
            $row = data('cslogs',"cid='$cid' AND `show`='all'",1);
            $exqa = empty($row[$exqkey]) ? $exqa : $row[$exqkey]."\n$exqa";
            db()->table('coms_cslogs')->data([$exqkey=>$exqa])->where("cid='$cid'")->update(); 
            // to:推送消息:对方不是自己时
            if($to!=$uname){
                $prow = ['did'=>$row['pid'], 'title'=>req('title'), 'exqa'=>$msg];
                tex('texBase')->msgSend($prow, 'AppCS', 'exqa', [$to]);
            }
        }
        return api::v($res, 'api');
    }

    function purchAct(){ 
        $res = &$this->re; 
        $subName = req('subName'); // thirdNo='+thirdNo+'&subName='+subName+'
        $thirdNo = req('thirdNo'); $tmp = explode('.', "$thirdNo..");
        if(empty($tmp[1])){
            $res = ['errno'=>'errorPurch','errmsg'=>'参数错误'];
        }else{
            $kar = glbDBExt::dbAutID('coms_cslogs');
            $loga = [
                'cid' => $kar[0], 'cno' => $kar[1], 'pid' => $tmp[1], 'exmsg' => $thirdNo,
                'mflag'=>'attapply', 'title'=>$subName, 'mname'=>'', 'auser'=>$res['vars']['uname'] 
            ]; 
            db()->table('coms_cslogs')->data($loga)->insert(); 
            // update 状态
            $doc = ['mflag'=>'attapply', 'douid'=>'']; // 设置默认???
            db()->table('docs_cstask')->data($doc)->where("did='{$tmp[1]}'")->update(); 
        }
        return api::v($res, 'api', 'api');
    }

    // 处理
    function deelAct(){
        $res = &$this->re; 
        $fm = basReq::arr('fm');
        $fm['atuids'] = empty($fm['atuids']) ? '' : $fm['atuids'];
        $mduids_old = empty($fm['mduids_old']) ? '' : $fm['mduids_old'];
        // check-perm
        $ismain = $fm['douid_old']==$res['vars']['uname']; // 主处理人
        $ismdu = strstr($mduids_old,$res['vars']['uname']); // 增援人
        if(!($ismain||$ismdu)){
            $res = ['errno'=>'NoPerm','errmsg'=>'没有处理权限'];
            return api::v($res, 'api');
        }
        // ismain
        if(!$ismdu){ // `!$ismdu == $ismain` 不一定成立
            if(!$fm['douid']) { $fm['douid'] = $fm['douid_old']; }
            $row = ['did'=>$fm['did'], 'title'=>$fm['title'], 'mflag'=>$fm['mflag']];
            // send-msg
            if($fm['douid'] && $fm['douid_old']!==$fm['douid']){
                tex('texBase')->msgSend($row, 'AppCS', '0', [$fm['douid']]);
            }
            $skips = $fm['douid'].','.$fm['atuids_old'].','.$fm['douid_old'];
            $atuids = array_diff(explode(',',$fm['atuids']), explode(',',$skips));
            tex('texBase')->msgSend($row, 'AppCS', 'tip', $atuids);
            // deel-log
            $tab = ['done'=>'fee','served'=>'map','aptime'=>'date']; // 打卡,费用,时间
            foreach ($tab as $mflag => $key) {
                if($fm['mflag']==$mflag && !empty($fm[$key])){
                    $fm['exmsg'] = $fm[$key];
                    if($mflag=='aptime' && !empty($fm['time'])){ $fm['exmsg'] .= ' '.$fm['time']; } // .':00'
                }
            }
        }else{
            if(!$fm['douid']) { $fm['douid'] = $res['vars']['uname']; }
        }
        $kar = glbDBExt::dbAutID('coms_cslogs');
        $loga = [
            'cid' => $kar[0], 'cno' => $kar[1], 'pid' => $fm['did'], 'exmsg' => empty($fm['exmsg']) ? '' : $fm['exmsg'],
            'mflag'=>$fm['mflag'], 'title'=>$fm['domsg'], 'mname'=>$fm['douid'], 'auser'=>$res['vars']['uname']
        ];
        db()->table('coms_cslogs')->data($loga)->insert(); 
        if($ismdu){
            return api::v($res, 'api');
        }
        // update+close
        $tmp = $this->doLogs($fm['did'], $row); 
        #$tmp['uFlags']['hasScore'] = 0; # forTest
        #$tmp['uFlags']['hasPaied'] = 0; # forTest
        $re['vars']['doLogs'] = $tmp['doLogs'];
        $re['vars']['uFlags'] = $tmp['uFlags'];
        $this->autoClose($fm, $re, $kar);
        #sleep(1); // --------------
        # $res = ['errno'=>'NoPerm','errmsg'=>'错误提示']; return api::v($res, 'api');
        return api::v($res, 'api');
    }

    function _defAct(){
        $re = &$this->re;
        $whrstr = texBase::sqlType($re);
        $whrstr .= texBase::sqlSo($re);
        $whrstr .= texBase::sqlAct($re); // waitme,history
        $re['vars']['whrstr'] = $whrstr; // dump($whrstr);
        if($retype=req('retype')){ 
            $re['vars']['count'] = data('cstask', $whrstr, 'count'); 
            $data = data('cstask', $whrstr, '20.page'); 
            texBase::convData($data);
            $re['vars']['list'] = $data;
        }
        $re['newtpl'] = 'task/lists';
        return api::v($re);
    }

    function homeAct(){
        return $this->_defAct();
    }

    function applyAct(){
        $re = &$this->re;
        $uinfo = $re['vars']['uinfo'];
        $uimod = $re['vars']['uimod'];
        // equip
        $this->setEqmsg($re, req('equip'), $uinfo, $uimod);
        return api::v($re); //return $re;
    }
    // 发布
    function appdoAct(){
        $re = &$this->re; 
        $uinfo = $re['vars']['uinfo'];
        $uimod = $re['vars']['uimod'];
        $fm = basReq::arr('fm');
        // check-perm
        if(empty($fm)){
            $re['vars']['errno'] = "Error-Form";
            $re['vars']['errtip'] = "表单数据为空！";
            $re['vars']['errmsg'] = "空表单，请重新提交";
            $re['newtpl'] = 'home/info'; // 设置模板
            return api::v($re, 'api');
        }
        // creat -------------
        $dop = new \imcat\dopDocs(read('cstask'));
        //$so = $dop->so; $cv = $dop->cv;
        $dop->svPrep(); $dop->svAKey();
        $kar = glbDBExt::dbAutID('docs_cstask', 'md2'); 
        $dop->fmv['did'] = $kar[0]; $dop->fmv['dno'] = $kar[1];
        $did = $dop->fmu['did'] = $dop->fmv['did']; //dump($dop->fmv); dump($dop->fmu);
        $dop->fmv['mflag'] = 'apnew';
        $dop->fmv['auser'] = $uinfo['uname'];
        $dop->fmv['mpic'] = $uinfo['mpic'];
        $dop->fmv['jump'] = $uinfo['umod'];
        db()->table($dop->tbid)->data($dop->fmv)->insert(); 
        $dop->fmu['detail'] = nl2br($dop->fmu['detail']);
        $dop->tbext && db()->table($dop->tbext)->data($dop->fmu)->insert(0); 
        // send-msg
        $row = $dop->fmv+$dop->fmu;
        tex('texBase')->msgSend($row, 'AppCS', '0', [$dop->fmv['douid']]);
        $atuids = array_diff(explode(',',$dop->fmv['atuids']), [$dop->fmv['douid']]); //dump($dop->fmv['douid'], $atuids);
        tex('texBase')->msgSend($row, 'AppCS', 'tip', $atuids);
        // deel-log
        $kar = glbDBExt::dbAutID('coms_cslogs');
        $exmsg = empty($fm['date']) ? '' : $fm['date'].' '.$fm['time'];
        $loga = [
            'cid' => $kar[0], 'cno' => $kar[1], 'pid' => $did, 'exmsg' => $exmsg,
            'mflag'=>'apnew', 'title'=>$fm['domsg'], 'mname'=>$dop->fmv['douid'], 'auser'=>$uinfo['uname']
        ];
        db()->table('coms_cslogs')->data($loga)->insert(); 
        # sleep(1); // --------------
        return api::v($re, 'api'); //return $re;
    }

    function _detailAct(){
        $re = &$this->re;
        $uname = $re['vars']['uinfo']['uname'];
        //$re['vars'] = $this->revars;
        $re['vars']['row'] = [];
        $did = $this->ucfg['key'];
        $row = data('cstask.join',"did='$did' AND `show`='all'",1);
        if($retype=req('retype')){ 
            $row = texBase::convRow($row);
        }
        if(empty($row)){
            vopShow::msg("资料不存在，请联系管理员！");    
        }elseif(empty($row['show'])){
            $re['vars']['errtip'] = "本单已撤销，请关闭窗口！";
            $re['vars']['errmsg'] = "{$row['did']} : {$row['title']}";
            $re['newtpl'] = 'home/info'; // 设置模板
            return api::v($re);
        }
        $re['vars']['row'] = $row;
        $re['vars']['whrstr'] = "pid='{$row['did']}'"; //  AND (m.show IN('0','1'))
        // doLogs
        $tmp = $this->doLogs($did, $row); 
        $re['vars']['doLogs'] = $tmp['doLogs'];
        $re['vars']['uFlags'] = $tmp['uFlags']; //dump($tmp['uFlags']);
        // return
        $enc = comConvert::sysRevert(time().'|'.$did, 0, "Abc123$did");
        $re['vars']['scoreUrl'] = PATH_BASE."?ajax-vimg&mod=qrShow&data=".surl("wxcs-score","",1).urlencode("?did=$did&enc=").$enc;
        $re['vars']['printUrl'] = basEnv::isMobile() ? '' : tex("texBase")->printLink($did,$uname);
        $re['vars']['uCancel'] = $row['auser']==$uname && in_array($row['mflag'],['apnew','assign','redo']);
        $mdUser3 = in_array($uname, [$row['auser'],$tmp['uFlags']['lastUser'],'ChenZhenHong']); // 创建人, 当前处理人，陈总
        $mdSetps = in_array($row['mflag'], ['assign','servchk','ushift','aptime','served']);
        $re['vars']['uMuldo'] = $mdUser3 && $mdSetps && !strstr($row['mduids'],$uname); // 多人处理
        // 多人处理-接收任务
        $re['vars']['fServchk'] = '';
        if(strstr($row['mduids'],$uname)){
            $getr = data('cslogs',"pid='$did' AND `show`='all' AND auser='$uname' AND mflag='servchk'",1); 
            if(empty($getr)){
                $re['vars']['fServchk'] = '1';
            } //dump($getr);
        }
        // pay
        $fee = empty($tmp['uFlags']['payFee']) ? '0.12' : $tmp['uFlags']['payFee']; $ordid = $did.'_'.date('His'); //67.89,
        $payurls = nativeUrl("工单($did)", $fee, $ordid, ''); //dump($payurls);
        $re['vars']['payUrl'] = PATH_BASE."?ajax-vimg&mod=qrShow&data=".$payurls['enc']."&payFee=$fee";
        $re['vars']['appcfg'] = $re['vars']['wecfgs']['AppCS'];
        // equip
        $this->setEqmsg($re, $row['eqstr'], $uinfo, $uimod);
        return api::v($re); //return $re;
    }

    function printAct(){
        $re = &$this->re;
        $re['vars']['row'] = [];
        $did = req('did');
        $row = data('cstask.join',"did='$did'",1);
        if(empty($row)){
            vopShow::msg("资料不存在，请联系管理员！");
        }
        $row = texBase::convRow($row);
        $flag = tex("texBase")->printLink('check',43200); // 86400
        $re['vars']['row'] = $row;
        // doLogs
        $tmp = $this->doLogs($did, $row);
        $re['vars']['doLogs'] = $tmp['doLogs'];
        $re['vars']['uFlags'] = $tmp['uFlags']; //dump($tmp['uFlags']);
        // extra-info
        $utab = $re['vars']['utab']; $deps = $re['vars']['deps']; 
        $puid = req('puid');
        $re['vars']['printName'] = ($puid && isset($utab[$puid])) ? $utab[$puid]['name'] : $puid;
        $re['vars'] = $re['vars'] + tex('texBase')->doUser($tmp, $utab, $deps); 
        return $re;
    }

    function init($ucfg, $vars){
        $re = &$this->re;
        if(empty($re['vars']['wecfgs']['isOpen'])){
            die('请配置:[ex_wework.php]:isOpen=1');
        }
        require_once DIR_ROOT."/a3rd/wepv3/example/WxPay.Config.php";
        require_once DIR_ROOT."/a3rd/wepv3/WxPay.funcs.php";
        include_once DIR_WEKIT."/sv-api/api/src/CorpAPI.class.php";
        require_once DIR_WEKIT."/js-sdk/lib/jssdk.php"; 
        $jssdk = new \JSSDK('AppCS');
        $re['vars']['signPackage'] = $jssdk->getSignPackage();
        $re['vars']['signAgent'] = $jssdk->getSignPackage(1);

        // base-config
        $re['vars']['tabEquip'] = basElm::text2arr('cstask.equip');
        $re['vars']['tabMflag'] = basElm::text2arr('cstask.mflag');
        $re['vars']['deps'] = extWework::getContacts('deps');
        $re['vars']['utab'] = extWework::getContacts('utab');
        $re['vars']['tabScore'] = $re['vars']['wecfgs']['stab']; //dump($revars);

    }

    function setEqmsg(&$re, $eqstr, $uinfo=[], $uimod=[]){
        $re['vars']['eqstr'] = $eqstr;
        $ex = explode('.', "$eqstr.."); // 2020AVHPH6.2020-b2-bd4n.P001
        if($ex[0] && $ex[1]){
            $re['vars']['cscorp'] = $cust = data('cscorp.join', "csno='$ex[0]'", 1);
            $re['vars']['equip'] = $eqp = data('equip.join', "did='$ex[1]'", 1); 
        }
        $apos = empty($eqp) ? [] : basElm::text2arr($eqp['npos']); 
        $eqmsg = '';
        if(!empty($eqp)){
            $eqmsg .= "名称：$eqp[title]<br>\n";
            $eqmsg .= "规格：$eqp[speci]<br>\n";
            if(isset($apos[$ex[2]])){ $eqmsg .= "位置：[$ex[2]] {$apos[$ex[2]]}<br>\n"; }
            if(!empty($cust)){ $eqmsg .= "单位：$cust[title]<br>\n"; }
        }
        $fname = '';
        if($uinfo['umod']=='company' && !empty($uimod['mname'])){ // 当前客户
            $fname = $uimod['mname'];
        }elseif(!empty($cust['mname'])){ // 当前设备联系人
            $fname = $cust['mname'];
        }
        $re['vars']['eqmsg'] = $eqmsg;
        $re['vars']['fname'] = $fname;
        $re['vars']['ftel'] = empty($cust) ? '' : $cust['mtel'];
        $re['vars']['faddr'] = empty($cust) ? '' : $cust['maddr'];
    }

}

/*

*/
