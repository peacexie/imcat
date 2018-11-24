
* 贴心猫(imcat) V4.4 Released (2018-11)
* 修复几处小bug。 Some bugs fixed! (2018-11-23)

--- --- --- --- --- --- --- --- --- 


### What is imcat(贴心猫)?

* `Imcat` means IntimateCat(贴心猫), It is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......
* Not entangled in OOP, not entangled in MVC, not entangled in the Design-Model, free and unrestrained!


### 【Functions】

* Support: PHP5.3~PHP7.2 / custom module / custom field / custom parameter / custom classification
* Data&share REST-API / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [imcat(贴心猫)](http://imcat.txjia.com/)


### 【Setup】

* Need Environmental
  - PHP5.3 ~ PHP7.2 (Recommend: PHP5.4 ~ PHP7.2)
  - mysql5.1+
  - Extended libs: MySQLi/MySQL, GD2, curl

* Set the relative path of the site: 
  - all files will be placed on the site of any directory; 
  - In file: /root/cfgs/boot/_paths.php Set Param: PATH_PROJ; 
  - The value of PATH_PROJ is the relative path of the site, eg:['/imcat'] or root [''](empty string)
  - For the first time, It will automatically correct the project path, so you can omit the operation

* Edit DB-Config(It can be edited while installing, advice you config manually) 
  - File: /root/cfgs/boot/cfg_db.php; 
  - Tips: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'; 
  - You can `Edit` it according to the service environment.

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.


### 【v4.4 ChangeLog】 (2018.11)

* Core: Add namespace(imcat), Adjust root entries, Adjust dirs;

* Add: `composer` extend;

* Add: Ali-Oss Store-extend;

* Add: Free-Data-tag, support Multi-db;

* Add: Haofangtong API (Only Data API, NO views) --- (provided by `CrabyLi`);

* Improve: Store-Config --- (It's can config by file-types and models);

* Improve: http-collection remote data, Add cache remote data;

* Add extend: ThinkPHP(v31)Core for PHP7 --- (third party packages that are almost independent of the system)

* Update: The db-manage component(Adminer)

* Fix: Some url shared in wechat, It will jump to homepage.


--- --- --- --- --- --- --- --- --- 

贴心猫(Imcat,IntimateCat) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……
不纠结于OOP，不纠结于MVC，不纠结于设计模式，自由奔放！


### 【功能介绍】

* 支持：PHP5.3~PHP7.2/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态/伪静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：REST-API/Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版


### 【安装提示】

* 环境需求
  - PHP5.3 ~ PHP7.2 (推荐: PHP5.4 ~ PHP7.1)
  - mysql5.1+
  - 扩展: MySQLi/MySQL, GD2, curl

* 设置站点相对目录；
  - 文件：/root/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/imcat”或 根目录用“”(空)等；
  - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：
  - 文件：/root/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
  - （可安装时配置，如果修改建议手动配置）

* 安装/配置: 
  - 访问起始页：/index.php?start 检查配置；
  - 访问地址：/root/tools/setup/ 安装程序。


### 【v4.4更新日志】(2018.11)

* 核心: 添加命名空间, 整合root入口, 调整目录 --- (本次调整范围较大,升级用户请安装新系统再导入旧资料)

* 添加: `composer` 扩展

* 添加: 阿里OSS云存储扩展

* 添加: 自由数据调用标签，支持多库调用

* 添加: 好房通数据API (仅数据接口,无显示页) --- (`CrabyLi` 提供)

* 改善: 存储配置 --- (支持按模块/类型配置不同存储类型)

* 改善: http采集远程数据，增加缓存功能

* 增加扩展: ThinkPHP(v31)核心-PHP7兼容包 --- (与本系统几乎无关的第三方包)

* 更新：数据库管理组件（Adminer）

* 修正：微信分享地址，跳转到首页

