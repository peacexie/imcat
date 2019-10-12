<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 域名相关设置


### 子域名跳主域名设置
$_sy_domain['subDirs'] = array(
    #'www.txjia.com' => 'txjia.com', //不要www.域名跳转
    #'tiexinmao.duapp.com' => 'imcat.txjia.com', //跳转到主域名
    #'127.0.0.1' => '127.0.0.2', //测试
    'www.txjia.com'          => 'imcat.txjia.com', 
    'txmao.txjia.com'        => 'imcat.txjia.com',
    'imcat.gz01.bdysite.com' => 'imcat.txjia.com',
); //dir-跳转
// http://imcat.gz01.bdysite.com/
// http://imcat.pswpower.com/


### 跨域子域名设置
if(IS_CLI){
    $_sy_domain['dmacc'] = array();
}else{
    $_sy_domain['dmacc'] = array(
        $_SERVER['HTTP_HOST'],
        //'yscode.txjia.com',
    ); 
}


### 顶级域名设置
// www.gdnet.dg.cn 则 part2=dg, part1=cn
// .cn.net = 2:只需要两节
// .mex.com = 3:需要增加第三节 
$_sy_domain['dmtop']['part2.part1'] = ""; // 2,3
/*
https://www.whois365.com/cn/listtld
目前有 669 个 国际顶级域名 (gTLD) 及 290 个 国家及地区顶级域名 (ccTLD) 列于 IANA。
"全球 WHOIS 查询" 支持查询 597 个国际顶级域名 (gTLD) 及 137 个 国家及地区顶级域名 (ccTLD)。
另可查询 32 个 CentralNIC 域名。
*/

