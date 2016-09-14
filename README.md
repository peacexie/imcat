
### What is IntimateCat(贴心猫)?

* IntimateCat(贴心猫) is a set of Light weight, free, sharing general PHP web application system!
* Apply to: online shop, hospital, school, enterprise, personal website, enterprise intranet, profession portal site, etc.......


### 【Functions】

* Support: PHP5.2~PHP7.0/ custom module / custom field / custom parameter / custom classification
* Data&share / sync / ourter import / Crawler / seo push / old vertion import
* Interface: WeChat / SMS / ip address / map (Baidu, Google) / payments (PayPal, Alipay, caifutong)


### 【Setup】

* Need Environmental
  - PHP5.2 ~ PHP7.x (Recommend: PHP5.3 ~ PHP5.6)
  - mysql5.0+
  - Extended libs: MySQLi/MySQL, GD2, curl

* Set the relative path of the site: 
  - all files will be placed on the site of any directory; 
  - In file: /root/run/_paths.php Param: PATH_PROJ; 
  - The value of PATH_PROJ is the relative path of the site, eg:['/txmao'] or root [''](empty string)
  - For the first time, It will automatically correct the project path, so you can omit the operation

* Edit DB-Config(It can be edited while installing, advice you config manually) 
  - File: /code/cfgs/boot/cfg_db.php; 
  - Tip: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.


### 【v3.3 ChangeLog】

* Add: English Language pack, Setup/AdminCenter/UserCenter, Add Enlish version(can change) 
* Add: Enlish Guides
* Add: Copy Product, Copy Data-Tools plans   
* Improve: Inner douc, Wechat
* Other Fixed/Optiming
* Release: 016-09-17, Weight: 3.30 MB
