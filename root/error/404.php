<?php
include './cfgs.php';

$_fp = '../cfgs/stinc/404_inc.php'; // 404_inc.php-cdemo
if(file_exists($_fp)){
    include($_fp);
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Error 404 : <?=httpStatus(404,1)?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name='viewport' content='width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no'>
<style type="text/css">
<?php include './style.css'; ?>
</style>
</head>
<body>
<div class="main">
    <!--head:start-->
    <?php include './head.php'; ?>
    <!--head:end-->
    <div class="content">
        <h1>404 <?=httpStatus(404,1)?> </h1>
        <h2>您访问的网页不存在 : `<?=$uri?>`</h2>
        <div class="intruduction">
            <p class="title">
                请检查您的网页文件是否存在，或者是网站改版了…
                <?php if($url){ ?>
                <br>
                <span id='secs'>5</span> ... Go ... 
                <a href="<?php echo $url; ?>" target="_self">推荐访问 <?php echo $url; ?></a>
                <?php } ?>
            </p>
        </div>
        <!--foot:start-->
        <?php include './foot.php'; ?>
        <!--foot:end-->
    </div>
</div>
<script>
function jump(){
    var idSec = document.getElementById('secs');
    if(!idSec) return;
    nSec = parseInt(idSec.innerHTML);
    if(nSec>0) { idSec.innerHTML = nSec-1; setTimeout("jump()",1000); }
    else{ location.href = '<?php echo $url; ?>'; }
}
setTimeout("jump()",1000);
</script>
</body>
</html>
