
<?php
(!defined('RUN_MODE')) && die('No Init');

echo '<br>'.date('y-m-d H:i:s');

echo '<br>';
echo '<br>'.comConvert::sysSn();
echo '<br>'.comConvert::sysSn('0735','.xys');
echo '<br>'.comConvert::sysSn('dg08','.cms');

echo '<br>';
echo '<br>'.basKeyid::kidRTable();
echo '<br>'.basKeyid::kidRTable('0');
echo '<br>'.basKeyid::kidRTable('a');
echo '<br>'.basKeyid::kidRTable('k');
echo '<br>'.basKeyid::kidRTable('f');
echo '<br>'.basKeyid::kidRTable('f','org');

echo '<br>';
echo '<br>'.basKeyid::kidRand('',32);
echo '<br>'.basKeyid::kidRand('fs',32);
echo '<br>'.basKeyid::kidRand('fs',32);
echo '<br>'.basKeyid::kidRand('fs',32);
echo '<br>'.basKeyid::kidTemp(4);
echo '<br>'.basKeyid::kidTemp('hm');

echo '<br>';
echo '<br>'.basKeyid::kidTemp('0');
echo '<br>4:'.basKeyid::kidTemp('4');
echo '<br>'.basKeyid::kidTemp('m-dh');

echo '<br>';
echo '<br>h:'.basKeyid::kidTemp('h');
echo '<br>'.basKeyid::kidTemp('hm','2012-01-01 12:30');
echo '<br>'.basKeyid::kidTemp('hm','2012-12-31 00:00');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-09 00:01');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-09 00:02');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-10 00:09');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-11 23:10');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-12 23:13');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-13 23:58');
echo '<br>'.basKeyid::kidTemp('hm','2012-09-14 23:59');
echo '<br>';

echo '<br>'.basKeyid::kidTemp('hms','2012-01-01 12:30');
echo '<br>'.basKeyid::kidTemp('hms','2012-12-31 00:00');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-09 00:01');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-10 00:09');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-11 23:10');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-12 23:13');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-13 23:58:59');
echo '<br>'.basKeyid::kidTemp('hms','2012-09-14 23:59:59');
echo '<br>';

echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(hms): ".basKeyid::kidTemp('hms');	
echo '<br>'.microtime(1);

echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(3.4): ".basKeyid::kidTemp('3.4');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(4.5): ".basKeyid::kidTemp('4.5');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(5.6): ".basKeyid::kidTemp('5.6');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(3): ".basKeyid::kidTemp('3');	
echo '<br>'.microtime(1);
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(4): ".basKeyid::kidTemp('4');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(5): ".basKeyid::kidTemp('5');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(6): ".basKeyid::kidTemp('6');	
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(7): ".basKeyid::kidTemp('7');	
echo '<br>'.microtime(1);
echo '<br>'.microtime(1);
for($i=0;$i<5;$i++) echo "<br>--- run - basKeyid::kidTemp(-): ".basKeyid::kidTemp('-');	
echo '<br>'.microtime(1);

$ktab32 = str_replace(array('0','E'),'',KEY_TAB32);

echo '<br>'.$ktab32{intval(0/2)};
echo '<br>'.$ktab32{intval(1/2)};
echo '<br>'.$ktab32{intval(2/2)};
echo '<br>'.$ktab32{intval(3/2)};
echo '<br>'.$ktab32{intval(59/2)};
echo '<br>'.$ktab32{intval(58/2)};
echo '<br>'.$ktab32{intval(57/2)};
echo '<br>'.$ktab32{intval(56/2)};

echo '<br>';
echo '<br>'.basKeyid::kidNext('','A006', 'A001',3,4);
echo '<br>'.basKeyid::kidNext('','YYYY', '001',1,4);
echo '<br>'.basKeyid::kidNext('','YYYY8','001',1,4);
echo '<br>'.basKeyid::kidNext('','ZZZ', '001',1,4);
echo '<br>';

echo '<br>'.microtime(1);
for($i=0;$i<3;$i++) echo "<br>--- run - kidTemp: ".basKeyid::kidTemp();	
echo '<br>'.microtime(1);

echo '<br>';
for($i=0;$i<10;$i++) echo '<br>'.substr(microtime(),2,8);

echo '<br>';
echo '<br>123456789-123456789-';
echo '<br>kidAuto:'.basKeyid::kidAuto();
echo '<br>';
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:27:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:29:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:31:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:33:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:35:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-06 09:37:00');
echo '<br>';
echo '<br>'.basKeyid::kidTemp('','2012-07-05 00:00:00');
echo '<br>'.basKeyid::kidTemp('','2012-07-05 00:00:01');
echo '<br>'.basKeyid::kidTemp('','2012-07-05 00:00:02');
echo '<br>'.basKeyid::kidTemp('','2012-07-05 00:00:03');
echo '<br>'.basKeyid::kidTemp('','2012-07-04 23:59:57');
echo '<br>'.basKeyid::kidTemp('','2012-07-04 23:59:58');
echo '<br>'.basKeyid::kidTemp('','2012-07-04 23:59:59');

echo '<br>';
echo '<br>'.basKeyid::kidRand('f',32);
echo '<br>'.basKeyid::kidRand('f',32);
echo '<br>'.basKeyid::kidRand('f',32);

echo '<br>';
//for($i=3;$i<12;$i++) echo '<br>'.basKeyid::kidSess($i);

echo '<br>';


?>
