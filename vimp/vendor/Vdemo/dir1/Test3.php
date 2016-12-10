<?php

namespace Vdemo\dir1;
//echo " test34567, ";

// ...类
class Test3{	
	
	static $prop3 = 'Test3-123';
	
	// func1
	static function func1($a=''){ 
		return "$a @ Core\Test3 : func1 / ".self::$prop3;
	}

}

