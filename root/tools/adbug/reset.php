<?php 
require(dirname(__FILE__).'/_config.php'); 

$act = basReq::val('act','');
$part = basReq::val('part','');
$inptype = @$_GET['inptype']; 
$inpval = @$_GET['inpval'];

$cfg = array(
	'check'=>'基本环境',
	'image'=>'Image',
	'memory'=>'Memory',
	'upload'=>'Upload',
	'bomcheck'=>'BOMCheck',
	'remote'=>'Remote',
);
$title = isset($cfg[$act]) ? $cfg[$act] : '？'.$act;
$orguser = 'adm_'.basKeyid::kidRand(0,3);
$orgpass = 'pass_'.basKeyid::kidRand(0,3);

glbHtml::page("系统重置-$title",1);
glbHtml::page('imp');
?>
<link rel='stylesheet' type='text/css' href='./style.css'/>
</head><body>

<div>
  <table width="100%" border="1" class="tblist">
    <?php tadbugNave(); ?>
  </table>
</div>

<div>
<p class="tip">系统重置 / DB操作</p>

  <table width="100%" border="1" class="tblist">
    <tr>
      <td class="tc" colspan="4">(code/cfgs/boot/cfg_adbug.php页参数)
      &nbsp; [can_reset=<?php echo $can_reset; ?>] &nbsp; 
      <span style="color: #<?php echo $can_reset ? "ff0000" : "008000"; ?>; font-weight : bold;"><?php echo $can_reset ? "危险" : "安全"; ?></span>
      正式使用请设置为0
      </td>
    </tr>
    <tr class="tc">
      <td width="25%" class="tip">清理</td>
      <td width="25%"><a href="?act=clrTmps">清理临时文件</a></td>
      <td width="25%"><a href="?act=clrLogs">清理DB日志</a></td>
      <td width="25%"><a href="?act=clrCTpl">清理模板缓存</a></td>
    </tr>     
    <tr class="tc">
      <td class="tip">DB检测</td>
      <!--td class="c999">重置DB数据</td-->
      <td><a href="?act=cdbPKey">无主索引数据表</a></td>
      <td><a href="?act=cdbV255">varchar(>255)字段</a></td>
      <td><a href="?act=cdbMKey">组合索引数据表</a></td>
    </tr>
    <tr class="tc">
      <td class="tip">DB导出</td>
      <td><a href="?act=expStru">导出结构</a></td>
      <td><a href="?act=expData">导出所有</a></td>
      <td><a href="?act=expBack">导出备份</a></td>
      </td>
    </tr> 
    <tr class="tc">
      <td class="tip">重置</td>
      <td><a href="?act=rstRndata">重置数据</a> , <a href="?act=rstTabcode">重建对比</a></td>
      <td><a href="?act=rstPub&part=main">重发布</a>:(<a href="?act=rstPub&part=vary">vary</a>,<a href="?act=rstPub&part=vimp">vimp</a>)</td>
      <td><a href="?act=rstCache">重置缓存</a></td>
    </tr>
    <form action="?" method="get">
    <tr class="tc">
      <td class="tip">帐号密码</td>
      <td><input name="uname" value="<?php echo $orguser; ?>" type="text" onBlur="chkIdpass(this,0,3)" maxlength="12"></td>
      <td><input name="upass" value="<?php echo $orgpass; ?>" type="text" onBlur="chkIdpass(this,1,6)" maxlength="18"></td>
      <td><input name="" value="重置" type="submit"><input name="act" type="hidden" value="rstIDPW"></td>
    </tr> 
    </form>
  </table>

</div>

<?php
$re = '-';

$names = array(

	'clrTmps'=>'清理临时文件',
	'clrLogs'=>'清理DB日志',
	'clrCTpl'=>'清理模板缓存',
	
	'rstCache'=>'重置缓存',
	'rstIDPW'=>'重置帐号密码',
	'rstPub'=>'重发布', 
	
	'cdbPKey'=>'无主索引数据表',
	'cdbV255'=>'varchar(>255)字段',
	'cdbMKey'=>'组合索引数据表',
	
	'expStru'=>'导出数据表结构',
	'expData'=>'DB导出',

);

if(in_array($act,array('expData','expBack'))){
	$method = $act=='expBack' ?  "dataExpGroup" : 'dataExp';
	$dpre = $act=='expBack' ?  "gbak" : 'data';
	devData::$method("/dbexp/$dpre~",$part); 
}elseif(in_array($act,array('expStru'))){
	devData::struExp('/dbexp/');
}elseif($act=='rstIDPW'){
	if(empty($can_reset)){
		$exmsg = "<br>当前设置不允许重置密码：请设置 <span class='cF03'>[ \$can_reset = '1' ]</span>";
		$_res = 'Error';
	}else{
		$uname = basReq::val('uname');
		$upass = basReq::val('upass');
		if($uname && $upass){ 
			devData::rstIDPW($uname,$upass);
			$exmsg = "<br>帐号:[<span class='cF03'>$uname</span>] 密码:[<span class='cF03'>$upass</span>] 请牢记！";
		}else{
			$exmsg = '设置错误！';	
		}
	}
//}elseif($act=='xxx'){
}elseif(in_array($act,array('cdbPKey','cdbV255','cdbMKey'))){
	$_res = devData::cdbStrus($act);
}elseif(method_exists('devData',$act)){
	devData::$act();
}elseif($act){
	echo "Error:$act";	
}
@$re = $act ? $names[$act].' ['."$act:".$part.'] - '.(empty($_res) ? '完成' : $_res) : '';
$re .= '<br> @ '.date('Y-m-d H:i:s');

?>

<div>
  <p>处理结果</p>
  <table width="100%" border="1" class="tblist">
    <tr id="res" class="tc">
      <td><?php echo $re.@$exmsg; ?></td>
    </tr> 
    <tr>
      <td class="tip">提示:未安装状态,不能执行DB相关操作。</td>
    </tr> 
  </table>
</div>

<script>
function chkIdpass(e,no,len){
	var orgcfgs = '<?php echo "$orguser,$orgpass"; ?>'.split(',');
	var simpass = ',<?php echo implode(',',glbConfig::read('simpass','ex')); ?>,';
	var tmp = $(e).val().replace(/\W/g, ""); //jsLog(tmp);
	if(simpass.indexOf(tmp)>0 || tmp.length<len){
		tmp = orgcfgs[no];
		alert('帐号密码不规范：\n字母/数字/下划线组成, 且字母开头\n帐号:3~15个字符, 密码:6~24个字符\n且不能是如下简单字串:\n'+simpass);
	}
	$(e).val(tmp);
}
</script>

<?php
glbHtml::page('end');
?>