<?php
require '_config.php';

$act = req('act');
$d = req('d','txjia.com');
$kw = req('kw'); 
$pn = req('pn','74','N'); 

$mpage = 0;
if(!empty($act)){
  $crw = new exmCrawl($d,$kw,$pn);
  $data = $crw->data;
  $mpage = end($data['pages']);
  if(empty($mpage)){
    $mpage = empty($data['links']) ? 0 : 1;
  }
  if($act=='json'){
    die(json_encode($crw->data));
  }
}

glbHtml::page("Baidu - Search Engine Scan",1);
eimp('initJs','jquery');
eimp('initCss','stpub'); 
?>
<style type="text/css">
div, form, h4{ margin:10px; }
div, hr{ display:block; text-align:left; clear: both;}
#divs p {  width: 300px; display:inline-block; float: left; margin: 5px; }
iframe {  width: 300px; height: 200px; }
</style>
<?php glbHtml::page('body'); ?>
<form action="?" method="get">
domain=<input type="text" name="d" id="d" value="<?php echo $d; ?>"> # 
pn=<input type="text" name="pn" id="pn" value="<?php echo $pn; ?>" size="2">
kw=<input type="text" name="kw" id="kw" value="<?php echo $kw; ?>" size="8">
<input type="submit" name="act" id="act" value="do" style="width:60px">
<br>
<a href="?d=txjia.com&kw=">txjia.com</a> : 
<a href="?d=txjia.com&kw=贴心猫">贴心猫</a> : 
<a href="?d=txjia.com&kw=IntimateCat">IntimateCat</a> : 
<a href="?d=txmao.txjia.com">txmao.txjia.com</a> - 
<a href="?d=yscode.txjia.com">yscode.txjia.com</a> # 
<a href="?d=dg.gd.cn">dg.gd.cn</a> - 
<a href="?d=elifebike.com">elifebike.com</a> -
</form>

<?php
if($act=='do'){
  $bar = "<h4>pn=$mpage; d=$d # <a href='?d=$d&pn=$pn&kw=$kw&act=debug'>@debug</a> - <a href='{$crw->data['url']}'>@baidu</a></h4>";
  echo "<hr>$bar<div id='idlinks'>\n"; 
  for($i=0;$i<$mpage+1;$i++) {
    $pnstr = $i ? "pn={$i}0" : "pn=0";
    $url = str_replace("pn={$pn}0",$pnstr,$crw->data['url']);
    echo "\n<pre id='pg$i'><b>no:$i: <a href='$url'>$url</a></b></pre>";
  }
  echo "</div>";
}elseif(!empty($data)){
  dump($data);
}
?>

<script>

var mpages = <?php echo $mpage; ?>; // 6~12
var wmax = 6; // 6~12

function funcOpen(){
  for(var i=0;i<wmax+1;i++){ window.open('','_w'+i); }
  $('#idlinks').find('a').each(function(no, ilink) {
    var xr = jsRnd(1000,2000);
    if(mpages>60 && xr>1000&&xr<1380) return true;
    if(mpages>45 && xr>1000&&xr<1320) return true;
    if(mpages>30 && xr>1000&&xr<1260) return true;
    if(mpages>15 && xr>1000&&xr<1200) return true;
    var ra = jsRnd(1700,4300); 
    setTimeout("funcOset("+no+");",(no+1)*2500+ra);
  });
}
function funcOset(no){
  var ilink = $('#idlinks').find('a')[no];
  var url = $(ilink).prop('href');
  var text = $(ilink).html().replace('https://www.baidu.com','');
  text = (text.indexOf('?wd=')>0 ? text : '')+' --- opened!';
  $(ilink).html(text);
  window.open(url,'_w'+(no%wmax));
}

function runPages(){
  for(var i=0;i<mpages+1;i++){
    var xr = jsRnd(1000,2000); if(i==mpages) xr = 317; //最大值,不忽略
    if(mpages>60 && xr>1000&&xr<1380) continue;
    if(mpages>45 && xr>1000&&xr<1320) continue;
    if(mpages>30 && xr>1000&&xr<1260) continue;
    if(mpages>15 && xr>1000&&xr<1200) continue;
    var rw = jsRnd(1700,2300);
    if(i>0){
      rw = i*3700+rw; //3700
    }
    setTimeout("oneLinks("+i+");",rw);
  }
}

function oneLinks(no){
  url = '<?php echo "?d=$d&kw=$kw&pn=(0)&act=json"; ?>&'+jsRnd();
  url = url.replace('(0)',''+no);
  $.get(url, function(re){ 
    eval("var _arr="+re+";"); 
    var links = _arr.links;
    var str = '';
    for(i=0;i<links.length;i++){ 
      str += '<br><a href='+links[i]+'>'+links[i]+'</a>';
    }
    $('#pg'+no).html($('#pg'+no).html()+str);
    if(no==mpages){
      funcOpen();
    }
  });
}

<?php if($act=='do'){ ?>
runPages();
<?php } ?>

</script>

<?php
echo "\n<div>".basDebug::runInfo()."</div>";
basDebug::runLoad();
?>

</body>
</html>