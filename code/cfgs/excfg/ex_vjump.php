<?php

### 分站跳转配置
// 分站配置,注意城市名称不要加“站/分站/市”等
$_ex_vjump['sites'] = array(
    'bj' => '北京', // http://bj.my_domain.cn/
    'sh' => '上海', // http://sh.my_domain.cn/
    'gz' => '广州', // http://gz.my_domain.cn/
    'dg' => '东莞', // http://dg.my_domain.cn/
    'sz' => '深圳', // http://sz.my_domain.cn/
    // ……
    'wm' => '无名', // http://wm.my_domain.cn/
);


### 多语言版跳转配置
// 语言版本配置
$_ex_vjump['langs'] = array(
    'zh' => 'chn', 
    'en' => 'doc', 
    //'fr' => 'fra', 
    //'ru' => 'rus',
    //'es' => 'esp',
    // ……
    //'ko' => 'kor',
);


### 短地址跳转配置
// redir配置
$_ex_vjump['redir'] = array(
    'yscode' =>  "http://yscode.txjia.com/", 
    'baidu' =>  'https://www.baidu.com/', 
);


// 默认值
$_ex_vjump['_defs'] = array(
    'site' => 'dg',
    'lang' => 'chn',
    'domain' => 'txjia.com', //my_domain.cn
    'api' => 'ip138',
); //defsite



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
- 在本系统：


### 本系统上扩展实现

- 原来的各分站，还是原始的系统，这样好啊，利于升级，维护！
- 在任意一个系统分站，配置文件：/code/cfgs/excfg/ex_vjump.php；
- 把 www.my_domain.com 或 my_domain.com 指向：/root/plus/api/，设置vjump.php为默认页
- 各分站调用所有分站列表接口：/vjump.php?html 或 /vjump.php?cfgs
- 调试连接：/vjump.php?nav


### 提示说明

- 要自动检测的分站，至少要为地区级，要实现 自动检测同一个地区级下的小区域是没有意义的；
- 自动检测分站原理是，按ip地址库中，匹配到的中文来判断的，即出现了"东莞"才会跳转到"东莞"分站；
- 有些ip地址库 的地址 类似如"广东省 联通统一出口"，那就根本 检测不到 "东莞"，也就不会跳转到"东莞"了。
- 手机端使用(没有意义)：
> 手机端检测的ip一般使用代理，用于判断分站跳转，没有意义；
> 当然，手机端有更准确的GPS，Wifi定位，但网页中无法调用，这里讨论无意义；

*/
