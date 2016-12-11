<?php

/*
限制click可操作的字段，避免把重要字段更新，如把价格+1
*/
$_click = array(
	// 可以js显示,并自增的的字段; 表中没有的字段自动过滤
	'fields'=>array(
		'click',
	),
	// db表单独设置,此时fields设置无效
	'docs_demo'=>array(
		'click',
		'atime',
		'etime',
	),
	// 如果某模型很在意click等字段，请设置如下，并另扩展脚本处理
	'docs_(somemodel)'=>array(
		'0',
	),
);

/*
限制digg可操作的字段
*/
$_digg = array(
	// 要保证模型存在，字段存在...
	'cargo' => array(
		'diggtop' => array(
			'login=1', // 登录发布
			'login=cvip,ccom', // 会员cvip,ccom等级:登录发布
			'iprep=1', // 同一ip间隔6可发布,cookie记录
		),
		'diggdown' => array(
			'login=1', // 登录发布
			'login=cvip,ccom', // 会员cvip,ccom等级:登录发布
			'iprep=1', // 同一ip间隔6可发布,cookie记录
		),
	),
);

/*
stops.限制发布交互
*/
$_stops = array(
	'corder', 'coitem', 'cocar',
	'qatag', 'votei',
);

/*
限制可操作的coms模型
*/
$_ex_coms = array(
	'stops' => $_stops,
	'click' => $_click,
	'digg' => $_digg,
);

/*
### digg使用：
* 添加模型字段如：(demo)diggtop,diggdown
* 配置本文件：_ex_digg
* html
    <span class="right" id='diggs2'>
    <a id="jsid_field_{$this->mod}:{$did}:diggtop" class="glyphicon glyphicon-thumbs-up c6F6 f24 hand">{$diggtop}</a>
    &nbsp;
    <a id="jsid_field_{$this->mod}:{$did}:diggdown" class="glyphicon glyphicon-thumbs-down cF33 f16 hand">{$diggdown}</a>
    &nbsp;
    </span>
* 模版js：
```
	var diggurl = '{PATH_PROJ}/root/plus/coms/digg.php?mod={$this->mod}&kid={$did}&opfid=';
	$(document).ready(function(){
	$('#diggs2 a').each(function(i1, e1) {
	    $(e1).click(function(){ 
	        var eid = $(e1).prop('id').split(':');
	        var ajurl = _cbase.run.rsite+diggurl+eid[2];
	        $.ajax({     
	            url:ajurl,
	            type:'get',       
	            async : false, //默认为true 异步     
	            error:function(){     
	                layer.tips('Error-A:'+data, '#diggs2', 3); 
	            },
	            success:function(data){     
	                if(data=='success'){
	                    data = '点赞成功！';
	                    $(e1).html(parseInt($(e1).html())+1); 
	                }
	                layer.tips(data, e1, 3);
	            }  
	        });   
	    });
	});
	});
```
*/
