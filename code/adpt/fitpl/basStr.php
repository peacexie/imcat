<?php

// 

class basStr{    

/*
* url
* sql
* js(tag)
*/

    static function ctPath($para,$tpldir){ 
        $fext = str_replace(array('/','+','*','|','?',':'),array('~','-','.','!','$',';'),$para); 
        $fext = str_replace(array('[modid,','[limit,','[cache,','[show,'),array('[m','[n','[c','[s'),$fext); 
        $fext = basStr::filTitle($fext); //del:&,#
        if(strlen($fext)>150) $fext = substr($fext,0,20).'~'.md5($fext);
        $path = "/_tagc/$tpldir$fext.cac_htm"; //".(substr($fmd5,0,1))."/
        return $path;
    }

	// 缓存:文件名/目录名
    static function cacKey($str,$pos=''){
		if($pos){
			$str = substr($str,strpos($str,$pos)+1);
		}
		$key = urlencode($str);
		$key = str_replace(array("%26","%3D",'%'),array(".","-",'u'),$key);
		$pos = strpos($key,'.row-'); 
		if($pos && $pos>72){
			$s1 = substr($key,0,15);	
			$s2 = substr($key,$pos-9,9);
			$s5 = md5($key);
			$key = "$s1($s5){$s2}".substr($key,$pos);
		}
		return $key;
		// ajaxGetPage&cityname=东莞&field=本地户口&row=0&startNum=&endNum=
		// ajaxGetPage%26cityname%3D%E4%B8%9C%E8%8E%9E%26field%3D%E6%9C%AC%E5%9C%B0%E6%88%B7%E5%8F%A3%26row%3D0%26startNum%3D%26endNum%3D
	}

}