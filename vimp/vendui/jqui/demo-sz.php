<?php
include dirname(dirname(dirname(__DIR__))).'/catmain/root/run/_init.php';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>jQuery File Upload Demo - Basic version</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src='<?=PATH_IMPS?>/vendui/jquery/jquery-2.x.js'></script>
<link href="<?=PATH_IMPS?>/vendui/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="./jquery.fileupload.css">
<script src="./jquery.ui.widget.js"></script>
<script src="./jquery.fileupload.js"></script>

<style type="text/css">

</style>
</head>

<body>
 
<!--DownLoadBegin-->
<div class="container">
    <div class="lgbox dl_box">
        <h3>jQuery File Upload Demo</h3>
        <form action="#" method="post" id="upform" name='upform' xonsubmit="return fmSubmit()">
            
            <ul class="dl_list">
                <li>标题：<input name="name" type="text" id="name" maxlength="12"></li>

                <li class='row_h5'>

                    <div class="load_1right" style="width:560px;">

                        <div class="row" id='files'>
                            <div class="col-md-4 upjqbtn celadd">
                                <span class="btn btn-info fileinput-button">
                                    <i class="fa fa-plus">+</i>
                                    <span>Pick-Multi</span>
                                    <input id="fileupload" type="file" name="filedata" multiple>
                                </span>
                            </div>
                        </div>
                        <!-- The global progress bar -->
                        <div id="progress" class="progress">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>

                    </div>           
                </li>  

            </ul>
            <input type="button" id="button" value="提交表单" class="button" onclick="fmSubmit()">
            <!--button>提交表单</button-->

        </form>
    </div>
</div>

<!--DownLoadEnd-->

<script>

var cnt = 0;
var burl = 'http://sz.xili.fzg360.com/';

var url =  '/home/index/upload.html?swf=1',
    minFileSize = 2*1024*1024,//文件不超过>2M
    maxFileSize = 50*1024*1024,//文件不超过50M 
    maxNumberOfFiles = 10;//最大上传文件数目

function formSend(){
    $('#form').submit();
    //alert('xx');
}

function removePic(it){
  cnt--;
  $(it).parent().remove(); 
  chkCount();
}
function itmImage(url){
    var img = "<img class=\"thumbimg\" width=\"100%\" src='http://sz.xili.fzg360.com/" + url + "'/>";
    var inp = "<input type='hidden' name='pics[]' value='" + url + "'/>";
    var del = "<a href='javascript:;' onclick='removePic(this)'>删除</a>";
    var link = "<a href='" + burl + url + "' rel=\"viewPhoto[xc]\" target='_blank'>"+img+"</a>";
    var itm = "<div class='col-md-4 celpic'>"+del+link+inp+"</div>";
    return itm;
}
function chkCount(){
    if(cnt >= maxNumberOfFiles ){
        alert('一次最多只能上传'+maxNumberOfFiles+'张图片,可多次投稿');  
        $('.upjqbtn').css('display','none');
    }else{
        $('.upjqbtn').css('display','block');
    }
}

$(function () {

    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            //console.log(data.originalFiles[0]['size']);
            var itm = itmImage(data.result.url);
            //$('#files').append(itm);
            $('.upjqbtn').before(itm);
            cnt++;
            chkCount();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
            //console.log('all:');
        }
    })
    .prop('disabled', !$.support.fileInput)
    .parent().addClass($.support.fileInput ? undefined : 'disabled')

    .bind('fileuploadadd', function (e, data) {
        //console.log(data.originalFiles[0]['size']);
        var tmp = data.originalFiles;
        for(var i=0;i<tmp.length;i++){
            if (tmp[i]['size'] > maxFileSize) {
                alert('文件太大了!');
                return false;
            }
            var type = $("input[name='type']:checked").val();
            var msize = parseInt(type) == 1 ? 5 : 2;
            if (tmp[i]['size'] < msize*1024*1024) {
                alert('文件太小了!');
                return false;
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
    .bind('click', function (e, data) {
        $('#progress .progress-bar').css('width', '0%');
    });
});
</script>


<script>

function fmSubmit() {
    return false;
}

</script>


</body>
</html>
