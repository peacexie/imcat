<?php (!defined('RUN_INIT')) && die('No Init');?>

<?php switch($part){ case '_test1_': ?> 

中-说明<?php echo $uarr['hname'] ?>
多语言实现，用这类文件来存放多语言；
不使用多语言，可把这些代码直接写到脚本内。

<?php break;case 'uc_indoc_list': ?> 

<th>选</th><th>标题</th><th>栏目</th><th>显示</th>
<th>添加时间</th><th>添加账号</th><th>修改时间</th>
<th>修改</th></tr>

<?php break;case 'docs_list': ?> 

<th>选</th><th>标题</th><th>栏目</th><th>显示</th>
<th>添加时间</th><th>添加账号</th><th>修改时间</th>
<th>修改</th></tr>

<?php break;case 'advs_list': ?> 

<th>选</th><th>标题</th><th>栏目</th><th>显示</th>
<th>Url</th><th><?php echo ($uarr==4 ? '用户' : '点击'); ?></th>
<th>添加</th><th>修改</th></tr>

<?php break;case 'adpush_list': ?> 

<th>选</th><th>aid</th><th>推送位标题</th><th>栏目</th><th>显示</th>
<th>Max</th><th>添加</th><th>修改</th><th>推送</th><th>Page</th><th>cfg/data</th></tr>

<?php break;case 'coms_list': ?> 

<th>选</th><th>标题</th><th>显示</th><th>会员名称</th>
<th>电话</th><th>E-Mail</th><th>聊天号</th>
<th>添加</th><th>添加IP</th>
<th>修改</th></tr>

<?php break;case 'users_list': ?> 

<th>选</th><th>账号</th><th>等级</th><th>名称</th>
<th>Show</th><th>电话</th><th>E-Mail</th><th>聊天号</th>
<th>注册</th><th>注册IP</th>
<th>修改</th></tr>

<?php break;case 'cargo_list': ?> 

<th>选</th><th>标题</th><th>栏目</th><th>显示</th>
<th>添加时间</th><th>修改时间</th>
<th>修改</th><th>复制</th></tr>

<?php break;case 'demo_list': ?> 

<th>选</th><th>标题/[评论][籍贯][流浪][工作][行业]</th><th>栏目</th><th>显示</th>
<th>添加时间/账号</th><th>修改时间/IP</th><th>电话/结束时间</th>
<th>修改</th></tr>

<?php break;case 'indoc_list': ?> 

<th>选</th><th>标题</th><th>栏目</th><th>部门</th><th>重要</th><th>显示</th>
<th>添加时间</th><th>添加账号</th><th>修改时间</th>
<th>修改</th></tr>

<?php break;case 'inread_list': ?> 

<th>选</th><th>公文标题</th><th>阅读者</th><th>帐号</th><th>浏览次数</th>
<th>首次阅读</th><th>首次IP</th>
<th>末次阅读</th><th>末次IP</th>
<th>修改</th></tr>

<?php break;case 'inrem_list': ?> 

<th>选</th><th>公文标题</th><th>评论标题</th><th>帐号</th>
<th>首次阅读</th><th>首次IP</th>
<th>修改</th></tr>

<?php break;case 'qarep_list': ?> 

<th>选</th><th>问答标题</th><th>回复标题</th><th>帐号</th>
<th>昵称</th><th>聊天号</th>
<th>添加</th><th>IP</th>
<th>修改</th></tr>

<?php break;case 'qatag_list': ?> 

<th>选</th><th>标签</th><th>热度</th>
<th>添加</th><th>IP</th>
<th>更新</th><th>IP</th>
<th>修改</th></tr>

<?php break;case 'cocar': ?> 

<th>选</th><th>货品ID</th><th>订单ID</th>
<th>数量</th><th>单价</th>
<th>会员名称</th>
<th>添加时间</th><th>修改</th></tr>

<?php break;case 'corder': ?> 

<th>选</th><th>订单号</th><th>状态</th><th>总额</th><th>数量</th><th>货品额</th>
<th>跟踪号</th>
<th>会员名称</th><th>电话</th>
<th>添加</th><th>修改</th></tr>

<?php } ?>