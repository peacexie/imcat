<?php
include './cfgs.php';

// dir
#

httpStatus(400);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Error 400 : <?=httpStatus(400,1)?></title>
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
        <h1>400 <?=httpStatus(400,1)?></h1>
        <h2>请求无效 : `<?=$uri?>`</h2>
        <div class="intruduction">
            <p class="title">可能原因：由于语法格式有误，服务器无法理解此请求。不作修改，客户程序就无法重复此请求。</p>
        </div>
        <!--foot:start-->
        <?php include './foot.php'; ?>
        <!--foot:end-->
    </div>
</div>
</body>
</html>