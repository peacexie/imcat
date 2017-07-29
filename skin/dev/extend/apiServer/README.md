

### Setup / 安装

* Down & Unpack;  
  下载解压
* Copy all files to Project [/] Dir;  
  把文件复制到项目根目录【/】下

=================================================


## ApiServer/AppServer 说明(Notice)


#### 说明

* 目的,意义
 - 目的: 这里仅做为App/Api的服务端，提供本系统的信息
 - 因为仅提供数据，本入口进去不启用模板解析
 - 为安全起见，每次请求，请安如下url签名进行处理

* url签名
 - 示例：_012[tm]=1486523837&_012[enc]=abc123&debug=1
 - _012[tm]：时间戳, 如：$stamp = $_cbase['run']['stamp']
 - _012[enc]：加密串，如：md5("$keyapi.$stamp"); 
 - 其中：$keyapi = $_cbase['safe']['api'];
 - 前缀`_012`：$safix = $_cbase['safe']['safix'];
 - 保证：app端和本系统中，参数：$safix, $keyapi 双方一致
 - retype: json, jsonp, xml
 - debug=1：表示调试。

* 第三方调用示例
 - http://yscode.txjia.com/mlabs/csframe.htm
 - 查看对应代码：Demo : Data From IntimateCat

* 第三方系统-同步数据
 - 接口：app.php?mod=data
 - 说明：见：/skin/app/b_files/data.php 注释


#### Notes

* Purpose
 - Here just act as the `App/Api server`, and apply infomations for it.
 - Because only provide data for app, This entry does not use the template parsing
 - For the safety, the url must deal(signature) as below

* Url signature
 - Demo: _012[tm]=1486523837&_012[enc]=abc123&debug=1
 - _012[tm]: timestamp, eg: $stamp = $_cbase['run']['stamp']
 - _012[enc]: encodestr, eg: md5("$keyapi.$stamp"); 
 - $keyapi = $_cbase['safe']['api'];
 - prefix`_012`: $safix = $_cbase['safe']['safix'];
 - In app and this system, the params `$safix`, `$keyapi` must set the same value
 - retype: json, jsonp, xml
 - debug=1: the param for debug

* Get data in 3nd-part 
 - http://yscode.txjia.com/mlabs/csframe.htm
 - See : Demo : Data From IntimateCat

* Third party system - synchronous data
 - Interface: app.php?mod=data
 - Notice: See the notice in：/skin/app/b_files/data.php 


#### Sample/Tips

* Sample:
 - <a href='?mod=info&act=sample&{sign}'>Demo Code &gt;&gt;</a>

* Tips:
 - 提示：请使用 新的 `REST-API` 替代本功能：
 - 去更强大更刺激的 <a href="rest.php">`REST-API`<a/>!
 - Tips: Please use `REST-API` instead this API:
 - Go to the More Power and Exciting <a href="rest.php">`REST-API`<a/>!


