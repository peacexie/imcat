<?php 
require 'api_cfg.php'; 
$larr = array(
    'cn'=>'zh-CN',
    'tw'=>'zh-TW',
);
edwimp('/summernote/summernote.min.js');
edwimp('/summernote/summernote.css');
$edt_Lang = 'en-US';
if($lang && isset($larr[$lang])){
    edwimp("/summernote/lang/summernote-{$larr[$lang]}.min.js");
    $edt_Lang = $larr[$lang];
}
?>

<?php if(1==2){ ?><script><?php } ?>

var edt_toolBar = {};
edt_toolBar['full'] = [
    ['para', ['paragraph', 'ul', 'ol']],
    ['_base', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']], 
    ['style', ['style', 'fontname', 'fontsize', 'height', 'color']],         
    ['_media',['link','picture','video','table','hr']],
    ['_ctrls',['fullscreen','codeview','undo','redo','help']],
]; 
edt_toolBar['base'] = [
    ['para', ['paragraph', 'ul', 'ol']], // 段落工具 
    ['_base', ['bold', 'italic', 'underline']], // 基本按钮
    ['style', ['style', 'fontsize']], // 字体工具              
    ['_media',['link', 'hr', 'clear']], // 媒体工具
    ['_ctrls',['fullscreen', 'codeview']], // 控制项
]; // 'music',

//_cbase.run.snflag = 0;
function edt_Init(fid,bar,w,h){
    fid = jsKey(fid); 
    bar = bar=='base' ? bar : 'full';
    //var flag = 0;
    $('#'+fid).summernote({
        toolbar: edt_toolBar[bar], lang: '<?=$edt_Lang?>',
        callbacks: {
            // upload: 上传监控
            onImageUpload: function(fs){
                $.each(fs,function(no,val){edt_Upfile(fs[no],fid)})
            },
            // blur: 离开editor后,插入内容在focus处
            onBlur: function(){
                //$('#'+fid).summernote('saveRange');
                //$('#'+fid).summernote('focus');
            },
            onFocus: function(){
                //_cbase.run.snflag++;
            }
            // onChange,onKeydown,
        }, 
        //height: h, 
        tabsize: 4
    });
    edt_showBar(fid);
}
function edt_Insert(fid,val){
    fid = jsKey(fid); //val = jsRep(val);
    if(val.indexOf('<')<0){
        edt_InsText(fid,val);
    }else{ 
        var node, tmp = $(val); 
        if(tmp.length>1){ jsLog('1');
            node = document.createElement('div'); $(node).html(val); 
        }else{ jsLog('2');
            node = $(val).get(0);
        } 
        $('#'+fid).summernote('insertNode', node);
    }
}
function edt_InsText(fid,val){
    fid = jsKey(fid);
    //$('#'+fid).summernote('restoreRange');
    $('#'+fid).summernote('insertText', val);
}
function edt_getHTML(fid){
    fid = jsKey(fid);
    return $('#'+fid).summernote('code');
}
function edt_getText(fid,val){
    fid = jsKey(fid);
    var html = $('#'+fid).summernote('code');
    html = html.replace(/<\/?[^>]*>/g, ''); //去除HTML tag
    html = html.replace(/[ | ]*\n/g, '\n'); //去除行尾空白
    html = html.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
    return html;
}
function edt_getSelect(fid, type){
    fid = jsKey(fid);
    var range = $('#'+fid).summernote('createRange');
    return $('#'+fid).summernote('saveRange');
}
function edt_Upfile(file, fid) {
    fid = jsKey(fid); 
    var data = new FormData();
    data.append("files", file); 
    $.ajax({
        data : data,
        type : "POST", 
        url : _cbase.run.roots+"/plus/file/updeel.php?recbk=json&_r=v02", 
        cache : false,
        contentType : false,
        processData : false,
        dataType : "json",
        success: function(row) {
            $('#'+fid).summernote('insertImage', row.url); 
        },
        error:function(){
            jsLog("["+file.name+"] Upload Fail!");
        }
    });
}

<?php if(1==2){ ?></script><?php } ?>
