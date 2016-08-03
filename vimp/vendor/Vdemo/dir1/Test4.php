<?php

namespace Vdemo\dir1;
//echo " test45678, ";

use Vdemo\dir1\Test3; 

// ...类
class Test4{	
	
	static $prop4 = 'Test4-234';
	
	// func3
	static function func2($a=''){ 
		echo "{{{ Test4:func2 call Test3:func1 : Start : [[ <br>";
		echo Test3::func1(' peace '); 
		echo "<br> ]] End : }}}<hr>";
		return "$a @ Core\Test4 : func2 / ".self::$prop4;
	}

}

