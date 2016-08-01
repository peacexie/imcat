<?php
require(dirname(__FILE__).'/we_cfgs.php');

//*
glbHtml::page("微信接口调试");
glbHtml::page('body');
wxDebugNavbar();
echo "<pre>\n";
//*/

$msgCont = '<a href="http://house.txmao.com/newhouse/201103/589868/detail.html">东湖花园-字标题四十个汉字标题四十个汉字标题四十个汉字标题四十个汉字标题四十个汉字</a>
地址：莲东片区是龙岩市唯一一个人居板块，定义为城市的后花园，登高山公园、莲花山公园就在附近
特色：小户型投资地产 教育地产 旅游地产
欢迎关注！<a href="http://house.txmao.com/newhouse/201103/589868/detail.html">点击看详情</a>';
$msgItems = array(
	array('title'=>'测试新闻1','description'=>'描述1','url'=>'http://192.168.1.11/auto/admina.php?','picurl'=>'http://192.168.1.11/auto/images/admina/logo.png',),
	array('title'=>'测试新闻2','description'=>'描述2','url'=>'http://192.168.1.11/auto/admina.php?','picurl'=>'http://192.168.1.11/auto/images/admina/logo.png',),
);

$msgItems = array( //较好的效果为大图360*200，小图200*200,  小语交互,微简历,微名片
	array('title'=>'鸽子小语','description'=>'Peace生命感言','url'=>'http://yscode.txjia.com/about/attitude.htm?','picurl'=>'http://yscode.txjia.com/uimgs/zohe/pi.gif',),
	array('title'=>'微名片','description'=>'和平鸽工作室','url'=>'http://yscode.txjia.com/about/card.htm?','picurl'=>'http://yscode.txjia.com/uimgs/logo/gezi1.jpg',),
	array('title'=>'天气预报','description'=>'全国天气','url'=>'http://yscode.txjia.com/about/tianqi.htm?','picurl'=>'http://yscode.txjia.com/uimgs/logo/gezi-fly.gif',),
);

$articles = array( //360*200，小图200*200
	array('title'=>'测试新闻1','medid'=>'kIdplY3EPMtFbCFIiwvrLIRVrEzHtXYmUfgQDSdhUYc','content'=>'aa图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS','url'=>'http://192.168.1.11/auto/admina.php?',),
	array('title'=>'测试新闻2','medid'=>'kIdplY3EPMtFbCFIiwvrLIRVrEzHtXYmUfgQDSdhUYc','content'=>'bb图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS','url'=>'http://192.168.1.11/auto/admina.php?',),
);

$url = 'http://192.168.1.11/08svn/auto_wet/index.php?/weixin/';

////////////////////////////////////////////////////////////////

// test-corp // ozhJVv2TgEaIxgQBcH9KnlWZdbZE,  gh_4d966875a7b4
$_ex_wechat['t08']['token'] = "corp20151234567890QYmxjx"; 
$_ex_wechat['t08']['appid'] = 'wx77827302390ce603'; 
$_ex_wechat['t08']['appsecret'] = 'b9c506dfe132f80255a6bbf7d65ce93d'; 

// zhanTester // ov-P4wv9pfUkcPGMuKdmh82pqD9s,  gh_a7f7e20ee310
// {svrtxmao}/plus/api/wechat.php
$_ex_wechat['zhl']['token'] = "Pe6_zhl_q9a_txjia_kmy"; 
$_ex_wechat['zhl']['appid'] = 'wxb864010c7215b0b1'; 
$_ex_wechat['zhl']['appsecret'] = 'd4624c36b6795d1d99dcf0547af5443d';  

// yangTester // ohcp-wpHkE96En9m59_TcjBls72g,  gh_c0db633da39e
$_ex_wechat['yql']['token'] = "yangqilin"; 
$_ex_wechat['yql']['appid'] = 'wx4b31d531425504bf'; 
$_ex_wechat['yql']['appsecret'] = 'd4624c36b6795d1d99dcf0547af5443d';  

