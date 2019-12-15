<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');

include_once(DIR_STATIC.'/ximp/class/QRcodeBase.cls_php'); 

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
	
	static function show($text, $size=3, $level=1, $margin=4, $type='png', $outfile=false){
		\QRcode::$type($text, $outfile, $level, $size, $margin);
	}

    static function logo($qrfp, $logo='', $newfp='') {
        //@ini_set("max_execution_time", "10"); // 设置该次请求超时时长，10s
        //@ini_set("request_terminate_timeout", "10"); // 兼容php-fpm设置超时
        dump(strpos($logo,'/'));
        if(empty($logo)) { // 使用系统的默认logo
            $logo = DIR_VIEWS . '/base/assets/logo/imcat-40x.png';
        }elseif(empty(strpos($logo,'/'))){
            $logo = DIR_VIEWS . '/base/assets/logo/'.$logo;
        }
        $newfp || $newfp = $qrfp;
        $qrfp = imagecreatefromstring(file_get_contents($qrfp)); 
        $logo = imagecreatefromstring(file_get_contents($logo));
        if ($qrfp && $logo) {
            $QR_width = imagesx($qrfp);
            $QR_height = imagesy($qrfp);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $dst_w = $dst_h = $QR_width / 4; // 中间logo是方的
            //$dst_h = $logo_height / ($logo_width/$dst_w);
            $from_width = ($QR_width - $dst_w) / 2;
            imagecopyresampled($qrfp, $logo, $from_width, $from_width, 0, 0, $dst_w, $dst_h, $logo_width, $logo_height);
        }
        return imagepng($qrfp, $newfp);
    }

}
