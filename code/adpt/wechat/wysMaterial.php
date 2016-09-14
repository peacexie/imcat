<?php
(!defined('RUN_MODE')) && die('No Init');
// 素材管理接口
// 如果本系统修改,就改这个文件，不用改wmp*文件

class wysMaterial extends wmpMaterial{
	
	function __construct($cfg=array()){
		parent::__construct($cfg); 
	}
	
	function getName($fdata){
		$header = strtoupper(bin2hex($fdata));
		$cfg = $this->cfgTypes();
		$ename = 'unknow';
		foreach(array(6,4,) as $k){ //14,12,10,8,
			$chi = substr($header,0,$k); 
			if(isset($cfg[$chi])){
				$ename = $cfg[$chi];
			}
		}
		$bname = date('Y-md-His-').basKeyid::kidTemp();
		return "$bname.$ename";
	} 
	
	function cfgTypes(){
        return array(
			//请按key顺序排列，如果前N位相同，则往后加两位
			'424D' => 'bmp',
			'4749' => 'gif',
			'4949' => 'tif',
			'8950' => 'png',
			'FFD8' => 'jpg',
			/* 需要的化，根据以下扩展吧
			// 视频格式手机支持的一般是mp4和3gp    音频一般是mp3 acc
			array("FFD8FFE1","jpg"),
			array("89504E47","png"),
			array("47494638","gif"),
			array("49492A00","tif"),
			array("424D","bmp"),
			array("41433130","dwg"),
			array("38425053","psd"),
			array("7B5C727466","rtf"),
			array("3C3F786D6C","xml"),
			array("68746D6C3E","html"),
			array("44656C69766572792D646174","eml"),
			array("CFAD12FEC5FD746F","dbx"),
			array("2142444E","pst"),
			array("D0CF11E0","xls/doc"),
			array("5374616E64617264204A","mdb"),
			array("FF575043","wpd"),
			array("252150532D41646F6265","eps/ps"),
			array("255044462D312E","pdf"),
			array("E3828596","pwl"),
			array("504B0304","zip"),
			array("52617221","rar"),
			array("57415645","wav"),
			array("41564920","avi"),
			array("2E7261FD","ram"),
			array("2E524D46","rm"),
			array("000001BA","mpg"),
			array("000001B3","mpg"),
			array("6D6F6F76","mov"),
			array("3026B2758E66CF11","asf"),
			array("4D546864","mid"),
			*/
		);
    }

	static function getLocal($path){ 
		return DIR_STATIC.$path;
	}

}
