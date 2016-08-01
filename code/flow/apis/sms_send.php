<?php
(!defined('RUN_MODE')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$sms = new extSms(); $sc = $sms->isClosed(); 

$stel = basReq::val('stel','','Safe4');
$smsg = basReq::val('smsg','','Safe4'); 
$act = basReq::val('act',''); 

// 发送操作
if($act=='chargeUp'){ //act=chargeUp&charge=
	$charge = basReq::val('charge'); 
	echo $charge;
}elseif(!empty($bsend)){
	$re = $sms->sendSMS($stel,$smsg,5);
	$msg = $re[1];
} 
$sb = $sc ? array(-3,0) : $sms->getBalance(); //print_r($sms->cnow);

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav('send','sms');
glbHtml::tab_bar("短信发送$umsg",$links,40);
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');

$links = " &nbsp; ---=&gt; <a ".(empty($sms->cnow['home']) ? '' : "href='{$sms->cnow['home']}'")." target='_blank'>官网</a>"; 
if(!empty($sms->cnow['admin'])) $links .= " # <a href='{$sms->cnow['admin']}' target='_blank'>管理</a>";
echo "<tr><th></th><th class='tr'>短信发送</th></tr>\n"; 
glbHtml::fmae_row('接口余额',$sc ? '<a class=cur>接口已关闭或无效!</a>' : "$sb[1]({$sms->cnow['unit']}) [{$sms->cnow['name']}] $links");
glbHtml::fmae_row('电话号码',"<textarea name='stel' id='stel' cols='60' rows=4></textarea>");
glbHtml::fmae_row("短信内容","<textarea name='smsg' id='smsg' cols='60' rows=4></textarea>"); 

$send = ($sc||$sb[1]==0) ? '(余额为0,不能发送)' : "<input name='bsend' type='submit' class='btn' value='发送' />";
echo "\n<tr><td class='tc' width='25%'>提交</td>\n<td class='tl'> &nbsp; 已输入[<span id='jscnt'>0</span>]个字符 &nbsp; 
<input name='bsend' type='button' class='btn' value='设置' onClick='sms_set()' /> &nbsp; &nbsp; $send </td></tr>";

$note = empty($sms->cnow['note']) ? '' : "<br>".str_replace('{file}',$file,$sms->cnow['note']); 
glbHtml::fmae_row('提示说明',"号码一行一个,或用,好分开; 内容240字以内{$note}");
glbHtml::fmt_end();

echo "<script>
var otel = jsElm.jeID('stel'); 
var omsg = jsElm.jeID('smsg'); 
function sms_set() {
	otel.value = '135-3743-2147';
	omsg.value = '【{$_cbase['sys_name']}】余额$sb[1]({$sms->cnow['unit']})，[{$sms->cnow['name']}]';
	omsg.onblur();
}
omsg.onblur = function(){ jsElm.jeID('jscnt').innerHTML = omsg.value.length; }
</script>";

?>
