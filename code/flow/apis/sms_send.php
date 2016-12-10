<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_pub_cfgs.php');

$sms = new extSms(); $sc = $sms->isClosed(); 

$stel = req('stel','','Safe4');
$smsg = req('smsg','','Safe4'); 
$act = req('act',''); 

// 发送操作
if($act=='chargeUp'){ //act=chargeUp&charge=
	$charge = req('charge'); 
	echo $charge;
}elseif(!empty($bsend)){
	$re = $sms->sendSMS($stel,$smsg,5);
	$msg = $re[1];
} 
$sb = $sc ? array(-3,0) : $sms->getBalance(); //print_r($sms->cnow);

$umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
$links = admPFunc::fileNav('send','sms');
glbHtml::tab_bar(lang('flow.ss_title')." $umsg",$links,40);
glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');

$links = " &nbsp; ---=&gt; <a ".(empty($sms->cnow['home']) ? '' : "href='{$sms->cnow['home']}'")." target='_blank'>".lang('flow.ss_web')."</a>"; 
if(!empty($sms->cnow['admin'])) $links .= " # <a href='{$sms->cnow['admin']}' target='_blank'>".lang('flow.title_admin')."</a>";
echo "<tr><th></th><th class='tr'>".lang('flow.ss_title')."</th></tr>\n"; 
glbHtml::fmae_row(lang('flow.ss_apib'),$sc ? '<a class=cur>'.lang('flow.ss_close').'!</a>' : "$sb[1]({$sms->cnow['unit']}) [{$sms->cnow['name']}] $links");
glbHtml::fmae_row(lang('flow.ss_tel'),"<textarea name='stel' id='stel' cols='60' rows=4></textarea>");
glbHtml::fmae_row(lang('flow.ss_msg'),"<textarea name='smsg' id='smsg' cols='60' rows=4></textarea>"); 

$send = ($sc||$sb[1]==0) ? lang('flow.ss_tip0') : "<input name='bsend' type='submit' class='btn' value='".lang('flow.ss_send')."' />";
echo "\n<tr><td class='tc' width='25%'>".lang('flow.ss_send')."</td>\n<td class='tl'> &nbsp; ".lang('flow.ss_cnta')."[<span id='jscnt'>0</span>]".lang('flow.ss_cntb')." &nbsp; 
<input name='bsend' type='button' class='btn' value='".lang('flow.ss_set')."' onClick='sms_set()' /> &nbsp; &nbsp; $send </td></tr>";

$note = empty($sms->cnow['note']) ? '' : "<br>".str_replace('{file}',$file,$sms->cnow['note']); 
glbHtml::fmae_row(lang('flow.ss_tips'),lang('flow.ss_tipc')."{$note}");
glbHtml::fmt_end();

echo "<script>
var otel = jsElm.jeID('stel'); 
var omsg = jsElm.jeID('smsg'); 
function sms_set() {
	otel.value = '135-3743-2147';
	omsg.value = '【{$_cbase['sys_name']}】".lang('flow.ss_balance')." $sb[1]({$sms->cnow['unit']}), [{$sms->cnow['name']}]';
	omsg.onblur();
}
omsg.onblur = function(){ jsElm.jeID('jscnt').innerHTML = omsg.value.length; }
</script>";

?>
