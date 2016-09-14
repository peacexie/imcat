<?php

### 子域名跳转配置

// domain
$_ex_ujump['domain'] = 'txjia.com'; 
// 分站配置,注意城市名称不要加“站/分站/市”等
$_ex_ujump['sites'] = array(
    'bj' => '北京', // http://bj.my_domain.cn/
    'sh' => '上海', // http://sh.my_domain.cn/
    'gz' => '广州', // http://gz.my_domain.cn/
    'dg' => '东莞', // http://dg.my_domain.cn/
    'sz' => '深圳', // http://sz.my_domain.cn/
    // ……
    'wm' => '无名', // http://wm.my_domain.cn/
);
// 未找到地区时的默认网站
$_ex_ujump['defsite'] = 'dg'; 



### 多语言版跳转配置

// 语言版本配置
$_ex_ujump['langs'] = array(
    'zh' => 'chn', 
    'en' => 'eng', 
    'fr' => 'fra', 
    'ru' => 'rus',
    'es' => 'esp',
    // ……
    'ko' => 'kor',
);
// 未找到匹配的语言版本
$_ex_ujump['deflang'] = 'chn'; 



### 短地址跳转配置

// redir配置
$_ex_ujump['redir'] = array(
    $_ex_redir['yscode'] = "http://yscode.txjia.com/", 
    $_ex_redir['baidu'] = 'https://www.baidu.com/', 
);




/*

### 目标：

- 复制n份程序代码，做n个分站
- 实现：根据用户ip自动跳转到分站
- 提示，域名解析，自行解析或找域名提供商....


### 规划：

- 北京站 http://bj.my_domain.cn/
- 上海站 http://sh.my_domain.cn/
- 广州站 http://gz.my_domain.cn/
- 东莞站 http://dg.my_domain.cn/
- 深圳站 http://sz.my_domain.cn/
- 无名站 http://wm.my_domain.cn/


### 分析：

- 单个子站点，不用(需要)跳转，即不管访问者在那里，输入sh.my_domain.cn就是上海站；
- 如果输入 my_domain.cn / www.my_domain.cn 自动跳转到相应的分站；
- 这样逻辑上其实非常简单：专门建立一个 www.my_domain.com 或 my_domain.com ，用于跳转；
- 到此：跳转本身，其实与程序已经没有多大关系，但ip判断，跳转代码等可用本系统的代码，也可完全不用；
- 思路分析到此为止；


### 本系统上扩展实现

- 原来的各分站，还是原始的系统，这样好啊，利于升级，维护！
- 在任意一个系统分站，添加一个目录如：subdir，扩展本功能；
- 准备活动：
> 建立分站配置表：(自行配置)
> 获得访问者ip地址：cls_envBase::OnlineIP() @ /libs/classes/common_08cms/envbase.cls.php
> 根据ip获得对应地址：$ip = new cls_ipAddr(); $addr = $ip->addr($ip); @ /libs/classes/api/ipAddr.cls.php
- subdir下建立一个index.php文件，根据上面提示写点代码 （自己写吧！）
- 服务器设置：设置 www.my_domain.com 或 my_domain.com ，站点，跟目录指向上述 subdir即可
- 就这么简单，跳转弄好了……


### 参考代码(subdir/index.php)：
 - ...



### 参考代码(subdir/config.php)：
 - ...



### 提示思路：

- 各分站调用所有分站列表
> 各分站做独立页，同步到各分站；
> 各分站也可调用 上述 www.my_domain.com 数据，免得每个分站维护，
   这里自己写扩展代码，用于各分站调用，自己处理模版……


### 完全diy

- 根据上述提示，分站跳转可完全脱离08cms系统，自己写相关代码即可
- 当然，上述subdir等目录，也不用建立在08cms系统下，自行规划


### 提示说明

- 要自动检测的分站，至少要为地区级，要实现 自动检测同一个地区级下的小区域是没有意义的；
- 自动检测分站原理是，按ip地址库中，匹配到的中文来判断的，即出现了"东莞"才会跳转到"东莞"分站；
- 有些ip地址库 的地址 类似如"广东省 联通统一出口"，那就根本 检测不到 "东莞"，也就不会跳转到"东莞"了。
- 手机端使用(没有意义)：
> 手机端检测的ip一般使用代理，用于判断分站跳转，没有意义；
> 当然，手机端有更准确的GPS，Wifi定位，但网页中无法调用，这里讨论无意义；


### 目前发布系统 说明：

- 家装自带站外分站设置，可使用自带设置，也可按上述设置；
- 汽车 前期版本，自带站内地区跳转；后续有改为：站内按地区读出地区相关信息
> 这里的跳转或按地区，都是同已站内（统一08cms系统）,上述的复制出n个分站是另一回事
- 目前发布的系统：定位为地方门户；
> 如果做分站，建议：按以上方式，一个分站，对应一个地区，对应一份程序
> 在同一个站点内，设置地区及分站（设置全国的分站）运营 可能并不太适合；


*/
