
<?php
require(dirname(__FILE__).'/_config.php'); 

$a = array(
	123,
	234,
	'a1'=>'abc1',
	'a2'=>array('b1'=>'v1','b2'=>'v2'),
	'a3'=>'abc3',
);

echo "<pre>";

print_r($a);
basArray::set($a,'a2.b1','isset');
print_r($a);
$re = basArray::get($a,'a2.b2');
print_r($re);

echo '<br>basArray::inStr:'; var_export(basArray::inStr(array('yong','263','369'),'xys@163.com'));
echo '<br>basArray::inStr:'; var_export(basArray::inStr(array('xie','163','xys'),'xys@163.com'));

echo '<br>basArray::inArr:'; var_export(basArray::inArr(array('163.org','163.com','163.net'),'.cn'));
echo '<br>basArray::inArr:'; var_export(basArray::inArr(array('163.org','163.com','163.net'),'.com'));

echo "</pre>";


?>
