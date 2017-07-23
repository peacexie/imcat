

### 功能说明

* 主要演示扩展包的制作规范及推荐格式


### Setup/安装

* 下载解压
* 手动复制【/web/】下所有文件到项目目录
* 复制【/sql/】和【index.php】到 /root/tools/extend/ 下  
  (extend目录 如果之前有文件，可清除)
* 打开文件：/root/tools/extend/ 执行安装
* 确认是否自动删除index.php安装文件，如果没有自动删除，请手动删除
* 访问：/root/tools/extest/index.php 查看安装效果


### 相关提示

* 这个扩展包，其实无实质功能，仅演示 `扩展包` 的相关制作规范
* 代码提示 见本 `扩展包` : index.php
* 以下为：`扩展包` 相关规范或提示


--------------------------------------------------------------------


### Dirs-Files/目录规划

* The Mormal Dirs-Files
* 扩展包典型目录结构

* {exend-root}/   
```
 - /sql/*.sql        -=>需要的sql
 - /web/*.*          -=>需要的文件
 - /index.php        -=>安装文件
 - /cfgs.php         -=>配置文件
 - /README.md        -=>说明文件
```


### Configs/配置(cfgs.php)

* Fields
```
 - kid : 
 - title : 
 - type : 模块-exmod, 功能-exfun, 代码-excode, Bug修正-bfix,
 - durl : down-url
 - size : Unit:KB
 - ver : version, eg: v3.8
 - thumb : thumbnail-url
 - ///////////////
 - vendor : Vendor-Name
 - vurl : Vendor-Url
 - vqq : Vendor-QQ 
 - fee : Free / 123.00(RMB)
 - ///////////////
 - hot : Reserve/预留
 - view : Reserve/预留
 - digg : Reserve/预留
```

