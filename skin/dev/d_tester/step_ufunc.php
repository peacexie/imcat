<?php
include_once dirname(__FILE__).'/step_ucfg.php';

$pid = req('pid'); 
$order = req('order','kid');
$order = in_array($order,array('kid','title','pid')) ? $order : 'kid';
$opkey = isset($_GET['opkey']) ? req('opkey') : ($order==='kid' ? 1 : 0);

$sfrom = 'kid,model,pid,title,cfgs FROM types_china_ys ';
$where = (empty($pid) ? "deep=2" : "pid='$pid'"); 

$pg = new comPager($sfrom,$where,10,"$order"); 
$pg->set('opkey',$opkey);
$rs = $pg->exe(); 

glbHtml::page('db,翻页:演示2');
glbHtml::page('imjq');
echo basJscss::imp('/skin/dev/b_jscss/comm.css');
glbHtml::page('body');
?>

<?php include(DIR_SKIN."/dev/d_tester/tester_nav.htm"); ?>

<style type="text/css">
body,nav,div {
    padding:5px;
}
body,td,th,div {
    font-size:14px; line-height:180%;
}
p { text-align:left; margin:auto; }
td,th {
    padding:2px;
}
</style>

<h3>多用点类库 - 还是不用模板调用:示例</h3>

<nav> pid: 
  <a href='?'>[RESET]</a> |
  <a href='?pid=cnhn'>cnhn</a> |
  <a href='?pid=cngd'>cngd</a> |
  <a href='?pid=cngx'>cngx</a> |
<br> orderby: 
  <a href='?order=kid&pid=<?php echo $pid; ?>'>kid</a> |
  <a href='?order=title&pid=<?php echo $pid; ?>'>title</a> |
  <a href='?order=pid&pid=<?php echo $pid; ?>'>pid</a> |
<br> ordermode: 
  <a href='?odesc=1&pid=<?php echo $pid; ?>'>odesc=1</a> |
  <a href='?odesc=0&pid=<?php echo $pid; ?>'>odesc=0</a> |
<br> view source-code: 
  <a href='<?php echo surl(0)."?mkv=info-coder&tpls=d_tester/ufunc.php"; ?>' target="_blank">查看代码</a>
</nav>

<hr />

<?php

echo "<table border=1>";
echo "<tr><th>kid</th><th>pid</th><th>title</th><th>detail</th></tr>"; 
$idfirst = ''; $idend = '';
if($rs){
foreach($rs as $v){
    if(empty($idfirst)) $idfirst = $v['kid'];
    $idend = $v['kid'];
    echo "<tr><td>$v[kid]</td><td>$v[pid]</td><td>$v[title]</td><td>$v[cfgs]</td></tr>";    
}}
echo "</table>";
echo "<div class='pg_bar'>".$pg->show($idfirst,$idend)."</div>";

?>

<hr />

<?php
echo(basDebug::runInfo());
glbHtml::page('end');
?>
