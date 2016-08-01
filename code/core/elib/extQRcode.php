<?php
(!defined('RUN_MODE')) && die('No Init');
require(DIR_STATIC.'/ilibs/QRcodeBase.cls_php'); 

/*
 * PHP QR Code encoder, Version: 1.1.4, Build: 2010100721
 * http://phpqrcode.sourceforge.net/
 * https://sourceforge.net/projects/phpqrcode/
  public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) 
  public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) 
  public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) 
  
 * 二维码最大容量是1850个大写字母,2710个数字,1108个字节,500多个汉字
  
 */
 
class extQRcode{
	
	public static function show($text, $size=3, $level=1, $margin=4, $type='png', $outfile=false){
		QRcode::$type($text, $outfile, $level, $size, $margin);
	}
}
