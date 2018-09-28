<?php
namespace imcat;
require 'api_cfg.php'; 
$larr = array(
    'en'=>'en',
    'gb'=>'en-gb',
    'cn'=>'zh-cn',
    'tw'=>'zh',
    'de'=>'de',
    'fr'=>'fr',
    'ru'=>'ru',
);
edwimp('/edt_ck/ckeditor.js'); 
edwimp("/edt_ck/lang/$larr[$lang].js");
?>
<?php if(1==2){ ?><script><?php } ?>
var edt_toolBar = [
    { name: 'source', items: [ 'Source' ] },
    { name: 'document', items: [ 'Undo', 'Redo' ] },
    { name: 'links', items: ['TextColor','BGColor', 'Link', 'Unlink', 'Anchor' ] },
    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
    { name: 'format', items: [ 'RemoveFormat' ] },
    { name: 'tools', items: [ 'Maximize' ] },
    { name: 'others', items: [ '-' ] },
    '/',
    { name: 'styles', items: [ 'Styles', 'Format' ] },
    { name: 'basics', items: [ 'Bold', 'Italic', 'Strike', 'Underline','Subscript','Superscript'] },
    { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', ] },
    { name: 'about', items: [ 'About' ] }
]; 
var edt_baseBar = [
    { name: 'document', items: [ 'Source' ] },
    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ,'-' , 'Link', 'Anchor' ] },
    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
    { name: 'tools', items: [ 'Maximize' ] },    
    { name: 'about', items: [ 'About' ] }
];
        
var edt_langType = '<?php echo $larr[$lang]; ?>';
function edt_Init(fid,bar,w,h){
    fid = jsKey(fid);
    bar = bar=='base' ? bar : 'tool';
    var para = "";
    para += "CKEDITOR.replace('"+fid+"', {";
    para += "language:edt_langType,"; 
    if(w) para += "width:"+w+",";
    if(h) para += "height:"+h+",";
    para += "toolbar:edt_"+bar+"Bar"; //edt_"+bar+"Bar
    para += "});"; 
    eval(para); //CKEDITOR.replace(fid);
    edt_showBar(fid);
}
function edt_Insert(fid,val){
    fid = jsKey(fid);
    //val = jsRep(val)
    eval("var editor = CKEDITOR.instances."+fid+";"); 
    editor.insertHtml( val );
}
function edt_InsText(fid,val){
    fid = jsKey(fid);
    //val = jsRep(val)
    eval("var editor = CKEDITOR.instances."+fid+";"); 
    editor.insertText( val );
}
function edt_getHTML(fid){
    fid = jsKey(fid);
    eval("var editor = CKEDITOR.instances."+fid+";");
    return editor.getData();
}
function edt_getText(fid){
    var v = edt_getHTML(fid);
    v = jsText(v);
    return v;
}
function edt_getSelect(fid, type){
    fid = jsKey(fid);
    eval("var editor = CKEDITOR.instances."+fid+";");
    var s = editor.getSelection();
    if(CKEDITOR.env.ie){
        s.unlock(true);
        var v = s.getNative().createRange().text;
    }else{
        var v = s.getNative();
    }
    //if(type=='text') v = jsText(v);
    return v; 
}
<?php if(1==2){ ?></script><?php } ?>
<?php
/*
//
*/
?>