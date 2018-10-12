

### composer + aliOss 使用说明


首先说明，贴心猫(imcat) 本身不依赖 composer，但她可以配合 composer 一起使用！  
本文以 composer + aliOss(阿里云OSS配合使用，作为composer使用演示说明)


### 只用 aliOss, 不使用 composer

* a. 下载 aliOss 到目录：\vendor\aliOss；(注意，原始目录为：`aliyuncs/oss-sdk-php`)
 - 本系统依赖如下配置，自动加载 getOssClient
 - ` cfg_load.php > $_cfgs['acpr4'] > 'OSS' => array('/aliOss/src/OSS'), `

* b. 下载同上；
 - 注释以上配置；
 - 手动加载：require DIR_VENDOR.'/aliOss/autoload.php'; 即可自动加载：getOssClient


### 使用 composer 加载 getOssClient：

* a. 下载同上；注释以上配置；安装 composer

* b. 使用 composer 直接安装 `vendor/aliyuncs/oss-sdk-php`

* c. 或使用原来的：`\vendor\aliOss` 目录；
  手动配置： `autoload_static.php`, `autoload_psr4.php` 等文件


### 演示代码：

```
    #require DIR_VENDOR.'/autoload.php';
    #require_once DIR_VENDOR.'/aliOss/autoload.php';
    $ocl = $oss->getOssClient();
    dump($ocl); 
```

