

### 模板指引


#### 模板目录

* /views/      - 项目模板:总目录
* /views/adm/  - 后台管理模板
* /views/base/ - 基础工具模板，如:map,动态js/css,工具等
* /views/comm/ - 功能演示（默认/中文）
* /views/dev/  - 中文文档
* /views/doc/  - 英文文档
* /views/mob/  - 手机版模板（预留）
* /views/umc/  - 会员中心模板

模板目录详情，以`功能演示版`为例，其他类似

* /views/comm/\_config/ - 功能演示版配置
* /views/comm/\_ctrls/  - 控制器方法扩展代码
* /views/comm/assets/   - 资源目录，如css,js,images
* /views/comm/about/    - 公司介绍模块模板
* /views/comm/cargo/    - 产品展示模块模板
* /views/comm/home/     - 中文版首页模板
* /views/comm/info/     - 留言/导航等杂项模板
* /views/comm/news/     - 资讯模块模板
* /views/comm/faqs/     - 问答模板
* /views/comm/topic/    - 专题模板
* /views/comm/c_pub/    - 公共区块，如头尾等
* /views/comm/c_pub/ahead.htm - 公共头文件
* /views/comm/c_pub/afoot.htm - 公共尾文件
* /views/comm/c_pub/amenu.htm - 公共菜单文件

导航首页相关文件目录

* /views/base/home/              - 首页相关 总目录
* /views/base/home/en.htm        - 首页模板(英文版)
* /views/base/home/cn.htm        - 首页模板(中文版)
* /views/base/home/\_layout.htm  - 首页布局
* 导航首页需跳转或更多DIY，请修改控制器文件 `/views/base/_ctrls/homeCtrl.php` 内的 `homeAct()` 方法
