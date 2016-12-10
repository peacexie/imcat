<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?>

中: 少用的大段文本:<?php echo $uarr['hname'] ?>

<?php break;case 'fldedit_note': 

$note  = "格式1:选项值=选项标题,一行一个;\n";
$note .= "格式2:模型id(栏目/类别);\n";
$note .= "格式3:pid:\"cnhn\",w:640;\n";
$note .= "格式4:bext_paras.logmode_cn, 取bext_paras资料;\n";
$note .= "[选择数组]配置规范:\n";
$note .= "*. [下拉选择][多选框][单选按钮]格式1或2或3:\n";
$note .= "*. [开窗单选][开窗多选]格式3:";
$reinc[$part] = $note;

break;case 'userm_empw':  

$data = "{$uarr['uname']} 您好！<br><br>\n\n";
$data .= "欢迎使用 {$uarr['sys_name']} 邮件找回密码功能！<br>\n";
$data .= "请点击（或复制）访问如下链接：<br>\n";
$data .= "{$uarr['url']}<br>\n";
$data .= "根据提示，找回密码。<br>\n<br>\n";
$data .= "{$uarr['sys_name']} ".date('Y-m-d H:i:s')."<br>\n";
$reinc[$part] = $note;

break;case 'plus_upbat': ?> 

说明：<br>
***1. 本程序受启发于<a href="http://www.babytree.com/">宝宝树</a>照片批量上传而制作，最先为asp版，后面php两次大改版而成；<br>
***2. 请先设置类别，再浏览图片；可用下方的(+n)按纽增加n个图片项目；一次最多可设置96个图片批量上传；<br>
***3. 本程序为增值程序，免费使用；请不要苛求它的功能；如不能满足您的需要，请用普通方式添加资料。<br>
***4. 建议把要上传的文件，放在同一文件夹中，用标题作为图片名(默认情况下,本系统把文件名作为信息的标题)；其文件名（除后缀外），
      不能用空格引号点等特殊字符； 建议全用英文半角的字母，数字或下划线；除图片名可用中文外，目录建议也不要用中文。

<?php break;case 'plus_fview': ?>   

注意：<br>
1. 大文件请用FTP上传，可参考[管理帮助] 或 [<a href="#readme.txt" target="_blank">文件目录规划</a>] 相关文件；<br>
2. 可用[预览&gt;&gt;复制链接地址]得到相关附件地址；<br>
3. [临时] 新上传文档,都放在这里,添加后会自动移动到相关文件夹； <br>
4. [当前] 编辑资料时显示此项,此文件夹下的附件关联当前资料；<br>
5. [上传] 可批量上传到文件；<br>
6. [插入] 可插入：iframe(内框架),map(地图),swf(Flash媒体),audio(音频媒体),video(视频媒体)；

<?php break;case 'wex_user': ?>  

<tr><th>昵称</th><th>OpenId</th><th>分组ID</th><th>城市</th><th>头像</th><th>性别</th><th>关注时间</th><th>发信息</th></tr>

<?php break;case '--end--': ?>  

-end-

<?php } ?>