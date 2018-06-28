<?php
include './cfgs.php';

// dir
#

httpStatus(502);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>Error 502 : Bad Gateway</title>
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
        <h1>502 Bad Gateway</h1>
        <h2>服务响应超时</h2>
        <div class="intruduction">
            <p class="title">如果您是网站管理员，请仔细检查您的网站代码是否包含<strong>死循环、获取文件超时</strong>等情况，优化代码逻辑。同时请检查<strong>内存使用情况</strong>，根据情况及时升级内存；或者可尝试<strong>重启主机</strong>或<strong>调整PHP超时时间</strong>缓解问题。</p>
        </div>
        <!--foot:start-->
        <?php include './foot.php'; ?>
        <!--foot:end-->
    </div>
</div>
</body>
</html>