<?php 
require(dirname(__FILE__).'/_config.php'); 

use Buzz\Browser;
use Buzz\Message\Request;
use Buzz\Message\Response;
use Buzz\Client\FileGetContents;

// doc : https://github.com/kriswallsmith/Buzz

$url = 'http://web.cr6868.com/asmx/smsservice.aspx';
//$url = 'http://www.163.com/';
$arr = array(
	"name" => "peace",
	"pwd" => "2E1F4F21BC064C03957461A5E2FB",
	"type" => "balance",
);
$par = "name=peace&pwd=2E1F4F21BC064C03957461A5E2FB&type=balance";

function buzz1(){
	global $url, $arr, $par;
	if(empty($_GET['act'])){
		echo comHttp::doGet("$url?$par");
		return;
	}
	$browser = new Browser(); 
	$response = $browser->get("$url?$par");
	dump($browser->getLastRequest()."\n");
	dump($response);	
}

function buzz2(){
	global $url, $arr, $par;
	if(empty($_GET['act'])){
		echo comHttp::doPost($url,$arr);
		return;
	}
	$request = new Request('HEAD', '/', "$url?$par");
	$response = new Response();
	$client = new FileGetContents();
	$client->send($request, $response);
	//dump($request);
	dump($response);
}

# ------------------------
$timer1 = microtime(1);

buzz1();

$timer2 = microtime(1);
echo "<hr>";
# ------------------------

buzz2();

$timer3 = microtime(1);
echo "<hr>";
echo "<br>t1:".($timer2-$timer1);
echo "<br>t2:".($timer3-$timer2);
echo "<br> index : <a href='?' target='_blank'>use old</a> # <a href='?act=buzz' target='_blank'>use buzz</a> # <br>";
// basDebug
echo basDebug::runInfo();

?>
