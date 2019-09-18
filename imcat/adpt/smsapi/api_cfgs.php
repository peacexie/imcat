<?php
namespace imcat;

$_apis = array(

    'qcloud' => array(
        'name' => '腾讯云短信',
        'home' => 'https://github.com/qcloudsms/qcloudsms_php',
        'unit' => '元', // 余额单位(元 或 条)
        'admin' => 'https://console.cloud.tencent.com/sms/smslist', //如无此项可不填
        'note' => 'note', 'nmem' => 'nmem', //会员提示
    ),
    'alidy' => array(
        'name' => '阿里大于',
        'home' => 'https://help.aliyun.com/document_detail/55359.html',
        'unit' => '元', // 余额单位(元 或 条)
        'admin' => 'https://homenew.console.aliyun.com/', //如无此项可不填
        'note' => 'note', 'nmem' => 'nmem', //会员提示
    ),
    'cr6868' => array(
        'name' => '创瑞传媒',
        'home' => 'http://www.cr6868.com/',
        'unit' => '条', // 余额单位(元 或 条)
        'admin' => 'http://web.cr6868.com/login.aspx', //如无此项可不填
        'note' => '信息中不能含&#特殊字符，具体咨询短信供应商。', 'nmem' => '', //会员提示
    ),
    'emhttp' => array(
        'name' => '亿美(http)',
        'unit' => '元', // 余额单位(元 或 条)
        'home' => 'http://www.emay.cn/', 
        'admin' => '', //
        'note' => '亿美软通接口(http调用), 新亿美用户建议首选本调用方式, 不需要login操作。', 'nmem' => '', //会员提示
    ),

    'winic' => array(
        'name' => '移动商务',
        'home' => 'http://www.winic.org/',
        'unit' => '元', // 余额单位(元 或 条)
        'admin' => 'http://www.900112.com/', //如无此项可不填
        'note' => 'HTTP发送,内容不支持空格换行',
        'nmem' => 'HTTP发送,内容不支持空格换行', //会员提示
    ),
    'bucp' => array(
        'name' => '博星(ws)',
        'unit' => '条', // 余额单位(元 或 条)
        'home' => 'http://www.bucp.net/', 
        'admin' => 'http://117.79.237.3:8060/webservice.asmx', 
        'note' => '',
        'nmem' => '', //会员提示
    ),
    '0test' => array(
        'name' => '流程测试',
        'unit' => '条', // 余额单位(元 或 条)
        'home' => '', //如无此项可不填
        'admin' => '', //如无此项可不填
        'note' => '测试接口,用于测试系统其它流程,提供[充值<a href="?file={file}&act=chargeUp&charge=20" target="_blank">[+20</a>|<a href="?file={file}&act=chargeUp&charge=-20" target="_blank">-20]</a>操作]<br />具体操作不会发短信,仅写一个文件记录表示发短信; <br />',
        'nmem' => '测试接口,用于测试系统其它流程。', //会员提示
    ),

);

/*

*/
