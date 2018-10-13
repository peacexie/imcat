<?php
namespace imcat;
require __DIR__.'/we_cfgs.php';

$act = req('act','main'); 
$kid = req('kid','admin');
$debug = basReq::arr('debug','Html');

$dcfg = array('api','appid','token','appsecret','orgid','openid');
$wecfg = wysBasic::getConfig($kid); 

if(@$debug['type']=='qrGet'){ //cookie-header问题,放在echo输出前
    $wxqr = new wysQrcode($wecfg); 
}

glbHtml::page("微信接口调试");
eimp('initJs','jquery;comm;comm(-lang);/base/assets/cssjs/weixin'); 
eimp('initCss','stpub;comm'); // bootstrap,
echo basJscss::imp("/base/assets/cssjs/weixin.js"); 
echo "<style type='text/css'>.radio{display:inline-block;}</style>";
glbHtml::page('body',' style="margin:20px;"');
wxDebugNavbar();

//$u = user(); print_r($u);

foreach($dcfg as $k){
    $$k = empty($debug[$k]) ? (empty($wecfg[$k]) ? '' : $wecfg[$k]) : $debug[$k];
}
$api || $api = $_cbase['run']['roots']."/plus/api/wechat.php?kid=$kid";
$orgid || $orgid = 'gh_'.basKeyid::kidRand(24,12);
$openid || $openid = 'open_'.basKeyid::kidRand(24,24);
$debug['type'] = empty($debug['type']) ? 'Location' : $debug['type'];

