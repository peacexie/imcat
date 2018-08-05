<?php
include dirname(dirname(dirname(__DIR__))).'/catmain/root/run/_init.php';
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<title>jQuery-UI Upload</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src='/peace/imcat/catmain/root/plus/ajax/comjs.php?act=initJs&tpldir=adm&lang=cn&mkv=dops-a&exjs=jquery,jspop;comm;comm(-lang)&_r=4.2'></script>
<link href='/peace/imcat/catmain/root/plus/ajax/comjs.php?act=initCss&tpldir=adm&lang=cn&skin=min&excss=bootstrap,stpub,jstyle;comm&_r=4.2' type='text/css' rel='stylesheet'/>

<?php echo basJscss::imp("/_pub/a_jscss/multpic.js"); ?>

<link rel="stylesheet" href="./jquery-fileupload.css">
<script src="./jquery-ui.min.js"></script>
<script src="./jquery-fileupload.js"></script>

</head>
<body class="container">


<table>

<tr ><td class='tc'>缩略图</td>
<td class='tl'>
    <input id='fm_mpic_' name='fm[mpic]' type='text' value='' class='file'  maxlength='255'  reg='nul:fix:image'  tip='gif/jpg/jpeg/png格式.'  style='width:320px';  >
    <input type='file' id='fm_mpic_f' name="files" style="display:none;">
    <input type='button' value='上传' id='fm_mpic_b'>
    <input type='button' value='浏览' onclick="winOpen('/peace/imcat/catmain/root/plus/file/fview.php?fid=fm_mpic_&mod=demo&kid=2018-7x-g5kp','浏览附件',720,480)">
    <input type='button' value='清空' onclick="$('#fm_mpic_').val('');">
    <div id="fm_mpic_bar" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>
</td></tr>

<tr ><td class='tc'>相关图片</td>
<td class='tl'>
    <div id='fm_rel_pic_out' class='mpic_out'>
        <div id='fm_rel_pic_show'>tout|外观图;tin|内饰;telse|配件;</div>
        <div id='fm_rel_pic_tarea' class='clear'>
            <textarea name='fm[rel_pic]' id='fm_rel_pic_' style='display:none;'></textarea>
        </div>
        <input type='file' id='fm_rel_pic_f' name="files" multiple style="display:none;">
        <input type='button' value='上传' id='fm_rel_pic_b'>
        <input type='button' value='浏览' onclick="winOpen('/peace/imcat/catmain/root/plus/file/fview.php?fid=fm_rel_pic_&mod=demo&kid=2018-7x-g5kp','浏览附件',720,560)">
        <input type='button' value='清空' onClick="mpic_clear('fm_rel_pic_');">
    </div>
    <script></script>
</td></tr>


<script>

$(function () {
    fp_upload('fm_mpic_', 1, 234);
    fp_upload('fm_rel_pic_', 23, 234);
});
</script><tr>

</table>

    <h1>jQuery-UI Upload</h1>
    <h2 class="lead">Basic version</h2>

    <div class="row" id='files'>
        <div class="col-md-2 upjqbtn">
            <span class="btn btn-info fileinput-button">
                <i class="glyphicon glyphicon-plus" style="font-size:larger;"></i>
                <span>Pick</span>
                <input id="fpbtn" type="file" name="filedata" multiple>
            </span>
        </div>

    </div>

    <!-- The global progress bar -->
    <div id="fpbar" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <!-- The container for the uploaded files -->

<input type="file" name="f1" style="width:75px; display:inline;"> ddd
<input type="file" name="f2" multiple>

<script>

// fpbtn, fpinp, fpimg

$(function () {

    var url = _cbase.run.roots+"/plus/file/updeel.php?recbk=json&_r=v02", 
        minFileSize = 1*1024,//文件不超过>10K
        maxFileSize = 960*1024,//文件不超过960K
        maxNumberOfFiles = 3;//最大上传文件数目

    $('#fpbtn').fileupload({
        url: url,
        dataType: 'json',
        method: 'post',
        done: function (e, data) { 
            var res = data.result;
            jsLog(res);
            //console.log(data.originalFiles[0]['size']);
            //var url = 'http://192.168.1.228/fzg360/szyjf/' + data.result;
            itm = "<div class='col-md-2'><img width='160' height='120' src='"+res.url+"'></div>";
            $('.upjqbtn').before(itm);
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#fpbar .progress-bar').css(
                'width',
                progress + '%'
            );
            //console.log('all:');
        }
    })
    .prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled')

    .bind('fileuploadadd', function (e, data) {
        var tmp = data.originalFiles;
        for(var i=0;i<tmp.length;i++){
            if (tmp[i]['size'] > maxFileSize) {
                alert('文件太大了!');
                return true;
            }
            if (tmp[i]['size'] < minFileSize) {
                alert('文件太小了!');
                return true;
            }
        }
        //console.log('fileuploadadd');
    })
    .bind('fileuploadfail', function (e, data) {
        //console.log('fail:');
        if (data.errorThrown=='abort') {
             alert('上传取消！', 'success',3);
        }else{
             alert('上传失败，请稍后重试！', 'error',3);
        }
    })
    .bind('fileuploadchange', function (e, data) {
        //console.log('fileuploadchange');
    })
    .bind('click', function (e, data) {
        $('#fpbar .progress-bar').css('width', '0%');
    });
});
</script>

<hr>

<pre>


http://www.jb51.net/article/99179.htm
jQuery File Upload文件上传插件使用详解

常用的回调函数：

1. add: 当文件被添加到上传组件时被触发

2. processalways: 当一个单独的文件处理队列结束（完成或失败时）触发

3. progressall: 全局上传处理事件的回调函数

4. fail : 上传请求失败时触发的回调函数，如果服务器返回一个带有error属性的json响应这个函数将不会被触发。

5. done : 上传请求成功时触发的回调函数，如果服务器返回一个带有error属性的json响应这个函数也会被触发。

6. always : 上传请求结束时（成功，错误或者中止）都会被触发。

$('#fileupload').bind('fileuploadadd', function (e, data) {/* ... */});

$('#fileupload').on('fileuploadprogressall', function (e, data) { //进度条显示
var progress = parseInt(data.loaded / data.total * 100, 10);
$('#fpbar .progress-bar').css(
'width',
progress + '%'
);
});


https://www.cnblogs.com/jinzhao/p/5914211.html
blueImp/jQuery file upload 的正确用法（限制上传大小和文件类型）

console.log(data.originalFiles[0]['size']) ;
if (data.originalFiles[0]['size'] > 5000000) {
    uploadErrors.push('Tamaño de Archivo demasiado Grande');
}

</pre>

</body>
</html>
