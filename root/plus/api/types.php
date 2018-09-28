<?php
namespace imcat;
require dirname(__FILE__).'/_config.php'; 
glbHtml::page('Types Pick',1);
eimp('initJs','jquery,jspop;comm;comm(-lang);/_pub/jslib/search;/_pub/jslib/jstypes'); 
eimp('initCss','stpub,jstyle;comm'); // bootstrap,
?>
<style type="text/css">
.highlight { background: green; font-weight: bold; color: white; padding: 0px 8px; }
.table { }
</style>
<script>

</script>
</head><body style="padding:3px">
<script>
var _wp = window.parent, _pd = parent.document;
var fid = urlPara('fid'), fid2 = jsKey(fid); 
str = '<table width="100%" border="0" cellpadding="3" cellspacing="3" class="table">';
str += '<tr><td><div class="w100 cF00 hand right tr" style="padding-top:3px;">'; 
str += '<span id="xid2_clear" class="ph2" onClick="popSetValue(\'clear\')" title="'+lang('adm.types_cltip')+'">'+lang('adm.types_clear')+'</span>';
str += '<span id="xid2_close" class="ph2" onClick="popClose()" title="'+lang('adm.types_cftip')+'">'+lang('adm.types_confirm')+'</span></div>';
str += '<span id="xid2_title" class="inblock"><input name="schVal" id="schVal" type="text"></span>';
str += '<input name="bsend" type="submit" class="btn" value="'+lang('adm.types_search')+'" onclick="schDone()" /></td></tr>';
str += '<tr><td id="xid2_now" class="h180"></td></tr>';
str += '<tr><td id="xid2_step" class="h180 tr"><span class="c999">'+lang('adm.types_new')+'</span></td></tr>';
str += '<tr><td id="xid2_list" class="h180" style="padding:5px 0 0 0;border-top:1px solid #CCC;">-list-</td></tr>';
str += '</table>';
document.write(str); // Table 初始化
popNames(); // 项目名称,多选-已择项目
popSetp('0'); // 级别梯,&List
<?php 
$cb = req('cb'); 
echo $cb ? "window['types_cb'] = '$cb';\n" : "\n"; 
?>
</script>
</body>
</html>
