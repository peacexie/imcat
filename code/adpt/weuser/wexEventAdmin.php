<?php
(!defined('RUN_INIT')) && die('No Init');
// 事件响应操作

class wexEventAdmin extends wysEvent{

    //public $haibaoMediaid = 'I7mPnzk9v0tF6nHiEmy0sDbtRksBp4pVHSYBZvdROjc';
    //public $haibaoPicurl = 'http://yscode.txjia.com/uimgs/logo/haibao.jpg';

    function __construct($post,$wecfg){ 
        parent::__construct($post,$wecfg); 
    }

    function scanLogin(){
        $msg = $this->scanLoginBase();
        die($this->remText($msg));
    }
    function scanLoginBase(){
        $msg = $this->subScanmsg;
        $qrInfo = $this->getQrinfo($this->eventKey);
        $row = $this->_db->table('users_uppt')->where("pptuid='{$this->fromName}' AND pptmod='weixin'")->find();
        //用授权链接..., 安全...
        $wxmenu = new wysMenu($this->cfg);
        if(!$row){  
            $msg .= "您未用微信扫描登录过本站账户:";
            $mname = '';
            $url = $this->oauthUrl($mname,'binding');
            $msg .= "\n<a href='$url'>点击绑定或添加账户</a>。";
        }else{
            $msg .= "欢迎使用微信扫描登录:";
            $mname = $row['uname'];
            $url = $this->oauthUrl($mname,'dologin');
            $msg .= "\n<a href='$url'>点击确认登录</a>。";
        } 

        return $msg;
    }
    
    function scanGetpw(){
        $msg = $this->scanGetpwBase();
        die($this->remText($msg));
    }
    // 点击找回密码(用于电脑端,客户端不需要密码登录)
    function scanGetpwBase(){ 
        //直接发微信信息...
        $msg = $this->subScanmsg;
        $row = $this->_db->table('users_uppt')->where("pptuid='{$this->fromName}' AND pptmod='weixin'")->find();
        if(!$row){  
           $msg .= "您未用微信扫描登录过本站账户:\n 1. 请点[微信登录]相关链接绑定或注册帐号！\n 2. 在微信菜单中点相关链接绑定或注册帐号！";
        }else{
            $msg .= "欢迎使用微信扫描找回密码:";
            $mname = $row['uname'];
            $url = $this->oauthUrl($mname,'dogetpw'); 
            $msg .= "\n<a href='$url'>点击重置密码</a>。";
        }
        return $msg;
    }
    // 登录绑定:mbind
    function scanMbind(){
        $msg = $this->scanMbindBase();
        die($this->remText($msg));
    }
    // 登录绑定:mbind
    function scanMbindBase(){ 
        //直接发微信信息...
        $msg = $this->subScanmsg;
        $row = $this->_db->table('users_uppt')->where("pptuid='{$this->fromName}' AND pptmod='weixin'")->find();
        $mname = comConvert::sysRevert($this->qrInfo['extp'], 1, '', 600);
        if(!$row){  
            $url = $this->oauthUrl($mname,'mbindDone'); 
               $msg .= "欢迎绑定微信:\n 请点[<a href='$url'>现在绑定</a>]确认！";
        }else{
               $mold = $row['uname'];
            $url = $this->oauthUrl($mname,'mbindChange');
               $msg .= "您的微信以前已经绑定: [{$mold}] \n 现在可 [<a href='$url'>更换绑定</a>] ";
            $url = $this->oauthUrl($mname,'mbindExit');  
               $msg .= " 或 [<a href='$url'>解除绑定</a>] ！";
        }
        return $msg;
    }
    function scanUploadBase($re=0){
        $msg = "您已开启微信传图模式，点击左下的小键盘图标，发送你需要上传的图片吧。";
        if($re) return $msg;
        die($this->remText($msg));
    }
    function scanSend(){
        $msg = $this->scanSendBase();
        die($this->remText($msg));
        //die("dd");    
    }
    function scanSendBase(){
        $qrInfo = $this->getQrinfo($this->eventKey);
        if(strpos($qrInfo['extp'],',')){
            $qrInfo['extp'] = explode(',',$qrInfo['extp']);
            $qrInfo['extp'] = $qrInfo['extp'][0];
        }
        $this->sendCfgs = explode('.',$qrInfo['extp']);
        $this->getSendInfo($this->sendCfgs[0], @$this->sendCfgs[1]);
        if($this->sendCfgs[0]=='cargo'){
            $fields = array('title'=>'商品','brand'=>'品牌','xinghao'=>'型号','guige'=>'规格','price'=>'价格');
            $plink = 'mob';
        }elseif(in_array($this->sendCfgs[0],array('company','govern','organize'))){ 
            $fields = array(
                'company'=>'商家','ftype'=>'行业','mname'=>'联系人','mtitle'=>'称呼','mtel'=>'联系电话',
                'memail'=>'邮件','maddr'=>'地址','mweb'=>'网址'
            );
            $plink = 'chn';
        }
        $msg = $this->getSendText($this->sendCfgs[0].'.'.@$this->sendCfgs[1],$fields,$plink);
        return $msg;
        /*
        # 商家[company]资料
        行业 ftype 
        联系人 mname （称呼mtitle）
        联系电话 mtel
        邮件 memail
        地址 maddr
        网址 mweb
        详情：>>> 
        # 商品[title]信息:
        品牌 brand
        型号 xinghao
        规格 guige
        价格 price
        详情：>>> 
        */
    }
    
