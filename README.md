

贴心猫(imcat) V5.0(Alpha)

* THIS IS A DEVELOPMENT PREVIEW - DO NOT USE IT IN PRODUCTION!

--- --- --- --- --- --- --- --- --- 


### What is imcat(贴心猫)?

* `Imcat` means IntimateCat(贴心猫), It is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......
* Not entangled in OOP, not entangled in MVC, not entangled in the Design-Model, free and unrestrained!


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

贴心猫(Imcat,IntimateCat) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……
不纠结于OOP，不纠结于MVC，不纠结于设计模式，自由奔放！


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

