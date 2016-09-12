<?php
(!defined('RUN_MODE')) && die('No Init');

/*
$s = '.com.net.org.edu.gov.idx.int.mil.';
$s .= 'bar.biz.cat.pro.tel.top.vip.xxx.xyz.';
$s .= 'aero.arpa.asia.coop.club.host.info.mobi.name.jobs.site.tech.wang.';
$s .= 'store.space.press.';
$s .= 'museum.travel.online.';
$s .= 'website.';
$_my_dmtop['tops'] = ".$s.";
$_my_dmtop['keep3'] = ""; //需要增加第三节 .mex.com 
$_my_dmtop['keep2'] = ""; //只需要两节 .cn.net 
*/

// www.gdnet.dg.cn 则 part2=dg, part1=cn
// 2:只需要两节 .cn.net 
// 3:需要增加第三节 .mex.com 
$_sy_dmtop['part2.part1'] = ""; // 2,3

/*
https://www.whois365.com/cn/listtld
目前有 669 个 国际顶级域名 (gTLD) 及 290 个 国家及地区顶级域名 (ccTLD) 列于 IANA。
"全球 WHOIS 查询" 支持查询 597 个国际顶级域名 (gTLD) 及 137 个 国家及地区顶级域名 (ccTLD)。
另可查询 32 个 CentralNIC 域名。

*/