    // 点击自定义菜单:Mylocal
    function clickMylocal(){
        $pinfo = $this->savePos(0);
        if(empty($pinfo)){
            $msg = "未能检测到您的地理位置信息；请重新关注,提示【是否允许公众号使用其地理位置】时选【是】即可使用本功能。";
        }else{
            $url = "{root}/root/run/umc.php?mkv=uio-wxlocal";
            $wem = new wysMenu($this->cfg);
            $url = $wem->fmtUrl($url);
            $msg = "您的位置信息是：\n[{$pinfo['longitude']},{$pinfo['latitude']}]！\n";
            $msg .= " 您可以使用以下服务：\n";
            $msg .= " <a href='$url&map={$pinfo['latitude']},{$pinfo['longitude']}&type=company'>企业会员</a>；\n";
            $msg .= " <a href='$url&map={$pinfo['latitude']},{$pinfo['longitude']}&type=govern'>政府机构</a>；\n";
            $msg .= " <a href='$url&map={$pinfo['latitude']},{$pinfo['longitude']}&type=organize'>非盈利组织</a>；\n";
        }
        die($this->remText($msg));
    }
    
    // 点击自定义菜单:Txworks
    function clickTxworks(){
        $news = array( //较好的效果为大图360*200，小图200*200,  小语交互,微简历,微名片
            array('title'=>'学无止境','desc'=>'英语学习，国学教育，数字数学','url'=>'{svrtxcode}/learn/index.htm?','picurl'=>'{svrtxcode}/uimgs/zohe/pi.gif',),
            array('title'=>'[国学]《弟子规》拼音版','desc'=>'','url'=>'{svrtxcode}/learn/cn-dizigui.htm?','picurl'=>'',),
            array('title'=>'[英语]迪斯尼神奇英语','desc'=>'','url'=>'{svrtxcode}/learn/disney.htm?','picurl'=>'',),
            array('title'=>'[数字数学]算24游戏','desc'=>'','url'=>'{svrtxcode}/learn/ms-suan24.htm?','picurl'=>'',),
            //array('title'=>'','desc'=>'','url'=>'?','picurl'=>'',),
            array('title'=>'[智力]称鸡蛋','desc'=>'','url'=>'{svrtxcode}/about/egg.htm?','picurl'=>'{svrtxcode}/uimgs/_pub/logo/gezi1-40x.jpg',),
            array('title'=>'[健康保健]保护视力-眼保健操','desc'=>'','url'=>'{svrtxcode}/health/cmshili.htm?','picurl'=>'',),
            array('title'=>'[健康保健]食物相克查询','desc'=>'','url'=>'{svrtxcode}/health/cmxke.htm?','picurl'=>'',),
            array('title'=>'[应用]全国城市-天气预报','desc'=>'','url'=>'{svrtxcode}/about/tianqi.htm?','picurl'=>'{svrtxcode}/uimgs/_pub/logo/gezi-fly.gif',),
        ); 
        die($this->remNews($news));
    }
    
    // 点击自定义菜单:Cnarea
    function clickCnarea(){
        $arr = read('china.i');
        $str = "回复下面括号里的两个字母获取详细信息：\n";
        foreach($arr as $k=>$v){
            if(empty($v['pid'])){
                $key = substr($k,2);
                $str .= "[$key]".$v['title']."\n";
            }
        }
        die($this->remText($str));
    }

    // 点击自定义菜单:Haibao
    function clickHaibao(){
        $hb = cfg('weixin.haibaoMediaid');
        die($this->remImage($hb));
    }
    
    # (文档/会员)的信息(仅数据)
    function getSendInfo($mod,$kid){ 
        $_groups = read('groups');
        $db = db();
        $pid = @$_groups[$mod]['pid'];
        $fid = substr($pid,0,1).'id';
        $data = $dext = array();
        if(in_array($pid,array('docs','users'))){
            $tabid = glbDBExt::getTable($mod);
            $data = $db->table($tabid)->where("$fid='$kid'")->find();
        }
        if(in_array($pid,array('docs'))){
            $tabid = glbDBExt::getTable($mod,1);
            $dext = $db->table($tabid)->where("$fid='$kid'")->find();
            $dext && $data += $dext; 
        } 
        $this->sendInfo = $data;
    }
    function getSendText($key,$fields,$tplink=''){ 
        $i = 0; $s = '';
        $_groups = read('groups');
        foreach($fields as $k=>$v){
            $val = $this->sendInfo[$k];
            if(isset($_groups[$k]) && $_groups[$k]['pid']=='types'){
                $vmcfg = read($k); 
                $vname = comTypes::getLnks(comTypes::getLays($vmcfg['i'],$val),'[v]');
                $val = empty($vname) ? $val : $vname;
            }
            if($i==0){
                $s .= "$v [$val] 资料\n"; 
            }else{
                $s .= "$v: $val\n";     
            }
            $i++;
        } 
        if(strpos($tplink,'{kid}')){
            $link = str_replace(array('{kid}'),$key,wysBasic::fmtUrl($tplink));
        }elseif($tplink){
            $link = wysBasic::fmtUrl(surl("$tplink:$key"));
        }
        $s .= "\n<a href='$link'>详情：>> </a>\n";
        return $s;
    }
    // $url : url / uname
    function oauthUrl($url, $state, $cope='snsapi_base'){ 
        if(!strpos($url,'.php')){
            $mname = $url;
            $url = "{root}/root/run/umc.php?mkv=uio-wxlogin&scene={$this->eventKey}&mname=$mname&sflag={$this->sflag}";
        }
        $wem = new wysMenu($this->cfg);
        return $wem->fmtUrl("oauth:$cope:$state:$url");
    }

}

/*

*/

