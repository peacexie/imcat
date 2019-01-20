
* 贴心博客(imblog) V4.5.1 Released (2019-01)
* Official Website: [imblog(贴心博客)](http://imblog.txjia.com/)
* [Download](https://github.com/peacexie/imcat/blob/patches/projs/imblog-4.5.0.rar)

--- --- --- --- --- --- --- --- --- 

### 【Setup】

* Need Environmental
  - PHP5.3 ~ PHP7.3 (Recommend: PHP5.4 ~ PHP7.2)
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

### 【安装提示】

* 环境需求
  - PHP5.3 ~ PHP7.3 (推荐: PHP5.4 ~ PHP7.2)
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

