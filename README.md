
---

* During 2006-12-10 ~ 2006-12-25 
V3.4 Upgrade...

* 2006-12-10 ~ 2006-12-25 期间 
V3.4升级中……

---


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

### 【v3.4 ChangeLog】 (2016.1225)

* Add: Votes system / Set Different templates for grade,category
* Add: Area sites jumping (solutions) / Subsidiary domain jump to the main domain (configuration)
* Add: digg Extend / Public-Free Parameter Setting 
* Add: bootstrap Frame / phpQuery / Snoopy Crawl Tools
* Fixed: security bugs 
* Improve: load css,js
* Merge: (move)tpls/skin dirs
* Update: Use NEW IntimateCat Icon 
* Update: layer / KindEditor / PHPMailer
