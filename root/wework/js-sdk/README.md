###概述
这是一个php版本的接口调用示例，包含了开发者在开发过程中常见的操作，比如获取不同应用的access_token、回调模式设置、jsapi的调用、通讯录管理、媒体上传等，方便企业开发者理解整套流程以及工作原理并能迅速上手进行开发。
> 企业微信开发者文档：http://work.weixin.qq.com/api/doc

### 代码结构
```
OpenApiSample/
├── cache/       
│   ├── txl.php    //通讯录应用的access_token缓存        
│   └── jsapi_ticket.php    //JSAPI的ticket缓存
└── devtool/    
│   ├── assets/   
│   ├── api_config.php    
│   ├── devtool.php    //开发者调试工具的界面
│   └── devhandler.php   
└── example/   //基本的接口调用示例
│   ├── app_manage.php   
│   ├── callback_valid.php  
│   ├── department.php   
│   ├── get_access_token.php  
│   ├── jsapi.php    
│   ├── message.php  
│   ├── upload_media.php 
│   └── user_manage.php  
└── lib/   //工具方法
    ├── access_token.php   
    ├── app_api.php    
    ├── helper.php    
    ├── jssdk.php    
    ├── media_api.php  
    ├── msgcrypt.php 
    ├── pkcs7Encoder.php 
    ├── sha1.php 
    ├── txl_api.php
    └── xmlparse.php
```
### 运行示例

 1. 编辑config.php文件，配置企业以及应用的配置信息，配置项请参考示例文件 
 2. 将代码部署到可以解析php的web服务器(php版本5.4+)
 3. 运行devtools/devtool.php可以了解并调试所有的接口调用，代码里面也包含了主要的业务处理逻辑，可以作为开发时的参考
 4. 运行example目录下的示例文件，修改参数查看程序的运行结果
 5. lib目录下的代码请根据具体情况修改，大多只为做demo测试用



