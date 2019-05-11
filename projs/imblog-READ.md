

--- --- --- --- --- --- --- --- --- 

* 贴心博客(imblog) V4.8 Released (2019-05-11)
* Official Website / 官网: http://imblog.txjia.com/
* Download / 下载: https://github.com/peacexie/imcat/blob/patches/projs/imblog-4.5.rar

--- --- --- --- --- --- --- --- --- 

### ChangeLog/更新项目 2019-05-11

* Fix/修正: 
 - The authentication code is not displayed while run under https
 - 部署在https下，验证码不显示
* Improve/优化: 
 - The demo data can set sptional while install
 - 安装时，演示资料可选
* Improve/优化: 
 - Improve account/password checking when instal or reset-password
 - 安装系统/重置密码时，改进账号密码的验证

--- --- --- --- --- --- --- --- --- 


### About 贴心博客(Imblog)

* 贴心博客(Imblog) is a blog system based on `jquery2+bootstrap4+贴心猫(imcat)` ;
* Include: 中文版/English two edition, Pc/wap adaptive based on bootstrap 4;
* The models include: 
  - Blog (Free classification), 
  - Memo-Wall (Mood-Wall,Wish-Wall,Words-Wall ...), 
  - About-Me (Website introduction), 
  - Else (Adv-sys, Friend-Link) And so on very practical basic module ...


### 【Setup】

* Need Environmental
  - PHP5.4 ~ PHP7.3 (Recommend: PHP5.6 ~ PHP7.2)
  - mysql5.1+
  - Extended libs: MySQLi/MySQL, GD2

* Set the relative path of the site: 
  - all files will be placed on the site of any directory; 
  - In file: /root/cfgs/boot/_paths.php Set Param: PATH_PROJ; 
  - The value of PATH_PROJ is the relative path of the site, eg:['/imblog'] or root [''](empty string)
  - For the first time, It will automatically correct the project path, so you can omit the operation

* Edit DB-Config(It can be edited while installing, advice you config manually) 
  - File: /root/cfgs/boot/cfg_db.php; 
  - Tips: the defalut DB-Type is: $_cfgs['db_class'] = 'mysqli'; 
  - You can `Edit` it according to the service environment.

* Setup/Config 
  - View Start Page: `/index.php?start` to Check and Config
  - View Url: `/root/tools/setup/` to Setup.

--- --- --- --- --- --- --- --- --- 


### 关于贴心博客(Imblog)

* 贴心博客(Imblog) 是 基于`jquery2+bootstrap4+贴心猫`开发的博客系统；
* 目前包含：中文版/English 版本，基于bootstrap4的pc/wap自适应；
* 包含模型有：
  - 博客文章（自由分类），
  - 便笺墙（心情墙/许愿墙/小语墙...自由分类），
  - 关于我（网站介绍），
  - 其它（广告/友情链接）等很实用的基本的模块。


### 【安装提示】

* 环境需求
  - PHP5.4 ~ PHP7.3 (推荐: PHP5.6 ~ PHP7.2)
  - mysql5.1+
  - 扩展: MySQLi/MySQL, GD2

* 设置站点相对目录；
  - 文件：/root/cfgs/boot/_paths.php 设置PATH_PROJ值为站点相对目录如：“/imblog”或 根目录用“”(空)等；
  - （首次安装使用会自动更正项目路径，所以可省略上述操作）

* 修改数据库配置：
  - 文件：/root/cfgs/boot/cfg_db.php；注意`数据库类`默认为：$_cfgs['db_class'] = 'mysqli';
  - （可安装时配置，如果修改建议手动配置）

* 安装/配置: 
  - 访问起始页：/index.php?start 检查配置；
  - 访问地址：/root/tools/setup/ 安装程序。

