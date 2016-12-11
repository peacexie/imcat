<?php
(!defined('RUN_INIT')) && die('No Init');
include_once(DIR_STATIC.'/ximp/class/Snoopy.cls_php'); 

class extSnoopy extends Snoopy{
	
	static function xx_($text){
		//return xxx;
	}
	
}

/*

//http://blog.csdn.net/snow_online/article/details/5474382
//Snoopy中文手册(php采集利器)-001【转】

$snoopy = new extSnoopy();  

$snoopy->submitlinks("http://www.php.net/");  
dump($snoopy->results);  ;  
  
$snoopy->fetchtext("http://www.php.net/");  
dump($snoopy->results);

/*
$submit_url = "http://lnk.ispi.net/texis/scripts/msearch/netsearch.html";  
$submit_vars["q"] = "amiga";  
$submit_vars["submit"] = "Search!";  
$submit_vars["searchhost"] = "Altavista";  
$snoopy->submit($submit_url,$submit_vars);  
print $snoopy->results; 
*/

/*
$snoopy->maxframes=5;  
$snoopy->fetch("http://www.ispi.net/");  
echo "<PRE>/n";  
echo htmlentities($snoopy->results[0]);   
echo htmlentities($snoopy->results[1]);   
echo htmlentities($snoopy->results[2]);   
echo "</PRE>/n";  
*-/

$snoopy->fetchform("http://www.altavista.com");  
dump($snoopy->results);  

*/
