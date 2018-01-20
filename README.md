
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

* Support: PHP5.3~PHP7.2 / custom module / custom field / custom parameter / custom classification
* Data&share REST-API / sync / ourter import / Crawler / seo push / old vertion import
* Interface: Ftp store / Sphinx search / WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
* Demo Website: [IntimateCat(贴心猫)](http://txmao.txjia.com/)


### 【Setup】

* Need Environmental
  - PHP5.3 ~ PHP7.2 (Recommend: PHP5.4 ~ PHP7.1)
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


### 【v4.0 ChangeLog】 (2018.01)

* topic Extended：
  - No matter how big/complex the thing, It's just a special topic to deal with it!
  - It can Extended subpage freely;
  - Image-text mix typesetting, personage/company presentation, electronic documents/books... We just can use a special topic to show it!
  - The topic built-in vote-system, the old vote model, we'll move it into extend package.
  - Demo: http://txmao.txjia.com/doc/topic.htm

* Add: Skin(theme) Switch：
  - Based on bootstrap skin
  - Please download the skin css：https://bootswatch.com/
  - Config See to: /vimp/vendui/bootstrap/css/notes.txt

* Improve:
  - Improve: Compatibility parameters after pseudo-static url. eg. /dev/mkv.htm?api=Local
  - Add: Custom Path replacement configuration, See file: /cfgs/excfg/ex_repath

* Fix: 
  -> Some notice in php7.2
  -> Some bugs

--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……
不纠结于OOP，不纠结于MVC，不纠结于设计模式，自由奔放！


### 【功能介绍】

* 支持：PHP5.3~PHP7.2/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态/伪静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口：REST-API/Ftp存储/Sphinx检索/微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版


### 【安装提示】

* 环境需求
  - PHP5.3 ~ PHP7.2 (推荐: PHP5.4 ~ PHP7.1)
  - mysql5.0+
  - 扩展: MySQLi/MySQL, GD2, curl

* 设置站点相对目录；
  - 文件：/root/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/txmao”或 根目录用“”(空)等；
  - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：
  - 文件：/root/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
  - （可安装时配置，如果修改建议手动配置）

* 安装/配置: 
  - 访问起始页：/index.php?start 检查配置；
  - 访问地址：/root/tools/setup/ 安装程序。


### 【v4.0更新日志】(2018.01)

* topic 扩展专题：
  - 再大的一件事，就是一个专题搞定的事！
  - 专题随意扩展专题子页面；
  - 图文混排，人物/公司专题介绍，电子文档/书籍……都可一个专题搞定！
  - 专题集成投票，之前的独立投票，后续可能整理成扩展包分离出去。
  - 演示：http://txmao.txjia.com/doc/topic.htm

* 增加 皮肤(主题)切换：
  - 基于bootstrap界面风格
  - 自行下载主题css：https://bootswatch.com/
  - 配置参考：/vimp/vendui/bootstrap/css/notes.txt

* 增强：
  - 改进：兼容伪静态?后的参数如：/dev/mkv.htm?api=Local
  - 增加：自定义路径替换配置 见文件：/cfgs/excfg/ex_repath

* 修正：
  - php7.2 下几处警告错误
  - 一些已知bug
