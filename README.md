
* 贴心猫(IntimateCat) v4.1
* Release: 2018.04

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


### 【v4.1 ChangeLog】 (2018.04)

* [Topic-extend]A: [Add] Free form system
  - Such as questionnaire ( single-selection, multiple-selection, input, short-answer);
  - Or a simple name-tel collect form.
 
* [Topic-extend]B: [Improve] E-book template
  - Single page article display
  - Multi page article display
  - Chapter-subchapter display
 
* [Improve] Maps for users
 
* [Bugfix]
  - Changelog adjusted in Demo-site
  - Some notice in php7.2
 
* [Sister-System]
  - (Python)Wepy, Mini framework for Crawler
  - https://github.com/peacexie/python
 
* [Clear/Move]
  - Data import: replace with REST-API or write code separately.
  - Data synchronization: replace with REST-API or write code separately.
  - Clean vote: use the `Free form system` of topic instead.
  - Move: Move topic/faqs to `chn` (view group)

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


### 【v4.1更新日志】(2018.04)

* [专题扩展]之一：[增加]自由表单
  - 可以是调查类：单选，多选，填空，简答
  - 当然可以是简单的报名类……
 
* [专题扩展]之二：[完善]电子书模板
  - 单页文章展示
  - 多页文章展示
  - 按章节目录展示
 
* [完善]前台商家地图调用
 
* [修正]
  - 调整更新发布记录（演示站）
  - php7.0+下的几处小兼容问题
 
* [贴心组合]
  - Python微爬，辅助系统发布
  - https://github.com/peacexie/python
 
* [清理/移动]
  - 数据导入：使用REST-API替代，或单独写代码
  - 数据同步：使用REST-API替代，或单独写代码
  - 清理投票：使用专题的表单替代更自由
  - 移动：专题/问答：移动到 chn 分组下显示
