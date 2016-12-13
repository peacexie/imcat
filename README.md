

### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......

### 【Functions】

* Support: PHP5.2~PHP7.0 / custom module / custom field / custom parameter / custom classification
* Data&share / sync / ourter import / Crawler / seo push / old vertion import
* Interface: WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)
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

### 【v3.4 ChangeLog】 (2016.12)

* Add: Votes system / Set Different templates for grade,category
* Add: Area sites jumping (solutions) / Subsidiary domain jump to the main domain (configuration)
* Add: digg Extend / Public-Free Parameter Setting 
* Add: bootstrap Frame / phpQuery / Snoopy Crawl Tools
* Fixed: security bugs 
* Improve: load css,js
* Merge: (move)tpls/skin dirs
* Update: Use NEW IntimateCat Icon 
* Update: layer / KindEditor / PHPMailer

--- --- --- --- --- --- --- --- --- 

贴心猫(Intimate) 是以PHP+MySQL架构设计的通用网站系统，简约、实用、轻量、开源。
适用于：网店，医院，学校，企业站，个人网站，企业内部Intranet，中小型行业门户站点等……

### 【功能介绍】

* 支持：PHP5.2~PHP7.0/自定义模块/自定义字段/自定义参数/自定义分类/模板继承/tag缓存/js标签/静态
* 模型：问答系统/内部公文/商品展销/新闻/专题/课程资源/样例文档/用户
* 接口: 微信/短信/地图/支付/数据分享/数据同步/外部导入/采集/推送/导入旧版

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

### 【更新日志】(2016.12)

* 增加: 投票系统/按等级,栏目设置不同模板
* 增加: 分站跳转/多域名跳转到主域名
* 增加: digg扩展/公共自由参数设置
* 内置: bootstrap框架/phpQuery,Snoopy采集工具 
* 修复: js显示点击等字段的安全漏洞
* 优化: css,js加载项 
* 合并: 合并和移动tpls/skin目录
* 更换: (IntimateCat)图标 
* 更新: layer组件/KindEditor编辑器/邮件组件PHPMailer
