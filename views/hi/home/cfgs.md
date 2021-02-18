

提示：以下为配置重要提示。


## 综合配置

### 基本配置

* 下载安装： http://txjia.com/custom/book.php/hello-down
* 配置文件： http://txjia.com/custom/book.php/hello-config

### 重要开关

* 支付配置：/root/cfgs/excfg/ex_a3rd.php, 
    ```
    $_cfgs['wepay']['isOpen'] = '1'; // 启用微信支付
    ```
* 企业微信：/root/cfgs/excfg/ex_wework.php,
    ```
    $_ex_wework['isOpen'] = '1'; // 启用企业微信; (工单,企业微信登录)
    ```
* 微信公账号：后台 > 架设 > 插件/接口 > 微信接口
  - admin > appid:配置
  - 启用状态，并配置好其他参数


## 工单配置

工单系统，基于企业微信自建应用。需要与企业微信接口对照配置；测试状态可申请测试企业。 

* 配置文件：
  - /root/cfgs/excfg/ex_wework.php

* 配置企业ID和通讯录Secret 到配置文件
  - CorpId：企业微信管理端 > "我的企业" > 企业信息 
  - TxlSecret：企业微信管理端->管理工具->"通讯录同步"（图2.3）

* 添加自定义应用：
  - 入口：应用管理 > 自建 > 创建应用 
  - 名称为`工单管理`
 
* 配置应用：
  - 入口：应用管理 > 工单管理(刚才创建的) > 点击打开
  - 配置参数 AgentId, Secret 如（图2.5）到 文件（如图2.1）的相应AgentId, Secret 位置

* 接收消息配置
  - 入口：应用管理 > 工单管理 > 接收消息 > 启用API接收
  - URL：http://xx-yy.com/hi.php/wework-AppCS (xx-yy根据你自己的域名设置，下同)
  - Token，EncodingAESKey 自行配置
 
* 菜单配置
  - 应用管理 > 工单管理 > 自定义菜单 > 
  - 新建：http://xx-yy.com/umc.php/task-apply
  - 处理
    - 待处理单：http://xx-yy.com/umc.php/task-waitme
    - 与我相关：http://xx-yy.com/umc.php/task-atme
    - 历史记录：http://xx-yy.com/umc.php/task-history
  - 我的
    - 用户中心：http://xx-yy.com/umc.php
    - 设置中心：http://xx-yy.com/umc.php/task-set

* 授权配置
  - 应用管理 > 工单管理 > 网页授权及JS-SDK / 企业微信授权登录
  - 相关域名：都填写：xx-yy.com，并按提示操作即可。

<p class="tr">
    Peace / 2021-02-15<br>
    修订 / 2021-02-18<br>
</p>

