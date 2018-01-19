
* During 2018-01-13 ~ 2018-01-21
v4.0 Beta Upgrading... Please use/down v3.9

* 2018-01-13 ~ 2018-01-21 期间 
v4.0 Beta 升级中…… 请使用/下载 v3.9

--- --- --- --- --- --- --- --- --- 


### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......
* Not entangled in OOP, not entangled in MVC, not entangled in the Design-Model, free and unrestrained!


### 【Functions】

* Support: PHP5.3~PHP7.1 / custom module / custom field / custom parameter / custom classification
* Data&share REST-API / sync / ourter import / Crawler / seo push / old vertion import
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
  - Tips: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'; 
  - You can `Edit` it according to the service environment.

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.


### 【v3.9 ChangeLog】 (2017.10)

* REST-API: GA-Version

* Synchronous publishing Extend model:
  - It's easier to pack and more flexible to extend!

* Synchronous publishing Nodejs Auxiliary system, Build Web application ecosystem!

* Icon: Update incon kit, Add menu-icon in admin

* Add: Pseudo static deployment

* Improve: Extend-Cache

* Improve: Perm-function
  - Improve: Perm-Set/Check
  - Add: Perm-Inherit

* Add: Short-Link API(use internally)

* Adjusting template, Admin-UI, use bootstrap model

* Fix: 
  - Page-jump
  - Some bugs

* Links & Reference

  - Rewrite Config(Apache/Nginx/iis7+)  
  - -> http://txmao.txjia.com/root/run/umc.php?faqs.2017-9h-4bq1

  - idea: 贴心猫(IntimateCat)Developer confession  
  - -> http://txmao.txjia.com/root/run/umc.php?faqs.2017-9a-f3a1

  - Extend[贴心扩展]  
  - -> http://txmao.txjia.com/dev.php?extend

  - Nodejs Mini Framework  
  - -> http://txjia.com/peace/wenode.htm

--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……
不纠结于OOP，不纠结于MVC，不纠结于设计模式，自由奔放！


### 【功能介绍】

* 支持：PHP5.3~PHP7.1/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态/伪静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：REST-API/Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版


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


### 【v3.9更新日志】(2017.10)

* REST-API：发布正式版

* 同步发布扩展模块：
  - 打包更轻松，扩展更灵活！

* 同步发布Nodejs辅助系统，打造Web应用生态圈！

* 图标：更新图标组件，后台菜单添加自定义图标设置

* 增加：伪静态部署

* 增强：扩展缓存

* 改善权限功能
  - 改善：权限判断，设置
  - 增加：权限继承

* 增加：短链接接口(内部使用)

* 调整模板，后台部分UI，使用bootstrap模态框

* 修正：
  - 分页跳转
  - 一些已知bug

* 相关连接/配置参考

  - Rewrite配置(Apache/Nginx/iis7+)  
  - -> http://txmao.txjia.com/root/run/umc.php?faqs.2017-9h-4bq1

  - idea：贴心猫(IntimateCat)开发者自白书  
  - -> http://txmao.txjia.com/root/run/umc.php?faqs.2017-9a-f3a1

  - 贴心扩展[Extend]  
  - -> http://txmao.txjia.com/dev.php?extend

  - Nodejs微框架  
  - -> http://txjia.com/peace/wenode.htm
