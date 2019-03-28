

--- --- --- --- --- --- --- --- --- 

* 外贸商店(Warm-Shop) V4.3 Released (2019-04)
* Official Website / 官网: http://txjia.com/peace/wmshop.htm
* Download / 下载: https://github.com/peacexie/imcat/blob/patches/projs/warmshop1.rar

--- --- --- --- --- --- --- --- --- 


### 关于 外贸商店(Warm-Shop)

* Warm-Shop(ping)：释义为外贸商店，暖心网店，温馨小店。
  它的前身是以贴心猫v4.3为核心，量身定制的一个个人外贸网店。
  现线上版本月均订单额60万（折算人民币）以上。


### 功能介绍

* Paypal支付，
* 批量价格，
* 运费接口，
* 移动适配。


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

