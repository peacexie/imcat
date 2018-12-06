<?php
include './cfgs.php';

// dir
#

httpStatus(403);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Error 403 : <?=httpStatus(403,1)?></title>
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
        <h1>403 <?=httpStatus(403,1)?></h1>
        <h2>服务器拒绝 : `<?=$uri?>`</h2>
        <div class="intruduction">
            <p class="title">可能原因：您的访问被拒绝，请联系网站管理员！</p>
        </div>
        <!--foot:start-->
        <?php include './foot.php'; ?>
        <!--foot:end-->
    </div>
</div>
</body>
</html>