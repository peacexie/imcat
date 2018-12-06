<?php
include './cfgs.php';

# dir-伪静态到动态
// `/chn/cargo.htm`  -=>  `/chn.php?cargo`
$cfgs = array('chn','dev','doc');
$url = '';
if(strpos($uri,'.htm')>0){
    foreach ($cfgs as $key) {
        if(substr($uri,0,5)=="/$key/"){
            if(strpos($uri,'?')>0){
                $url = str_replace(array("/$key/",'.htm?'),array("/$key.php?",'&'),$uri);
            }else{
                $url = str_replace(array("/$key/",'.htm'),array("/$key.php?",''),$uri);
            }
        }
    }
}

# dir-blog
// /tip/?2011-39-8TR9  -=>  /chn.php?news.2011-39-8tr9
if(strpos($uri,'tip/?')>0){
    $url = "http://imcat.txjia.com".str_replace('/tip/?','/chn.php?news.',strtolower($uri));
}

# baby,kgfood,peace,tools,wee
$pre3 = substr($uri,1,3);
if(strpos(",bab,kgf,pea,tip,too,wee,",$pre3)){
   $url = "http://txjia.com".$uri;
}

ob_clean();
// 本身是404页,可能存在404的head,所以以下跳转代码无效
if($url){
    header("location:$url");
    //die($url);
}
httpStatus(404);

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
