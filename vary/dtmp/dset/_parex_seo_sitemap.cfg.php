<?php
$_parex_seo_sitemap = array (
  'baidu_map.html' => 
  array (
    'pid' => 'seo_sitemap',
    'title' => '百度Siatemap',
    'detail' => '<!doctype html><html><head>
<meta charset="utf-8">
<title>Baidu Sitemap</title>
</head><body>
(*)
</body></html>',
    'numa' => '0',
    'numb' => '0',
    'cfgs' => '<li><a href="(url)">(title)</a></li>',
    'note' => 'news,100,chn
cargo,100,chn
topic,100,chn',
  ),
  'baidu_push.txt' => 
  array (
    'pid' => 'seo_sitemap',
    'title' => '百度主动推送',
    'detail' => '',
    'numa' => '0',
    'numb' => '0',
    'cfgs' => '(url)',
    'note' => 'news,100,chn
cargo,100,chn
topic,100,chn',
  ),
  'google_map.xml' => 
  array (
    'pid' => 'seo_sitemap',
    'title' => 'Google站点地图',
    'detail' => '<?xml version="1.0" encoding="utf-8"?>
<urlset>
(*)
</urlset>',
    'numa' => '0',
    'numb' => '0',
    'cfgs' => '<url>
<loc>(url)</loc>
<lastmod>(time)</lastmod>
<changefreq>(freq)</changefreq>
<priority>(priority)</priority>
</url>',
    'note' => 'news,100,chn,monthly,0.5
cargo,100,chn,monthly,0.7
topic,100,chn,monthly,0.8',
  ),
);
?>