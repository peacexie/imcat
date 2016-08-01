<?php
header("Content-type:text/html;charset=utf-8");

header("Access-Control-Allow-Origin: *");
$act = empty($_GET['act']) ? '' : $_GET['act'];

if(empty($act)){

	//header('Content-Type:application/json');
	$revar = empty($_GET['revar']) ? 'aabbcc' : $_GET['revar'];
	$flag = @$_GET['flag'];
	$tmp = '{"aid":"234-","subject":"汽车房产家装标题{fix}"}';
	$rnd = mt_rand(3,5);
	$s = '';
	for($i=0;$i<$rnd;$i++){
		$itm = str_replace(array('234-','{fix}'),array("234-$i",":$i:$flag:"),$tmp);
		$s .= (empty($s) ? '' : ',')."$itm";	
	}
	echo "var $revar = [$s];";

}elseif($act=='on_off'){

	$a = array('HTTP_X_REAL_FORWARDED_FOR','HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR','HTTP_VIA'); 
	foreach($a as $k) if(isset($_SERVER[$k])) echo "<i>$k:</i> {$_SERVER[$k]}<br>\n";
	echo "<i>Server Time:</i> ".date('Y-m-d H:i:s')."<br>\n";
	
}elseif($act=='ver_upd'){

}elseif($act=='fso'){
	echo "<a href='?act=fputs'>fputs</a> # <a href='?act=dirc'>dirc</a> # <a href='?act=erlog'>erlog</a>";
}elseif($act=='fputs'){
	file_put_contents("./ufile1.htm",'write ok.');
	echo "<a href='ufile1.htm'>ufile1.htm</a>";
}elseif($act=='dirc'){	
	mkdir("udir1", 0777, true);		
	var_dump(is_dir("./udir1"));
}elseif($act=='erlog'){		
	error_log("info\r\n",3,'d');
}

?>