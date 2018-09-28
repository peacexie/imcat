<?php
namespace imcat;
require 'api_cfg.php'; 
$larr = array(
    'en'=>'en',
    'gb'=>'en',
    'cn'=>'zh-cn',
    'tw'=>'zh-cn',
    'de'=>'en',
    'fr'=>'en',
    'ru'=>'en',
);
edwimp('/edt_um/umeditor.config.js');
edwimp('/edt_um/umeditor.min.js');
// 'xx1', 'xx2',
?>
<?php if(1==2){ ?><script><?php } ?>
var edt_toolBar = [
    'source | undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | removeformat |',
    'insertorderedlist insertunorderedlist | selectall cleardoc paragraph | fontfamily fontsize' ,
    '| justifyleft justifycenter justifyright justifyjustify |',
    'link unlink | emotion image video  | map',
    '| horizontal print preview fullscreen', 'drafts'
]; //  'formula'
var edt_baseBar = [
    'source | justifyleft justifycenter justifyright | insertorderedlist insertunorderedlist |',
    'fontfamily fontsize | bold italic underline | horizontal link fullscreen'
]; // 'music',
var edt_langType = '<?php echo $larr[$lang]; ?>';
function edt_Init(fid,bar,w,h){
    fid = jsKey(fid); 
    bar = bar=='base' ? bar : 'tool';
    var para = "";
    para += "editor_"+fid+" = UM.getEditor('"+fid+"',{";
    para += "lang:edt_langType,";
    if(w) para += "initialFrameWidth:"+w+",";
    if(h) para += "initialFrameHeight:"+h+",";
    para += "toolbar:edt_"+bar+"Bar,";
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
    eval("var v = UM.getEditor('"+fid+"').getContent();");
    return v;
}
function edt_getText(fid,val){
    fid = jsKey(fid);
    eval("var v = UM.getEditor('"+fid+"').getContentTxt();"); //getPlainTxt()
    return v;
}
function edt_getSelect(fid, type){
    fid = jsKey(fid);
    //eval("var range = editor_"+fid+".selection.getRange();");
    eval("var text = editor_"+fid+".selection.getText();");
    v = text; //type=='text';
    return v; 
}
<?php if(1==2){ ?></script><?php } ?>
<?php
/*
//
*/
?>