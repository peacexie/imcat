

### composer 使用说明


首先说明，贴心猫(imcat) 的基本功能本身不依赖 composer；
但她可以配合 composer 一起使用（如果需要）！  


### Composer 安装与使用

* [runoob.com文档](https://www.runoob.com/w3cnote/composer-install-and-usage.html)

* 更改 Packagist 为国内镜像：
  - `composer config -g repo.packagist composer https://packagist.phpcomposer.com`

* composer.json 配置
  - require配置代码`"monolog/monolog": "1.2.*"`

* 安装依赖包（命令）：
  - `composer install`

* require 命令快速的安装一个依赖
  - `composer require monolog/monolog`


### 安装(ali)对象存储 OSS 

* [aliyun.com文档](https://help.aliyun.com/document_detail/85580.html?spm=a2c4g.11186623.6.1051.470f661donknJY)

* 命令/配置：
  - `composer require aliyuncs/oss-sdk-php`
  - `"aliyuncs/oss-sdk-php": "~2.x.x"`

* 演示代码

```
    use OSS\OssClient;
    use OSS\Core\OssException;
    $ocl = $oss->getOssClient();
    dump($ocl); 
```


### 安装(ali)短信服务 dysms

* [aliyun.com文档](https://help.aliyun.com/document_detail/112186.html?spm=a2c4g.11186623.6.649.5c6c15ec5KKdph)

* 命令/配置：
  - 内置类库：`/dysms/` 
  - `composer require alibabacloud/client` (依赖太多，目前没有使用)


### 安装(tencent)短信SDK qcsms

* [tencent.com文档](https://cloud.tencent.com/document/product/382/9557)

* 命令/配置：
  - `composer require qcloudsms/qcloudsms_php`


### 安装PHPMailer

* [github.com文档](https://github.com/PHPMailer/PHPMailer)

* 命令/配置：
  - `composer require phpmailer/phpmailer`


### 安装Swiftmailer

* [symfony.com文档](https://swiftmailer.symfony.com/docs/introduction.html)

* 命令/配置：
  - `composer require "swiftmailer/swiftmailer:^6.0"`


### end