// Tester // oyDK8vjjcn2cFbxMLaMBhKEsYbCk,  gh_70af3f6f3acf
$_ex_wechat['tys']['token'] = "ffckgkq6tbtjk7jaahbv5q8w5y55sdad"; 
$_ex_wechat['tys']['appid'] = 'wx20b06b3c8d4e2a46'; 
$_ex_wechat['tys']['appsecret'] = '331634c914433f3d48b963cd2dacbc4d';  

// peaceys, gh_8b0e4d1bb70c
$_ex_wechat['pys']['token'] = "Z1GNFQwRHqZ5ahfnAepL"; 
$_ex_wechat['pys']['appid'] = 'wxce96c26e3d9b8799'; 
$_ex_wechat['pys']['appsecret'] = '2f86466887576e5c3f0018a4c7e09662';

// corp // oA1n9tlixahY2kgCVm9u2tuNgvcI, gh_a94178b33562
$_ex_wechat['h08']['token'] = "djkskk4ss33fs"; //正式号，不要测试创建菜单... 
$_ex_wechat['h08']['appid'] = 'wx6ada54f4b9754685'; 
$_ex_wechat['h08']['appsecret'] = '3eb11ebe5c16421905d8652c16aaa7ca';

// 张华 // ocrnVwRC6iJqvvghjbCjKnosKWRs
$_ex_wechat['tzh']['token'] = "louistest1234567890"; //
$_ex_wechat['tzh']['appid'] = 'wx3b915d8db305b742'; 
$_ex_wechat['tzh']['appsecret'] = 'cf283cea2f16d06561501a581e5fb90a';

$api = 'tys';
$cfg = $_ex_wechat[$api];

$media_id_mat = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=oizdEBcYX3sjwEaoB-q6LhjXn4VN72k4BWzXb7lY2OlPWKSINR9Sce44K1XlaoHeUYmtAo4kegOoJoOAQZFSqHWL3sh-PxRx3vKqFVpI2o0CRQjAEAXML&media_id=JFmoZQO2Uv5opkMqklFmGL_75OyOnyAdhqzTpSH0vtJt3KAeTa0e87hh-CW89TGn';
$media_id_news = 'kIdplY3EPMtFbCFIiwvrLIRVrEzHtXYmUfgQDSdhUYc';


$tpl = 'u6DK6CKG8TnCFGaOwglBPUPa_UvE3nwpQU-k8kP1YpA';
$data = '"first": {
                       "value":"您好！您收到一条公文消息：",
                       "color":"#173177"
                   },
                   "title":{
                       "value":"XXX项目启动会",
                       "color":"#173177"
                   },
                   "time": {
                       "value":"2014年1月16日14时",
                       "color":"#173177"
                   },
                   "auser": {
                       "value":"peace",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"请点击查看！",
                       "color":"#173177"
                   }';
$wxo = new wmpMsgmass($_ex_wechat['tys']);
$data = $wxo->sendTpl('oyDK8vjjcn2cFbxMLaMBhKEsYbCk', $tpl, $data, 'http://txmao.txjia.com/root/run/umc.php?indoc.2015-9m-dq21');
echo "\n"; echo "<pre>"; 
print_r($data); die();

/*
$wxmat = new wmpMaterial($_ex_wechat['tys']);
$data = $wxmat->matAdd('/media/cover/haibao.jpg');
echo "\n"; echo "<pre>"; 
print_r($data); die();
*/

/*
$i=1; $m=100000; $s='"oA1n9tlixahY2kgCVm9u23-100000"';
for($i=1;$i<$m;$i++){
	$s.=",\"oA1n9tlixahY2kgCVm9u23-".(100000+$i)."\"";	
}
$data = '{"total":673,"count":671,"data":{"openid":['.$s.']},"next_openid":"oA1n9tnC0WM26W17uu2f9NFBSc_4"}';
//echo $data;

print_r(json_decode($data,1));
//123456789-123456789-123456789-
//oA1n9tlixahY2kgCVm9u2tuNgvcI
//oA1n9tlixahY2kgCVm9u2-100000
//{"total":673,"count":671,"data":{"openid":["oA1n9tlixahY2kgCVm9u2tuNgvcI","oA1n9tqn9Yu2f9NFBSc_4"}
*/


//$s = '&_#';
//echo urlencode($s); die();



