<?php 
require('api_cfg.php'); 
$larr = array(
	'en'=>'en',
	'gb'=>'en',
	'cn'=>'zh_CN',
	'tw'=>'zh_TW',
	'de'=>'en',
	'fr'=>'en',
	'ru'=>'en',
);
echo basJscss::write(basJscss::imp('/KindEditor/kindeditor-min.js','vui'))."\n";
echo basJscss::write(basJscss::imp("/KindEditor/lang/$larr[$lang].js",'vui'))."\n";
?>
<?php if(1==2){ ?><script><?php } ?>
var edt_toolBar = [
	"source","|","undo","redo","|","preview","code","plainpaste","wordpaste","|",
	"justifyleft","justifycenter","justifyright","justifyfull","insertorderedlist","insertunorderedlist","indent","outdent",
	"anchor","link","clearhtml","quickformat","|","fullscreen","/",
	"formatblock","fontname","fontsize","|",
	"bold","italic","underline","strikethrough","subscript","superscript","|","forecolor","hilitecolor","lineheight","removeformat","|",
	"image","baidumap","|","table","hr","|","about"
]; //"multiimage","flash","media","insertfile",,"map"
var edt_baseBar = [
	"source","|","justifyleft","justifycenter","justifyright","insertorderedlist","insertunorderedlist","indent","outdent","|",
	"fontname","fontsize","forecolor","bold","italic","underline","link","|","fullscreen","about"
];
var edt_langType = '<?php echo $larr[$lang]; ?>';
function edt_Init(fid,bar,w,h){
	fid = jsKey(fid);
	bar = bar=='base' ? bar : 'tool';
	KindEditor.ready(function(K){
		var para = "";
		para += "editor_"+fid+" = K.create('#"+fid+"',{";
		para += "langType:edt_langType,";
		para += "resizeType:1,";
		para += "allowImageUpload:false,";
		if(w) para += "width:'"+w+"px',";
		if(h) para += "height:'"+h+"px',";
		para += "items:edt_"+bar+"Bar";
		para += "});"; //alert(para);
		eval(para);
		edt_showBar(fid);
	});
}
function edt_Insert(fid,val){
	fid = jsKey(fid);
	val = jsRep(val)
	eval("editor_"+fid+".insertHtml('"+val+"');");
}
function edt_InsText(fid,val){
	edt_Insert(fid,val);
}
function edt_getHTML(fid){
	fid = jsKey(fid);
	eval("var v = editor_"+fid+".html();");
	return v;
}
function edt_getText(fid){
	fid = jsKey(fid);
	eval("var v = editor_"+fid+".text();"); 
	return v;
}
function edt_getSelect(fid, type){
	fid = jsKey(fid);
	eval("var v = editor_"+fid+".selectedHtml("+fid+");");
	if(type=='text') v = jsText(v);
	return v; 
}
<?php if(1==2){ ?></script><?php } ?>
<?php
/*
//
*/
?>