if($act=='main'){

    glbHtml::fmt_head('fmlist',"?kid=$kid",'tbdata');
    glbHtml::fmae_row('接口地址',"<input name='debug[api]' type='text' value='$api' class='txt w650' />");
    glbHtml::fmae_row('AppId',"<input name='debug[appid]' type='text' value='$appid' class='txt w650' />");
    glbHtml::fmae_row('AppSecret',"<input name='debug[appsecret]' type='text' value='$appsecret' class='txt w650' />");
    glbHtml::fmae_row('Token',"<input name='debug[token]' type='text' value='$token' class='txt w650' />");
    
    $acta = array(    
        'Signurl'=>'测试接入', //(取消关注) ($wecfg=array())
        'Subscribe'=>'关注', //(取消关注)
        'Send'=>'发信息',
        'qrGet'=>'获取二维码',
        'qrPush'=>'推送二维码',
        'Click'=>'Click相关',
        'Location'=>'地理位置', // 113.740003,23.008843,17   23.018224, 113.760718 (x:0.03, 0.01)
        'oaLink'=>'授权链接(会员中心)',
        'Spic'=>'发图片(传图使用)',
    );
    $acts = '';  
    foreach($acta as $k=>$v){
        if($k=='oaLink') $acts .= "<br>";
        $acts .= "<label><input class=\"radio\" type=\"radio\" name=\"debug[type]\" value=\"$k\" onclick=\"wxSetDebugType('$k')\"".(@$debug['type'] == $k ? ' checked="checked"' : '').">$v</label> &nbsp; ";
    }
    $long = 113.700003+mt_rand(10000,60000)/1000000; 
    $lati = 22.9951234+mt_rand(1000,18000)/1000000; 
    
    glbHtml::fmae_row('调试类型',"$acts");
    
    glbHtml::fmae_row('<!--,Sub,qrP,Cli,-->事件Key',"<input name='debug[key]' type='text' value='".(empty($debug['key']) ? 'KEY_'.strtoupper(basKeyid::kidRand(24,6)) : $debug['key'])."' class='txt w240' /><br>1.关注一般不需要KEY,扫描带参数二维码事件KEY类似:qrscene_123123; <br>2.点菜单事件请填写菜单中对应Click的KEY（Mylocal,Txworks,Cnarea）; <br>3. 推送二维码请填写二维码的场景值;");
    glbHtml::fmae_row('<!--,qrG,-->事件Key',"<input name='debug[qrmod]' type='text' value='".(empty($debug['qrmod']) ? 'login' : $debug['qrmod'])."' class='txt w240' /><br>如：login, getpw, send, upload 等系统能处理的扫描[模块];");
    glbHtml::fmae_row('<!--,qrG,-->附加参数',"<input name='debug[extp]' type='text' value='".(empty($debug['extp']) ? date('H').'_'.date('mdis') : $debug['extp'])."' class='txt w240' /><br>如：cargo.2015-97-dad1, company.2015-7p-hhw1, govern.2015-6x-f401 等系统能处理的扫描[模块];");
    glbHtml::fmae_row('<!--,Sen,-->信息内容',"<textarea name='debug[detial]' rows='8' cols='60' wrap='off' class='txt w560'>你好！微信测试！".date('Y-m-d H:i:s')."</textarea><br>eg：产品, 新闻, zx, hn, gd");
    glbHtml::fmae_row('<!--,Loc,-->Precision',"<input name='debug[prec]' type='text' value='".(mt_rand(500000,3000000)/10000)."' />");
    glbHtml::fmae_row('<!--,Loc,-->坐标位置',"<input id='debug[sitemap]' name='debug[sitemap]' type='text' value='$lati,$long,10' class='txt'  maxlength='48'  style='width:240px'; /><span class='fldicon fmap' onClick=\"mapPick('baidu','debug[sitemap]');\">&nbsp;</span>");
    glbHtml::fmae_row('<!--,Loc,-->自动/主动',"<input name='debug[loctype]' type='text' value='".(empty($debug['loctype']) ? 'auto' : $debug['loctype'])."' class='txt w240' />auto/send");
    glbHtml::fmae_row('<!--,Spi,-->PicUrl',"<input name='debug[PicUrl]' type='text' value='".@$debug['PicUrl']."' class='txt w650' />");
    glbHtml::fmae_row('<!--,Spi,-->MediaId',"<input name='debug[MediaId]' type='text' value='".@$debug['MediaId']."' class='txt w650' />");
    
    glbHtml::fmae_row('OrgID',"<input name='debug[orgid]' type='text' value='".@$orgid."' class='txt w650' />");
    glbHtml::fmae_row('OpenID',"<input name='debug[openid]' type='text' value='".@$openid."' class='txt w650' />");
    glbHtml::fmae_row('提交'," &nbsp; <input name='bsend' type='submit' class='btn w240' value=' ------ 提交 ------ ' /> 
      &nbsp; <a href='?kid=$kid'>(刷新)</a> # <a href='?kid=$kid&act=tmptest'>(tmptest)</a>");

    glbHtml::fmt_end();
    echo "\r\n<script type='text/javascript'>wxSetDebugType('{$debug['type']}',1);</script>";
    
    echo "<hr><p>调试结果：</p><pre>"; //print_r($debug);
    
    if(!empty($bsend)){
        $data = "<ToUserName><![CDATA[$orgid]]></ToUserName>";
        $data .= "<FromUserName><![CDATA[$openid]]></FromUserName>";
        $data .= "<CreateTime>".time()."</CreateTime>";
        if($debug['type']=='Signurl'){
            $debug['token'] = $token; //保证token一致...
            $signurl = wysTester::getSignurl($debug, $api);
            echo "\n<br>接入链接: <a href='$signurl' target='_blank'>".basStr::cutWidth($signurl,56)."".substr($signurl,-12,12)."</a>";
            echo "\n<br>接入Url: $signurl";
        }
        if($debug['type']=='Subscribe'){
            $data .= "<MsgType><![CDATA[event]]></MsgType>";
            $data .= "<Event><![CDATA[".strtolower($debug['type'])."]]></Event>";
            if($debug['key'] && strstr($debug['key'],'qrscene_')){
                $data .= "<EventKey><![CDATA[{$debug['key']}]]></EventKey>";
            }
        }
        if($debug['type']=='Send'){
            $data .= "<MsgType><![CDATA[text]]></MsgType>";
            $detial = $debug['detial'];
            $data .= "<Content><![CDATA[$detial]]></Content>";
        }
        if($debug['type']=='qrGet'){
            //$wxqr = new wysQrcode($wecfg); 
            $tmp = $wxqr->getQrcode($debug['qrmod'], 'limit', $debug['extp']); 
            echo "\n<br>场景ID: {$tmp['sid']} ; ticket: {$tmp['ticket']} ";
            echo "\n<br>二维码Url: {$tmp['url']}"; 
            echo "\n<br>二维码链接: <a href='{$tmp['url']}' target='_blank'><img src='{$tmp['url']}' width='230'></a>";
        }
        if($debug['type']=='qrPush'){
            $data .= "<MsgType><![CDATA[event]]></MsgType>";
            $data .= "<Event><![CDATA[SCAN]]></Event>";
            $data .= "<EventKey><![CDATA[".$debug['key']."]]></EventKey>";
            $data .= "<Ticket><![CDATA[Ticket_".basKeyid::kidRand(24,24)."]]></Ticket>";
        }
        if($debug['type']=='Click'){
            $data .= "<MsgType><![CDATA[event]]></MsgType>";
            $data .= "<Event><![CDATA[CLICK]]></Event>";
            $data .= "<EventKey><![CDATA[{$debug['key']}]]></EventKey>";
        }
        if($debug['type']=='Location'){
            $smap = explode(',',$debug['sitemap']); 
            if($debug['loctype']=='send'){
                $data .= "<MsgType><![CDATA[location]]></MsgType>";
                $data .= "<Location_X>".$smap[0]."</Location_X>";
                $data .= "<Location_Y>".$smap[1]."</Location_Y>";
                $data .= "<Scale>".mt_rand(5,15)."</Scale>";
                $data .= "<Label><![CDATA[SomePlace]]></Label>";
                //$data .= "<MsgId>1234567890123456</MsgId>    ";
            }else{
                $data .= "<MsgType><![CDATA[event]]></MsgType>";
                $data .= "<Event><![CDATA[LOCATION]]></Event>";
                $data .= "<Latitude>".$smap[0]."</Latitude>";
                $data .= "<Longitude>".$smap[1]."</Longitude>";
                $data .= "<Precision>{$debug['prec']}</Precision>";
            }
        }
        if($debug['type']=='oaLink'){ 
            $url = "{root}/mob.php?user&oauth=snsapi_base&state=mlogin"; //&_tm=".time()."
            $wem = new wysMenu($wecfg);
            $url = $wem->fmtUrl($url);
            echo "\n<br>授权链接Url: <input name='' type='text' value='{$url}' style='width:100%'>"; 
            echo "\n<br>Html链接: <a href='{$url}' target='_blank'>Html链接</a> 点这里不能打开，请复制到手机打开。";
        }
        if($debug['type']=='Spic'){ 
            $data .= "<MsgType><![CDATA[image]]></MsgType>";
            $data .= "<PicUrl><![CDATA[$debug[PicUrl]]]></PicUrl>";
            $data .= "<MediaId><![CDATA[$debug[MediaId]]]></MediaId>";

        }
        
        $data = "<xml>$data</xml>";
        if(in_array($debug['type'],array('Subscribe','Send','qrPush','Click','Location','Spic',))){
            $dstr = wysTester::showInfo($data); 
            echo "提交的数据：<pre>$dstr";
            $data = comHttp::doPost($api, $data, 3);
            echo "<hr>返回的结果：";
            echo empty($data) ? 'NULL' : wysTester::showInfo($data);
            //echo "提示：由于xml中的文字为utf8，如果系统为gbk版，则显示的xml中有乱码算正常现象";
            echo "</pre>";
        }
            
    }
    
    /*


    echo "</td><tr>\n</table>";
    */


}elseif($act=='tmptest'){
    
    $user['nickname'] = "和平鸽(peace)-测his--223"; //xieys,和平鸽(peace)-测his--223
    $res = usrExtra::fmtUserName($user);
    echo ("<br>".$res);
    
    #$unow = user(); 
    $res = usrMember::addUser('person',$res.date('Hms'),'ssss232333232',$user['nickname']);
    echo "<pre>"; print_r($res);
    
    /*
    #$res = wysUser::addUser("openid_$res",$res,'ssss232333232',$user['nickname'],'person'); 
    #echo "<pre>"; print_r($res);
    
    $res = wysUser::bindUser("openid_$res",'hepinggep_qv8','ssss232333232');
    echo "<pre>"; print_r($res); */
    
    #$res = wysUser::resetPwd("openid_$res",44334,'openid_hepinggepeacece');
    #echo "<pre>"; print_r($res); 
    
    #$res = usrBase::setLogin('m','hepinggep_qv8');
    #echo "<pre>"; print_r($res); 
    
    echo "<br>".dechex(6677);
    echo "<br>".dechex(7173);
    echo "<br>".dechex(13780);
    echo "<br>";
    
    //hepinggep_qv8,[password] => 
    
    
    die('dd');
    
    /*
    $wecfg = wysBasic::getConfig(); 
    //$url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=%&media_id=%";
    //$url = sprintf($url, $wecfg['actoken'], '6174967231493613929');
    $url = "http://mp.weixin.qq.com/debug/zh_CN/htmledition/images/bg/bg_logo1f2fc8.pngx";
    $url = "http://mmbiz.qpic.cn/mmbiz/kCd8LZdoPibUpCj1IhZLdtNmGTHI8UN6etJATibgCJxTQhTo0ZOBkc5AslmQbC4u7AbtgqUyYIU3xvLTqDyIAgtw/0";
    $media = _08_Http_Request::getResources($url); var_dump($media);
    comFiles::put("./aaa.jpg", $media); die($media);
    return $qrcode;
    //*/

    $openid = 'oyDK8vjjcn3cFbxMLaMBhKEsYbCk';
    $mname08 = 'jp000';
    $password = 'jp000';
    $mchid = 1;
    
    $re = wysUser::bindUser($openid,$mname08,$password);
    print_r($re);
    die();

    $re = wysUser::addUser($openid,$mname08,$password,$mchid);
    print_r($re);
    die();

    $wecfg = wysBasic::getConfig('wx3b915d8db305b742','appid'); //
    
    $wem = new wmpMenu($wecfg);
    $re = $wem->menuGet();
    echo $re.'<pre>';
    print_r($re);
    echo $re.'<br>';
    
    // snsapi_base, snsapi_userinfo
    $url = "{mobileurl}wxlogin.php?test=3&oauth=snsapi_base&state=getpw";
    $wem = new wysMenuBase($wecfg);
    $url = $wem->fmtUrl($url);
    echo $url.'<br>';

    echo ucfirst('abc').'<br>';
    echo ucfirst('ABC').'<br>';
    echo ucfirst('AbC').'<br>';
    $wxqr = new wysQrcode($wecfg);
    //echo wysEventBase::getSceneID('login', 'test');
    $tmp = $wxqr->getQrcode('sendaid_7654321', 'temp', 'test'); print_r($tmp); echo "\n<br>\n";
    $lmt = $wxqr->getQrcode('login', 'limit', 'test'); print_r($lmt);
    
    //93964         login         好好2    gQEM8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0dVV1ViRWJsVWpzcmIxckRqV2tNAAIENqUtVgMEAAAAAA==
    $wx2 = new wmpQrcode($wecfg); echo "<pre>";
    $lmt = $wx2->qrcodeTicket(93964, 'fnum'); print_r($lmt); 
    $lmt = $wx2->qrcodeTicket(19927, 'fnum'); print_r($lmt);
    //qrcodeTicket($sid,$type='temp',$exp=86400)
    

}elseif($act=='xxx'){
    
/*    
[User Message]=</b>(_08_M_Weixin_Event::Scan-27a
[ToUserName]=(gh_a94178b33562)
[FromUserName]=(oA1n9tl_fnXi8ouleydL0hkvVBwI)
[CreateTime]=(1437721595)
[MsgType]=(image)
[PicUrl]=(http://mmbiz.qpic.cn/mmbiz/kCd8LZdoPibUpCj1IhZLdtNmGTHI8UN6etJATibgCJxTQhTo0ZOBkc5AslmQbC4u7AbtgqUyYIU3xvLTqDyIAgtw/0)
[MsgId]=(6174967231493613929)
[MediaId]=(fp60ATiIUa80QCr73PZsDfPFw2khbpf641m_d62g19aqwhvLPx_FjNQNWa9OKZyw)
*/

}elseif($act=='xxx'){

    
}elseif($act=='xxx' && $tab=='msgget'){ echo 'xx';


}elseif($act=='xxx'){
    
    echo '完善中…';
    
} //echo "$act=='message' && $tab=='msgget'";

glbHtml::page('end');

?>