/*
$url = "{$cms_abs}wxlogin.php?test=1&oauth=uinfo&state=";
preg_match("/oauth=(\w+)/i",$url,$m);
echo "<br>aaa: "; print_r($m); echo "<br>";
preg_match("/state=(\w+)/i",$url,$m);
echo "<br>bbb: "; print_r($m); echo "<br>";
//preg_replace("/$key=([^\f\n\r\t\v\&\#]{1,80})/i",$para,$url);
*/


/*/* ??? 失败
$weixin = new wmpSemantic($cfg);
$q = '查一下明天从广州到上海的南航机票'; 
//东莞-郴州,火车票
//' 查一下明天东莞到郴州的火车票 , 查一下明天从北京到上海的南航机票
$city = '广州';
$category = 'hotel'; //,hotel
$uid = 'oyDK8vjjcn2cFbxMLaMBhKEsYbCk';
$data = $weixin->getResult($q, $city, $category, $uid); 
print_r($data);
//*/


/*/*
$data = wysTester::getEvent('Scan');
$data = wysBasic::getResource(array(
	'urls' => $url,
	'timeOut' => 3,
	'method' => 'POST',
	'postData' => $data,
)); 
print_r($data);
//*/




/*/*
$data = wysTester::getMessage();
$data = wysBasic::iconv(cls_env::getBaseIncConfigs('mcharset'),'utf-8',$data);
$data = wysBasic::getResource(array(
	'urls' => $url,
	'timeOut' => 3,
	'method' => 'POST',
	'postData' => $data,
)); 
print_r($data);
//*/

//


/*/ ============= xxxxxxx : 
$wxmat = new wmpMaterial($_ex_wechat['tys']); //tys
echo "\n"; echo "<pre>"; 
$data = $wxmat->mdelMedia('kIdplY3EPMtFbCFIiwvrLOUFWt1b7vONiuYQmIaGaTQ');
print_r($data); 
//
$data = $wxmat->mgetList('image');
print_r($data); 
$data = $wxmat->mgetList('news');
print_r($data); 
$data = $wxmat->mgetList('video');
print_r($data); 
//*/
//echo "</pre>"; echo "\n";
//*/


/*/ ============= xxxxxxx : 
$wxmat = new wmpMaterial($_ex_wechat['tys']);
$data = $wxmat->matNews($articles,'08点点');
echo "\n"; echo "<pre>"; 
print_r($data); 
echo "</pre>"; echo "\n";
//*/


	
/*/ ============= xxxxxxx
$wxmat = new wmpMaterial($_ex_wechat['zhl']);
$data = $wxmat->matAdd('/media/cover/haibao.jpg');
echo "\n"; echo "<pre>"; 
print_r($data); 
$url = @$data['url']; //$wxmat->tmpGet();
echo "<a href='$url'>$url</a>
<br><img src='$url'>";
echo "</pre>"; echo "\n";
//*/

	
/*/echo "<img src='$media_id_t1'>";	
// ============= xxxxxxx
$wxmat = new wmpMaterial($_ex_wechat['tys']);
$data = $wxmat->tmpUpload('/skin/logo/gezi1-40x.jpg');
echo "\n"; echo "<pre>"; 
print_r($data); 
$url = $wxmat->tmpGet(@$data['media_id']);
echo "<a href='$url'>$url</a>
<br><img src='$url'>";
echo "</pre>"; echo "\n";
//*/


/*/ ============= xxxxxxx
$data = wysTester::getMessage(); //print_r($cfg);
$data = wysTester::showInfo($data);
echo "\n"; echo "<pre>$data</pre>"; echo "\n";
//*/


/*/ ============= xxxxxxx
$signurl = wysTester::getSignurl($cfg); //print_r($cfg);
echo "\n"; echo "<a href='$signurl'>$signurl</a>"; echo "\n";
//*/


