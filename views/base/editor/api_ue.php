<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

$larr = array(
    'en'=>'en',
    'gb'=>'en',
    'cn'=>'zh-cn',
    'tw'=>'zh-cn',
    'de'=>'en',
    'fr'=>'en',
    'ru'=>'en',
);
edwimp('/edt_ue/ueditor.config.js');
edwimp('/edt_ue/ueditor.all.min.js');
// 'preview', 'backcolor', 
?>
<?php if(1==2){ ?><script><?php } ?>
var edt_toolBar = [[
    'fullscreen', 'source', '|', 'undo', 'redo', '|',
    'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'blockquote', 'pasteplain', '|', 
    'insertorderedlist', 'insertunorderedlist', '|',
    'lineheight', '|', 'anchor','link', 'removeformat', 'autotypeset', 
],[
    'paragraph', 'fontfamily', 'fontsize', '|',
    'directionalityltr', 'directionalityrtl', '|',
    'forecolor', 'justifyleft', 'justifycenter', 'justifyright', '|',
    'insertimage', 'map', 'inserttable', 'horizontal', '|',
    'help'
]]; //'insertvideo','attachment','insertframe',
var edt_baseBar = [[
    'fullscreen', 'source',"|",'justifyleft', 'justifycenter', '|','directionalityltr', 'directionalityrtl', "|",
    'customstyle', 'paragraph', 'fontsize','anchor','link',"|","about"
]]; // 'music',
var edt_langType = '<?php echo $larr[$lang]; ?>';
function edt_Init(fid,bar,w,h){
    fid = jsKey(fid); 
    bar = bar=='base' ? bar : 'tool';
    var para = "";
    para += "editor_"+fid+" = UE.getEditor('"+fid+"',{";
    para += "lang:edt_langType,";
    if(w) para += "initialFrameWidth:"+w+",";
    if(h) para += "initialFrameHeight:"+h+",";
    para += "toolbars:edt_"+bar+"Bar,";
    para += "focus:true"; 
    para += "});"; 
    eval(para);
    edt_showBar(fid);
}
function edt_Insert(fid,val){
    fid = jsKey(fid);
    val = jsRep(val);
    eval("editor_"+fid+".execCommand('insertHtml','"+val+"');");
}
function edt_InsText(fid,val){
    edt_Insert(fid,val);
}
function edt_getHTML(fid){
    fid = jsKey(fid);
    eval("var v = UE.getEditor('"+fid+"').getContent();");
    return v;
}
function edt_getText(fid,val){
    fid = jsKey(fid);
    eval("var v = UE.getEditor('"+fid+"').getPlainTxt();"); 
    return v;
}
function edt_getSelect(fid, type){
    fid = jsKey(fid);
    eval("var range = editor_"+fid+".selection.getRange();");
    range.select();
    eval("var text = editor_"+fid+".selection.getText();");
    //eval("var html = editor_"+fid+".selection.getText();");
    v = text; //type=='text';
    return v; 
}
function edt_setHtml(fid, data){
    fid = jsKey(fid);
    eval("var edtObj = UE.getEditor('"+fid+"');");
    edtObj.setContent(data);
}
<?php if(1==2){ ?></script><?php } ?>
<?php
/*
//
*/
?>