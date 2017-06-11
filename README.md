

* During 2017-06 ~ 2017-07 
V3.8 Upgrading...
During this time, please use v3.7

* 2017-06 ~ 2017-07 期间 
V3.8升级中……再
在此期间, 请使用v3.7


--- --- --- --- --- --- --- --- --- 

### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......
* Not entangled in OOP, not entangled in MVC, not entangled in the Design-Model, free and unrestrained!

### 【Functions】

* Support: PHP5.3~PHP7.1 / custom module / custom field / custom parameter / custom classification
* Data&share AppServer / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [IntimateCat(贴心猫)](http://txmao.txjia.com/)

### 【Setup】

* Need Environmental
  - PHP5.3 ~ PHP7.1 (Recommend: PHP5.4 ~ PHP7.0)
  - mysql5.0+
  - Extended libs: MySQLi/MySQL, GD2, curl

* Set the relative path of the site: 
  - all files will be placed on the site of any directory; 
  - In file: /root/cfgs/boot/_paths.php Set Param: PATH_PROJ; 
  - The value of PATH_PROJ is the relative path of the site, eg:['/txmao'] or root [''](empty string)
  - For the first time, It will automatically correct the project path, so you can omit the operation

* Edit DB-Config(It can be edited while installing, advice you config manually) 
  - File: /root/cfgs/boot/cfg_db.php; 
  - Tip: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.

### 【v3.7 ChangeLog】 (2017.05)

* Improve: Member:reg/login 
  - Add: E-mail active the account after register
  - Add: Send sms verification code before register

* Improve: Mobile experience
  - Dynamic page: automatic mobile terminal adaptation
  - Static page: JS mobile adapter jump
  - Tools/Info page: responsive device adaptation

* Optimization: Code details:
  - Template : the default suffix change to `.htm`
  - Delete some php debug code
  - Delete some js debug code

* Adjust logo/sinc directory
* Add: Add some demo template

* Fix:
  - Page Jump error
  - The cache switch Does'nt work 

* Improve: 
  - English language pack
  - Faq-(Infomation)


--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……
不纠结于OOP，不纠结于MVC，不纠结于设计模式，自由奔放！

### 【功能介绍】

* 支持：PHP5.3~PHP7.1/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：App服务端/Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版

### 【安装提示】

* 设置站点相对目录；
  - 文件：/root/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/txmao”或 根目录用“”(空)等；
  - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：
  - 文件：/root/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
  - （可安装时配置，如果修改建议手动配置）

* 安装/配置: 
  - 访问起始页：/index.php?start 检查配置；
  - 访问地址：/root/tools/setup/ 安装程序。


### 【v3.7更新日志】(2017.05)

* 增强：会员:注册/登录相关功能 
  - 增加：注册邮件激活：
  - 增加：短信验证码注册：

* 增强：移动版体验
  - 动态页:自动移动端适配
  - 静态页:js移动适配跳转
  - 工具提示页:响应式设备适配

* 优化：代码细节：
  - 模板，默认后缀改为 `.htm`
  - 删除一批php调试代码
  - 删除一批js调试代码

* 调整logo/sinc目录
* 增加：模板调用演示

* 修正：
  - 翻页跳转错误
  - 模板缓存开关 无效问题

* 完善：
  - 中英文文档
  - 问答系统（资料）
 
