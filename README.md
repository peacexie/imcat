


### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......

### 【Functions】

* Support: PHP5.2~PHP7.1 / custom module / custom field / custom parameter / custom classification
* Data&share AppServer / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [IntimateCat(贴心猫)](http://txmao.txjia.com/)

### 【Setup】

* Need Environmental
  - PHP5.2 ~ PHP7.1 (Recommend: PHP5.3 ~ PHP7.0)
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

### 【v3.6 ChangeLog】 (2017.03)

* Add: app.php - Private interface:
 - Apply infomation for app/api (The server of app)
 - Include: Third party data synchronization interface

* Add: `Save Content to file` (NOT save to db as common fields)
 - Whow to use see: Faq system online
* Add: Multi-db config, It's a nother step to the High-end!
 - Whow to use see: code/cfgs/boot/cfg_db.php-cdemo (The notice)
* Add: js plug: prettyPhoto --- a set of image player plug
 - Whow to use see: /vimp/vendui/prettyPhoto/demo.html
* Add: doT --- a set of js template engine
 - Whow to use see: /vimp/vendui/common/doT.demo.html
* Add: js plug: swiper --- a set of slide plug

* Fixed: 
 - Put the system in a virtual directory, It will appear a bug
 - Several compatible in PHP v7.1
 - One security bug
 - The default links in Share DIY

* Impove: 
 - English language pack
 - Faq-(Infomation)


--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……

### 【功能介绍】

* 支持：PHP5.2~PHP7.1/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：App服务端/Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版

### 【安装提示】

* 设置站点相对目录；

 - 文件：/code/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/txmao”或 根目录用“”(空)等；
 - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：

 - 文件：/code/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
 - （可安装时配置，如果修改建议手动配置）

- 安装/配置:

 - 访问起始页：/index.php?start 检查配置；
 - 访问地址：/root/tools/setup/ 安装程序。


### 【v3.6更新日志】(2017.03)

* 增加：app.php - 专用接口 ：
 - 为app/api提供服务器端支持
 - 包含：与第三方数据同步接口

* 增加：`字段内容存文件`
 - 使用见：问答系统，为高大上系统，悄悄地又做一铺垫！
* 增加：多库调用配置
 - 使用见：code/cfgs/boot/cfg_db.php-cdemo 说明
* 增加：前端js插件：prettyPhoto图片播放插件，
 - 使用见：/vimp/vendui/prettyPhoto/demo.html
* 增加：前端js模版引擎：doT，
 - 使用见：/vimp/vendui/common/doT.demo.html
* 增加：前端js插件：swiper滑动插件, 

* 修正：
 - 把整个系统，放置在虚拟目录下，检测目录可能出问题；
 - PHP v7.1下 几处兼容性
 - 一处字符过滤安全bug
 - 分享DIY - 默认连接错误

* 完善：
 - 中英文文档
 - 问答系统（资料）
 
