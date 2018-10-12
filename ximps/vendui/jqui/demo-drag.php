<?php
include dirname(dirname(dirname(__DIR__))).'/catmain/root/run/_init.php';
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8">
<title>jQuery-UI Draggable</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src='/peace/imcat/catmain/root/plus/ajax/comjs.php?act=initJs'></script>
<script src='<?=PATH_IMPS?>/vendui/jquery/jquery-2.x.js'></script>
<link href="<?=PATH_IMPS?>/vendui/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<script src="./jquery-ui.min.js"></script>
<style>
#dragout{ min-width:720px; min-height:360px; padding:10px; margin:20px auto; border:1px solid #CCC; }
#draggable { width:120px; height:50px; padding:0.5em; border:1px solid #6F6; }
</style>

</head>
<body class="container">

    <h1>jQuery-UI Draggable</h1>
    <h2 class="lead">http://api.jqueryui.com/draggable/</h2>

<div id="dragout">
    <div id="draggable">
      <p>Drag me around</p>
    </div>
</div>

<script>
$( function(){
    var opt = {containment:"parent"};
    $("#draggable").draggable(opt);
});
</script>

</body>
</html>
