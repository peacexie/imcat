<?php
namespace imcat;
$_cbase['run']['wedemo'] = 1;
require dirname(__FILE__).'/we_cfgs.php';

$act = req('act','main'); 
$kid = req('kid','admin');
$debug = basReq::arr('debug','Html');

$ubase = PATH_ROOT."/plus/api/wechat.php?";
$dcfg = array('api','appid','token','appsecret','orgid','openid');
$wecfg = wysBasic::getConfig($kid); 
$db = db(); 

glbHtml::page("微信接口调试");
eimp('initJs','jquery;comm;comm(-lang);/_pub/a_jscss/weixin'); 
eimp('initCss','stpub;comm'); // bootstrap,
echo basJscss::imp("/_pub/a_jscss/weixin.js"); 
echo '<style type="text/css">
#tester { left:620px; top10px; position:fixed; background:#FFF; padding:10px; border:1px solid #CCC; }
.ngray { color:#F3F; }
#pic_res img { margin:5px; }
</style>';
glbHtml::page('body',' style="margin:20px;"');
wxDebugNavbar();

?>

<div id="tester">

<p>点击出二维码：[<a href="#" onClick="getQrcode('login')">登录</a> 
        # <a href="#" onClick="getQrcode('upload')">上传</a> 
        # <a href="#" onClick="getQrcode('mbind')">mbind</a>
        # <a href="#" onClick="getQrcode('getpw')">getpw</a>] <br>
* <span id="qr_sid">qr_sid</span>；<br>
* <span id="qr_stampys">qr_stampys</span>；<br> 
* <span id="qr_signys">qr_signys</span>；<br>
<img src="#" width="320" id="scanimg">
</p>
</div>

<table width="600" border="1">
    <tr>
        <td>登录结果</td>
        <td>上传结果</td>
    </tr>
    <tr>
        <td id="msg_res">&nbsp;</td>
        <td id="pic_res">&nbsp;</td>
    </tr>
</table>

<hr>

<p class="ngray">电脑扫描登录-流程</p>

<p>1. （电脑）点出二维码，并定时检测：</p>
&nbsp; * a. 一旦二维码出来，定时检测js：checkLogin(data.sid,extp);；<br>
&nbsp; * b. 加参数：sid,extp,stampys,signys是为了安全考虑；<br>

<p>2. （手机）扫描二维码：<br>
&nbsp; * a. 配置在公网上，可直接扫描；<br>
&nbsp; * b. 配置在内网上，可用后台的调试程序，复制qr_sid，推送二维码，把结果的链接复制到手机登录；<br>
</p>

<p>3. 检测结果：见上</p>

<p>4. 登出重新调试：<br>
&nbsp; * a. 因已经是登录状态就无需登录；所以请另开浏览器窗口，在未登录情况下测试；<br>
&nbsp; * b. 如果是登录状态：<a href="<?php echo surl('umc:'); ?>?logout">登出重新调试</a>
</p>

<hr>

<p class="ngray">电脑扫描上传-流程(还未处理FTP路径)</p>

<p>1. （电脑）点出二维码，并定时检测：</p>
&nbsp; * a. 一旦二维码出来，定时检测js：checkUpload(data.sid,extp);；<br>
&nbsp; * b. 加参数：sid,extp,stampys,signys是为了安全考虑；<br>

<p>2. （手机）扫描二维码：<br>
&nbsp; * a. 配置在公网上，可直接扫描，根据提示在公众号上传图片；<br>
&nbsp; * b. 配置在内网上，可用后台的调试程序，复制qr_sid，推送二维码；并使用【发图片(传图使用)】模拟发图；<br>
&nbsp; * c. 传图片后，观察电脑屏幕变化；<br>
</p>

<p>3. 检测结果：见上</p>

<p>4. 文档（二手车/二手房）保存成功后：<br>
清除相关状态：<br>
weixin_qrlimit - 二维码：atime=0<br>
weixin_msgget - 发送记录：atime=0<br>
</p>

<hr>

<p class="ngray">电脑扫描-发信息到微信</p>
<p>
<select name="sendaid" id="sendaid" onChange="getQrscan(this)">
<option value="">---请选文档（商品）---</option>
<?php
//$list = $db->table('base_model')->field('kid,title')->limit(3)->select(); if($list)foreach($list as $r){}}
$list = $db->table('docs_cargo')->order('did DESC')->select();
foreach($list as $row){
    echo "<option value='cargo.{$row['did']}'>[商品]{$row['did']}:{$row['title']}</option>";
}
?>
</select>
<br>
<select name="sendmid" id="sendmid" onChange="getQrscan(this)">
<option value="">---请选会员（新商家）---</option>
<?php
foreach(array('company'=>'公司','organize'=>'组织','govern'=>'单位') as $umod=>$title){
    $list = $db->table("users_$umod")->order('uid DESC')->select();
    foreach($list as $row){
        echo "<option value='{$umod}.{$row['uid']}'>[$title]{$row['uid']}:{$row['company']}</option>";
    }
}
?>
</select>
</p>

<p>end</p>
<p>&nbsp;</p>
<p>&nbsp; </p>

<script type="text/javascript">
var ubase = '<?php echo $ubase; ?>';
function clearQrcode(){
    $('#scanimg').attr('src','');
}
function getQrscan(obj){
    $('#scanimg').attr('src','');
    var kid = $(obj).val();
    if(!kid){ alert('请选一个会员或文档！'); return; }
    var extp = Math.random().toString(36).substr(2); 
    extp = kid+','+extp;
    var url = 'actys=getQrcode&qrmod=send&extp='+extp+'&datatype=js&varname=data';
    $.getScript(ubase+url, function(){ 
        $('#scanimg').attr('src',data.url);
        $('#qr_sid').html('qr_sid='+data.sid);
        $('#qr_stampys').html('qr_stampys='+data.stampys);
        $('#qr_signys').html('qr_signys='+data.signys);
        jsLog('getQrcode:'+extp); //调试
    });
}

function getQrcode(qrmod){
    clearQrcode();
    var extp = Math.random().toString(36).substr(2); 
    if(qrmod=='mbind'){ extp = '<?php echo comConvert::sysRevert('yufish', 0, '', 600); ?>'; }
    var url = 'actys=getQrcode&qrmod='+qrmod+'&extp='+extp+'&varname=data';
    $.getScript(ubase+url, function(){
        $('#scanimg').attr('src',data.url);
        $('#qr_sid').html('qr_sid='+data.sid);
        $('#qr_stampys').html('qr_stampys='+data.stampys);
        $('#qr_signys').html('qr_signys='+data.signys);
        if(qrmod=='login'){
            checkLogin(data.sid,extp,data.stampys,data.signys);
        }else if(qrmod=='mbind'){
            extp = 'yufish'; 
            //checkMbind(data.sid,extp,data.stampys,data.signys);
        }else if(qrmod=='getpw'){
            extp = ''; 
        }else if(qrmod=='upload'){
            //$('#pic_res').html('检测图片中…');
            checkUpload(data.sid,extp,data.stampys,data.signys);
        }
        jsLog('getQrcode:'+extp); //调试
    });
}

function checkLogin(sid,extp,stampys,signys){
    var url = 'actys=chkLogin&scene='+sid+'&extp='+extp+'&stampys='+stampys+'&signys='+signys+'&varname=data';
    $.getScript(ubase+url, function(){
        if(typeof(data.error)=='undefined' || typeof(data.message)=='undefined' ){
            alert('服务器返回格式错误。');
            return '';
        //}else if(data.user_info.mid=="-1"){
            //$('#msg_res').html("已经是登录状态，请先登出！<br>mid="+data.user_ibak.mid+"<br>mname="+data.user_ibak.mname+"");
            //return '';
        }else if(data.uname){
            $('#msg_res').html("登录成功！<br>mname="+data.uname+"");
            return '';
        } 
        setTimeout("checkLogin('"+sid+"','"+extp+"','"+stampys+"','"+signys+"')",2000);
    });    
}

var pstr = ','; //用这个判断，已经显示了的，就不再显示
function checkUpload(sid,extp,stampys,signys){ //xxx
    var url = 'actys=chkUpload&scene='+sid+'&extp='+extp+'&stampys='+stampys+'&signys='+signys+'&varname=data';
    $.getScript(ubase+url, function(){        
        if(typeof(data.error)=='undefined' || typeof(data.message)=='undefined' ){
            alert('服务器返回格式错误。');
            return '';
        }else if(data.error=='noScan'){
            $('#pic_res').html('['+data.error+']'+data.error);
            //不能return
        }else if(data.error){
            $('#pic_res').html(data.error);
            return '';
        }else if(data.res){
            var nstr = ''; 
            for (var i = 0; i < data.res.length; i++) { 
                var medid = data.res[i].media_id; 
                var imgmid = ubase+'actys=loadFile&mediaid='+medid+'&kid=<?php echo $kid; ?>'; 
                var imgurl = data.res[i].detail; 
                var img = imgurl.indexOf('mmbiz.qpic')>0 ? imgmid : imgurl;
                if(pstr.indexOf(medid)<=0){
                    nstr += '<a href="'+img+'"><img src="'+img+'" height="60"></a>';
                    pstr += medid+','; 
                }
            };
            nstr && $('#pic_res').append(nstr);
        } 
        setTimeout("checkUpload('"+sid+"','"+extp+"','"+stampys+"','"+signys+"')",2000);
    });    
}

</script>

<?php

glbHtml::page('end');

?>

