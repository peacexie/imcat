
<!--
  TODO:
  栅格, tips, dialog, pop-menu
  880/4=220, 1200/4=300
-->

Weys.微样式库 是一个轻巧的css微样式库，遵循MIT开源协议。


## Weys 简介

* 功能特色：
  - 小巧：未压缩15K；
  - 多端：移动端、PC自适应；
  - 组件：包含 头尾、导航、菜单、文章详情、PC分栏(自适应)、列表、按钮、栅格、提示、弹窗、Toast；
  - 主题：支持换肤，使用CSS3-var语法配置。

* weys.解题：
  - 微样式库，英文 WeCSSLib；是一个纯css样式库；缩写：weys=微样式；
  - we：既是中文`微`的谐音，也是英文`we/我们`的意义：共享技术，大家来用；
  - ys：既是中文`样式`的首字母，也是`作者名字`的首字母。


## Hack 兼容

目标：兼容现代浏览器，享受CSS3。

* 推荐浏览器：Chrome，Firefox，Edge；
* 不兼容 IE6,7,8；IE9,10可能部分不兼容；  
  考虑IE11不支持CSS3-var变量，针对CSS的var变量做了Hack；
* 外壳浏览器：大致99%兼容，1%我忽略。


## use 使用

### 调用方式

```
[php]
// url设置主题或固定: blue1,gray1,dark1
$skin = req('skin'); 
basJscss::weysInit('jsbase', '', '', $skin); 
[/php]
或
<?php weys($excss, $exjs, 'jq,fa', 'dark1'); ?>
或
<?=weys($excss, $exjs, 'jq,fa', 'dark1'); ?>

```

### 颜色主题

1. skin 默认为空，根据需要，设置 skin 参数；

2. 点击切换

| 1                                 | 2                                 | 3 
| ----                              | ----                              | ----
| [blue1<br>淡蓝(默认)](?skin=blue1) | [gray1<br>灰色(素雅)](?skin=gray1) | [dark1<br>黑色(庄重)](?skin=dark1)



## DIY 二开

### CSS源码

* [主体CSS: weys.css]({=PATH_VIEWS}/base/assets/weys.css)
* [主题CSS: weskin.css]({=PATH_VIEWS}/base/assets/weskin.css)


### DIY 主题

* 如上：`weskin.css`文件，按照现有风格，自己增加一段 颜色主题样式；
* 设置一下 skin 参数 即可。


<p class="tc grc">--- 最后更新：2021-0205 by Peace ---</p>
<p class="tc">就地过年，饥寒交迫；精神粮食，多吃几碗！</p>


<!--
  
-->