// ============= xxxxxxx
/*/$wxbase = new wmpBasic($_ex_wechat['h08']);
//$actoken = $wxbase->actoken;
$weixin = new wmpMsgresp('',$_ex_wechat['pys']);
$data = $weixin->getRule($weixin->actoken);
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx // oyDK8vvPhm2vcuI-xehPJUJgTZbo,oyDK8vjjcn2cFbxMLaMBhKEsYbCk
$weixin = new wmpMsgmass($_ex_wechat['tys']);
$data = $weixin->sendText($msgCont,"oyDK8vvPhm2vcuI-xehPJUJgTZbo,oyDK8vjjcn2cFbxMLaMBhKEsYbCk");
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpMsgsend($_ex_wechat['zhl']);
//$data = $weixin->sendText('oyDK8vjjcn2cFbxMLaMBhKEsYbCk',$msgCont);
$data = $weixin->sendNews('ov-P4wv9pfUkcPGMuKdmh82pqD9s',$msgItems);
echo "\n"; print_r($data); echo "\n";
///



// ============= xxxxxxx
$weixin = new wmpBasic($cfg);
$data = $weixin->xxxx();
echo "\n"; print_r($data); echo "\n";
//*/


/*/
$weixin = new wysMenu($cfg);
//$data = $weixin->create($_ex_wechat['menu2']); echo "\n"; print_r($data); echo "\n";
$data = $weixin->get();
echo "\n"; print_r($data); echo "\n";
//*/


/*/
$weixin = new wmpQrcode($_ex_wechat['zhl']); 
$data = $weixin->qrcodeTicket('1001','fnum'); //[1-9]+[999,999
$url = $weixin->qrcodeShowurl($data);
echo "\n"; print_r($data); echo "<img src='$url'>\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpQrcode($_ex_wechat['tzh']);
$data = $weixin->shortUrl('http://192.168.1.11/08svn/auto_wet/admina.php?isframe=1&entry=channels&action=channeledit&mkv=info-coder&tpls=c_page/info_coder,c_pub/ahead,c_pub/amenu,c_page/info_side,c_pub/afeet#s_c_pub/ahead');
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpUser($cfg);
$data = $weixin->groupMove('oyDK8vjjcn2cFbxMLaMBhKEsYbCk',2);
echo "\n"; print_r($data); echo "\n";
$data = $weixin->groupMove('oyDK8vvPhm2vcuI-xehPJUJgTZbo',2);
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpUser($cfg);
$data = $weixin->groupDelete(109);
echo "\n"; print_r($data); echo "\n";
//*/




/*/ ============= xxxxxxx
$weixin = new wmpUser($cfg);
$data = $weixin->groupRename(108,'测试8组');
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpUser($cfg);
$data = $weixin->groupCreate('测试4组');
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpUser($cfg);
$data = $weixin->groupList();
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$weixin = new wmpUser($_ex_wechat['yql']);
$data = $weixin->getUserInfoList();
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= xxxxxxx
$us = 'oA1n9tlixahY2kgCVm9u2tuNgvcI,oA1n9tqn9Yi318wA-w9PmZ2AwRU8';
$weixin = new wmpUser($_ex_wechat['h08']);
$data = $weixin->getUserBatch($us);
echo "\n"; print_r($data); echo "\n";
//*/



/*/ ============= getUserRemark
$weixin = new wmpUser($_ex_wechat['h08']);
$data = $weixin->setUserRemark('oA1n9tlixahY2kgCVm9u2tuNgvcI','备注…'); 
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= getUserInfo
$weixin = new wmpUser($_ex_wechat['tys']);
$data = $weixin->getUserInfo('oyDK8vjjcn2cFbxMLaMBhKEsYbCk'); 
print_r(wysUser::fmtUserName($data));
echo "\n"; print_r($data); echo "\n";
//*/


/*/ ============= WeixinIP
$weixin = new wmpBasic($cfg);
$iplist = $weixin->getWeixinIP();
echo "\n"; print_r($iplist); echo "\n";
//*/


/*/ ============= getAccessToken测试
$weixin1 = new wmpBasic($cfg);
$token1 = $weixin1->actoken;
//管理多个公众号测试
$weixin2 = new wmpBasic($_ex_wechat['tzh']);
$token2 = $weixin2->actoken;
echo "\n".$token1."\n";
echo "\n".$token2."\n";
//*/


/*/ ============= txmao_loader测试
print_r(wmpError::errGet(40001));
echo "\n";
print_r(wmpError::errGet(888));
//*/

/*/ ============= txmao_loader测试
print_r(wysBasic::getConfig());
echo "\n";
//*/



// ============= end 

/*

*/
