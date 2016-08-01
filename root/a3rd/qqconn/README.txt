====================PHP SDK使用说明====================
开发者只需要按照下面的说明修改几行代码，就可以在网站上实现“QQ登录”功能。
1. 完成【QQ登录】准备工作(http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%BC%80%E5%8F%91%E6%94%BB%E7%95%A5_Server-side#.E5.87.86.E5.A4.87.E5.B7.A5.E4.BD.9C)。

2. 使用前先修改 comm/config.php 中的4个变量
	$_SESSION["appid"];
	$_SESSION["appkey"];
	$_SESSION["callback"];  
	$_SESSION["scope"];  

3. 在页面添加QQ登录按钮。详见文档说明（http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E5%BC%80%E5%8F%91%E6%94%BB%E7%95%A5_Server-side#Step1.EF.BC.9A.E6.94.BE.E7.BD.AEQQ.E7.99.BB.E5.BD.95.E6.8C.89.E9.92.AE）
   
   示例代码：	
   <a href="#" onclick='toQzoneLogin()'><img src="img/qq_login.png"></a>

4. 页面需要的js代码
	<script>
		function toQzoneLogin()
		{
			var A=window.open("oauth/qq_login.php","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizab
			le=1,status=1,titlebar=0,toolbar=0,location=1");
		} 
	</script>

5. SDK中使用session来保存必要的信息。为避免网站存在多个子域名或同一个主域名不同服务器造成的session无法共享问题，请开发者按照本SDK中comm/session.php中的注释对session.php进行必要的修改，以解决这2个问题。


====================当前版本信息====================
当前版本：V1.0

发布日期：2011-11-08

文件大小：16.5 K 


====================修改历史====================
V1.0  第一版发布，适用于基于OAuth2.0的PHP的网站接入。


====================文件结构信息====================
blog文件夹：        
	add_blog.php：登录用户发表一篇新日志

comm文件夹：
	config.php:配置文件，配置代码包中的宏参数
	util.php:  包含了OAuth认证过程中会用到的公用方法
        session.php: 支持子域名共享session，支持跨服务器共享session。

img文件夹：
	QQ登录图标，程序中将引用这个图片作为按钮图标

oauth文件夹：
	qq_login.php：响应登录按钮事件，引导用户跳转到QQ登录授权页
	qq_ccallback/php：获取具有Qzone访问权限的access_token，存储获取到的信息

photo文件夹：
	add_album.php： 获取登录用户的相册列表
	list_album.php：登录用户创建相册
	upload_pic.php：登录用户上传照片

share文件夹：
        add_share.php：将一条动态（feeds）同步到QQ空间中，展现给好友

topic文件夹：
	add_topic.php：发表一条说说到QQ空间


user文件夹：
	get_user_info.php：获取用户信息


weibo文件夹：
	add_weibo.php：发表一条微博    


QQ登录更多OpenAPI正在不断开放，详见API列表：http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91API%E6%96%87%E6%A1%A3



====================联系我们====================
QQ登录官网：http://connect.qq.com/
开发者在使用过程中有任何意见和建议，请发邮件至connect@qq.com 进行反馈。
此外，你也可以通过企业QQ（号码：800030681。直接在QQ的“查找联系人”中输入号码即可开始对话）咨询。

