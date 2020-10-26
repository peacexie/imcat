
<?php
// 本页以php为主
$n = req('n', 10);
$s = 0;
for($i=0; $i<=$n; $i++) {
    $s += $i;
}
echo "1+2+3+...+$n=".$s;
?>
<p>demo.htm：扩展示例：</p>
<p>访问：?exinc-demo，显示本页</p>
