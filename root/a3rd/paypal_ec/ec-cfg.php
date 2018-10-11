<?php
$_cbase['tpl']['vdir'] = 'ven'; // 指定模板目录
$_cbase['sys']['lang'] = 'en'; // 指定语言
require dirname(dirname(__DIR__)).'/run/_init.php';

$db = db(); //dump($user);
if(empty($logfid)){
    $user = usrBase::userObj('Member'); 
    $unqid = usrPerm::getUniqueid(); 
}

$qstr = @$_SERVER['QUERY_STRING'];
$act = req('act');
$enc = req('enc');
$ordid = req('ordid');

/*
     sFrom = "^^"&RequestS("address_name","C",512)&"^"&RequestS("address_street","C",512)&"^"&RequestS("address_city","C",512)&""
     sFrom = sFrom&"^"&RequestS("address_state","C",512)&"^"&RequestS("address_zip","C",512)&"^"
     sFrom = sFrom&"^"&country&"("&RequestS("address_country_code","C",512)&")"
    'Frm2,address_name    用于地址的名称 （在客户提供礼品地址时包含在内）  128<br />
    'Frm3,address_street    客户的街道地址  200<br />
    'Frm4,address_city    客户地址中的市/  县  40<br />
    'Frm5,address_state    客户地址中的省/  直辖市/  自治区  40<br />
    'Frm6,address_zip    客户地址中的邮政编码  20 <br />
    'Frm8,address_country    客户地址中的国家或地区  64<br />
    'address_country_code    两位   ISO 3166  国家或地区代码  2<br />
    'address_status  Confirmed/Unconfirmed 已确认的地址/未确认的地址<br />  
    'mc_currency' => 'USD   curIDNow(0)

     sql = " INSERT INTO [OrdInfo] (" 
     sql = sql& "  KeyID,KeyCode,KeyMod,InfSubj,InfCont" 
     sql = sql& ", InfNum,InfWght,InfSum1,InfSum2,InfSum3"
     sql = sql& ", LnkName,LnkSex,LnkAddr,LnkPost,LnkMobile,LnkTel,LnkEmail" 
     sql = sql& ", InfPay,InfSend,InfTime,SetCheck,SetPay,SetSend,SetState" 
     sql = sql& ",InfWght3,InfCurID,LogAddIP,LogAUser,LogATime" 
     sql = sql& ")VALUES(" 
     sql = sql& "  '" &KeyID&"','"&OrdID&"','PicS224','Order','"&InfCont&"'" 
     sql = sql& ", " &InfNum&","&aWght&","&saveCurNum(aSum)&","&saveCurNum(bSum)&","&saveCurNum(cSum)&"" 
     sql = sql& ", '" &LnkName&"','"&LnkSex&"','"&sFrom&"','"&LnkPost&"','"&LnkMobile&"','"&LnkTel&"','"&LnkEmail&"'" 
     sql = sql& ", '"&rad_03&"','"&rad_02&"','"&OrdTM&"','"&SetCheck&"','-','-','"&SetState&"'" 
     sql = sql& " ,"&aWght3&",'"&curIDNow(0)&"','"&Get_CIP()&"','"&MemID&"','"&OrdTM&"'"
     sql = sql& ")" ': Response.Write sql

     [item_name] = 171104-HCV604 / 2017-3s-rkv01
     [mc_gross] = 252.28
     [custom] = enc(timestamp.item_name)
     // (556459174)^4.05^0.00^16^206.93^45.35^HKDHL^821098953658^Korea

    LnkName = RequestS("first_name","C",512)&" "&RequestS("last_name","C",512)
    LnkMobile = RequestS("LnkMobile","C",512)
    LnkTel = ca(7) 'RequestS("LnkTel","C",512)
    LnkEmail = RequestS("payer_email","C",512)
    LnkSex = "-" 
    InfCont = ca(8) 'RequestS("InfCont","C",512)
    country = RequestS("address_country","C",512)

*/
