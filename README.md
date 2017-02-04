


### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......

### 【Functions】

* Support: PHP5.2~PHP7.0 / custom module / custom field / custom parameter / custom classification
* Data&share / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [IntimateCat(贴心猫)](http://txmao.txjia.com/)

### 【Setup】

* Need Environmental
  - PHP5.2 ~ PHP7.x (Recommend: PHP5.3 ~ PHP5.6)
  - mysql5.0+
  - Extended libs: MySQLi/MySQL, GD2, curl

* Set the relative path of the site: 
  - all files will be placed on the site of any directory; 
  - In file: /code/cfgs/boot/_paths.php Set Param: PATH_PROJ; 
  - The value of PATH_PROJ is the relative path of the site, eg:['/txmao'] or root [''](empty string)
  - For the first time, It will automatically correct the project path, so you can omit the operation

* Edit DB-Config(It can be edited while installing, advice you config manually) 
  - File: /code/cfgs/boot/cfg_db.php; 
  - Tip: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.

### 【v3.5 ChangeLog】 (2017.02)

* Add: Hook Function(Class), for easy extend 
* Add: Manual(free) push info at front 
* Add: Ftp store, it can easy add a cloud-store api in system 
* Add: Sphinx search, for large data
* Impove thumb, support Ftp store 
* Add [广告] Flag on the advertise 
* Impove: batch static create, support mod-kid-view params
* Optiming: static dirs( move html/ures dirs from vary) 


--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……

### 【功能介绍】

* 支持：PHP5.2~PHP7.0/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版

### 【安装提示】

* 设置站点相对目录；
 - 文件：/code/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/txmao”或 根目录用“”(空)等；
 - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：
 - 文件：/code/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
 - （可安装时配置，如果修改建议手动配置）

* 安装/配置:
 - 访问起始页：/index.php?start 检查配置；
 - 访问地址：/root/tools/setup/ 安装程序。

### 【v3.5更新日志】(2017.02)

* 增加：Hook钩子函数(类), 用于扩展 
* 增加：前台-手动推送资料 
* 增加：附件Ftp存储, 同时与后期云存储打下基础 
* 增加：Sphinx检索, 应对海量数据
* 优化: thumb 优化, 支持ftp存储 
* 优化: 为广告位添加 [广告]标识 
* 优化：静态文件url目录（默认把html目录移动到跟目录） 
* 增强：静态生成，支持 mod-kid-view的第三个参数批量生成静态 
