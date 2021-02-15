<?php
  require_once "../lib/jssdk.php";  

  //第一步: 在服务端生成签名
  $jssdk = new JSSDK(1000002);
  $signPackage = $jssdk->getSignPackage();
  $signAgent = $jssdk->getSignPackage(1);
  $thirdNo = date('md_Hi');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=3, minimum-scale=1, user-scalable=no">
    <title>WWOpenData 实例页面</title>
    <script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript" src="//open.work.weixin.qq.com/wwopen/js/jwxwork-1.0.0.js"></script>
    <script>
        /**
         * wx.config 参数
         *
         * @see https://open.work.weixin.qq.com/api/doc/90001/90144/90547
         */
        window.configParams = <?=\imcat\comParse::jsonEncode($signPackage)?>; //{ /* ... */ }
        /**
         * wx.agentConfig 参数
         *
         * @see https://open.work.weixin.qq.com/api/doc/90001/90144/90548
         */
        window.agentConfigParams = <?=\imcat\comParse::jsonEncode($signAgent)?>; //{ /* ... */ }
    </script>
</head>
<body>

    <div id="container">
        <div>安全控件展示页面</div>
        <div>显示出前 100 名可见范围人员名单</div>
    </div>


  <p>
    <b>提示：[<?=$thirdNo?>]</b>
    这里要正常得到两组配置；<br>
    企业微信里面正常拉取出审批界面即走出了接口调试第一步！
  </p>
  <hr>
  <p><b>wx.config</b></p>
  <p>appId：<span><?php echo $signPackage["appId"];?></span></p>
  <p>timestamp：<span><?php echo $signPackage["timestamp"];?></span></p>
  <p>nonceStr：<span><?php echo $signPackage["nonceStr"];?></span></p>
  <p>signature：<span><?php echo $signPackage["signature"];?></span></p>
  <p>ticket：<span><?php echo $signPackage["ticket"];?></span></p>
  <hr>
  <p><b>wx.agentConfig</b></p>
  <p>appId：<span><?php echo $signAgent["appId"];?></span></p>
  <p>timestamp：<span><?php echo $signAgent["timestamp"];?></span></p>
  <p>nonceStr：<span><?php echo $signAgent["nonceStr"];?></span></p>
  <p>signature：<span><?php echo $signAgent["signature"];?></span></p>
  <p>ticket：<span><?php echo $signAgent["ticket"];?></span></p>

    
    <script>
        (async () => {
            try {
                console.info('WWOpenData demo start')
                if (/MicroMessenger/i.test(navigator.userAgent)) {
                    await config(window.configParams)
                }
                await agentConfig(window.agentConfigParams)
                // 若一切正常，此时可以在 window 上看到 WWOpenData 对象
                console.info('window.WWOpenData', window.WWOpenData)
                if (WWOpenData.checkSession) {
                    WWOpenData.checkSession({
                        success() {
                            console.info('open-data 登录态校验成功')
                        },
                        fail() {
                            console.error('open-data 登录态过期')
                        },
                    })
                }
                if (WWOpenData.on) {
                    /**
                     * ww-open-data 元素数据发生变更时触发
                     */
                    WWOpenData.on('update', event => {
                        const openid = event.detail.element.getAttribute('openid')
                        console.info('渲染数据发生变更', openid, event.detail.hasData)
                    })
                    /**
                     * ww-open-data 获取数据失败时触发
                     */
                    WWOpenData.on('error', () => {
                        console.error('获取数据失败')
                    })
                }
                /**
                 * 创建 ww-open-data 元素
                 */
                const container = document.getElementById('container')
                // 这里的 window.openidList 是该 demo 页面自行组织的数据，不具备普遍性
                // 开发者进行开发时，需要自己拿到授权企业相对应的 openid
                // 关于 openid 的定义与获得方式，可以关注文档注意事项的第 5 点
                for (const openid of window.openidList) {
                    const element = document.createElement('ww-open-data')
                    element.setAttribute('type', 'userName')
                    element.setAttribute('openid', openid)
                    container.appendChild(element)
                }
                /**
                 * 绑定 document 上全部的 ww-open-data 元素
                 */
                WWOpenData.bindAll(document.querySelectorAll('ww-open-data'))
                console.info('WWOpenData demo end')
            } catch (error) {
                console.error('WWOpenData demo error', error)
            }
            /**
             * 调用 wx.config
             *
             * @see https://open.work.weixin.qq.com/api/doc/90001/90144/90547
             */
            async function config(config) {
                return new Promise((resolve, reject) => {
                    console.info('wx.config', config)
                    wx.config(config)
                    wx.ready(resolve)
                    wx.error(reject)
                }).then(() => {
                    console.info('wx.ready')
                }, error => {
                    console.error('wx.error', error)
                    throw error
                })
            }
            /**
             * 调用 wx.agentConfig
             *
             * @see https://open.work.weixin.qq.com/api/doc/90001/90144/90548
             */
            async function agentConfig(config) {
                return new Promise((success, fail) => {
                    console.info('wx.agentConfig', config)
                    wx.agentConfig({ ...config, success, fail })
                }).then(res => {
                    console.info('wx.agentConfig success', res)
                    return res
                }, error => {
                    console.error('wx.agentConfig fail', error)
                    throw error
                })
            }
        })()
    </script>
</body>
</html>