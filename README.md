


贴心猫(imcat) V5.0 Released (2019-10)


--- --- --- --- --- --- --- --- --- 


### What is imcat(贴心猫)?

* `Imcat` means IntimateCat(贴心猫), It is a set of general web system, 
  It is a Contracted, Lightweight and Practical System following MIT open source protocol.
* It is between the framework and CMS, and it is also a practical PHP toolkit.
* It deeply optimizes the architecture of multi model, multi classification, multi sub page, multi language (sub sites).
* It's not only the PC side, It can also provide mobile-terminal API conveniently, like as Wap / App / Mini-program.
* It can be infinitely extended, like as customized Model / user fields / user parameter / classification...
* It has a cross-border auxiliary system, like as Python crawler, nodejs push or chat service.


### 【Functions】

* Support: PHP5.4~PHP7.3 / custom module / custom field / custom parameter / custom classification
* Data&share REST-API / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [imcat(贴心猫)](http://imcat.txjia.com/)


### 【Setup】

* Need Environmental
  - PHP5.4 ~ PHP7.3 (Recommend: PHP5.6 ~ PHP7.3)
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


### 【v5.0 ChangeLog】 (2019.10)

* [View-Details](http://imcat.txjia.com/doc.php?uplog-5_0)


--- --- --- --- --- --- --- --- --- 

贴心猫(Imcat,IntimateCat) 是以PHP+MySQL架构设计的通用网站系统，简约、轻量、实用、开源、共享。  
介于框架和CMS之间，同时也是一个实用的php工具包；  
深入优化： 多模型， 多分类， 多子页面， 多语言（分站） 架构；  
深度结合：Pc/Wap/App/小程序多端展示；  
无限扩展：无限自定义:模型/字段/参数/分类…  
跨界辅助：拥有 Python爬虫， Nodejs推送聊天 等辅助系统。


### 【功能介绍】

* 支持：PHP5.4~PHP7.3/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态/伪静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：REST-API/Ftp存储/Sphinx 检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版


### 【安装提示】

* 环境需求
  - PHP5.4 ~ PHP7.3 (推荐: PHP5.6 ~ PHP7.3)
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


### 【v5.0更新日志】(2019.10)

* [查看详情](http://imcat.txjia.com/dev.php?uplog-5_0)

